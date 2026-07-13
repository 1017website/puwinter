<?php

use App\Http\Middleware\CheckPremiumAccess;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EnsureStudentTryoutEnabled;
use App\Providers\AuthServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => CheckRole::class,
            'premium' => CheckPremiumAccess::class,
            'feature.tryout' => EnsureStudentTryoutEnabled::class,
        ]);

        // Guest yang mengakses area staff diarahkan ke login staff,
        // selain itu ke login siswa.
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('admin*') || $request->is('mentor*')) {
                return route('staff.login');
            }

            return route('login');
        });

        $middleware->validateCsrfTokens(except: [
            'payment/callback',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->withProviders([
        AuthServiceProvider::class,
    ])
    ->create();
