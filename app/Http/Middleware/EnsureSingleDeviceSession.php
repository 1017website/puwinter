<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class EnsureSingleDeviceSession
{
    private static ?bool $hasActiveSessionColumn = null;

    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Hotfix: jangan paksa cek single device sebelum migration active_session_id dijalankan.
        // Ini membuat halaman admin/artisan tetap bisa dibuka untuk menjalankan migration.
        if (!$this->usersTableHasActiveSessionColumn()) {
            return $next($request);
        }

        $currentSessionId = (string) $request->session()->getId();
        $activeSessionId  = (string) ($user->active_session_id ?? '');

        // User lama yang belum punya session aktif akan dikunci ke session pertama
        // yang sedang digunakan setelah fitur ini dipasang.
        if ($activeSessionId === '') {
            $user->forceFill(['active_session_id' => $currentSessionId])->save();
            return $next($request);
        }

        if (!hash_equals($activeSessionId, $currentSessionId)) {
            $isStaff = in_array($user->role, ['superadmin', 'admin', 'mentor'], true);

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route($isStaff ? 'staff.login' : 'login')
                ->withErrors([
                    'email' => 'Akun ini sedang digunakan di perangkat lain. Silakan login kembali untuk memindahkan sesi ke perangkat ini.',
                ]);
        }

        return $next($request);
    }

    private function usersTableHasActiveSessionColumn(): bool
    {
        if (self::$hasActiveSessionColumn !== null) {
            return self::$hasActiveSessionColumn;
        }

        try {
            return self::$hasActiveSessionColumn = Schema::hasColumn('users', 'active_session_id');
        } catch (\Throwable) {
            return self::$hasActiveSessionColumn = false;
        }
    }
}
