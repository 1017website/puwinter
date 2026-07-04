@extends('admin.layouts.app')
@section('title', 'Edit Tryout')

@section('content')

<div style="max-width:680px;">
    <div class="page-header">
        <div>
            <h2>Edit Tryout</h2>
            <p>{{ $tryout->title }}</p>
        </div>
        <a href="{{ route('admin.tryouts.show', $tryout) }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Kembali ke Soal
        </a>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('admin.tryouts.update', $tryout) }}">
            @csrf @method('PUT')

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">

                <div class="form-group" style="grid-column:span 2;">
                    <label>Judul Tryout <span style="color:red;">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $tryout->title) }}"
                           class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" required>
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Mata Pelajaran</label>
                    <select name="subject_id" class="form-control">
                        <option value="">Semua Mata Pelajaran</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ old('subject_id', $tryout->subject_id) == $subject->id ? 'selected' : '' }}>
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
                        <option value="{{ $g }}" {{ old('grade', $tryout->grade) == $g ? 'selected' : '' }}>Kelas {{ $g }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Durasi (menit) <span style="color:red;">*</span></label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $tryout->duration_minutes) }}"
                           class="form-control" min="1" required>
                </div>

                <div class="form-group">
                    <label>Seri / Gelombang</label>
                    <input type="text" name="series" value="{{ old('series', $tryout->series) }}"
                           class="form-control" placeholder="Gelombang 1, Paket A, dll">
                </div>

                <div class="form-group">
                    <label>Pengaturan</label>
                    <div style="display:flex; flex-direction:column; gap:10px; padding-top:8px;">
                        <label style="display:flex; align-items:center; gap:8px; font-size:13.5px; cursor:pointer; font-weight:400;">
                            <input type="checkbox" name="is_premium" value="1" {{ old('is_premium', $tryout->is_premium) ? 'checked' : '' }}
                                   style="width:16px; height:16px; accent-color:var(--primary);">
                            Konten Premium
                        </label>
                        <label style="display:flex; align-items:center; gap:8px; font-size:13.5px; cursor:pointer; font-weight:400;">
                            <input type="checkbox" name="is_published" value="1" {{ old('is_published', $tryout->is_published) ? 'checked' : '' }}
                                   style="width:16px; height:16px; accent-color:var(--primary);">
                            Publikasikan
                        </label>
                    </div>
                </div>

                <div class="form-group" style="grid-column:span 2;">
                    <label>Mode Penilaian <span style="color:red;">*</span></label>
                    <select name="scoring_mode" class="form-control">
                        <option value="regular" {{ old('scoring_mode', $tryout->scoring_mode ?? 'regular') == 'regular' ? 'selected' : '' }}>Regular (benar +4 / salah -1)</option>
                        <option value="irt" {{ old('scoring_mode', $tryout->scoring_mode ?? '') == 'irt' ? 'selected' : '' }}>IRT (bobot kesulitan, ala UTBK)</option>
                    </select>
                    <small style="color:var(--text-muted); font-size:12px;">
                        Mengubah mode akan mereset status kalibrasi.
                        @if($tryout->irt_calibrated ?? false) <span style="color:#059669;">Sudah dikalibrasi.</span> @endif
                    </small>
                </div>

                <div class="form-group" style="grid-column:span 2;">
                    <label>Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $tryout->description) }}</textarea>
                </div>

            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:8px;">
                <a href="{{ route('admin.tryouts.show', $tryout) }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
