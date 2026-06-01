<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    // =========================================================================
    // LOGIN SISWA  (/login)
    // =========================================================================

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        // Hanya izinkan akun siswa lewat pintu ini.
        $request->authenticate(['student']);

        $request->session()->regenerate();

        return $this->redirectByRole($request->user());
    }

    // =========================================================================
    // LOGIN STAFF (admin / mentor / superadmin)  (/staff/login)
    // =========================================================================

    public function createStaff(): View
    {
        return view('auth.staff-login');
    }

    public function storeStaff(LoginRequest $request): RedirectResponse
    {
        // Hanya izinkan akun staff lewat pintu ini.
        $request->authenticate(['superadmin', 'admin', 'mentor']);

        $request->session()->regenerate();

        return $this->redirectByRole($request->user());
    }

    // =========================================================================
    // LOGOUT
    // =========================================================================

    public function destroy(Request $request): RedirectResponse
    {
        $wasStaff = $request->user()
            && in_array($request->user()->role, ['superadmin', 'admin', 'mentor']);

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Kembalikan ke halaman login yang sesuai.
        return redirect()->route($wasStaff ? 'staff.login' : 'login');
    }

    // =========================================================================
    // Helper redirect sesuai role
    // =========================================================================

    private function redirectByRole(?User $user): RedirectResponse
    {
        if (!$user) {
            return redirect()->route('login');
        }

        return match (true) {
            in_array($user->role, ['superadmin', 'admin']) => redirect()->route('admin.dashboard'),
            $user->role === 'mentor'                        => redirect()->route('mentor.dashboard'),
            default                                         => redirect()->intended(route('dashboard', absolute: false)),
        };
    }
}
