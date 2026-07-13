@extends('admin.layouts.app')
@section('title', 'Kode Pendaftar')

@section('content')
<div class="page-header">
    <div>
        <h2>Kode Pendaftar</h2>
        <p>Buat kode khusus untuk mengelompokkan siswa berdasarkan jalur pendaftaran.</p>
    </div>
</div>

<div class="stats-grid" style="grid-template-columns:repeat(3,1fr); margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:#EFF6FF;color:#2563EB;"><i class="fas fa-ticket"></i></div>
        <div><div class="stat-value">{{ number_format($stats['codes']) }}</div><div class="stat-label">Total Kode</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#ECFDF5;color:#059669;"><i class="fas fa-circle-check"></i></div>
        <div><div class="stat-value">{{ number_format($stats['active']) }}</div><div class="stat-label">Kode Aktif</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#FFF7ED;color:#EA580C;"><i class="fas fa-user-group"></i></div>
        <div><div class="stat-value">{{ number_format($stats['students']) }}</div><div class="stat-label">Siswa Berkode</div></div>
    </div>
</div>

<div class="registration-code-grid" style="display:grid; grid-template-columns:320px minmax(0,1fr); gap:20px; align-items:start;">
    <div class="card">
        <div style="font-size:15px;font-weight:700;margin-bottom:4px;">Generate Kode Baru</div>
        <p style="font-size:12px;color:var(--muted);line-height:1.5;margin-bottom:16px;">Kode unik akan dibuat otomatis setelah form disimpan.</p>

        <form method="POST" action="{{ route('admin.registration-codes.store') }}">
            @csrf
            <div style="margin-bottom:12px;">
                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:5px;">Nama Kelompok <span style="color:var(--danger);">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control" required
                       placeholder="Contoh: Sekolah A - Gelombang 1">
                @error('name') <div style="font-size:11px;color:var(--danger);margin-top:4px;">{{ $message }}</div> @enderror
            </div>
            <div style="margin-bottom:12px;">
                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:5px;">Keterangan</label>
                <textarea name="description" rows="3" class="form-control" style="resize:vertical;" placeholder="Catatan internal (opsional)">{{ old('description') }}</textarea>
                @error('description') <div style="font-size:11px;color:var(--danger);margin-top:4px;">{{ $message }}</div> @enderror
            </div>
            <div style="margin-bottom:16px;">
                <label style="font-size:12px;font-weight:600;display:block;margin-bottom:5px;">Berlaku Sampai</label>
                <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}" class="form-control">
                <div style="font-size:11px;color:var(--muted);margin-top:4px;">Kosongkan jika tidak ada batas waktu.</div>
                @error('expires_at') <div style="font-size:11px;color:var(--danger);margin-top:4px;">{{ $message }}</div> @enderror
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                <i class="fas fa-wand-magic-sparkles"></i> Generate Kode
            </button>
        </form>
    </div>

    <div class="card">
        <form method="GET" style="display:flex;gap:8px;margin-bottom:16px;">
            <input type="search" name="search" value="{{ request('search') }}" class="form-control" style="flex:1;" placeholder="Cari nama kelompok atau kode...">
            <button class="btn btn-outline" type="submit"><i class="fas fa-search"></i> Cari</button>
        </form>

        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="text-align:left;color:var(--muted);border-bottom:1.5px solid var(--border);">
                        <th style="padding:10px 8px;">Kelompok & Kode</th>
                        <th style="padding:10px 8px;text-align:center;">Siswa</th>
                        <th style="padding:10px 8px;">Status</th>
                        <th style="padding:10px 8px;text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registrationCodes as $item)
                    @php
                        $expired = $item->expires_at && $item->expires_at->isPast();
                        $available = $item->is_active && !$expired;
                    @endphp
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:13px 8px;">
                            <div style="font-weight:700;">{{ $item->name }}</div>
                            <div style="display:flex;align-items:center;gap:6px;margin-top:5px;">
                                <code style="font-size:12px;background:var(--bg);padding:4px 7px;border-radius:5px;color:var(--primary);font-weight:700;">{{ $item->code }}</code>
                                <button type="button" onclick="copyRegistrationCode('{{ $item->code }}', this)" title="Salin kode"
                                        style="border:0;background:transparent;color:var(--muted);cursor:pointer;padding:3px;">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </td>
                        <td style="padding:13px 8px;text-align:center;">
                            <a href="{{ route('admin.registration-codes.show', $item) }}" style="font-weight:800;color:var(--primary);text-decoration:none;font-size:15px;">
                                {{ number_format($item->students_count) }}
                            </a>
                        </td>
                        <td style="padding:13px 8px;">
                            <span class="badge {{ $available ? 'badge-success' : 'badge-danger' }}">
                                {{ $available ? 'Aktif' : ($expired ? 'Kedaluwarsa' : 'Nonaktif') }}
                            </span>
                            @if($item->expires_at)
                                <div style="font-size:10px;color:var(--muted);margin-top:4px;">s.d. {{ $item->expires_at->format('d M Y H:i') }}</div>
                            @endif
                        </td>
                        <td style="padding:13px 8px;text-align:right;white-space:nowrap;">
                            <a href="{{ route('admin.registration-codes.show', $item) }}" class="btn btn-outline btn-sm"><i class="fas fa-users"></i> Detail</a>
                            <form method="POST" action="{{ route('admin.registration-codes.toggle-active', $item) }}" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $item->is_active ? 'btn-danger' : 'btn-primary' }}" title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }} kode">
                                    <i class="fas {{ $item->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="padding:40px 10px;text-align:center;color:var(--muted);">Belum ada kode pendaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($registrationCodes->hasPages())
            <div style="margin-top:16px;">{{ $registrationCodes->links() }}</div>
        @endif
    </div>
</div>

<script>
async function copyRegistrationCode(code, button) {
    try {
        await navigator.clipboard.writeText(code);
        const icon = button.querySelector('i');
        icon.className = 'fas fa-check';
        setTimeout(() => icon.className = 'fas fa-copy', 1200);
    } catch (error) {
        window.prompt('Salin kode berikut:', code);
    }
}
</script>

@push('styles')
<style>
@media (max-width: 900px) {
    .registration-code-grid { grid-template-columns:1fr !important; }
}
</style>
@endpush
@endsection
