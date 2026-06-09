<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\SendVerificationCode;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->has('name') && !$request->has('first_name')) {
            $parts = explode(' ', trim($request->input('name')));
            $lastName = count($parts) > 1 ? array_pop($parts) : '';
            $firstName = implode(' ', $parts);
            if (empty($firstName) && !empty($lastName)) {
                $firstName = $lastName;
                $lastName = '';
            }
            $request->merge([
                'first_name' => $firstName,
                'last_name' => $lastName,
            ]);
        }

        $request->validate([
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', Rules\Password::defaults()],
            'role' => ['required', Rule::in(['student', 'instructor'])],
        ]);

        $code = (string) rand(100000, 999999);

        // Store registration info in session temporarily
        session([
            'registration_data' => [
                'name' => trim($request->first_name . ' ' . $request->last_name),
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ],
            'email_verification_code' => $code,
            'email_verification_expires_at' => now()->addMinutes(15),
        ]);

        // Send validation code via SMTP
        try {
            Mail::to($request->email)->send(new SendVerificationCode($code));
        } catch (\Exception $e) {
            // Log warning, but let session continue for local/dev if needed, or fail gracefully
            logger()->warning("Failed sending registration email to {$request->email} (Verification Code: {$code}): " . $e->getMessage());
        }

        return redirect()->route('register.verify-email');
    }

    /**
     * Show the email verification code screen.
     */
    public function showVerifyEmail(): View|RedirectResponse
    {
        $data = session('registration_data');
        if (!$data || !session('email_verification_code')) {
            return redirect()->route('register')->withErrors(['email' => __('messages.auth.register.fill_first')]);
        }

        return view('auth.verify-email-code', [
            'email' => $data['email']
        ]);
    }

    /**
     * Handle email verification code submission.
     */
    public function verifyEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $regData = session('registration_data');
        $sessionCode = session('email_verification_code');
        $expiresAt = session('email_verification_expires_at');

        if (!$regData || !$sessionCode || !$expiresAt) {
            return redirect()->route('register')->withErrors(['email' => __('messages.auth.register.session_expired')]);
        }

        if (now()->greaterThan($expiresAt)) {
            return redirect()->route('register.verify-email')->withErrors(['code' => __('messages.auth.register.code_expired')]);
        }

        if ($request->code !== $sessionCode) {
            return redirect()->route('register.verify-email')->withErrors(['code' => __('messages.auth.register.invalid_code')]);
        }

        // Create the verified user
        $user = User::create([
            'name' => $regData['name'],
            'email' => $regData['email'],
            'password' => $regData['password'],
            'role' => $regData['role'],
            'email_verified_at' => now(),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Clear verification session data
        session()->forget(['registration_data', 'email_verification_code', 'email_verification_expires_at']);

        if ($user->role === 'instructor') {
            return redirect()->route('teacher.dashboard');
        }

        return redirect()->route('home');
    }

    /**
     * Resend verification code.
     */
    public function resendCode(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $regData = session('registration_data');
        if (!$regData) {
            if ($request->wantsJson()) {
                return response()->json(['error' => __('messages.auth.register.data_missing')], 422);
            }
            return redirect()->route('register')->withErrors(['email' => __('messages.auth.register.data_missing')]);
        }

        $code = (string) rand(100000, 999999);

        session([
            'email_verification_code' => $code,
            'email_verification_expires_at' => now()->addMinutes(15),
        ]);

        try {
            Mail::to($regData['email'])->send(new SendVerificationCode($code));
        } catch (\Exception $e) {
            logger()->warning("Failed resending registration email to {$regData['email']} (Verification Code: {$code}): " . $e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => __('messages.auth.register.code_resent')]);
        }

        return redirect()->route('register.verify-email')->with('success', __('messages.auth.register.code_resent'));
    }
}
