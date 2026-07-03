<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Models\Grade;

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

        $user = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'role'         => 'student',
            'school'       => $request->school,
            'city'         => $request->city,
            'grade_id'     => $request->grade_id,
            'grade'        => $grade?->code,   // jaga kompatibilitas kolom lama
            'grade_locked' => true,            // dikunci; ganti kelas harus via request admin
        ]);

        // Buat token verifikasi email custom
        $token = Str::random(64);

        EmailVerification::create([
            'user_id'    => $user->id,
            'token'      => $token,
            'expires_at' => now()->addHours(24),
        ]);

        // Kirim email verifikasi
        event(new Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        if (Schema::hasColumn('users', 'active_session_id')) {
            $user->forceFill(['active_session_id' => $request->session()->getId()])->save();
        }

        return redirect()->route('verification.notice')
            ->with('success', 'Registrasi berhasil! Cek email kamu untuk verifikasi.');
    }
}
