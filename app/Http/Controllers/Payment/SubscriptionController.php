<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\PaymentLog;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    // Halaman pricing / upgrade
    public function index(Request $request): View
    {
        $plans = SubscriptionPlan::active()->get();
        $user  = $request->user();

        $activeSubscription = $user->activeSubscription();

        return view('payment.upgrade', compact('plans', 'activeSubscription'));
    }

    // Buat order dan redirect ke Midtrans Snap
    public function checkout(Request $request, string $slug): RedirectResponse
    {
        $plan = SubscriptionPlan::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $user = $request->user();

        // Batalkan subscription pending sebelumnya
        Subscription::where('user_id', $user->id)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);

        $orderId = 'PWR-' . $user->id . '-' . time();

        $subscription = Subscription::create([
            'user_id'           => $user->id,
            'plan_id'           => $plan->id,
            'status'            => 'pending',
            'payment_method'    => null,
            'midtrans_order_id' => $orderId,
            'amount_paid'       => $plan->price,
        ]);

        // Midtrans Snap
        \Midtrans\Config::$serverKey    = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $plan->price,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email'      => $user->email,
                'phone'      => $user->phone,
            ],
            'item_details' => [[
                'id'       => $plan->slug,
                'price'    => $plan->price,
                'quantity' => 1,
                'name'     => $plan->name . ' - Puwinter',
            ]],
        ];

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        $subscription->update(['midtrans_snap_token' => $snapToken]);

        return redirect()->route('payment.snap', ['token' => $snapToken, 'orderId' => $orderId]);
    }

    // Halaman Snap (redirect ke Midtrans popup)
    public function snap(Request $request): View
    {
        $token   = $request->get('token');
        $orderId = $request->get('orderId');

        return view('payment.snap', compact('token', 'orderId'));
    }

    // Webhook dari Midtrans
    public function callback(Request $request): Response
    {
        \Midtrans\Config::$serverKey    = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');

        $notification = new \Midtrans\Notification();

        $orderId           = $notification->order_id;
        $transactionStatus = $notification->transaction_status;
        $fraudStatus       = $notification->fraud_status;
        $paymentType       = $notification->payment_type;

        $subscription = Subscription::where('midtrans_order_id', $orderId)->firstOrFail();

        // Log payload
        PaymentLog::create([
            'subscription_id' => $subscription->id,
            'user_id'         => $subscription->user_id,
            'event_type'      => $transactionStatus,
            'payload'         => $request->all(),
            'status'          => $transactionStatus,
        ]);

        // Proses status
        if ($transactionStatus === 'capture' && $fraudStatus === 'accept') {
            $this->activateSubscription($subscription, $paymentType);
        } elseif ($transactionStatus === 'settlement') {
            $this->activateSubscription($subscription, $paymentType);
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $subscription->update(['status' => 'cancelled']);
        }

        return response('OK', 200);
    }

    // Halaman sukses
    public function success(Request $request): View
    {
        $orderId      = $request->get('order_id');
        $subscription = Subscription::where('midtrans_order_id', $orderId)
            ->where('user_id', $request->user()->id)
            ->with('plan')
            ->firstOrFail();

        return view('payment.success', compact('subscription'));
    }

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
