<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\UserCourseEnrollment;
use App\Models\UserMaterialProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    // Daftar kelas yang diikuti user
    public function index(Request $request): View
    {
        $user   = $request->user();
        $filter = $request->get('filter', 'semua');

        // Fix: eager load modules.materials supaya tidak N+1 di course-card
        $query = $user->enrollments()->with(['course.subject', 'course.mentor', 'course.modules.materials']);

        $query = match ($filter) {
            'aktif'   => $query->active(),
            'selesai' => $query->completed(),
            'arsip'   => $query->whereNotNull('completed_at'),
            default   => $query,
        };

        $enrollments = $query->latest('last_accessed_at')->get();

        return view('student.courses.index', compact('enrollments', 'filter'));
    }

    // Detail kelas: daftar modul + materi
    public function show(Request $request, string $slug): View
    {
        $user   = $request->user();
        $course = Course::published()->where('slug', $slug)
            ->with(['subject', 'mentor', 'modules.materials'])
            ->firstOrFail();

        $this->authorize('view', $course);

        // Auto-enroll jika belum
        $enrollment = UserCourseEnrollment::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            ['enrolled_at' => now(), 'last_accessed_at' => now()]
        );

        $enrollment->update(['last_accessed_at' => now()]);

        // Progress materi user untuk kelas ini
        $completedMaterialIds = $user->materialProgress()
            ->whereHas('material.module', fn($q) => $q->where('course_id', $course->id))
            ->where('is_completed', true)
            ->pluck('material_id')
            ->toArray();

        $totalMaterials     = $course->materials()->count();
        $completedMaterials = count($completedMaterialIds);
        $progressPercentage = $totalMaterials > 0
            ? (int) round(($completedMaterials / $totalMaterials) * 100)
            : 0;

        $enrollment->update(['progress_percentage' => $progressPercentage]);

        return view('student.courses.show', compact(
            'course',
            'enrollment',
            'completedMaterialIds',
            'progressPercentage',
        ));
    }

    // Buka detail materi (video / pdf / quiz)
    public function showMaterial(Request $request, string $slug, int $materialId): View
    {
        $user   = $request->user();
        $course = Course::published()->where('slug', $slug)
            ->with(['subject', 'mentor', 'modules.materials'])
            ->firstOrFail();

        $this->authorize('view', $course);

        $material = CourseMaterial::whereHas('module', fn($q) => $q->where('course_id', $course->id))
            ->findOrFail($materialId);

        // Lock check: materi premium untuk non-premium user
        $isLocked = ($material->is_locked || $material->is_premium) && !$user->isPremium();

        // Enrollment
        $enrollment = UserCourseEnrollment::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            ['enrolled_at' => now(), 'last_accessed_at' => now()]
        );
        $enrollment->update(['last_accessed_at' => now()]);

        // Progress user
        $completedMaterialIds = $user->materialProgress()
            ->whereHas('material.module', fn($q) => $q->where('course_id', $course->id))
            ->where('is_completed', true)
            ->pluck('material_id')
            ->toArray();

        $totalMaterials     = $course->materials()->count();
        $completedMaterials = count($completedMaterialIds);
        $progressPercentage = $totalMaterials > 0
            ? (int) round(($completedMaterials / $totalMaterials) * 100)
            : 0;

        // Navigasi prev/next dalam urutan flat semua materi
        $allMaterials = $course->modules->flatMap(fn($m) => $m->materials)->values();
        $currentIndex = $allMaterials->search(fn($m) => $m->id === $material->id);
        $prevMaterial = $currentIndex > 0 ? $allMaterials[$currentIndex - 1] : null;
        $nextMaterial = $currentIndex < $allMaterials->count() - 1 ? $allMaterials[$currentIndex + 1] : null;

        return view('student.courses.material', compact(
            'course',
            'material',
            'enrollment',
            'isLocked',
            'completedMaterialIds',
            'progressPercentage',
            'prevMaterial',
            'nextMaterial',
        ));
    }

    // Tandai materi selesai
    public function markComplete(Request $request, int $materialId): RedirectResponse
    {
        $user = $request->user();

        UserMaterialProgress::updateOrCreate(
            ['user_id' => $user->id, 'material_id' => $materialId],
            ['is_completed' => true, 'completed_at' => now()]
        );

        return back()->with('success', 'Materi ditandai selesai.');
    }
}
