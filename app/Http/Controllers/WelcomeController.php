<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Tryout;
use App\Models\TryoutQuestion;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    public function index(): View|\Illuminate\Http\RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        // Program dari DB
        $plans = SubscriptionPlan::active()->orderBy('order')->get();

        // Stats platform dari DB
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'total_soal'     => TryoutQuestion::count(),
            'total_materi'   => \App\Models\CourseMaterial::count(),
            'total_kelas'    => Course::published()->count(),
        ];

        return view('welcome', compact('plans', 'stats'));
    }

    public function index2(): View|\Illuminate\Http\RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        // Program dari DB
        $plans = SubscriptionPlan::active()->orderBy('order')->get();

        // Stats platform dari DB
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'total_soal'     => TryoutQuestion::count(),
            'total_materi'   => \App\Models\CourseMaterial::count(),
            'total_kelas'    => Course::published()->count(),
        ];

        return view('welcome2', compact('plans', 'stats'));
    }

    public function index3(): View|\Illuminate\Http\RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        // Program dari DB
        $plans = SubscriptionPlan::active()->orderBy('order')->get();

        // Stats platform dari DB
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'total_soal'     => TryoutQuestion::count(),
            'total_materi'   => \App\Models\CourseMaterial::count(),
            'total_kelas'    => Course::published()->count(),
        ];

        return view('welcome3', compact('plans', 'stats'));
    }
}
