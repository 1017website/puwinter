<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\Grade;
use App\Models\RegistrationCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationCodeAndTryoutSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_generate_a_registration_code(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.registration-codes.store'), [
            'name' => 'Sekolah Mitra Gelombang 1',
            'description' => 'Kelompok siswa sekolah mitra.',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        $code = RegistrationCode::firstOrFail();
        $this->assertStringStartsWith('PWIN-', $code->code);
        $this->assertSame($admin->id, $code->created_by);
        $this->assertTrue($code->is_active);
    }

    public function test_student_registration_is_grouped_by_the_used_registration_code(): void
    {
        $grade = Grade::query()->firstOrFail();
        $code = RegistrationCode::create([
            'name' => 'Komunitas Belajar A',
            'code' => 'PWIN-TEST1234',
            'is_active' => true,
        ]);

        $response = $this->post(route('register'), [
            'name' => 'Siswa Kode',
            'email' => 'siswa.kode@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'grade_id' => $grade->id,
            'registration_code' => strtolower($code->code),
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'siswa.kode@example.com',
            'registration_code_id' => $code->id,
        ]);
        $this->assertSame(1, $code->students()->count());
    }

    public function test_disabled_registration_code_cannot_be_used(): void
    {
        $grade = Grade::query()->firstOrFail();
        $code = RegistrationCode::create([
            'name' => 'Kode Nonaktif',
            'code' => 'PWIN-OFF12345',
            'is_active' => false,
        ]);

        $response = $this->post(route('register'), [
            'name' => 'Siswa Ditolak',
            'email' => 'ditolak@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'grade_id' => $grade->id,
            'registration_code' => $code->code,
        ]);

        $response->assertSessionHasErrors('registration_code');
        $this->assertGuest();
    }

    public function test_student_tryout_routes_are_blocked_when_admin_disables_the_feature(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        AppSetting::set('student_tryout_enabled', '0');

        $response = $this->actingAs($student)->get(route('student.tryout.index'));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_student_login_does_not_expose_staff_login_link(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee(route('staff.login'))
            ->assertDontSee('Login Staff');
    }
}
