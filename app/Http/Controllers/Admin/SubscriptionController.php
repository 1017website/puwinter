<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            'revenue' => Subscription::where('status','active')->sum('amount_paid'),
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

        $subscription->update([
            'status'     => 'active',
            'started_at' => now(),
            'expired_at' => now()->addMonths($plan->duration_months),
        ]);

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
            ? $subscription->expired_at->addMonths($request->months)
            : now()->addMonths($request->months);

        $subscription->update([
            'status'     => 'active',
            'expired_at' => $newExpiry,
        ]);

        return back()->with('success', "Subscription diperpanjang {$request->months} bulan.");
    }
}
