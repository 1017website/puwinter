<?php

namespace Tests\Feature\Auth;

use App\Models\AppSetting;
use App\Models\EmailVerification;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $grade = Grade::query()->firstOrFail();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '081234567892',
            'password' => 'password',
            'password_confirmation' => 'password',
            'grade_id' => $grade->id,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('verification.notice', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'phone' => '081234567892',
        ]);

        $user = User::where('email', 'test@example.com')->firstOrFail();
        $this->assertFalse($user->hasVerifiedEmail());
        $this->assertSame(1, EmailVerification::where('user_id', $user->id)->count());
    }

    public function test_phone_number_is_required_to_register(): void
    {
        $grade = Grade::query()->firstOrFail();

        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'grade_id' => $grade->id,
        ])->assertSessionHasErrors('phone');

        $this->assertGuest();
    }

    public function test_new_user_can_register_without_email_verification_when_admin_disables_it(): void
    {
        AppSetting::set('email_verification_enabled', '0');
        $grade = Grade::query()->firstOrFail();

        $response = $this->post('/register', [
            'name' => 'User Tanpa Verifikasi',
            'email' => 'tanpa.verifikasi@example.com',
            'phone' => '081234567893',
            'password' => 'password',
            'password_confirmation' => 'password',
            'grade_id' => $grade->id,
        ]);

        $user = User::where('email', 'tanpa.verifikasi@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertTrue($user->hasVerifiedEmail());
        $this->assertSame(0, EmailVerification::where('user_id', $user->id)->count());
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_email_verification_remains_enabled_by_default(): void
    {
        $this->assertTrue(AppSetting::emailVerificationEnabled());
    }
}
