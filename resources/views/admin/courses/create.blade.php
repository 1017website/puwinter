@extends('admin.layouts.app')
@section('title', 'Buat Kelas Baru')

@section('content')

<div style="max-width:680px;">
    <div class="page-header">
        <div>
            <h2>Buat Kelas Baru</h2>
            <p>Isi detail kelas, lalu tambahkan modul dan materi.</p>
        </div>
        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
            @csrf

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">

                <div class="form-group" style="grid-column:span 2;">
                    <label>Judul Kelas <span style="color:red;">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}"
                           placeholder="Matematika TPS — Persiapan UTBK 2024">
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Mata Pelajaran <span style="color:red;">*</span></label>
                    <select name="subject_id" class="form-control {{ $errors->has('subject_id') ? 'is-invalid' : '' }}">
                        <option value="">Pilih mata pelajaran</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('subject_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Mentor <span style="color:red;">*</span></label>
                    <select name="mentor_id" class="form-control {{ $errors->has('mentor_id') ? 'is-invalid' : '' }}">
                        <option value="">Pilih mentor</option>
                        @foreach($mentors as $mentor)
                        <option value="{{ $mentor->id }}" {{ old('mentor_id') == $mentor->id ? 'selected' : '' }}>
                            {{ $mentor->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('mentor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group" style="grid-column:span 2;">
                    <label>Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3"
                              placeholder="Deskripsi singkat kelas...">{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Thumbnail</label>
                    <input type="file" name="thumbnail" class="form-control" accept="image/*">
                    <div style="font-size:11.5px; color:var(--muted); margin-top:4px;">Maks. 2MB. Format: JPG, PNG.</div>
                </div>

                <div class="form-group">
                    <label>Tipe Konten</label>
                    <div style="display:flex; flex-direction:column; gap:10px; padding-top:8px;">
                        <label style="display:flex; align-items:center; gap:8px; font-size:13.5px; cursor:pointer; font-weight:400;">
                            <input type="checkbox" name="is_premium" value="1" {{ old('is_premium') ? 'checked' : '' }}
                                   style="width:16px; height:16px; accent-color:var(--primary);">
                            Konten Premium
                        </label>
                    </div>
                </div>

            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:8px;">
                <a href="{{ route('admin.courses.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan & Tambah Modul
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
