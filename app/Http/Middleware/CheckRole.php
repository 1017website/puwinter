<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Penggunaan: middleware('role:admin') atau middleware('role:admin,mentor')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            // Area staff (admin/mentor) diarahkan ke login staff, selain itu login siswa.
            $staffLogin = $request->is('admin*') || $request->is('mentor*');
            return redirect()->route($staffLogin ? 'staff.login' : 'login');
        }

        // Superadmin selalu lolos
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if (!in_array($user->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akses ditolak.'], 403);
            }

            abort(403, 'Kamu tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
