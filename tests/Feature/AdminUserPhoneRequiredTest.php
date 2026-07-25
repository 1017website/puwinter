<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserPhoneRequiredTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_create_user_without_phone_number(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Siswa Tanpa HP',
            'email' => 'tanpa.hp@example.com',
            'role' => 'student',
            'password' => 'password123',
            'is_active' => '1',
        ])->assertSessionHasErrors('phone');

        $this->assertDatabaseMissing('users', [
            'email' => 'tanpa.hp@example.com',
        ]);
    }

    public function test_admin_created_user_phone_number_is_saved(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Siswa Dengan HP',
            'email' => 'dengan.hp@example.com',
            'phone' => '081234567899',
            'role' => 'student',
            'password' => 'password123',
            'is_active' => '1',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'dengan.hp@example.com',
            'phone' => '081234567899',
        ]);
    }
}
