<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LiveClass;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Progress kelas yang di-enroll
        $enrollments = $user->enrollments()
            ->with(['course.subject', 'course.mentor', 'course.modules.materials'])
            ->latest('last_accessed_at')
            ->take(4)
            ->get();

        // Materi terakhir diakses (untuk "Lanjutkan Belajar")
        $lastProgress = $user->materialProgress()
            ->with(['material.module.course.subject'])
            ->where('is_completed', false)
            ->latest('updated_at')
            ->first();

        // Jadwal live class mendatang
        $upcomingLiveClasses = LiveClass::upcoming()
            ->with(['mentor', 'subject'])
            ->take(3)
            ->get();

        // Live class sedang berlangsung
        $liveLiveClass = LiveClass::live()
            ->with(['mentor', 'subject'])
            ->first();

        // Aktivitas belajar terbaru
        $recentActivities = $user->studyHistories()
            ->latest('created_at')
            ->take(5)
            ->get();

        // Statistik belajar 7 hari terakhir
        $studyStats = $user->studyHistories()
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, SUM(duration_seconds) as total_seconds')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Badge/achievement terbaru
        $recentAchievements = $user->achievements()
            ->with('achievement')
            ->latest('earned_at')
            ->take(4)
            ->get();

        // Rekomendasi kelas: published, belum dienroll user, max 4
        $enrolledCourseIds = $user->enrollments()->pluck('course_id');
        $recommendedCourses = Course::published()
            ->whereNotIn('id', $enrolledCourseIds)
            ->with(['subject', 'mentor'])
            ->withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->take(4)
            ->get();

        // Jumlah notifikasi (pending live class yang belum dilihat — sederhana)
        $notifCount = LiveClass::upcoming()
            ->where('scheduled_at', '<=', now()->addDays(3))
            ->count();

        return view('student.dashboard.index', compact(
            'user',
            'enrollments',
            'lastProgress',
            'upcomingLiveClasses',
            'liveLiveClass',
            'recentActivities',
            'studyStats',
            'recentAchievements',
            'recommendedCourses',
            'notifCount',
        ));
    }
}
