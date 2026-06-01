@extends('admin.layouts.app')
@section('title', 'Edit Kelas — '.$course->title)

@section('content')

<div style="max-width:680px;">
    <div class="page-header">
        <div>
            <h2>Edit Kelas</h2>
            <p>{{ $course->title }}</p>
        </div>
        <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data" x-data="{ ctype: '{{ old('course_type', $course->course_type) }}', get typeHint(){ return this.ctype==='extra' ? 'Bebas diakses semua siswa tanpa langganan premium.' : 'Mengikuti kelas siswa dan flag premium di bawah.'; } }">
            @csrf @method('PUT')

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">

                <div class="form-group" style="grid-column:span 2;">
                    <label>Judul Kelas <span style="color:red;">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $course->title) }}"
                           class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" required>
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Mata Pelajaran <span style="color:red;">*</span></label>
                    <select name="subject_id" class="form-control">
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ old('subject_id', $course->subject_id) == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Tipe Kelas <span style="color:red;">*</span></label>
                    <select name="course_type" class="form-control" x-model="ctype">
                        <option value="regular" {{ old('course_type', $course->course_type) == 'regular' ? 'selected' : '' }}>Reguler (ikut kelas siswa)</option>
                        <option value="extra"   {{ old('course_type', $course->course_type) == 'extra'   ? 'selected' : '' }}>Extra (mis. TOEFL) — bebas, tanpa premium</option>
                    </select>
                    <small style="color:#94a3b8; font-size:11px;" x-text="typeHint"></small>
                </div>

                <div class="form-group" x-show="ctype !== 'extra'">
                    <label>Kelas / Tingkat</label>
                    <select name="grade_id" class="form-control">
                        <option value="">Semua Kelas</option>
                        @foreach($grades as $g)
                        <option value="{{ $g->id }}" {{ old('grade_id', $course->grade_id) == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                        @endforeach
                    </select>
                    <small style="color:#94a3b8; font-size:11px;">Kosongkan = tampil untuk semua kelas siswa.</small>
                </div>

                <div class="form-group">
                    <label>Mentor <span style="color:red;">*</span></label>
                    <select name="mentor_id" class="form-control">
                        @foreach($mentors as $mentor)
                        <option value="{{ $mentor->id }}" {{ old('mentor_id', $course->mentor_id) == $mentor->id ? 'selected' : '' }}>
                            {{ $mentor->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="grid-column:span 2;">
                    <label>Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $course->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label>Thumbnail Baru</label>
                    @if($course->thumbnail)
                    <div style="margin-bottom:8px;">
                        <img src="{{ asset('storage/'.$course->thumbnail) }}" style="height:60px; border-radius:6px; border:1px solid var(--border);">
                    </div>
                    @endif
                    <input type="file" name="thumbnail" class="form-control" accept="image/*">
                </div>

                <div class="form-group">
                    <label>Pengaturan</label>
                    <div style="display:flex; flex-direction:column; gap:10px; padding-top:8px;">
                        <label style="display:flex; align-items:center; gap:8px; font-size:13.5px; cursor:pointer; font-weight:400;">
                            <input type="checkbox" name="is_premium" value="1" {{ old('is_premium', $course->is_premium) ? 'checked' : '' }}
                                   :disabled="ctype === 'extra'"
                                   x-bind:checked="ctype === 'extra' ? false : $el.checked"
                                   style="width:16px; height:16px; accent-color:var(--primary);">
                            Konten Premium
                            <span x-show="ctype === 'extra'" style="font-size:11px; color:#94a3b8;" x-text="'(extra: non-premium)'"></span>
                        </label>
                        <label style="display:flex; align-items:center; gap:8px; font-size:13.5px; cursor:pointer; font-weight:400;">
                            <input type="checkbox" name="is_published" value="1" {{ old('is_published', $course->is_published) ? 'checked' : '' }}
                                   style="width:16px; height:16px; accent-color:var(--primary);">
                            Publikasikan Sekarang
                        </label>
                    </div>
                </div>

            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:8px;">
                <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
