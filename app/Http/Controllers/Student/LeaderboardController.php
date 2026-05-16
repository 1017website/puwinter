<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LeaderboardScore;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function index(Request $request): View
    {
        $user      = $request->user();
        $filter    = $request->get('filter', 'global');
        $subjectId = $request->get('subject_id');

        $query = LeaderboardScore::with('user')
            ->when($subjectId, fn($q) => $q->where('subject_id', $subjectId))
            ->unless($subjectId, fn($q) => $q->whereNull('subject_id'));

        $query = match ($filter) {
            'sekolah'  => $query->whereHas('user', fn($q) => $q->where('school', $user->school)),
            'kota'     => $query->whereHas('user', fn($q) => $q->where('city', $user->city)),
            'provinsi' => $query->whereHas('user', fn($q) => $q->where('province', $user->province)),
            default    => $query,
        };

        $leaderboard = $query->orderByDesc('total_score')->take(50)->get();

        // Posisi user sendiri
        $myScore = LeaderboardScore::where('user_id', $user->id)
            ->when($subjectId, fn($q) => $q->where('subject_id', $subjectId))
            ->unless($subjectId, fn($q) => $q->whereNull('subject_id'))
            ->first();

        $subjects = Subject::active()->get();

        return view('student.leaderboard.index', compact(
            'leaderboard', 'myScore', 'subjects', 'filter', 'subjectId'
        ));
    }
}
