<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\UserTryoutAttempt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TryoutHistoryController extends Controller
{
    /**
     * Daftar riwayat pengerjaan tryout milik user (yang sudah disubmit).
     */
    public function index(Request $request): View
    {
        $attempts = UserTryoutAttempt::where('user_id', $request->user()->id)
            ->whereNotNull('submitted_at')
            ->with(['tryout:id,title,slug,subject_id,total_questions', 'tryout.subject:id,name'])
            ->latest('submitted_at')
            ->paginate(15);

        // Statistik ringkas
        $base = UserTryoutAttempt::where('user_id', $request->user()->id)
            ->whereNotNull('submitted_at');

        $stats = [
            'total_attempt' => (clone $base)->count(),
            'avg_score'     => round((float) (clone $base)->avg('score'), 1),
            'best_score'    => (float) (clone $base)->max('score'),
            'best_rank'     => (clone $base)->whereNotNull('rank_at_submit')->min('rank_at_submit'),
        ];

        return view('student.tryout-history.index', compact('attempts', 'stats'));
    }
}
