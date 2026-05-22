@extends('admin.layouts.app')
@section('title', 'Pengaturan')

@section('content')

<div class="page-header">
    <div>
        <h2>Pengaturan</h2>
        <p>Kelola identitas visual platform.</p>
    </div>
</div>

<div style="max-width:640px; display:flex; flex-direction:column; gap:20px;">

    {{-- Logo --}}
    <div class="card">
        <div style="font-size:14px; font-weight:700; margin-bottom:4px;">Logo Utama</div>
        <p style="font-size:12.5px; color:var(--muted); margin-bottom:16px;">
            Muncul di sidebar admin, sidebar siswa, halaman utama, dan footer. Gunakan PNG dengan background transparan.
        </p>

        {{-- Preview --}}
        <div style="background:#1E293B; border-radius:10px; padding:20px 24px; margin-bottom:16px; display:flex; align-items:center; gap:16px;">
            <div style="flex:1;">
                <div style="font-size:11px; color:#475569; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.5px;">Preview (sidebar gelap)</div>
                <img src="{{ asset('images/logo.png') }}?v={{ time() }}" alt="Logo"
                     style="height:32px; width:auto; object-fit:contain; filter:brightness(0) invert(1);"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                <div style="display:none; align-items:center; gap:8px; color:#475569; font-size:12px;">
                    <i class="fas fa-image"></i> Logo belum diupload
                </div>
            </div>
            <div style="flex:1; background:#fff; border-radius:8px; padding:16px;">
                <div style="font-size:11px; color:var(--muted); margin-bottom:8px; text-transform:uppercase; letter-spacing:0.5px;">Preview (background putih)</div>
                <img src="{{ asset('images/logo.png') }}?v={{ time() }}" alt="Logo"
                     style="height:32px; width:auto; object-fit:contain;"
                     onerror="this.style.display='none'">
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.logo') }}" enctype="multipart/form-data">
            @csrf
            <div style="display:flex; gap:10px; align-items:flex-end;">
                <div style="flex:1;">
                    <label style="font-size:12.5px; font-weight:600; display:block; margin-bottom:6px;">Upload Logo Baru</label>
                    <input type="file" name="logo" accept="image/png,image/jpeg,image/svg+xml,image/webp"
                           class="form-control" style="font-size:12.5px;" required>
                    <div style="font-size:11px; color:var(--muted); margin-top:4px;">Format: PNG, JPG, SVG, WebP. Maks 2MB. Rekomendasi: PNG transparan.</div>
                    @error('logo') <div style="font-size:12px; color:var(--danger); margin-top:4px;">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="btn btn-primary btn-sm" style="flex-shrink:0;">
                    <i class="fas fa-upload"></i> Upload
                </button>
            </div>
        </form>
    </div>

    {{-- Favicon --}}
    <div class="card">
        <div style="font-size:14px; font-weight:700; margin-bottom:4px;">Favicon</div>
        <p style="font-size:12.5px; color:var(--muted); margin-bottom:16px;">
            Ikon kecil yang muncul di tab browser. Gunakan gambar square minimal 32×32px.
        </p>

        {{-- Preview --}}
        <div style="background:var(--bg); border-radius:10px; padding:16px; margin-bottom:16px; display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:8px; border:1px solid var(--border); display:flex; align-items:center; justify-content:center; background:#fff; overflow:hidden; flex-shrink:0;">
                <img src="{{ asset('images/favicon.png') }}?v={{ time() }}" alt="Favicon"
                     style="width:32px; height:32px; object-fit:contain;"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
                <i class="fas fa-globe" style="display:none; color:var(--muted); font-size:20px;"></i>
            </div>
            <div>
                <div style="font-size:12.5px; font-weight:600;">favicon.png</div>
                <div style="font-size:11px; color:var(--muted);">Muncul di tab browser</div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.favicon') }}" enctype="multipart/form-data">
            @csrf
            <div style="display:flex; gap:10px; align-items:flex-end;">
                <div style="flex:1;">
                    <label style="font-size:12.5px; font-weight:600; display:block; margin-bottom:6px;">Upload Favicon Baru</label>
                    <input type="file" name="favicon" accept="image/png,image/jpeg,image/x-icon"
                           class="form-control" style="font-size:12.5px;" required>
                    <div style="font-size:11px; color:var(--muted); margin-top:4px;">Format: PNG, JPG, ICO. Maks 512KB. Rekomendasi: PNG 32×32 atau 64×64px.</div>
                    @error('favicon') <div style="font-size:12px; color:var(--danger); margin-top:4px;">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="btn btn-primary btn-sm" style="flex-shrink:0;">
                    <i class="fas fa-upload"></i> Upload
                </button>
            </div>
        </form>
    </div>

    {{-- Info --}}
    <div class="card" style="background:#FFFBEB; border-color:#FCD34D;">
        <div style="display:flex; gap:10px; align-items:flex-start;">
            <i class="fas fa-triangle-exclamation" style="color:#F59E0B; margin-top:1px; flex-shrink:0;"></i>
            <div style="font-size:12.5px; color:#92400E; line-height:1.6;">
                Setelah upload, lakukan <strong>hard refresh</strong> di browser (Ctrl+Shift+R) agar perubahan terlihat.
                Logo akan otomatis terganti di semua halaman tanpa perlu deploy ulang.
            </div>
        </div>
    </div>

</div>

@endsection
