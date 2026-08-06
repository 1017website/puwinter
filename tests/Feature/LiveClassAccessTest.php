<?php

namespace Tests\Feature;

use App\Models\Grade;
use App\Models\LiveClass;
use App\Models\ProgramEnrollment;
use App\Models\Subject;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiveClassAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_tier_live_class_auto_enrolls_matching_grade_student(): void
    {
        [$student, $liveClass, $plan] = $this->createScenario();

        $this->assertFalse($student->isEnrolledInProgram($plan->id));
        $this->assertSame(LiveClass::ACCESS_OK, $liveClass->accessStatusFor($student));

        $this->actingAs($student)
            ->get(route('student.live.show', $liveClass->id))
            ->assertOk()
            ->assertSee($liveClass->title);

        $this->assertDatabaseHas('program_enrollments', [
            'user_id' => $student->id,
            'plan_id' => $plan->id,
            'status'  => ProgramEnrollment::STATUS_FREE,
        ]);
    }

    public function test_free_tier_live_class_shows_join_button_in_list(): void
    {
        [$student, $liveClass] = $this->createScenario();

        $this->actingAs($student)
            ->get(route('student.live.index'))
            ->assertOk()
            ->assertSee($liveClass->title)
            ->assertSee('Bergabung Sekarang')
            ->assertDontSee('Daftar Program');
    }

    public function test_free_tier_live_class_join_redirects_to_zoom(): void
    {
        [$student, $liveClass] = $this->createScenario();

        $this->actingAs($student)
            ->get(route('student.live.join', $liveClass->id))
            ->assertRedirect($liveClass->zoom_link);

        $this->assertDatabaseHas('live_class_attendances', [
            'live_class_id' => $liveClass->id,
            'user_id'       => $student->id,
        ]);
    }

    public function test_paid_tier_live_class_reports_program_reason_not_grade_reason(): void
    {
        [$student, $liveClass, $plan] = $this->createScenario();
        $liveClass->update(['access_tier' => 'paid']);

        $this->assertSame(LiveClass::ACCESS_NOT_ENROLLED, $liveClass->fresh()->accessStatusFor($student));

        $this->actingAs($student)
            ->get(route('student.live.show', $liveClass->id))
            ->assertRedirect(route('student.program.show', $plan->id))
            ->assertSessionHas('error', fn ($error) => str_contains($error, 'belum terdaftar')
                && ! str_contains($error, 'kelasmu'));

        $this->assertDatabaseMissing('program_enrollments', [
            'user_id' => $student->id,
            'plan_id' => $plan->id,
        ]);
    }

    public function test_paid_tier_live_class_stays_locked_for_free_program_member(): void
    {
        [$student, $liveClass] = $this->createScenario();
        $liveClass->update(['access_tier' => 'paid']);
        ProgramEnrollment::create([
            'user_id'     => $student->id,
            'plan_id'     => $liveClass->plan_id,
            'status'      => ProgramEnrollment::STATUS_FREE,
            'enrolled_at' => now(),
        ]);

        $this->assertSame(
            LiveClass::ACCESS_NEEDS_PAID,
            $liveClass->fresh()->accessStatusFor($student->fresh())
        );

        $this->actingAs($student->fresh())
            ->get(route('student.live.index'))
            ->assertOk()
            ->assertSee('Khusus Peserta Berbayar')
            ->assertDontSee('Bergabung Sekarang');
    }

    public function test_wrong_grade_reports_the_actual_target_grade(): void
    {
        [$student, $liveClass] = $this->createScenario();
        $grade11 = Grade::where('code', '11')->firstOrFail();
        $liveClass->update(['grade_id' => $grade11->id]);

        $this->assertSame(LiveClass::ACCESS_WRONG_GRADE, $liveClass->fresh()->accessStatusFor($student));

        $this->actingAs($student)
            ->get(route('student.live.show', $liveClass->id))
            ->assertRedirect(route('student.live.index'))
            ->assertSessionHas('error', fn ($error) => str_contains($error, $grade11->name));
    }

    public function test_student_without_grade_is_not_blocked_by_grade_gate(): void
    {
        [$student, $liveClass] = $this->createScenario();
        $student->update(['grade_id' => null]);

        $this->assertNotSame(
            LiveClass::ACCESS_WRONG_GRADE,
            $liveClass->fresh()->accessStatusFor($student->fresh())
        );
    }

    /**
     * Siswa Kelas 12 (free, belum terdaftar program) + kelas online LIVE
     * tier gratis untuk Kelas 12 pada program Kelas 12.
     *
     * @return array{0: User, 1: LiveClass, 2: SubscriptionPlan}
     */
    private function createScenario(): array
    {
        $grade12 = Grade::where('code', '12')->firstOrFail();

        $student = User::factory()->create([
            'role'     => 'student',
            'grade_id' => $grade12->id,
        ]);
        $mentor  = User::factory()->create(['role' => 'mentor']);
        $subject = Subject::create([
            'name'      => 'Bahasa Inggris',
            'slug'      => 'bahasa-inggris',
            'is_active' => true,
        ]);
        $plan = SubscriptionPlan::create([
            'name'            => 'Siap-Siap Literasi Bahasa Inggris UTBK-SNBT',
            'slug'            => 'literasi-bahasa-inggris-utbk-snbt',
            'duration_months' => 6,
            'price'           => 100000,
            'original_price'  => 150000,
            'is_active'       => true,
        ]);
        $plan->grades()->sync([$grade12->id]);

        $liveClass = LiveClass::create([
            'title'            => 'Detail Location - Scanning Skill UTBK SNBT',
            'subject_id'       => $subject->id,
            'mentor_id'        => $mentor->id,
            'grade_id'         => $grade12->id,
            'class_type'       => LiveClass::TYPE_REGULAR,
            'plan_id'          => $plan->id,
            'access_tier'      => 'free',
            'is_premium'       => false,
            'scheduled_at'     => now()->subMinutes(5),
            'duration_minutes' => 90,
            'zoom_link'        => 'https://zoom.us/j/123456789',
            'status'           => 'live',
        ]);

        return [$student, $liveClass, $plan];
    }
}
