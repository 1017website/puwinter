@extends('admin.layouts.app')
@section('title', 'Pengaturan')

@section('content')

<div class="page-header">
    <div>
        <h2>Pengaturan</h2>
        <p>Kelola identitas visual, tools server, dan informasi sistem.</p>
    </div>
</div>

{{-- FRONTEND, SEO & TRACKING --}}
<div class="card" style="margin-bottom:20px;">
    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:18px;">
        <div>
            <div style="font-size:15px; font-weight:700; margin-bottom:4px;"><i class="fas fa-globe" style="color:var(--primary); margin-right:7px;"></i>SEO & Tracking Frontend</div>
            <p style="font-size:12px; color:var(--muted); line-height:1.6;">Atur tampilan di mesin pencari serta integrasi pemasaran. Video pembelajaran dikelola melalui menu Video Demo.</p>
        </div>
        <a href="{{ route('home') }}" target="_blank" class="btn btn-sm" style="border:1px solid var(--border); color:var(--primary); flex-shrink:0;"><i class="fas fa-arrow-up-right-from-square"></i> Lihat Frontend</a>
    </div>

    @if($errors->any())
    <div style="background:#FEF2F2; border:1px solid #FECACA; color:#B91C1C; border-radius:8px; padding:11px 14px; font-size:12px; margin-bottom:16px;">
        <strong>Pengaturan belum disimpan.</strong> Periksa kembali field yang ditandai di bawah.
    </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.frontend') }}" enctype="multipart/form-data">
        @csrf
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:20px;">
            <div style="border:1px solid var(--border); border-radius:10px; padding:16px;">
                <div style="font-size:13px; font-weight:700; margin-bottom:12px;"><i class="fas fa-magnifying-glass" style="color:#059669; margin-right:6px;"></i>SEO & Social Sharing</div>
                <div style="margin-bottom:11px;">
                    <label style="font-size:12px;font-weight:600;display:block;margin-bottom:5px;">SEO Title <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="seo_title" value="{{ old('seo_title', $frontend['seo_title']) }}" maxlength="70" required class="form-control">
                    @error('seo_title')<div style="font-size:11px;color:var(--danger);margin-top:3px;">{{ $message }}</div>@enderror
                </div>
                <div style="margin-bottom:11px;">
                    <label style="font-size:12px;font-weight:600;display:block;margin-bottom:5px;">Meta Description <span style="color:var(--danger);">*</span></label>
                    <textarea name="seo_description" rows="3" maxlength="170" required class="form-control">{{ old('seo_description', $frontend['seo_description']) }}</textarea>
                    @error('seo_description')<div style="font-size:11px;color:var(--danger);margin-top:3px;">{{ $message }}</div>@enderror
                </div>
                <div style="margin-bottom:11px;">
                    <label style="font-size:12px;font-weight:600;display:block;margin-bottom:5px;">Keywords</label>
                    <input type="text" name="seo_keywords" value="{{ old('seo_keywords', $frontend['seo_keywords']) }}" placeholder="grammar, TKA Inggris, TOEFL" class="form-control">
                </div>
                <div style="display:grid;grid-template-columns:1fr 145px;gap:10px;margin-bottom:11px;">
                    <div><label style="font-size:12px;font-weight:600;display:block;margin-bottom:5px;">Canonical URL</label><input type="url" name="seo_canonical_url" value="{{ old('seo_canonical_url', $frontend['seo_canonical_url']) }}" placeholder="https://puwinter.com" class="form-control"></div>
                    <div><label style="font-size:12px;font-weight:600;display:block;margin-bottom:5px;">Robots</label><select name="seo_robots" class="form-control">@foreach(['index,follow','index,nofollow','noindex,follow','noindex,nofollow'] as $robots)<option value="{{ $robots }}" {{ old('seo_robots', $frontend['seo_robots']) === $robots ? 'selected' : '' }}>{{ $robots }}</option>@endforeach</select></div>
                </div>
                <div style="margin-bottom:11px;">
                    <label style="font-size:12px;font-weight:600;display:block;margin-bottom:5px;">Open Graph Title</label>
                    <input type="text" name="seo_og_title" value="{{ old('seo_og_title', $frontend['seo_og_title']) }}" placeholder="Kosongkan untuk memakai SEO Title" class="form-control">
                </div>
                <div style="margin-bottom:11px;">
                    <label style="font-size:12px;font-weight:600;display:block;margin-bottom:5px;">Open Graph Description</label>
                    <textarea name="seo_og_description" rows="2" class="form-control" placeholder="Kosongkan untuk memakai Meta Description">{{ old('seo_og_description', $frontend['seo_og_description']) }}</textarea>
                </div>
                <div><label style="font-size:12px;font-weight:600;display:block;margin-bottom:5px;">Open Graph Image</label><input type="file" name="seo_og_image" accept="image/png,image/jpeg,image/webp" class="form-control">@if($frontend['seo_og_image'])<a href="{{ $frontend['seo_og_image'] }}" target="_blank" style="font-size:10.5px;color:var(--primary);">Lihat gambar saat ini</a>@endif</div>
            </div>

            <div style="border:1px solid var(--border); border-radius:10px; padding:16px;">
                <div style="font-size:13px; font-weight:700; margin-bottom:12px;"><i class="fas fa-chart-line" style="color:#2563EB; margin-right:6px;"></i>Marketing & Analytics</div>
                <div style="margin-bottom:13px;"><label style="font-size:12px;font-weight:600;display:block;margin-bottom:5px;">Google Tag Manager ID</label><input type="text" name="google_tag_manager_id" value="{{ old('google_tag_manager_id', $frontend['google_tag_manager_id']) }}" placeholder="GTM-XXXXXXX" class="form-control" style="text-transform:uppercase;">@error('google_tag_manager_id')<div style="font-size:11px;color:var(--danger);margin-top:3px;">{{ $message }}</div>@enderror</div>
                <div style="margin-bottom:13px;"><label style="font-size:12px;font-weight:600;display:block;margin-bottom:5px;">Google Analytics 4 ID</label><input type="text" name="google_analytics_id" value="{{ old('google_analytics_id', $frontend['google_analytics_id']) }}" placeholder="G-XXXXXXXXXX" class="form-control" style="text-transform:uppercase;">@error('google_analytics_id')<div style="font-size:11px;color:var(--danger);margin-top:3px;">{{ $message }}</div>@enderror</div>
                <div style="margin-bottom:13px;"><label style="font-size:12px;font-weight:600;display:block;margin-bottom:5px;">Google Ads Tag ID</label><input type="text" name="google_ads_id" value="{{ old('google_ads_id', $frontend['google_ads_id']) }}" placeholder="AW-123456789" class="form-control" style="text-transform:uppercase;">@error('google_ads_id')<div style="font-size:11px;color:var(--danger);margin-top:3px;">{{ $message }}</div>@enderror<small style="font-size:10.5px;color:var(--muted);">Global site tag Google Ads dimuat otomatis di seluruh frontend.</small></div>
                <div style="margin-bottom:15px;"><label style="font-size:12px;font-weight:600;display:block;margin-bottom:5px;">Meta Pixel ID</label><input type="text" inputmode="numeric" name="meta_pixel_id" value="{{ old('meta_pixel_id', $frontend['meta_pixel_id']) }}" placeholder="123456789012345" class="form-control">@error('meta_pixel_id')<div style="font-size:11px;color:var(--danger);margin-top:3px;">{{ $message }}</div>@enderror</div>
                <div style="background:#EFF6FF;border-radius:8px;padding:11px;font-size:11px;color:#1E40AF;line-height:1.6;"><i class="fas fa-circle-info"></i> ID kosong tidak akan memuat script apa pun di frontend. Google Analytics dan Meta Pixel mencatat PageView otomatis.</div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:18px;"><i class="fas fa-save"></i> Simpan Pengaturan Frontend</button>
    </form>
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

        {{-- FITUR SISWA --}}
        <div class="card">
            <div style="font-size:14px; font-weight:700; margin-bottom:4px; display:flex; align-items:center; gap:8px;">
                <i class="fas fa-toggle-on" style="color:var(--primary);"></i> Fitur Siswa
            </div>
            <p style="font-size:12px; color:var(--muted); margin-bottom:14px; line-height:1.6;">
                Atur fitur yang tampil dan dapat diakses dari akun siswa.
            </p>
            <form method="POST" action="{{ route('admin.settings.features') }}">
                @csrf
                <label style="display:flex; align-items:flex-start; gap:10px; padding:12px; border:1px solid var(--border); border-radius:8px; cursor:pointer; margin-bottom:14px;">
                    <input type="checkbox" name="student_tryout_enabled" value="1"
                           {{ ($features['student_tryout_enabled'] ?? true) ? 'checked' : '' }}
                           style="margin-top:2px; width:16px; height:16px;">
                    <span>
                        <strong style="display:block; font-size:13px;">Aktifkan halaman Tryout</strong>
                        <small style="display:block; color:var(--muted); margin-top:3px; line-height:1.5;">
                            Jika dimatikan, menu dan konten tryout disembunyikan serta URL tryout siswa tidak dapat diakses.
                        </small>
                    </span>
                </label>
                <button type="submit" class="btn btn-primary btn-sm" style="width:100%; justify-content:center;">
                    <i class="fas fa-save"></i> Simpan Pengaturan Fitur
                </button>
            </form>
        </div>

        {{-- REKENING TRANSFER MANUAL --}}
        <div class="card">
            <div style="font-size:14px; font-weight:700; margin-bottom:4px;">Rekening Transfer Manual</div>
            <p style="font-size:12px; color:var(--muted); margin-bottom:14px;">Ditampilkan ke siswa pada halaman instruksi pembayaran.</p>
            <form method="POST" action="{{ route('admin.settings.bank') }}">
                @csrf
                <div style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; display:block; margin-bottom:5px;">Nama Bank</label>
                    <input type="text" name="bank_name" value="{{ $bank['bank_name'] }}" placeholder="mis. BCA"
                           class="form-control" style="width:100%; padding:9px 12px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; outline:none;">
                </div>
                <div style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; display:block; margin-bottom:5px;">No. Rekening</label>
                    <input type="text" name="bank_account" value="{{ $bank['bank_account'] }}" placeholder="mis. 1234567890"
                           class="form-control" style="width:100%; padding:9px 12px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; outline:none;">
                </div>
                <div style="margin-bottom:12px;">
                    <label style="font-size:12px; font-weight:600; display:block; margin-bottom:5px;">Atas Nama</label>
                    <input type="text" name="bank_holder" value="{{ $bank['bank_holder'] }}" placeholder="mis. PT Puwinter Edukasi"
                           class="form-control" style="width:100%; padding:9px 12px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; outline:none;">
                </div>
                <div style="margin-bottom:14px;">
                    <label style="font-size:12px; font-weight:600; display:block; margin-bottom:5px;">Catatan Pembayaran (opsional)</label>
                    <textarea name="payment_note" rows="2" placeholder="mis. Konfirmasi via WhatsApp 0812xxxx setelah transfer."
                              class="form-control" style="width:100%; padding:9px 12px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; outline:none; resize:vertical;">{{ $bank['payment_note'] }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm" style="width:100%; justify-content:center;">
                    <i class="fas fa-save"></i> Simpan Rekening
                </button>
            </form>
        </div>



        {{-- AFFILIATE --}}
        <div class="card">
            <div style="font-size:14px; font-weight:700; margin-bottom:4px; display:flex; align-items:center; gap:8px;">
                <i class="fas fa-handshake" style="color:var(--primary);"></i> Pengaturan Affiliate
            </div>
            <p style="font-size:12px; color:var(--muted); margin-bottom:14px; line-height:1.6;">
                Atur benefit/reward untuk pemilik kode affiliate. Siswa yang memakai kode hanya mencatat referrer; benefit diberikan kepada orang yang mengajak saat pembayaran divalidasi admin.
            </p>
            <form method="POST" action="{{ route('admin.settings.affiliate') }}">
                @csrf
                <div style="margin-bottom:14px;">
                    <label style="font-size:12px; font-weight:600; display:block; margin-bottom:5px;">Benefit / Reward Pemilik Kode (Rp)</label>
                    <input type="number" name="affiliate_reward_amount" value="{{ old('affiliate_reward_amount', $affiliate['affiliate_reward_amount'] ?? 0) }}" min="0" placeholder="mis. 10000"
                           class="form-control" style="width:100%; padding:9px 12px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; outline:none;">
                    <div style="font-size:11px; color:var(--muted); margin-top:4px;">Benefit ini dicatat untuk pemilik kode ketika admin mengaktifkan pembayaran siswa yang memakai kode tersebut.</div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm" style="width:100%; justify-content:center;">
                    <i class="fas fa-save"></i> Simpan Affiliate
                </button>
            </form>
        </div>

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
                ],
                'Cache & Optimasi' => [
                    'optimize:clear'   => ['label'=>'optimize:clear',   'desc'=>'Hapus semua cache sekaligus',               'color'=>'yellow','icon'=>'fa-broom'],
                ],
                'Storage & Lainnya' => [
                    'storage:link'     => ['label'=>'storage:link',     'desc'=>'Buat symlink public/storage → storage/app/public', 'color'=>'green','icon'=>'fa-link'],
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
