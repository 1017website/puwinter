<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AchievementController extends Controller
{
    /**
     * Daftar semua achievement. Yang sudah diraih user ditandai,
     * yang belum tampil sebagai "terkunci".
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Semua achievement aktif
        $achievements = Achievement::active()->orderBy('id')->get();

        // Map achievement_id => earned_at milik user
        $earned = $user->achievements()
            ->with('achievement')
            ->get()
            ->keyBy('achievement_id');

        $totalEarned = $earned->count();
        $totalAll    = $achievements->count();

        return view('student.achievements.index', compact(
            'achievements', 'earned', 'totalEarned', 'totalAll'
        ));
    }
}
