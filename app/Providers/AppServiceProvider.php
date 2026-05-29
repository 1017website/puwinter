<?php

namespace App\Providers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
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
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $userId = Auth::id();

                $view->with('notifCount', Notification::forUser($userId)->unread()->count());

                // Hanya bila view butuh dropdown (hemat query): tetap ringan, ambil 5 terbaru
                if (!array_key_exists('recentNotifs', $view->getData())) {
                    $view->with('recentNotifs', Notification::forUser($userId)->latest()->take(5)->get());
                }
            } else {
                $view->with('notifCount', 0);
                $view->with('recentNotifs', collect());
            }
        });
    }
}
