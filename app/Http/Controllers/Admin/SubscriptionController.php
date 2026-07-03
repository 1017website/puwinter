<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Subscription::with(['user', 'plan'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('user', fn($q) =>
                $q->where('name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
            );
        }

        $subscriptions = $query->paginate(20)->withQueryString();

        $stats = [
            'total'   => Subscription::count(),
            'active'  => Subscription::where('status','active')->where('expired_at','>',now())->count(),
            'pending' => Subscription::where('status','pending')->count(),
            'revenue' => (int) Subscription::where('status','active')
                ->selectRaw('COALESCE(SUM(COALESCE(total_amount, amount_paid, 0)), 0) as total')
                ->value('total'),
        ];

        return view('admin.subscriptions.index', compact('subscriptions', 'stats'));
    }

    // Aktifkan subscription manual
    public function activate(Subscription $subscription): RedirectResponse
    {
        if ($subscription->status === 'active') {
            return back()->with('warning', 'Subscription sudah aktif.');
        }

        $plan = $subscription->plan;

        // Cek kuota program (peserta berbayar). Jika penuh & user ini belum terhitung, tolak.
        if ($plan && $plan->isQuotaFull()) {
            $alreadyPaid = \App\Models\ProgramEnrollment::where('user_id', $subscription->user_id)
                ->where('plan_id', $plan->id)->where('status', 'paid')->exists();
            if (!$alreadyPaid) {
                return back()->with('error', 'Kuota peserta berbayar program ' . $plan->name . ' sudah penuh.');
            }
        }

        $subscription->update([
            'tier'       => $plan->tier ?? $subscription->tier ?? 'regular',
            'status'     => 'active',
            'started_at' => now(),
            'expired_at' => now()->addMonths((int) $plan->duration_months),
        ]);

        // Akses per-program: tandai/buat enrollment program ini menjadi BERBAYAR.
        \App\Models\ProgramEnrollment::updateOrCreate(
            ['user_id' => $subscription->user_id, 'plan_id' => $subscription->plan_id],
            [
                'status'          => \App\Models\ProgramEnrollment::STATUS_PAID,
                'subscription_id' => $subscription->id,
                'paid_at'         => now(),
                'paid_expires_at' => $subscription->fresh()->expired_at,
                'enrolled_at'     => now(),
            ]
        );

        Notification::notify(
            $subscription->user_id,
            'payment',
            'Pembayaran terverifikasi — ' . ($plan->name ?? 'Premium'),
            'Akun kamu kini Premium hingga ' . $subscription->fresh()->expired_at->translatedFormat('d M Y') . '.',
            route('dashboard'),
            'fa-crown'
        );

        return back()->with('success', 'Subscription berhasil diaktifkan.');
    }

    // Batalkan subscription
    public function cancel(Subscription $subscription): RedirectResponse
    {
        $subscription->update(['status' => 'cancelled']);
        return back()->with('success', 'Subscription berhasil dibatalkan.');
    }

    // Extend subscription
    public function extend(Request $request, Subscription $subscription): RedirectResponse
    {
        $request->validate(['months' => 'required|integer|min:1|max:24']);

        $newExpiry = ($subscription->expired_at && $subscription->expired_at->isFuture())
            ? $subscription->expired_at->copy()->addMonths((int) $request->months)
            : now()->addMonths((int) $request->months);

        $subscription->update([
            'status'     => 'active',
            'expired_at' => $newExpiry,
        ]);

        return back()->with('success', "Subscription diperpanjang {$request->months} bulan.");
    }
}
