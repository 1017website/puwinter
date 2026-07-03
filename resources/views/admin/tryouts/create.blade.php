{{-- ============================================================ --}}
{{-- FILE: resources/views/admin/tryouts/create.blade.php        --}}
{{-- ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'Buat Tryout')

@section('content')
<div style="max-width:680px;">
    <div class="page-header">
        <div>
            <h2>Buat Tryout Baru</h2>
            <p>Isi detail tryout, lalu tambahkan soal setelahnya.</p>
        </div>
        <a href="{{ route('admin.tryouts.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('admin.tryouts.store') }}">
            @csrf

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div class="form-group" style="grid-column:span 2;">
                    <label>Judul Tryout <span style="color:red;">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" placeholder="Tryout UTBK 2024 Gelombang 1">
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Mata Pelajaran</label>
                    <select name="subject_id" class="form-control">
                        <option value="">Semua Mata Pelajaran</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Kelas / Tingkat</label>
                    <select name="grade" class="form-control">
                        <option value="">Semua Kelas</option>
                        @foreach(['10','11','12'] as $g)
                        <option value="{{ $g }}" {{ old('grade') == $g ? 'selected' : '' }}>Kelas {{ $g }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Durasi (menit) <span style="color:red;">*</span></label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 90) }}" class="form-control" min="1">
                </div>

                <div class="form-group">
                    <label>Seri / Gelombang</label>
                    <input type="text" name="series" value="{{ old('series') }}" class="form-control" placeholder="Gelombang 1, Program A, dll">
                </div>

                <div class="form-group">
                    <label>Program <span style="color:red;">*</span></label>
                    <select name="plan_id" class="form-control">
                        <option value="">— Pilih Program —</option>
                        @foreach($plans as $pl)
                        <option value="{{ $pl->id }}" {{ old('plan_id') == $pl->id ? 'selected' : '' }}>{{ $pl->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Akses Untuk <span style="color:red;">*</span></label>
                    <select name="access_tier" class="form-control">
                        <option value="both" {{ old('access_tier','both') == 'both' ? 'selected' : '' }}>Semua peserta program</option>
                        <option value="free" {{ old('access_tier') == 'free' ? 'selected' : '' }}>Hanya gratis</option>
                        <option value="paid" {{ old('access_tier') == 'paid' ? 'selected' : '' }}>Hanya BERBAYAR</option>
                    </select>
                    <input type="hidden" name="is_premium" value="0">
                </div>

                <div class="form-group" style="grid-column:span 2;">
                    <label>Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi singkat tryout...">{{ old('description') }}</textarea>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:8px;">
                <a href="{{ route('admin.tryouts.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan & Tambah Soal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
