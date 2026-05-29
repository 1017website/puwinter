@extends('admin.layouts.app')
@section('title', 'Pengaturan')

@section('content')

<div class="page-header">
    <div>
        <h2>Pengaturan</h2>
        <p>Kelola identitas visual, tools server, dan informasi sistem.</p>
    </div>
</div>

{{-- Artisan output --}}
@if(session('artisan_output'))
@php $ao = session('artisan_output'); @endphp
<div style="margin-bottom:20px; background:#0F172A; border-radius:10px; padding:16px 20px; font-family:monospace;">
    <div style="font-size:11px; color:#475569; margin-bottom:8px;">
        $ php artisan {{ $ao['command'] }}
    </div>
    <div style="font-size:12.5px; color:{{ $ao['success'] ? '#86EFAC' : '#FCA5A5' }}; white-space:pre-wrap; line-height:1.6;">{{ $ao['output'] }}</div>
</div>
@endif

<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; align-items:flex-start;">

    {{-- KIRI --}}
    <div style="display:flex; flex-direction:column; gap:20px;">

        {{-- Logo --}}
        <div class="card">
            <div style="font-size:14px; font-weight:700; margin-bottom:4px;">Logo Utama</div>
            <p style="font-size:12px; color:var(--muted); margin-bottom:14px;">Muncul di sidebar, halaman utama, login. Gunakan PNG transparan.</p>
            <div style="background:#1E293B; border-radius:8px; padding:16px; display:flex; gap:16px; margin-bottom:14px;">
                <div style="flex:1; text-align:center;">
                    <div style="font-size:10px; color:#475569; margin-bottom:6px;">Dark bg</div>
                    <img src="{{ asset('images/logo.png') }}?v={{ time() }}" alt="Logo" style="height:36px; width:auto; filter:brightness(0) invert(1);" onerror="this.style.display='none'">
                </div>
                <div style="flex:1; background:#fff; border-radius:6px; padding:10px; text-align:center;">
                    <div style="font-size:10px; color:var(--muted); margin-bottom:6px;">Light bg</div>
                    <img src="{{ asset('images/logo.png') }}?v={{ time() }}" alt="Logo" style="height:36px; width:auto;" onerror="this.style.display='none'">
                </div>
            </div>
            <form method="POST" action="{{ route('admin.settings.logo') }}" enctype="multipart/form-data">
                @csrf
                <div style="display:flex; gap:8px; align-items:flex-end;">
                    <div style="flex:1;">
                        <input type="file" name="logo" accept="image/*" class="form-control" style="font-size:12px;" required>
                        <div style="font-size:11px; color:var(--muted); margin-top:3px;">PNG/JPG/SVG, maks 2MB</div>
                        @error('logo') <div style="font-size:11px; color:var(--danger); margin-top:2px;">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm" style="flex-shrink:0;"><i class="fas fa-upload"></i> Upload</button>
                </div>
            </form>
        </div>

        {{-- Favicon --}}
        <div class="card">
            <div style="font-size:14px; font-weight:700; margin-bottom:4px;">Favicon</div>
            <p style="font-size:12px; color:var(--muted); margin-bottom:14px;">Ikon tab browser. Minimal 32×32px.</p>
            <div style="background:var(--bg); border-radius:8px; padding:12px; display:flex; align-items:center; gap:10px; margin-bottom:14px;">
                <div style="width:36px; height:36px; border:1px solid var(--border); border-radius:6px; background:#fff; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                    <img src="{{ asset('images/favicon.png') }}?v={{ time() }}" style="width:28px; height:28px; object-fit:contain;" onerror="this.style.display='none'; this.nextSibling.style.display='block'">
                    <i class="fas fa-globe" style="display:none; color:var(--muted); font-size:18px;"></i>
                </div>
                <span style="font-size:12px; color:var(--muted);">favicon.png</span>
            </div>
            <form method="POST" action="{{ route('admin.settings.favicon') }}" enctype="multipart/form-data">
                @csrf
                <div style="display:flex; gap:8px; align-items:flex-end;">
                    <div style="flex:1;">
                        <input type="file" name="favicon" accept="image/*" class="form-control" style="font-size:12px;" required>
                        <div style="font-size:11px; color:var(--muted); margin-top:3px;">PNG/ICO, maks 512KB</div>
                        @error('favicon') <div style="font-size:11px; color:var(--danger); margin-top:2px;">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm" style="flex-shrink:0;"><i class="fas fa-upload"></i> Upload</button>
                </div>
            </form>
        </div>

    </div>

    {{-- KANAN --}}
    <div style="display:flex; flex-direction:column; gap:20px;">

        {{-- ARTISAN PANEL --}}
        <div class="card">
            <div style="font-size:14px; font-weight:700; margin-bottom:4px; display:flex; align-items:center; gap:8px;">
                <div style="width:28px; height:28px; background:#0F172A; border-radius:6px; display:flex; align-items:center; justify-content:center;">
                    <i class="fas fa-terminal" style="font-size:12px; color:#86EFAC;"></i>
                </div>
                Artisan Panel
            </div>
            <p style="font-size:12px; color:var(--muted); margin-bottom:16px;">Jalankan perintah artisan tanpa akses terminal. Berguna untuk shared hosting.</p>

            @php
            $cmdGroups = [
                'Database' => [
                    'migrate'          => ['label'=>'migrate',          'desc'=>'Jalankan semua migration baru',             'color'=>'blue',  'icon'=>'fa-database'],
                    'migrate:rollback' => ['label'=>'migrate:rollback', 'desc'=>'Rollback migration terakhir',               'color'=>'yellow','icon'=>'fa-rotate-left'],
                    'db:seed'          => ['label'=>'db:seed',          'desc'=>'Jalankan semua database seeder',            'color'=>'green', 'icon'=>'fa-seedling'],
                ],
                'Cache & Optimasi' => [
                    'optimize'         => ['label'=>'optimize',         'desc'=>'Cache config, route, dan view sekaligus',   'color'=>'green', 'icon'=>'fa-bolt'],
                    'optimize:clear'   => ['label'=>'optimize:clear',   'desc'=>'Hapus semua cache sekaligus',               'color'=>'yellow','icon'=>'fa-broom'],
                    'cache:clear'      => ['label'=>'cache:clear',      'desc'=>'Hapus application cache',                   'color'=>'yellow','icon'=>'fa-trash-can'],
                    'config:cache'     => ['label'=>'config:cache',     'desc'=>'Cache semua file konfigurasi',              'color'=>'blue',  'icon'=>'fa-gear'],
                    'config:clear'     => ['label'=>'config:clear',     'desc'=>'Hapus config cache',                        'color'=>'yellow','icon'=>'fa-gear'],
                    'route:cache'      => ['label'=>'route:cache',      'desc'=>'Cache semua routes',                        'color'=>'blue',  'icon'=>'fa-route'],
                    'route:clear'      => ['label'=>'route:clear',      'desc'=>'Hapus route cache',                         'color'=>'yellow','icon'=>'fa-route'],
                    'view:cache'       => ['label'=>'view:cache',       'desc'=>'Compile semua Blade views',                 'color'=>'blue',  'icon'=>'fa-eye'],
                    'view:clear'       => ['label'=>'view:clear',       'desc'=>'Hapus compiled view cache',                 'color'=>'yellow','icon'=>'fa-eye'],
                ],
                'Storage & Lainnya' => [
                    'storage:link'     => ['label'=>'storage:link',     'desc'=>'Buat symlink public/storage → storage/app/public', 'color'=>'green','icon'=>'fa-link'],
                    'queue:restart'    => ['label'=>'queue:restart',    'desc'=>'Restart queue worker setelah deploy',       'color'=>'blue',  'icon'=>'fa-refresh'],
                    'schedule:run'     => ['label'=>'schedule:run',     'desc'=>'Jalankan scheduled tasks sekarang',         'color'=>'blue',  'icon'=>'fa-clock'],
                    'event:clear'      => ['label'=>'event:clear',      'desc'=>'Hapus event & listener cache',              'color'=>'yellow','icon'=>'fa-bolt'],
                ],
            ];
            $colorMap = [
                'blue'   => ['bg'=>'#EFF6FF', 'color'=>'#2563EB'],
                'green'  => ['bg'=>'#ECFDF5', 'color'=>'#059669'],
                'yellow' => ['bg'=>'#FFFBEB', 'color'=>'#D97706'],
                'red'    => ['bg'=>'#FEF2F2', 'color'=>'#DC2626'],
            ];
            @endphp

            @foreach($cmdGroups as $groupName => $commands)
            <div style="margin-bottom:16px;">
                <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--muted); margin-bottom:8px; padding-bottom:6px; border-bottom:1px solid var(--border);">
                    {{ $groupName }}
                </div>
                <div style="display:flex; flex-direction:column; gap:6px;">
                    @foreach($commands as $cmd => $meta)
                    @php $c = $colorMap[$meta['color']]; @endphp
                    <form method="POST" action="{{ route('admin.settings.artisan') }}"
                          @if($meta['color']==='red') onsubmit="return confirm('⚠️ PERINGATAN: php artisan {{ $cmd }} akan menghapus semua data di database!\n\nYakin ingin melanjutkan?')" @endif>
                        @csrf
                        <input type="hidden" name="command" value="{{ $cmd }}">
                        <button type="submit"
                                style="width:100%; display:flex; align-items:center; gap:10px; padding:9px 12px; background:{{ $c['bg'] }}; border:1px solid {{ $c['color'] }}22; border-radius:8px; cursor:pointer; text-align:left; font-family:inherit; transition:all 0.15s;"
                                onmouseover="this.style.borderColor='{{ $c['color'] }}66'; this.style.background='{{ $c['color'] }}18'"
                                onmouseout="this.style.borderColor='{{ $c['color'] }}22'; this.style.background='{{ $c['bg'] }}'">
                            <div style="width:28px; height:28px; border-radius:6px; background:{{ $c['color'] }}20; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <i class="fas {{ $meta['icon'] }}" style="font-size:12px; color:{{ $c['color'] }};"></i>
                            </div>
                            <div style="flex:1; min-width:0;">
                                <div style="font-size:12px; font-weight:700; color:#1E293B; font-family:monospace;">php artisan {{ $meta['label'] }}</div>
                                <div style="font-size:11px; color:var(--muted); margin-top:1px;">{{ $meta['desc'] }}</div>
                            </div>
                            <i class="fas fa-play" style="font-size:10px; color:{{ $c['color'] }}; flex-shrink:0;"></i>
                        </button>
                    </form>
                    @endforeach
                </div>
            </div>
            @endforeach

            <div style="background:#FEF3C7; border:1px solid #FCD34D; border-radius:8px; padding:10px 12px; display:flex; gap:8px; margin-top:4px;">
                <i class="fas fa-triangle-exclamation" style="color:#F59E0B; flex-shrink:0; margin-top:1px;"></i>
                <div style="font-size:11px; color:#92400E; line-height:1.5;">
                    Setelah <strong>config:cache</strong> atau <strong>route:cache</strong>, jalankan <strong>optimize:clear</strong> jika ada perubahan kode.
                </div>
            </div>
        </div>

        {{-- SYSTEM INFO --}}
        <div class="card">
            <div style="font-size:14px; font-weight:700; margin-bottom:14px; padding-bottom:10px; border-bottom:1px solid var(--border);">
                <i class="fas fa-circle-info" style="color:var(--primary); margin-right:6px;"></i> Informasi Sistem
            </div>
            <table style="width:100%; font-size:12.5px; border-collapse:collapse;">
                @foreach([
                    ['label'=>'PHP Version',     'value'=>$sysInfo['php_version'],    'ok'=>version_compare($sysInfo['php_version'],'8.1','>=')],
                    ['label'=>'Laravel Version', 'value'=>$sysInfo['laravel_version'],'ok'=>true],
                    ['label'=>'Environment',     'value'=>$sysInfo['env'],            'ok'=>$sysInfo['env']==='production'],
                    ['label'=>'Debug Mode',      'value'=>$sysInfo['debug'],          'ok'=>$sysInfo['debug']==='OFF'],
                    ['label'=>'App URL',         'value'=>$sysInfo['app_url'],        'ok'=>true],
                    ['label'=>'Storage Link',    'value'=>$sysInfo['storage_linked'] ? 'Terhubung ✓':'Belum dibuat ✗', 'ok'=>$sysInfo['storage_linked']],
                    ['label'=>'Cache Driver',    'value'=>$sysInfo['cache_driver'],   'ok'=>true],
                    ['label'=>'Queue Driver',    'value'=>$sysInfo['queue_driver'],   'ok'=>true],
                    ['label'=>'DB Connection',   'value'=>$sysInfo['db_connection'],  'ok'=>true],
                ] as $row)
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:8px 0; color:var(--muted); width:45%;">{{ $row['label'] }}</td>
                    <td style="padding:8px 0; font-weight:600; color:{{ $row['ok'] ? '#1E293B':'var(--danger)' }}; font-family:monospace;">
                        {{ $row['value'] }}
                    </td>
                </tr>
                @endforeach
            </table>
        </div>

    </div>
</div>

@endsection
