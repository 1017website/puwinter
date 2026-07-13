@extends('admin.layouts.app')
@section('title', 'Detail Kode Pendaftar')

@section('content')
<div class="page-header">
    <div>
        <a href="{{ route('admin.registration-codes.index') }}" style="font-size:12px;color:var(--muted);text-decoration:none;display:inline-block;margin-bottom:6px;">
            <i class="fas fa-arrow-left"></i> Kembali ke Kode Pendaftar
        </a>
        <h2>{{ $registrationCode->name }}</h2>
        <p>Daftar siswa yang mendaftar menggunakan kode ini.</p>
    </div>
    <form method="POST" action="{{ route('admin.registration-codes.toggle-active', $registrationCode) }}">
        @csrf @method('PATCH')
        <button class="btn {{ $registrationCode->is_active ? 'btn-danger' : 'btn-primary' }}" type="submit">
            <i class="fas {{ $registrationCode->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
            {{ $registrationCode->is_active ? 'Nonaktifkan Kode' : 'Aktifkan Kode' }}
        </button>
    </form>
</div>

<div class="card" style="margin-bottom:20px;display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
    <div style="flex:1;min-width:220px;">
        <div style="font-size:11px;color:var(--muted);margin-bottom:5px;">KODE PENDAFTAR</div>
        <div style="display:flex;align-items:center;gap:8px;">
            <code id="registration-code" style="font-size:20px;font-weight:800;color:var(--primary);background:var(--bg);padding:8px 12px;border-radius:8px;">{{ $registrationCode->code }}</code>
            <button class="btn btn-outline btn-sm" type="button" onclick="navigator.clipboard.writeText(document.getElementById('registration-code').textContent.trim())">
                <i class="fas fa-copy"></i> Salin
            </button>
        </div>
        @if($registrationCode->description)
            <p style="font-size:12px;color:var(--muted);margin-top:9px;">{{ $registrationCode->description }}</p>
        @endif
    </div>
    <div style="padding:0 20px;border-left:1px solid var(--border);">
        <div style="font-size:28px;font-weight:800;">{{ number_format($registrationCode->students_count) }}</div>
        <div style="font-size:12px;color:var(--muted);">Total siswa</div>
    </div>
    <div style="padding:0 20px;border-left:1px solid var(--border);">
        <span class="badge {{ $registrationCode->isAvailable() ? 'badge-success' : 'badge-danger' }}">
            {{ $registrationCode->isAvailable() ? 'Aktif' : 'Tidak aktif' }}
        </span>
        <div style="font-size:11px;color:var(--muted);margin-top:6px;">
            {{ $registrationCode->expires_at ? 'Berakhir '.$registrationCode->expires_at->format('d M Y H:i') : 'Tanpa batas waktu' }}
        </div>
    </div>
</div>

<div class="card">
    <div style="font-size:15px;font-weight:700;margin-bottom:14px;">Siswa dalam Kelompok Ini</div>
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="text-align:left;color:var(--muted);border-bottom:1.5px solid var(--border);">
                    <th style="padding:10px 8px;">Nama</th>
                    <th style="padding:10px 8px;">Email</th>
                    <th style="padding:10px 8px;">Sekolah / Kota</th>
                    <th style="padding:10px 8px;">Kelas</th>
                    <th style="padding:10px 8px;">Tanggal Daftar</th>
                    <th style="padding:10px 8px;text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:12px 8px;font-weight:700;">{{ $student->name }}</td>
                    <td style="padding:12px 8px;color:var(--muted);">{{ $student->email }}</td>
                    <td style="padding:12px 8px;">
                        <div>{{ $student->school ?: '-' }}</div>
                        @if($student->city)<div style="font-size:11px;color:var(--muted);margin-top:2px;">{{ $student->city }}</div>@endif
                    </td>
                    <td style="padding:12px 8px;">{{ $student->grade?->name ?? $student->grade ?? '-' }}</td>
                    <td style="padding:12px 8px;color:var(--muted);">{{ $student->created_at?->format('d M Y H:i') }}</td>
                    <td style="padding:12px 8px;text-align:right;">
                        <a href="{{ route('admin.users.show', $student) }}" class="btn btn-outline btn-sm"><i class="fas fa-eye"></i> Profil</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="padding:45px 10px;text-align:center;color:var(--muted);">Belum ada siswa yang menggunakan kode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($students->hasPages())
        <div style="margin-top:16px;">{{ $students->links() }}</div>
    @endif
</div>
@endsection
