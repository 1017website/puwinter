<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\Rule;
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
        $frontend = AppSetting::frontendInfo();

        return view('admin.settings.index', compact('sysInfo', 'bank', 'affiliate', 'features', 'frontend'));
    }

    public function updateFrontend(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'video_url' => 'nullable|url|max:2048',
            'video_title' => 'nullable|string|max:120',
            'video_description' => 'nullable|string|max:500',
            'video_file' => 'nullable|file|mimes:mp4,webm,mov|max:102400',
            'video_poster' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096',
            'seo_title' => 'required|string|max:70',
            'seo_description' => 'required|string|max:170',
            'seo_keywords' => 'nullable|string|max:500',
            'seo_canonical_url' => 'nullable|url|max:2048',
            'seo_robots' => ['required', Rule::in(['index,follow', 'index,nofollow', 'noindex,follow', 'noindex,nofollow'])],
            'seo_og_title' => 'nullable|string|max:95',
            'seo_og_description' => 'nullable|string|max:200',
            'seo_og_image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096',
            'google_tag_manager_id' => ['nullable', 'regex:/^GTM-[A-Z0-9]+$/i'],
            'google_analytics_id' => ['nullable', 'regex:/^G-[A-Z0-9]+$/i'],
            'meta_pixel_id' => ['nullable', 'regex:/^[0-9]{5,30}$/'],
        ], [
            'video_url.url' => 'URL video harus berupa URL lengkap (https://...).',
            'google_tag_manager_id.regex' => 'Format Google Tag Manager harus seperti GTM-XXXXXXX.',
            'google_analytics_id.regex' => 'Format Google Analytics harus seperti G-XXXXXXXXXX.',
            'meta_pixel_id.regex' => 'Meta Pixel ID hanya boleh berisi 5–30 digit.',
        ]);

        $uploadDirectory = public_path('uploads/frontend');
        if (($request->hasFile('video_file') || $request->hasFile('video_poster') || $request->hasFile('seo_og_image')) && ! is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0755, true);
        }

        if ($request->hasFile('video_file')) {
            $extension = $request->file('video_file')->getClientOriginalExtension();
            $filename = 'landing-video-'.time().'.'.$extension;
            $request->file('video_file')->move($uploadDirectory, $filename);
            $data['video_url'] = asset('uploads/frontend/'.$filename);
        }

        foreach (['video_poster', 'seo_og_image'] as $imageKey) {
            if ($request->hasFile($imageKey)) {
                $extension = $request->file($imageKey)->getClientOriginalExtension();
                $filename = str_replace('_', '-', $imageKey).'-'.time().'.'.$extension;
                $request->file($imageKey)->move($uploadDirectory, $filename);
                $data[$imageKey] = asset('uploads/frontend/'.$filename);
            }
        }

        $keys = [
            'video_url' => 'frontend_video_url',
            'video_title' => 'frontend_video_title',
            'video_description' => 'frontend_video_description',
            'video_poster' => 'frontend_video_poster',
            'seo_title' => 'seo_title',
            'seo_description' => 'seo_description',
            'seo_keywords' => 'seo_keywords',
            'seo_canonical_url' => 'seo_canonical_url',
            'seo_robots' => 'seo_robots',
            'seo_og_title' => 'seo_og_title',
            'seo_og_description' => 'seo_og_description',
            'seo_og_image' => 'seo_og_image',
            'google_tag_manager_id' => 'google_tag_manager_id',
            'google_analytics_id' => 'google_analytics_id',
            'meta_pixel_id' => 'meta_pixel_id',
        ];

        AppSetting::set('frontend_video_enabled', $request->boolean('video_enabled') ? '1' : '0');
        foreach ($keys as $input => $settingKey) {
            if (array_key_exists($input, $data)) {
                AppSetting::set($settingKey, is_string($data[$input]) ? trim($data[$input]) : $data[$input]);
            }
        }

        return back()->with('success', 'Pengaturan frontend, SEO, dan tracking berhasil disimpan.');
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
