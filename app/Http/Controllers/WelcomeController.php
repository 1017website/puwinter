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
            ->orderByRaw("CASE category WHEN '7' THEN 1 WHEN '8' THEN 2 WHEN '9' THEN 3 WHEN '10' THEN 4 WHEN '11' THEN 5 WHEN '12' THEN 6 WHEN 'toefl' THEN 7 ELSE 8 END")
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy('category');
    }
}
