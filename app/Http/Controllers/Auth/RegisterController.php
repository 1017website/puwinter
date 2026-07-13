<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\EmailVerification;
use App\Models\Grade;
use App\Models\RegistrationCode;
use App\Models\User;
use App\Services\EmailVerificationMailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        $grades = Grade::active()->get();

        return view('auth.register', compact('grades'));
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        // Ambil code grade untuk mengisi kolom string `grade` (backward-compat).
        $grade = Grade::find($request->grade_id);
        $referrer = null;
        if ($request->filled('affiliate_code')) {
            $referrer = User::where('affiliate_code', strtoupper(trim((string) $request->affiliate_code)))
                ->where('role', 'student')
                ->first();
        }
        $registrationCode = $request->filled('registration_code')
            ? RegistrationCode::available()->where('code', $request->registration_code)->first()
            : null;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
            'school' => $request->school,
            'city' => $request->city,
            'grade_id' => $request->grade_id,
            'grade' => $grade?->code,   // jaga kompatibilitas kolom lama
            'grade_locked' => true,            // dikunci; ganti kelas harus via request admin
            'referred_by_user_id' => $referrer?->id,
            'registration_code_id' => $registrationCode?->id,
        ]);

        // Buat token verifikasi email custom
        $token = Str::random(64);

        $verification = EmailVerification::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => now()->addHours(24),
        ]);

        // Kirim email verifikasi + catat hasilnya ke tabel email_logs.
        $emailLog = app(EmailVerificationMailService::class)->send($user, $verification, 'register');

        Auth::login($user);

        $redirect = redirect()->route('verification.notice');

        if ($emailLog->status === 'failed') {
            return $redirect->with('error', 'Registrasi berhasil, tetapi email verifikasi gagal dikirim. Cek menu Log Email di admin untuk melihat response/error mail server.');
        }

        return $redirect->with('success', 'Registrasi berhasil! Email verifikasi sudah dikirim. Cek inbox atau spam kamu.');
    }
}
