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
