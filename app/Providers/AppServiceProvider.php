<?php

namespace App\Providers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Arahkan link email verifikasi bawaan Laravel ke route token custom.
        //
        // Tanpa ini, event(new Registered($user)) memicu notifikasi VerifyEmail
        // bawaan yang membangun URL route 'verification.verify' dengan {id}/{hash}
        // — sedangkan route custom kita memakai {token} → UrlGenerationException.
        \Illuminate\Auth\Notifications\VerifyEmail::createUrlUsing(function ($notifiable) {
            $token = \App\Models\EmailVerification::where('user_id', $notifiable->getKey())
                ->whereNull('used_at')
                ->latest()
                ->value('token');

            // Fallback: bila token belum ada (jalur lain memicu notifikasi),
            // buat token baru agar link selalu valid.
            if (!$token) {
                $token = \Illuminate\Support\Str::random(64);
                \App\Models\EmailVerification::create([
                    'user_id'    => $notifiable->getKey(),
                    'token'      => $token,
                    'expires_at' => now()->addHours(24),
                ]);
            }

            return url('/email/verify/' . $token);
        });
        // Bagikan jumlah notifikasi belum dibaca ($notifCount) & beberapa notifikasi
        // terbaru ($recentNotifs) ke SEMUA view (layout student & admin pakai ini).
        //
        // PENTING: dibungkus pengaman. Jika tabel app_notifications belum ada
        // (mis. migrasi belum dijalankan di server) atau koneksi DB bermasalah,
        // composer TIDAK boleh melempar error — kalau tidak, SEMUA halaman 500
        // dan kita tidak bisa membuka halaman migrate sekalipun.
        View::composer('*', function ($view) {
            $notifCount   = 0;
            $recentNotifs = collect();

            try {
                if (Auth::check() && Schema::hasTable('app_notifications')) {
                    $userId       = Auth::id();
                    $notifCount   = Notification::forUser($userId)->unread()->count();
                    $recentNotifs = Notification::forUser($userId)->latest()->take(5)->get();
                }
            } catch (\Throwable $e) {
                // Diamkan: badge notif tidak boleh menjatuhkan seluruh aplikasi.
                $notifCount   = 0;
                $recentNotifs = collect();
            }

            $view->with('notifCount', $notifCount);

            if (!array_key_exists('recentNotifs', $view->getData())) {
                $view->with('recentNotifs', $recentNotifs);
            }
        });
    }
}
