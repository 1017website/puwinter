<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Notification;
use App\Models\PaymentLog;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    // =========================================================================
    // INDEX — Halaman Pricing / Upgrade
    // =========================================================================

    public function index(Request $request): View
    {
        $plans = SubscriptionPlan::active()
            ->with(['grade', 'grades'])
            ->forGrade($request->user()->grade_id)
            ->get();
        $activeSubscription = $request->user()->activeSubscription();

        // Subscription pending milik user (untuk lanjut bayar / upload bukti)
        $pending = Subscription::where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->with('plan')
            ->latest()
            ->first();

        $affiliate = AppSetting::affiliateInfo();
        $hasAffiliate = !is_null($request->user()->referred_by_user_id);

        return view('payment.upgrade', compact('plans', 'activeSubscription', 'pending', 'affiliate', 'hasAffiliate'));
    }

    // =========================================================================
    // CHECKOUT — Buat order transfer manual + kode unik
    // =========================================================================

    public function checkout(Request $request, string $slug): RedirectResponse
    {
        $plan = SubscriptionPlan::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $user = $request->user();

        if (!$plan->appliesToGrade($user->grade_id)) {
            return redirect()->route('student.program.index')
                ->with('error', 'Program ini tidak tersedia untuk kelasmu.');
        }

        // Cek kuota: program berkuota & sudah penuh -> tolak (kecuali user sudah berbayar di program ini).
        if (!$user->hasPaidProgram($plan->id) && $plan->isQuotaFull()) {
            return back()->with('error', 'Maaf, kuota peserta untuk program ' . $plan->name . ' sudah penuh.');
        }

        // Batalkan subscription pending sebelumnya
        Subscription::where('user_id', $user->id)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);

        $affiliate = AppSetting::affiliateInfo();
        $referrer = $user->referredBy;
        $originalAmount = (int) $plan->price;
        // Affiliate tidak memberi potongan ke siswa yang memakai kode.
        // Benefit/reward hanya dicatat untuk pemilik kode sesuai pengaturan admin.
        $discountAmount = 0;
        $rewardAmount   = $referrer ? (int) $affiliate['affiliate_reward_amount'] : 0;
        $payableAmount  = $originalAmount;

        // Kode unik 3 digit (100-999) agar nominal transfer mudah dicocokkan
        $uniqueCode  = random_int(100, 999);
        $totalAmount = $payableAmount + $uniqueCode;

        $orderId = 'PWR-' . $user->id . '-' . time();

        $subscription = Subscription::create([
            'user_id'           => $user->id,
            'plan_id'           => $plan->id,
            'tier'              => $plan->tier ?? 'regular',
            'affiliate_referrer_id' => $referrer?->id,
            'affiliate_code'        => $referrer?->affiliate_code,
            'status'            => 'pending',
            'payment_method'    => 'transfer_manual',
            'amount_paid'       => $payableAmount,
            'unique_code'       => $uniqueCode,
            'total_amount'      => $totalAmount,
            'affiliate_original_amount' => $originalAmount,
            'affiliate_discount_amount' => $discountAmount,
            'affiliate_reward_amount'   => $rewardAmount,
            'midtrans_order_id' => $orderId,
        ]);

        PaymentLog::create([
            'subscription_id' => $subscription->id,
            'user_id'         => $user->id,
            'event_type'      => 'order.created',
            'payload'         => ['order_id' => $orderId, 'total' => $totalAmount, 'affiliate_reward' => $rewardAmount],
            'status'          => 'pending',
        ]);

        return redirect()->route('upgrade.instruction', $subscription->id);
    }

    // =========================================================================
    // INSTRUCTION — Halaman instruksi transfer + form upload bukti
    // =========================================================================

    public function instruction(Request $request, int $id): View|RedirectResponse
    {
        $subscription = Subscription::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with('plan')
            ->firstOrFail();

        if ($subscription->status === 'active') {
            return redirect()->route('dashboard')->with('info', 'Langganan kamu sudah aktif.');
        }

        $bank = AppSetting::bankInfo();

        return view('payment.instruction', compact('subscription', 'bank'));
    }

    // =========================================================================
    // UPLOAD PROOF — Siswa unggah bukti transfer
    // =========================================================================

    public function uploadProof(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'proof' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ], [
            'proof.required' => 'Bukti transfer wajib diunggah.',
            'proof.image'    => 'File harus berupa gambar.',
            'proof.max'      => 'Ukuran maksimal 4MB.',
        ]);

        $subscription = Subscription::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with('plan')
            ->firstOrFail();

        if ($subscription->status === 'active') {
            return redirect()->route('dashboard')->with('info', 'Langganan kamu sudah aktif.');
        }

        // Simpan ke public/uploads/proofs (kompatibel shared hosting tanpa storage:link)
        $dir = public_path('uploads/proofs');
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $filename = 'proof_' . $subscription->id . '_' . time() . '.'
            . $request->file('proof')->getClientOriginalExtension();
        $request->file('proof')->move($dir, $filename);

        // Hapus bukti lama jika ada
        if ($subscription->payment_proof) {
            $old = public_path('uploads/proofs/' . $subscription->payment_proof);
            if (is_file($old)) @unlink($old);
        }

        $subscription->update([
            'payment_proof'     => $filename,
            'proof_uploaded_at' => now(),
        ]);

        PaymentLog::create([
            'subscription_id' => $subscription->id,
            'user_id'         => $subscription->user_id,
            'event_type'      => 'proof.uploaded',
            'payload'         => ['file' => $filename],
            'status'          => 'pending',
        ]);

        // Notifikasi ke semua admin/superadmin
        $admins = \App\Models\User::whereIn('role', ['admin', 'superadmin'])->pluck('id');
        foreach ($admins as $adminId) {
            Notification::notify(
                $adminId,
                'payment',
                'Bukti transfer baru — ' . ($subscription->plan->name ?? 'Premium'),
                $request->user()->name . ' mengunggah bukti transfer. Mohon divalidasi.',
                route('admin.subscriptions.index'),
                'fa-receipt'
            );
        }

        return redirect()->route('upgrade.instruction', $subscription->id)
            ->with('success', 'Bukti transfer berhasil diunggah. Mohon tunggu validasi admin (maks. 1×24 jam).');
    }
}
