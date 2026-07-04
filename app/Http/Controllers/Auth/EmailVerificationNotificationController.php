<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\EmailVerificationMailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Fallback untuk route bawaan Breeze jika masih pernah terpanggil dari cache
     * atau link lama. Tetap gunakan mailer custom Puwinter, bukan template default Laravel.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $emailLog = app(EmailVerificationMailService::class)->send($user, null, 'resend');

        if ($emailLog->status === 'failed') {
            return back()->with('error', 'Email verifikasi gagal dikirim. Cek menu Log Email di admin untuk melihat response/error mail server.');
        }

        return back()->with('success', 'Email verifikasi sudah dikirim ulang. Cek inbox atau spam kamu.');
    }
}
