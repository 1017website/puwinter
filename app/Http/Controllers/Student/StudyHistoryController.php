<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudyHistory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudyHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $user   = $request->user();
        $period = $request->get('period', '7');

        // Aktivitas terbaru
        $activities = $user->studyHistories()
            ->where('created_at', '>=', now()->subDays((int) $period))
            ->latest('created_at')
            ->paginate(15);

        // Statistik ringkasan
        $stats = $user->studyHistories()
            ->where('created_at', '>=', now()->subDays((int) $period))
            ->selectRaw('
                SUM(duration_seconds) as total_seconds,
                COUNT(*) as total_activities,
                AVG(score) as avg_score
            ')
            ->first();

        // Grafik waktu belajar per hari
        $chartData = $user->studyHistories()
            ->where('created_at', '>=', now()->subDays((int) $period))
            ->selectRaw('DATE(created_at) as date, SUM(duration_seconds) / 3600 as hours')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Distribusi per mata pelajaran (via tryout)
        $subjectDistribution = $user->tryoutAttempts()
            ->whereNotNull('submitted_at')
            ->with('tryout.subject')
            ->get()
            ->groupBy('tryout.subject.name')
            ->map(fn($attempts) => $attempts->sum(fn($a) => now()->diffInSeconds($a->started_at)));

        return view('student.study-history.index', compact(
            'activities', 'stats', 'chartData', 'subjectDistribution', 'period'
        ));
    }
}
