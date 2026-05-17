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
                    <label>Durasi (menit) <span style="color:red;">*</span></label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 90) }}" class="form-control" min="1">
                </div>

                <div class="form-group">
                    <label>Seri / Gelombang</label>
                    <input type="text" name="series" value="{{ old('series') }}" class="form-control" placeholder="Gelombang 1, Paket A, dll">
                </div>

                <div class="form-group">
                    <label>Tipe</label>
                    <div style="display:flex; gap:16px; padding-top:8px;">
                        <label style="display:flex; align-items:center; gap:8px; font-size:13.5px; cursor:pointer; font-weight:400;">
                            <input type="checkbox" name="is_premium" value="1" {{ old('is_premium') ? 'checked' : '' }}
                                   style="width:16px; height:16px; accent-color:var(--primary);">
                            Konten Premium
                        </label>
                    </div>
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
