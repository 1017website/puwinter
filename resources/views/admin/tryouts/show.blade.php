@extends('admin.layouts.app')
@section('title', 'Kelola Soal — '.$tryout->title)

@section('content')

<div class="page-header">
    <div>
        <h2>{{ $tryout->title }}</h2>
        <p>{{ $tryout->total_questions }} soal · {{ $tryout->duration_minutes }} menit ·
            <span style="color:{{ $tryout->is_published ? 'var(--success)' : 'var(--muted)' }}; font-weight:600;">
                {{ $tryout->is_published ? 'Dipublikasikan' : 'Draft' }}
            </span>
        </p>
    </div>
    <div style="display:flex; gap:8px;">
        <form method="POST" action="{{ route('admin.tryouts.toggle-publish', $tryout) }}">
            @csrf @method('PATCH')
            <button type="submit" class="btn {{ $tryout->is_published ? 'btn-outline' : 'btn-primary' }}">
                <i class="fas {{ $tryout->is_published ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                {{ $tryout->is_published ? 'Sembunyikan' : 'Publikasikan' }}
            </button>
        </form>
        <a href="{{ route('admin.tryouts.edit', $tryout) }}" class="btn btn-outline">
            <i class="fas fa-pen"></i> Edit
        </a>
        <a href="{{ route('admin.tryouts.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 400px; gap:20px; align-items:start;">

    {{-- Daftar soal --}}
    <div>
        <div class="card" style="margin-bottom:16px;">
            <div style="font-size:14px; font-weight:700; margin-bottom:14px;">
                Daftar Soal ({{ $tryout->questions->count() }})
            </div>

            @forelse($tryout->questions as $index => $question)
            <div style="border:1px solid var(--border); border-radius:10px; margin-bottom:10px; overflow:hidden;">
                <div style="padding:12px 16px; background:var(--bg); display:flex; align-items:center; justify-content:space-between;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <span style="width:28px; height:28px; border-radius:6px; background:var(--primary); color:#fff; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; flex-shrink:0;">
                            {{ $index + 1 }}
                        </span>
                        <div>
                            <span class="badge {{ match($question->difficulty) { 'mudah'=>'badge-success','sedang'=>'badge-warning',default=>'badge-danger' } }}">
                                {{ ucfirst($question->difficulty) }}
                            </span>
                            <span style="font-size:11px; color:var(--muted); margin-left:8px;">{{ $question->subject->name ?? '' }}</span>
                        </div>
                    </div>
                    <div style="display:flex; gap:6px;">
                        <span style="font-size:12px; color:var(--muted);">Jawaban: <strong style="color:var(--success);">{{ strtoupper($question->correct_answer) }}</strong></span>
                        <form method="POST" action="{{ route('admin.tryouts.questions.destroy', $question) }}" onsubmit="return confirm('Hapus soal ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
                <div style="padding:12px 16px; font-size:13px; color:var(--text); line-height:1.6;">
                    {{ Str::limit($question->question_text, 120) }}
                </div>
            </div>
            @empty
            <div style="text-align:center; padding:40px; color:var(--muted);">
                <i class="fas fa-bullseye" style="font-size:32px; opacity:0.2; display:block; margin-bottom:10px;"></i>
                Belum ada soal. Tambahkan soal di sebelah kanan.
            </div>
            @endforelse
        </div>
    </div>

    {{-- Form tambah soal --}}
    <div style="position:sticky; top:80px;">
        <div class="card">
            <div style="font-size:14px; font-weight:700; margin-bottom:16px;">Tambah Soal Baru</div>
            <form method="POST" action="{{ route('admin.tryouts.questions.store', $tryout) }}">
                @csrf

                <div class="form-group">
                    <label>Mata Pelajaran <span style="color:red;">*</span></label>
                    <select name="subject_id" class="form-control">
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ old('subject_id', $tryout->subject_id) == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Teks Soal <span style="color:red;">*</span></label>
                    <textarea name="question_text" class="form-control {{ $errors->has('question_text') ? 'is-invalid' : '' }}" rows="4"
                              placeholder="Tulis soal di sini...">{{ old('question_text') }}</textarea>
                    @error('question_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                @foreach(['a'=>'A','b'=>'B','c'=>'C','d'=>'D','e'=>'E (opsional)'] as $key => $label)
                <div class="form-group">
                    <label>Opsi {{ $label }}</label>
                    <input type="text" name="option_{{ $key }}" value="{{ old('option_'.$key) }}"
                           class="form-control" {{ $key !== 'e' ? 'required' : '' }}
                           placeholder="Jawaban {{ $label }}...">
                </div>
                @endforeach

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label>Kunci Jawaban <span style="color:red;">*</span></label>
                        <select name="correct_answer" class="form-control">
                            @foreach(['a','b','c','d','e'] as $opt)
                            <option value="{{ $opt }}" {{ old('correct_answer') === $opt ? 'selected' : '' }}>
                                {{ strtoupper($opt) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tingkat Kesulitan</label>
                        <select name="difficulty" class="form-control">
                            <option value="mudah" {{ old('difficulty') === 'mudah' ? 'selected' : '' }}>Mudah</option>
                            <option value="sedang" {{ old('difficulty','sedang') === 'sedang' ? 'selected' : '' }}>Sedang</option>
                            <option value="sulit" {{ old('difficulty') === 'sulit' ? 'selected' : '' }}>Sulit</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Pembahasan</label>
                    <textarea name="explanation" class="form-control" rows="3" placeholder="Penjelasan jawaban...">{{ old('explanation') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                    <i class="fas fa-plus"></i> Tambah Soal
                </button>
            </form>
        </div>
    </div>

</div>

@endsection
