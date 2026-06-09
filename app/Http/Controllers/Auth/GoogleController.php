<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function redirectToGoogle(Request $request): RedirectResponse
    {
        if ($request->has('role')) {
            session(['social_role' => $request->query('role')]);
        }

        if (empty(config('services.google.client_id')) || empty(config('services.google.client_secret'))) {
            if (app()->environment('local')) {
                return redirect()->route('auth.google.mock');
            }
            return redirect()->route('login')->withErrors([
                'google' => __('messages.auth.google.not_configured')
            ]);
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return RedirectResponse
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception $e) {
            return redirect()->route('login')->withErrors([
                'google' => __('messages.auth.google.failed', ['message' => $e->getMessage()])
            ]);
        }

        return $this->loginOrCreateSocialUser($googleUser);
    }

    /**
     * Show the developer mock consent screen.
     */
    public function showMockConsent()
    {
        if (!app()->environment('local')) {
            abort(404);
        }
        return view('auth.google-mock');
    }

    /**
     * Handle the callback from the mock consent screen.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function handleMockCallback(Request $request): RedirectResponse
    {
        if (!app()->environment('local')) {
            abort(404);
        }

        $request->validate([
            'google_id' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|email',
            'role' => 'required|string|in:student,instructor,admin',
        ]);

        // Construct a mock social user object
        $mockUser = new class($request->google_id, $request->name, $request->email, $request->role) {
            private $id;
            private $name;
            private $email;
            private $role;

            public function __construct($id, $name, $email, $role)
            {
                $this->id = $id;
                $this->name = $name;
                $this->email = $email;
                $this->role = $role;
            }

            public function getId() { return $this->id; }
            public function getName() { return $this->name; }
            public function getEmail() { return $this->email; }
            public function getRole() { return $this->role; }
        };

        // Put the role into session if redirecting
        session(['social_role' => $request->role]);

        return $this->loginOrCreateSocialUser($mockUser);
    }

    /**
     * Core registration & login logic for both actual and mock Google accounts.
     *
     * @param mixed $socialUser
     * @return RedirectResponse
     */
    protected function loginOrCreateSocialUser($socialUser): RedirectResponse
    {
        // Try to find the user by google_id
        $user = User::where('google_id', $socialUser->getId())->first();

        if (!$user) {
            // Try to find the user by email
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                // Associate the existing user with Google ID
                $user->update([
                    'google_id' => $socialUser->getId(),
                ]);
            } else {
                // Retrieve the role stored in redirect, defaulting to 'student'
                $role = session()->pull('social_role', 'student');
                
                // Keep role within allowed values
                if (!in_array($role, ['student', 'instructor', 'admin'])) {
                    $role = 'student';
                }

                // Create a new user
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'google_id' => $socialUser->getId(),
                    'password' => Hash::make(Str::random(24)),
                    'role' => $role,
                    'email_verified_at' => now(),
                ]);
            }
        }

        // Log the user in
        Auth::login($user);

        // Redirect based on role
        if ($user->role === 'admin') {
            return redirect('/admin');
        } elseif ($user->role === 'instructor') {
            return redirect('/teacher');
        }

        return redirect('/my-learning');
    }
}
