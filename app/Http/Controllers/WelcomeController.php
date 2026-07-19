<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Course;
use App\Models\CourseMaterial;
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

        return view('welcome', compact('plans', 'stats', 'frontend'));
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
        $settings = AppSetting::frontendInfo();
        $url = trim((string) $settings['video_url']);
        $settings['video_type'] = 'file';
        $settings['video_provider'] = 'file';
        $settings['video_embed_url'] = '';

        if (preg_match('~(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{6,})~', $url, $matches)) {
            $settings['video_type'] = 'embed';
            $settings['video_provider'] = 'youtube';
            $settings['video_embed_url'] = 'https://www.youtube-nocookie.com/embed/'.$matches[1]
                .'?rel=0&modestbranding=1&iv_load_policy=3&fs=0&disablekb=1&color=white&controls=1&playsinline=1&enablejsapi=0';
        } elseif (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $url, $matches)) {
            $settings['video_type'] = 'embed';
            $settings['video_provider'] = 'vimeo';
            $settings['video_embed_url'] = 'https://player.vimeo.com/video/'.$matches[1].'?title=0&byline=0&portrait=0&dnt=1';
        }

        return $settings;
    }
}
