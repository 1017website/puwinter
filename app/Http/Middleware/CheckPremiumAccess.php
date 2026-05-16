<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPremiumAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isPremium()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akses premium diperlukan.'], 403);
            }

            return redirect()->route('upgrade.index')
                ->with('warning', 'Fitur ini hanya tersedia untuk member Premium. Upgrade sekarang!');
        }

        return $next($request);
    }
}
