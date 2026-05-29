<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

/**
 * Jalur khusus untuk menjalankan migrasi & perawatan dari browser pada
 * shared hosting yang TIDAK punya akses terminal.
 *
 * Sengaja TIDAK merender layout (tidak ada View Composer, tidak ada query DB
 * untuk badge) agar tetap berfungsi walaupun ada tabel yang belum termigrasi.
 * Output berupa teks polos.
 *
 * Akses dibatasi hanya untuk superadmin. Route dilindungi middleware 'auth'
 * dan pengecekan role dilakukan di dalam method ini.
 */
class SystemMaintenanceController extends Controller
{
    /** Daftar command yang diizinkan dijalankan via browser. */
    private array $allowed = [
        'migrate'          => ['migrate', ['--force' => true]],
        'migrate:rollback' => ['migrate:rollback', ['--force' => true]],
        'migrate:status'   => ['migrate:status', []],
        'optimize:clear'   => ['optimize:clear', []],
        'cache:clear'      => ['cache:clear', []],
        'config:clear'     => ['config:clear', []],
        'route:clear'      => ['route:clear', []],
        'view:clear'       => ['view:clear', []],
        'storage:link'     => ['storage:link', []],
    ];

    /** Halaman daftar tombol (plain HTML, tanpa layout app). */
    public function index(Request $request)
    {
        $this->authorizeSuperadmin($request);

        $token = csrf_token();
        $buttons = '';
        foreach (array_keys($this->allowed) as $cmd) {
            $danger = $cmd === 'migrate:rollback';
            $buttons .= '<form method="POST" action="' . route('system.maintenance.run') . '" style="display:inline-block;margin:4px;">'
                . '<input type="hidden" name="_token" value="' . $token . '">'
                . '<input type="hidden" name="command" value="' . e($cmd) . '">'
                . '<button type="submit" style="padding:10px 16px;border:none;border-radius:8px;cursor:pointer;font-weight:600;'
                . 'background:' . ($danger ? '#DC2626' : '#2563EB') . ';color:#fff;"'
                . ($danger ? ' onclick="return confirm(\'Yakin jalankan rollback?\')"' : '')
                . '>php artisan ' . e($cmd) . '</button></form>';
        }

        return response(
            '<!doctype html><html lang="id"><head><meta charset="utf-8">'
            . '<meta name="viewport" content="width=device-width,initial-scale=1">'
            . '<title>System Maintenance — Puwinter</title></head>'
            . '<body style="font-family:system-ui,Segoe UI,Arial,sans-serif;max-width:760px;margin:40px auto;padding:0 16px;color:#0f172a;">'
            . '<h1 style="font-size:20px;">🛠️ System Maintenance</h1>'
            . '<p style="color:#475569;font-size:14px;">Khusus superadmin. Gunakan ini untuk menjalankan migrasi di hosting tanpa terminal. '
            . 'Untuk update database setelah deploy, klik <strong>php artisan migrate</strong>.</p>'
            . '<div style="margin:18px 0;">' . $buttons . '</div>'
            . '<p style="color:#94a3b8;font-size:12px;">Halaman ini sengaja tidak memakai layout aplikasi agar tetap bisa diakses walau ada tabel yang belum termigrasi.</p>'
            . '</body></html>'
        );
    }

    /** Eksekusi command, tampilkan output sebagai teks polos. */
    public function run(Request $request)
    {
        $this->authorizeSuperadmin($request);

        $command = (string) $request->input('command');

        if (!array_key_exists($command, $this->allowed)) {
            return response('Command tidak diizinkan: ' . e($command), 422)
                ->header('Content-Type', 'text/plain; charset=utf-8');
        }

        [$artisanCmd, $params] = $this->allowed[$command];

        try {
            $exit   = Artisan::call($artisanCmd, $params);
            $output = trim(Artisan::output()) ?: '(tidak ada output)';

            $body = "\$ php artisan {$command}\n"
                . str_repeat('-', 50) . "\n"
                . $output . "\n"
                . str_repeat('-', 50) . "\n"
                . ($exit === 0 ? "✅ SELESAI (exit code 0)" : "❌ GAGAL (exit code {$exit})")
                . "\n\nKembali: " . route('system.maintenance.index');

            return response($body, 200)->header('Content-Type', 'text/plain; charset=utf-8');
        } catch (\Throwable $e) {
            return response(
                "❌ ERROR menjalankan {$command}:\n\n" . $e->getMessage(),
                500
            )->header('Content-Type', 'text/plain; charset=utf-8');
        }
    }

    /** Hanya superadmin yang boleh; selain itu 403. */
    private function authorizeSuperadmin(Request $request): void
    {
        $user = $request->user();
        if (!$user || !$user->isSuperAdmin()) {
            abort(403, 'Halaman ini hanya untuk superadmin.');
        }
    }
}
