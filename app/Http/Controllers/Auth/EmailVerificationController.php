<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailVerification;
use App\Services\EmailVerificationMailService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    // Halaman "cek email kamu"
    public function notice(): View|RedirectResponse
    {
        if (auth()->check() && auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify-email');
    }

    // Proses klik link dari email.
    // Tidak wajib login karena token verifikasi sudah unik dan acak.
    public function verify(Request $request, string $token): RedirectResponse
    {
        $verification = EmailVerification::with('user')
            ->where('token', $token)
            ->first();

        if (!$verification) {
            return redirect()->route('login')
                ->with('error', 'Link verifikasi tidak valid. Silakan login lalu kirim ulang email verifikasi.');
        }

        if ($verification->isExpired()) {
            return redirect()->route('login')
                ->with('error', 'Link verifikasi sudah kedaluwarsa. Silakan login lalu kirim ulang email verifikasi.');
        }

        $user = $verification->user;

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Akun untuk link verifikasi ini tidak ditemukan.');
        }

        if (!$verification->isUsed()) {
            $verification->update(['used_at' => now()]);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->forceFill(['email_verified_at' => now()])->save();
            event(new Verified($user));
        }

        // Jika link dibuka dari browser/HP lain, langsung login-kan user agar
        // tidak kembali lagi ke halaman "Verifikasi Email Kamu".
        if (!Auth::check() || Auth::id() !== $user->id) {
            Auth::logout();
            Auth::login($user);
            $request->session()->regenerate();
        }

        return redirect()->route('dashboard')
            ->with('success', 'Email berhasil diverifikasi. Selamat belajar di Puwinter!');
    }

    // Kirim ulang email verifikasi
    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        // Hapus token lama agar link terakhir yang dipakai selalu paling baru.
        EmailVerification::where('user_id', $user->id)->delete();

        $verification = EmailVerification::create([
            'user_id'    => $user->id,
            'token'      => Str::random(64),
            'expires_at' => now()->addHours(24),
        ]);

        // Kirim ulang email + catat hasilnya ke tabel email_logs.
        $emailLog = app(EmailVerificationMailService::class)->send($user, $verification, 'resend');

        if ($emailLog->status === 'failed') {
            return back()->with('error', 'Email verifikasi gagal dikirim. Cek menu Log Email di admin untuk melihat response/error mail server.');
        }

        return back()->with('success', 'Email verifikasi sudah dikirim ulang. Cek inbox atau spam kamu.');
    }
}
