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
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => 'student',
            'school'     => $request->school,
            'city'       => $request->city,
            'grade'      => $request->grade,
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

        return redirect()->route('verification.notice')
            ->with('success', 'Registrasi berhasil! Cek email kamu untuk verifikasi.');
    }
}
