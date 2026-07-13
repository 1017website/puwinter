<?php

namespace App\Http\Middleware;

use App\Models\AppSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentTryoutEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! AppSetting::studentTryoutEnabled()) {
            return redirect()->route('dashboard')
                ->with('error', 'Halaman tryout sedang dinonaktifkan oleh admin.');
        }

        return $next($request);
    }
}
