<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    // Halaman "cek email kamu"
    public function notice(): View
    {
        return view('auth.verify-email');
    }

    // Proses klik link dari email
    public function verify(Request $request, string $token): RedirectResponse
    {
        $verification = EmailVerification::where('token', $token)->first();

        if (!$verification) {
            return redirect()->route('verification.notice')
                ->with('error', 'Link verifikasi tidak valid.');
        }

        if ($verification->isExpired()) {
            return redirect()->route('verification.notice')
                ->with('error', 'Link verifikasi sudah kadaluarsa. Kirim ulang email verifikasi.');
        }

        if ($verification->isUsed()) {
            return redirect()->route('dashboard')
                ->with('info', 'Email sudah diverifikasi sebelumnya.');
        }

        // Tandai sebagai terpakai
        $verification->update(['used_at' => now()]);

        // Update user
        $verification->user->update(['email_verified_at' => now()]);

        return redirect()->route('dashboard')
            ->with('success', 'Email berhasil diverifikasi! Selamat belajar.');
    }

    // Kirim ulang email verifikasi
    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            return redirect()->route('dashboard');
        }

        // Hapus token lama
        EmailVerification::where('user_id', $user->id)->delete();

        // Buat token baru
        $token = Str::random(64);
        EmailVerification::create([
            'user_id'    => $user->id,
            'token'      => $token,
            'expires_at' => now()->addHours(24),
        ]);

        // Kirim ulang email
        $user->sendEmailVerificationNotification();

        return back()->with('success', 'Email verifikasi sudah dikirim ulang. Cek inbox kamu.');
    }
}
