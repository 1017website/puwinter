<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class EnsureSingleDeviceSession
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $user = $request->user();

        if (!$user) {
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
}
