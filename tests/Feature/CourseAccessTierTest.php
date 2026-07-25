<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\CourseModule;
use App\Models\ProgramEnrollment;
use App\Models\Subject;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseAccessTierTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_program_member_can_open_both_tier_course_with_legacy_premium_flag(): void
    {
        [$student, $course] = $this->createCourseScenario();

        $response = $this->actingAs($student)->get(route('student.course.explore', [
            'type' => 'gratis',
        ]));

        $response->assertOk()
            ->assertSee($course->title)
            ->assertSee('Mulai Kelas')
            ->assertDontSee('Upgrade untuk Akses');
    }

    public function test_both_tier_material_is_not_locked_by_legacy_premium_flag(): void
    {
        [$student, $course, $material] = $this->createCourseScenario();

        $this->actingAs($student)
            ->get(route('student.course.show', $course->slug))
            ->assertOk()
            ->assertSee(route('student.course.material.show', [$course->slug, $material->id]), false);

        $this->actingAs($student)
            ->get(route('student.course.material.show', [$course->slug, $material->id]))
            ->assertOk()
            ->assertViewHas('isLocked', false);
    }

    public function test_paid_tier_remains_locked_for_free_program_member(): void
    {
        [$student, $course, $material] = $this->createCourseScenario();
        $course->update(['access_tier' => 'paid', 'is_premium' => false]);
        $material->update(['access_tier' => 'paid', 'is_premium' => false]);

        $this->actingAs($student)
            ->get(route('student.course.explore', ['type' => 'premium']))
            ->assertOk()
            ->assertSee($course->title)
            ->assertSee('Upgrade untuk Akses');

        $this->assertFalse($material->fresh()->isAccessibleBy($student));
    }

    public function test_admin_update_synchronizes_legacy_premium_flag_from_access_tier(): void
    {
        [, $course] = $this->createCourseScenario();
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->put(route('admin.courses.update', $course), [
            'title' => $course->title,
            'subject_id' => $course->subject_id,
            'course_type' => Course::TYPE_REGULAR,
            'plan_id' => $course->plan_id,
            'access_tier' => 'both',
            'mentor_id' => $course->mentor_id,
            'is_published' => '1',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'access_tier' => 'both',
            'is_premium' => false,
        ]);
    }

    private function createCourseScenario(): array
    {
        $student = User::factory()->create(['role' => 'student']);
        $mentor = User::factory()->create(['role' => 'mentor']);
        $subject = Subject::create([
            'name' => 'Bahasa Inggris',
            'slug' => 'bahasa-inggris',
            'is_active' => true,
        ]);
        $plan = SubscriptionPlan::create([
            'name' => 'Program TKA',
            'slug' => 'program-tka',
            'duration_months' => 6,
            'price' => 100000,
            'original_price' => 150000,
            'is_active' => true,
        ]);

        ProgramEnrollment::create([
            'user_id' => $student->id,
            'plan_id' => $plan->id,
            'status' => ProgramEnrollment::STATUS_FREE,
            'enrolled_at' => now(),
        ]);

        $course = Course::create([
            'subject_id' => $subject->id,
            'mentor_id' => $mentor->id,
            'plan_id' => $plan->id,
            'course_type' => Course::TYPE_REGULAR,
            'access_tier' => 'both',
            'title' => 'Kelas Gratis untuk Semua Peserta',
            'slug' => 'kelas-gratis-semua-peserta',
            'is_premium' => true,
            'is_published' => true,
        ]);
        $module = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Modul Pertama',
            'order' => 1,
        ]);
        $material = CourseMaterial::create([
            'module_id' => $module->id,
            'title' => 'Materi Gratis',
            'type' => 'video',
            'content_url' => 'https://www.youtube.com/watch?v=test',
            'access_tier' => 'both',
            'is_premium' => true,
            'order' => 1,
        ]);

        return [$student, $course, $material];
    }
}
