<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\PaymentLog;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\BayarGgService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct(private BayarGgService $bayarGg) {}

    // =========================================================================
    // INDEX — Halaman Pricing / Upgrade
    // =========================================================================

    public function index(Request $request): View
    {
        $plans              = SubscriptionPlan::active()->get();
        $activeSubscription = $request->user()->activeSubscription();

        return view('payment.upgrade', compact('plans', 'activeSubscription'));
    }

    // =========================================================================
    // CHECKOUT — Buat order & redirect ke bayar.gg
    // =========================================================================

    public function checkout(Request $request, string $slug): RedirectResponse
    {
        $plan = SubscriptionPlan::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $user = $request->user();

        // Batalkan subscription pending sebelumnya
        Subscription::where('user_id', $user->id)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);

        // Buat order ID internal
        $orderId = 'PWR-' . $user->id . '-' . time();

        // Buat subscription record dulu (pending)
        $subscription = Subscription::create([
            'user_id'  => $user->id,
            'plan_id'  => $plan->id,
            'status'   => 'pending',
            'amount_paid' => $plan->price,
        ]);

        // Simpan order ID di subscription (pakai midtrans_order_id field yang ada)
        $subscription->update(['midtrans_order_id' => $orderId]);

        // Buat payment di bayar.gg
        $payment = $this->bayarGg->createPayment(
            amount: $plan->price,
            description: $plan->name . ' - Puwinter',
            customer: [
                'name'  => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            orderId: $orderId
        );

        if (!$payment) {
            $subscription->update(['status' => 'cancelled']);
            return back()->with('error', 'Gagal membuat pembayaran. Coba lagi.');
        }

        // Simpan invoice ID dari bayar.gg
        $invoiceId = $payment['payment']['invoice_id'];
        $paymentUrl = $payment['payment_url'];

        $subscription->update([
            'midtrans_snap_token' => $invoiceId, // kita pakai field ini untuk invoice_id bayar.gg
        ]);

        // Log
        PaymentLog::create([
            'subscription_id' => $subscription->id,
            'user_id'         => $user->id,
            'event_type'      => 'payment.created',
            'payload'         => $payment,
            'status'          => 'pending',
        ]);

        // Redirect ke halaman bayar.gg
        return redirect($paymentUrl);
    }

    // =========================================================================
    // CALLBACK / WEBHOOK dari bayar.gg
    // =========================================================================

    public function callback(Request $request): Response
    {
        $payload = $request->all();

        \Log::info('BayarGg Callback received', $payload);

        $invoiceId = $payload['invoice_id'] ?? null;

        if (!$invoiceId) {
            return response('Missing invoice_id', 400);
        }

        // Verifikasi dengan double-check ke API
        if (!$this->bayarGg->verifyCallback($payload)) {
            \Log::warning('BayarGg callback verification failed', $payload);
            return response('Verification failed', 400);
        }

        // Cari subscription berdasarkan invoice ID yang kita simpan di midtrans_snap_token
        $subscription = Subscription::where('midtrans_snap_token', $invoiceId)->first();

        if (!$subscription) {
            \Log::warning('BayarGg callback: subscription not found for invoice ' . $invoiceId);
            return response('Subscription not found', 404);
        }

        // Hindari proses duplikat
        if ($subscription->status === 'active') {
            return response('Already processed', 200);
        }

        $status      = $payload['status'] ?? 'pending';
        $paymentMethod = $payload['payment_method'] ?? 'qris';

        // Log
        PaymentLog::create([
            'subscription_id' => $subscription->id,
            'user_id'         => $subscription->user_id,
            'event_type'      => 'callback.' . $status,
            'payload'         => $payload,
            'status'          => $status,
        ]);

        if ($status === 'paid') {
            $this->activateSubscription($subscription, $paymentMethod);
        } elseif (in_array($status, ['expired', 'cancelled'])) {
            $subscription->update(['status' => 'cancelled']);
        }

        return response('OK', 200);
    }

    // =========================================================================
    // SUCCESS — Halaman setelah redirect dari bayar.gg
    // =========================================================================

    public function success(Request $request): View|RedirectResponse
    {
        $orderId = $request->get('order');

        if (!$orderId) {
            return redirect()->route('dashboard');
        }

        // Cari subscription berdasarkan order ID internal
        $subscription = Subscription::where('midtrans_order_id', $orderId)
            ->where('user_id', $request->user()->id)
            ->with('plan')
            ->first();

        if (!$subscription) {
            return redirect()->route('dashboard')->with('info', 'Pembayaran sedang diproses.');
        }

        // Jika belum active, cek ke API bayar.gg sekali lagi
        if ($subscription->status !== 'active') {
            $invoiceId = $subscription->midtrans_snap_token;
            $payment   = $this->bayarGg->checkPayment($invoiceId);

            if ($payment && $payment['status'] === 'paid') {
                $this->activateSubscription($subscription, $payment['payment_method'] ?? 'qris');
                $subscription->refresh();
            }
        }

        return view('payment.success', compact('subscription'));
    }

    // =========================================================================
    // CHECK STATUS — AJAX polling dari frontend
    // =========================================================================

    public function checkStatus(Request $request, int $subscriptionId)
    {
        $subscription = Subscription::where('id', $subscriptionId)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$subscription) {
            return response()->json(['status' => 'not_found'], 404);
        }

        // Kalau sudah active, return langsung
        if ($subscription->status === 'active') {
            return response()->json([
                'status'      => 'active',
                'redirect_url'=> route('dashboard'),
            ]);
        }

        // Cek ke API bayar.gg
        $invoiceId = $subscription->midtrans_snap_token;
        $payment   = $this->bayarGg->checkPayment($invoiceId);

        if ($payment && $payment['status'] === 'paid') {
            $this->activateSubscription($subscription, $payment['payment_method'] ?? 'qris');
            return response()->json([
                'status'      => 'active',
                'redirect_url'=> route('upgrade.success') . '?order=' . $subscription->midtrans_order_id,
            ]);
        }

        return response()->json([
            'status'     => $subscription->status,
            'expires_at' => $payment['expires_at'] ?? null,
        ]);
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function activateSubscription(Subscription $subscription, string $paymentMethod): void
    {
        $plan = $subscription->plan;

        $subscription->update([
            'status'         => 'active',
            'payment_method' => $paymentMethod,
            'started_at'     => now(),
            'expired_at'     => now()->addMonths($plan->duration_months),
        ]);
    }
}
