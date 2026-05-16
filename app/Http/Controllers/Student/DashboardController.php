<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LiveClass;
use App\Models\StudyHistory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Progress kelas yang di-enroll
        $enrollments = $user->enrollments()
            ->with(['course.subject', 'course.mentor'])
            ->latest('last_accessed_at')
            ->take(4)
            ->get();

        // Materi terakhir diakses (untuk "Lanjutkan Belajar")
        $lastProgress = $user->materialProgress()
            ->with(['material.module.course'])
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

        // Progress kelas per subject
        $classProgress = $user->enrollments()
            ->with('course.subject')
            ->get()
            ->groupBy('course.subject.name');

        return view('student.dashboard.index', compact(
            'user',
            'enrollments',
            'lastProgress',
            'upcomingLiveClasses',
            'liveLiveClass',
            'recentActivities',
            'studyStats',
            'recentAchievements',
            'classProgress',
        ));
    }
}
