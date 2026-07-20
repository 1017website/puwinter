<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\DemoVideo;
use App\Models\SubscriptionPlan;
use App\Models\TryoutQuestion;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        // Program dari DB
        $plans = SubscriptionPlan::active()->orderBy('order')->get();

        // Stats platform dari DB
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'total_soal' => TryoutQuestion::count(),
            'total_materi' => CourseMaterial::count(),
            'total_kelas' => Course::published()->count(),
        ];

        $frontend = $this->frontendSettings();
        $demoVideos = $this->demoVideos();

        return view('welcome', compact('plans', 'stats', 'frontend', 'demoVideos'));
    }

    public function index2(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        // Program dari DB
        $plans = SubscriptionPlan::active()->orderBy('order')->get();

        // Stats platform dari DB
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'total_soal' => TryoutQuestion::count(),
            'total_materi' => CourseMaterial::count(),
            'total_kelas' => Course::published()->count(),
        ];

        $frontend = $this->frontendSettings();

        return view('welcome2', compact('plans', 'stats', 'frontend'));
    }

    public function index3(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        // Program dari DB
        $plans = SubscriptionPlan::active()->orderBy('order')->get();

        // Stats platform dari DB
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'total_soal' => TryoutQuestion::count(),
            'total_materi' => CourseMaterial::count(),
            'total_kelas' => Course::published()->count(),
        ];

        $frontend = $this->frontendSettings();

        return view('welcome3', compact('plans', 'stats', 'frontend'));
    }

    private function frontendSettings(): array
    {
        return AppSetting::frontendInfo();
    }

    private function demoVideos()
    {
        return DemoVideo::query()
            ->active()
            ->orderBy('grade_level')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy('grade_level');
    }
}
