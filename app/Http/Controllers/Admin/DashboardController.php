<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Subscription;
use App\Models\Tryout;
use App\Models\User;
use App\Models\UserTryoutAttempt;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Stats utama
        $stats = [
            'total_users'         => User::where('role', 'student')->count(),
            'total_premium'       => Subscription::where('status', 'active')
                                        ->where('expired_at', '>', now())->count(),
            'total_courses'       => Course::count(),
            'total_tryouts'       => Tryout::count(),
            'revenue_month'       => Subscription::where('status', 'active')
                                        ->whereMonth('started_at', now()->month)
                                        ->sum('amount_paid'),
            'new_users_today'     => User::whereDate('created_at', today())->count(),
            'active_attempts'     => UserTryoutAttempt::whereNull('submitted_at')->count(),
            'pending_payments'    => Subscription::where('status', 'pending')->count(),
        ];

        // User terbaru
        $recentUsers = User::latest()->take(8)->get();

        // Transaksi terbaru
        $recentTransactions = Subscription::with(['user', 'plan'])
            ->latest()
            ->take(8)
            ->get();

        // Revenue 7 hari terakhir (untuk chart)
        $revenueChart = Subscription::where('status', 'active')
            ->where('started_at', '>=', now()->subDays(6))
            ->selectRaw('DATE(started_at) as date, SUM(amount_paid) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Lengkapi 7 hari terakhir
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartData[] = [
                'date'  => now()->subDays($i)->format('d M'),
                'total' => $revenueChart[$date]->total ?? 0,
            ];
        }

        // Tryout terpopuler
        $popularTryouts = Tryout::withCount('attempts')
            ->orderByDesc('attempts_count')
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'stats', 'recentUsers', 'recentTransactions', 'chartData', 'popularTryouts'
        ));
    }
}
