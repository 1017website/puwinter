<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
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
        $this->setActiveSession($request);

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
        $this->setActiveSession($request);

        return $this->redirectByRole($request->user());
    }

    // =========================================================================
    // LOGOUT
    // =========================================================================

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        $wasStaff = $user && in_array($user->role, ['superadmin', 'admin', 'mentor']);

        if ($user && $this->usersTableHasActiveSessionColumn() && hash_equals((string) $user->active_session_id, (string) $request->session()->getId())) {
            $user->forceFill(['active_session_id' => null])->save();
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Kembalikan ke halaman login yang sesuai.
        return redirect()->route($wasStaff ? 'staff.login' : 'login');
    }

    // =========================================================================
    // Helper session aktif
    // =========================================================================

    private function setActiveSession(Request $request): void
    {
        if (!$request->user()) {
            return;
        }

        $payload = [
            'last_login_at' => now(),
        ];

        if ($this->usersTableHasActiveSessionColumn()) {
            $payload['active_session_id'] = $request->session()->getId();
        }

        $request->user()->forceFill($payload)->save();
    }

    private function usersTableHasActiveSessionColumn(): bool
    {
        try {
            return Schema::hasColumn('users', 'active_session_id');
        } catch (\Throwable) {
            return false;
        }
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
