@extends('admin.layouts.app')
@section('title', 'Master Kelas')

@section('content')

<div class="page-header">
    <div>
        <h2>Master Kelas</h2>
        <p>Kelola daftar kelas/tingkat. Tidak lagi hardcode 10/11/12 — bisa ditambah sesuai kebutuhan.</p>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 340px; gap:20px; align-items:flex-start;">

    {{-- Daftar Kelas --}}
    <div>
        <div class="card" style="padding:0; overflow:hidden;">
            <table style="width:100%; border-collapse:collapse; font-size:13px;">
                <thead>
                    <tr style="background:var(--bg); border-bottom:1px solid var(--border);">
                        <th style="padding:12px 16px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--muted);">Kelas</th>
                        <th style="padding:12px 16px; text-align:center; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--muted);">Siswa</th>
                        <th style="padding:12px 16px; text-align:center; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--muted);">Kelas/Tryout</th>
                        <th style="padding:12px 16px; text-align:center; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--muted);">Status</th>
                        <th style="padding:12px 16px; text-align:right; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($grades as $grade)
                    <tr style="border-bottom:1px solid var(--border);" x-data="{ editOpen: false }">
                        <td style="padding:12px 16px;">
                            <div style="font-weight:700;">{{ $grade->name }}</div>
                            <div style="font-size:11px; color:var(--muted);">Kode: {{ $grade->code }} &middot; Urutan: {{ $grade->order }}</div>

                            {{-- Inline edit --}}
                            <div x-show="editOpen" style="margin-top:10px; padding:14px; background:var(--bg); border-radius:8px; display:none;" x-cloak>
                                <form method="POST" action="{{ route('admin.grades.update', $grade) }}">
                                    @csrf @method('PUT')
                                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;">
                                        <div>
                                            <label style="font-size:11px; font-weight:600; color:var(--muted); display:block; margin-bottom:3px;">Nama</label>
                                            <input type="text" name="name" value="{{ $grade->name }}" class="form-control" style="font-size:12.5px;" required>
                                        </div>
                                        <div>
                                            <label style="font-size:11px; font-weight:600; color:var(--muted); display:block; margin-bottom:3px;">Kode</label>
                                            <input type="text" name="code" value="{{ $grade->code }}" class="form-control" style="font-size:12.5px;">
                                        </div>
                                        <div>
                                            <label style="font-size:11px; font-weight:600; color:var(--muted); display:block; margin-bottom:3px;">Urutan</label>
                                            <input type="number" name="order" value="{{ $grade->order }}" class="form-control" style="font-size:12.5px;">
                                        </div>
                                    </div>
                                    <div style="display:flex; gap:6px;">
                                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                        <button type="button" @click="editOpen=false" class="btn btn-outline btn-sm">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </td>
                        <td style="padding:12px 16px; text-align:center; font-weight:700;">{{ $grade->users_count }}</td>
                        <td style="padding:12px 16px; text-align:center; font-weight:700;">{{ $grade->courses_count }} / {{ $grade->tryouts_count }}</td>
                        <td style="padding:12px 16px; text-align:center;">
                            <form method="POST" action="{{ route('admin.grades.toggle-active', $grade) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="badge {{ $grade->is_active ? 'badge-success' : 'badge-warning' }}"
                                        style="border:none; cursor:pointer; font-size:11px; padding:4px 10px;">
                                    {{ $grade->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </form>
                        </td>
                        <td style="padding:12px 16px; text-align:right;">
                            <div style="display:flex; gap:6px; justify-content:flex-end;">
                                <button type="button" @click="editOpen=!editOpen" class="btn btn-outline btn-sm"><i class="fas fa-pen"></i></button>
                                <form method="POST" action="{{ route('admin.grades.destroy', $grade) }}"
                                      onsubmit="return confirm('Hapus kelas {{ $grade->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding:60px; text-align:center; color:var(--muted);">
                            <i class="fas fa-layer-group" style="font-size:32px; opacity:0.2; display:block; margin-bottom:10px;"></i>
                            Belum ada kelas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Form tambah --}}
    <div style="position:sticky; top:80px;">
        <div class="card">
            <div style="font-size:14px; font-weight:700; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid var(--border);">
                Tambah Kelas
            </div>
            <form method="POST" action="{{ route('admin.grades.store') }}">
                @csrf
                <div class="form-group">
                    <label>Nama <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Kelas 10 / Alumni / Gap Year" required value="{{ old('name') }}">
                    @error('name') <div style="font-size:11px; color:var(--danger); margin-top:3px;">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Kode <span style="font-size:11px; color:var(--muted); font-weight:400;">(opsional, unik)</span></label>
                    <input type="text" name="code" class="form-control" placeholder="10 / 11 / 12 / alumni" value="{{ old('code') }}">
                    @error('code') <div style="font-size:11px; color:var(--danger); margin-top:3px;">{{ $message }}</div> @enderror
                    <div style="font-size:11px; color:var(--muted); margin-top:3px;">Kosongkan untuk auto dari nama.</div>
                </div>
                <div class="form-group">
                    <label>Urutan</label>
                    <input type="number" name="order" class="form-control" value="{{ old('order', ($grades->max('order') ?? 0) + 1) }}">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                    <i class="fas fa-plus"></i> Tambah Kelas
                </button>
            </form>
        </div>
    </div>

</div>

@endsection
