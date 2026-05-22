<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        // Info sistem
        $sysInfo = [
            'php_version'     => PHP_VERSION,
            'laravel_version' => app()->version(),
            'env'             => app()->environment(),
            'debug'           => config('app.debug') ? 'ON' : 'OFF',
            'app_url'         => config('app.url'),
            'storage_linked'  => file_exists(public_path('storage')),
            'cache_driver'    => config('cache.default'),
            'queue_driver'    => config('queue.default'),
            'db_connection'   => config('database.default'),
        ];

        return view('admin.settings.index', compact('sysInfo'));
    }

    public function uploadLogo(Request $request): RedirectResponse
    {
        $request->validate(['logo' => 'required|image|mimes:png,jpg,jpeg,svg,webp|max:2048']);
        $request->file('logo')->move(public_path('images'), 'logo.png');
        return back()->with('success', 'Logo berhasil diperbarui.');
    }

    public function uploadFavicon(Request $request): RedirectResponse
    {
        $request->validate(['favicon' => 'required|image|mimes:png,jpg,jpeg,ico|max:512']);
        $request->file('favicon')->move(public_path('images'), 'favicon.png');
        return back()->with('success', 'Favicon berhasil diperbarui.');
    }

    // =========================================================================
    // ARTISAN COMMANDS
    // =========================================================================

    public function runArtisan(Request $request): RedirectResponse
    {
        $command = $request->input('command');

        // Whitelist command yang aman
        $allowed = [
            'migrate'               => ['migrate', '--force'],
            'migrate:fresh'         => ['migrate:fresh', '--force'],
            'migrate:rollback'      => ['migrate:rollback', '--force'],
            'db:seed'               => ['db:seed', '--force'],
            'storage:link'          => ['storage:link'],
            'optimize'              => ['optimize'],
            'optimize:clear'        => ['optimize:clear'],
            'cache:clear'           => ['cache:clear'],
            'config:clear'          => ['config:clear'],
            'config:cache'          => ['config:cache'],
            'route:clear'           => ['route:clear'],
            'route:cache'           => ['route:cache'],
            'view:clear'            => ['view:clear'],
            'view:cache'            => ['view:cache'],
            'queue:restart'         => ['queue:restart'],
            'schedule:run'          => ['schedule:run'],
            'event:clear'           => ['event:clear'],
        ];

        if (!array_key_exists($command, $allowed)) {
            return back()->with('error', 'Command tidak diizinkan.');
        }

        try {
            $exitCode = Artisan::call($allowed[$command][0],
                count($allowed[$command]) > 1
                    ? array_slice($allowed[$command], 1, null, false) + ['--force' => true]
                    : []
            );

            $output = Artisan::output();
            $output = $output ?: 'Command selesai dijalankan.';

            // Simpan output ke session
            session(['artisan_output' => [
                'command' => $command,
                'output'  => trim($output),
                'success' => $exitCode === 0,
            ]]);

            if ($exitCode === 0) {
                return back()->with('success', "✅ php artisan {$command} berhasil dijalankan.");
            } else {
                return back()->with('error', "❌ php artisan {$command} gagal. Exit code: {$exitCode}");
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
