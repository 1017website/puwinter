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
    // Jelajahi semua kelas yang tersedia (published)
    public function explore(Request $request): View
    {
        $user      = $request->user();
        $subjectId = $request->get('subject_id');
        $type      = $request->get('type'); // gratis | premium
        $search    = $request->get('search');

        $enrolledIds = $user->enrollments()->pluck('course_id');

        $query = Course::published()
            ->regularType()
            ->forUser($user)
            ->with(['subject', 'mentor'])
            ->withCount('enrollments')
            ->when($subjectId, fn($q) => $q->where('subject_id', $subjectId))
            ->when($type === 'gratis',  fn($q) => $q->where('is_premium', false))
            ->when($type === 'premium', fn($q) => $q->where('is_premium', true))
            ->when($search, fn($q) => $q->where('title', 'like', "%{$search}%"))
            ->orderByDesc('enrollments_count');

        $courses  = $query->paginate(12)->withQueryString();
        $subjects = \App\Models\Subject::active()->get();

        return view('student.courses.explore', compact(
            'courses', 'subjects', 'enrolledIds', 'subjectId', 'type', 'search'
        ));
    }

    // Daftar kelas yang diikuti user
    public function index(Request $request): View
    {
        $user   = $request->user();
        $filter = $request->get('filter', 'semua');

        // Fix: eager load modules.materials supaya tidak N+1 di course-card
        $query = $user->enrollments()->with(['course.subject', 'course.mentor', 'course.modules.materials']);

        // Hanya tampilkan enrollment kelas REGULAR di menu ini. Extra Class punya menu sendiri.
        $query->whereHas('course', fn($q) => $q->where('course_type', \App\Models\Course::TYPE_REGULAR));

        // Batasi hanya kelas (course) yang sesuai grade siswa
        // Batasi kelas yang sesuai grade siswa. Kelas EXTRA (mis. TOEFL) selalu lolos.
        if (!in_array($user->role, ['superadmin', 'admin', 'mentor']) && !empty($user->grade_id)) {
            $query->whereHas('course', function ($q) use ($user) {
                $q->where(function ($qq) use ($user) {
                    $qq->where('course_type', \App\Models\Course::TYPE_EXTRA)
                        ->orWhereNull('grade_id')
                        ->orWhere('grade_id', $user->grade_id);
                });
            });
        }

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
    public function show(Request $request, string $slug): View|RedirectResponse
    {
        $user   = $request->user();
        $course = Course::published()->where('slug', $slug)
            ->with(['subject', 'mentor', 'modules.materials'])
            ->firstOrFail();

        // Cek akses kelas (grade + program + access_tier) dalam satu pintu.
        if (!$course->isAccessibleBy($user)) {
            // Belum terdaftar di program -> arahkan daftar program dulu.
            if ($course->plan_id && !$user->isEnrolledInProgram($course->plan_id)) {
                return redirect()->route('student.program.show', $course->plan_id)
                    ->with('error', 'Daftar dulu ke program ini untuk mengakses kelasnya.');
            }
            if ($course->plan_id && $course->access_tier === 'paid' && !$user->hasPaidProgram($course->plan_id)) {
                abort(403, 'Kelas ini hanya untuk peserta berbayar di program ini.');
            }
            abort(403, 'Kelas ini tidak tersedia untuk kelas kamu.');
        }

        // Catat enrollment course (progress tracking) — hanya jika sudah berhak akses.
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
    public function showMaterial(Request $request, string $slug, int $materialId): View|RedirectResponse
    {
        $user   = $request->user();
        $course = Course::published()->where('slug', $slug)
            ->with(['subject', 'mentor', 'modules.materials'])
            ->firstOrFail();

        // Cek akses kelas (grade + program + access_tier) dalam satu pintu.
        if (!$course->isAccessibleBy($user)) {
            if ($course->plan_id && !$user->isEnrolledInProgram($course->plan_id)) {
                return redirect()->route('student.program.show', $course->plan_id)
                    ->with('error', 'Daftar dulu ke program ini untuk mengakses kelasnya.');
            }
            if ($course->plan_id && $course->access_tier === 'paid' && !$user->hasPaidProgram($course->plan_id)) {
                abort(403, 'Kelas ini hanya untuk peserta berbayar di program ini.');
            }
            abort(403, 'Kelas ini tidak tersedia untuk kelas kamu.');
        }

        $material = CourseMaterial::whereHas('module', fn($q) => $q->where('course_id', $course->id))
            ->findOrFail($materialId);

        // Lock check: materi 'paid' untuk peserta yang belum berbayar di program ini.
        $materialTier = $material->access_tier ?: ($course->access_tier ?? 'both');
        $isLocked = ($materialTier === 'paid')
            && !$user->hasPaidProgram($course->plan_id)
            && !in_array($user->role, ['superadmin', 'admin', 'mentor']);

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
