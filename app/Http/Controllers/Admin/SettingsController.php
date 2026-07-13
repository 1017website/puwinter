<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
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
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'env' => app()->environment(),
            'debug' => config('app.debug') ? 'ON' : 'OFF',
            'app_url' => config('app.url'),
            'storage_linked' => file_exists(public_path('storage')),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'db_connection' => config('database.default'),
        ];

        $bank = AppSetting::bankInfo();
        $affiliate = AppSetting::affiliateInfo();
        $features = [
            'student_tryout_enabled' => AppSetting::studentTryoutEnabled(),
        ];

        return view('admin.settings.index', compact('sysInfo', 'bank', 'affiliate', 'features'));
    }

    // =========================================================================
    // REKENING TRANSFER MANUAL
    // =========================================================================

    public function updateBank(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bank_name' => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:50',
            'bank_holder' => 'nullable|string|max:100',
            'payment_note' => 'nullable|string|max:500',
        ]);

        foreach ($data as $key => $value) {
            AppSetting::set($key, $value ?? '');
        }

        return back()->with('success', 'Informasi rekening berhasil disimpan.');
    }

    public function updateAffiliate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'affiliate_reward_amount' => 'nullable|integer|min:0',
        ]);
        // Benefit affiliate hanya untuk pemilik kode/referrer, bukan potongan untuk pendaftar.
        AppSetting::set('affiliate_discount_amount', 0);
        AppSetting::set('affiliate_reward_amount', (int) ($data['affiliate_reward_amount'] ?? 0));

        return back()->with('success', 'Pengaturan affiliate berhasil disimpan.');
    }

    public function updateFeatures(Request $request): RedirectResponse
    {
        AppSetting::set('student_tryout_enabled', $request->boolean('student_tryout_enabled') ? '1' : '0');

        $status = $request->boolean('student_tryout_enabled') ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Halaman tryout siswa berhasil {$status}.");
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

        // Whitelist command yang aman.
        // Format: 'key' => ['nama-command-artisan', [parameter asosiatif]]
        // Parameter HARUS associative (mis. ['--force' => true]), bukan numerik,
        // karena Artisan::call() memperlakukan key numerik sebagai nama argument
        // (itulah penyebab error "The '1' argument does not exist").
        $allowed = [
            'migrate' => ['migrate', ['--force' => true]],
            'optimize:clear' => ['optimize:clear', []],
            'storage:link' => ['storage:link', []],
        ];

        if (! array_key_exists($command, $allowed)) {
            return back()->with('error', 'Command tidak diizinkan.');
        }

        try {
            [$artisanCommand, $parameters] = $allowed[$command];
            $exitCode = Artisan::call($artisanCommand, $parameters);

            $output = Artisan::output();
            $output = $output ?: 'Command selesai dijalankan.';

            // Simpan output ke session
            session(['artisan_output' => [
                'command' => $command,
                'output' => trim($output),
                'success' => $exitCode === 0,
            ]]);

            if ($exitCode === 0) {
                return back()->with('success', "✅ php artisan {$command} berhasil dijalankan.");
            } else {
                return back()->with('error', "❌ php artisan {$command} gagal. Exit code: {$exitCode}");
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Error: '.$e->getMessage());
        }
    }
}
