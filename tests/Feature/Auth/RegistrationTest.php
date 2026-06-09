<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendVerificationCode;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_registration_stores_session_and_redirects_to_verify(): void
    {
        Mail::fake();

        $response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name'  => 'User',
            'email'      => 'test@example.com',
            'password'   => 'password',
            'role'       => 'student',
        ]);

        // Should NOT be authenticated yet – needs to verify email first
        $this->assertGuest();

        // Should redirect to verification page
        $response->assertRedirect(route('register.verify-email'));

        // Verification code email should have been sent
        Mail::assertSent(SendVerificationCode::class);

        // Session should contain registration data
        $response->assertSessionHas('registration_data');
        $response->assertSessionHas('email_verification_code');
    }

    public function test_new_users_can_register_after_email_verification(): void
    {
        Mail::fake();

        // Step 1: submit registration form
        $this->post('/register', [
            'first_name' => 'Test',
            'last_name'  => 'User',
            'email'      => 'test@example.com',
            'password'   => 'password',
            'role'       => 'student',
        ]);

        // Grab the code from session
        $code = session('email_verification_code');
        $this->assertNotNull($code);

        // Step 2: submit the correct verification code
        $response = $this->post(route('register.verify-email.submit'), [
            'code' => $code,
        ]);

        // User should now be authenticated and redirected home
        $this->assertAuthenticated();
        $response->assertRedirect(route('home'));
    }
}
