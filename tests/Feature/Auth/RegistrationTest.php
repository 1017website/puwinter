<?php

namespace Tests\Feature\Auth;

use App\Models\Grade;
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
}
