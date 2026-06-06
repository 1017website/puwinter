@extends('admin.layouts.app')
@section('title', 'Kelola Soal — '.$tryout->title)

@push('styles')
<style>
    [x-cloak]{display:none !important;}
    .q-card{border:1px solid var(--border); border-radius:12px; margin-bottom:12px; overflow:hidden; background:#fff;}
    .q-head{padding:14px 18px; display:flex; align-items:flex-start; justify-content:space-between; gap:14px; cursor:pointer;}
    .q-head:hover{background:var(--bg);}
    .q-num{width:30px; height:30px; border-radius:8px; background:var(--primary); color:#fff; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; flex-shrink:0;}
    .q-body{padding:0 18px 18px 18px; border-top:1px solid var(--border);}
    .opt-row{display:flex; gap:10px; align-items:flex-start; padding:10px 12px; border:1px solid var(--border); border-radius:8px; margin-bottom:8px; font-size:13.5px; line-height:1.55;}
    .opt-row.correct{background:#ECFDF5; border-color:#A7F3D0;}
    .opt-key{width:24px; height:24px; border-radius:6px; background:var(--bg); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; flex-shrink:0;}
    .opt-row.correct .opt-key{background:var(--success); color:#fff;}
    /* Modal */
    .modal-overlay{position:fixed; inset:0; background:rgba(15,23,42,0.5); z-index:1000; display:flex; align-items:flex-start; justify-content:center; padding:32px 16px; overflow-y:auto;}
    .modal-box{background:#fff; border-radius:14px; width:100%; max-width:820px; box-shadow:0 16px 50px rgba(0,0,0,0.25);}
    .modal-head{padding:18px 24px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between;}
    .modal-content{padding:24px;}
    .opt-grid{display:grid; grid-template-columns:1fr 1fr; gap:14px;}
    @media(max-width:680px){ .opt-grid{grid-template-columns:1fr;} }
    .field-label{font-size:12.5px; font-weight:600; display:block; margin-bottom:6px;}
    .field-input{width:100%; padding:10px 12px; border:1px solid var(--border); border-radius:8px; font-size:13.5px; font-family:inherit; outline:none; resize:vertical;}
    .field-input:focus{border-color:var(--primary);}
    .key-pick{display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border:1.5px solid var(--border); border-radius:8px; cursor:pointer; font-size:13px; font-weight:600;}
    .key-pick.active{background:var(--primary); color:#fff; border-color:var(--primary);}
</style>
@endpush

@section('content')

<div x-data="{ showAdd: {{ $errors->any() ? 'true' : 'false' }} }">

<div class="page-header">
    <div>
        <h2>{{ $tryout->title }}</h2>
        <p>{{ $tryout->questions->count() }} soal · {{ $tryout->duration_minutes }} menit ·
            <span style="color:{{ $tryout->is_published ? 'var(--success)' : 'var(--muted)' }}; font-weight:600;">
                {{ $tryout->is_published ? 'Dipublikasikan' : 'Draft' }}
            </span>
        </p>
    </div>
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
        <button @click="showAdd=true" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Soal
        </button>
        <form method="POST" action="{{ route('admin.tryouts.toggle-publish', $tryout) }}">
            @csrf @method('PATCH')
            <button type="submit" class="btn {{ $tryout->is_published ? 'btn-outline' : 'btn-outline' }}">
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

{{-- Daftar soal full-width --}}
<div class="card">
    <div style="font-size:15px; font-weight:700; margin-bottom:16px; display:flex; align-items:center; justify-content:space-between;">
        <span>Daftar Soal ({{ $tryout->questions->count() }})</span>
    </div>

    @forelse($tryout->questions as $index => $question)
    @php $opts = ['a'=>$question->option_a,'b'=>$question->option_b,'c'=>$question->option_c,'d'=>$question->option_d,'e'=>$question->option_e]; @endphp
    <div class="q-card" x-data="{ open:false }">
        <div class="q-head" @click="open=!open">
            <div style="display:flex; gap:12px; flex:1; min-width:0;">
                <span class="q-num">{{ $index + 1 }}</span>
                <div style="flex:1; min-width:0;">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px; flex-wrap:wrap;">
                        <span class="badge {{ match($question->difficulty) { 'mudah'=>'badge-success','sedang'=>'badge-warning',default=>'badge-danger' } }}">
                            {{ ucfirst($question->difficulty) }}
                        </span>
                        <span style="font-size:11.5px; color:var(--muted);">{{ $question->subject->name ?? '' }}</span>
                        <span style="font-size:11.5px; color:var(--muted);">· Kunci: <strong style="color:var(--success);">{{ strtoupper($question->correct_answer) }}</strong></span>
                    </div>
                    <div style="font-size:13.5px; line-height:1.6; color:var(--text-main);"
                         x-show="!open">{{ Str::limit($question->question_text, 140) }}</div>
                </div>
            </div>
            <div style="display:flex; gap:8px; align-items:center; flex-shrink:0;">
                <i class="fas fa-chevron-down" style="font-size:12px; color:var(--muted); transition:transform .2s;" :style="open ? 'transform:rotate(180deg)' : ''"></i>
                <form method="POST" action="{{ route('admin.tryouts.questions.destroy', $question) }}" onsubmit="return confirm('Hapus soal ini?')" @click.stop>
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </div>

        <div class="q-body" x-show="open" x-cloak>
            <div style="font-size:14px; line-height:1.7; color:var(--text-main); margin:14px 0 16px; white-space:pre-wrap;">{{ $question->question_text }}</div>

            @foreach($opts as $key => $val)
                @if(!is_null($val) && $val !== '')
                <div class="opt-row {{ $question->correct_answer === $key ? 'correct' : '' }}">
                    <span class="opt-key">{{ strtoupper($key) }}</span>
                    <span style="white-space:pre-wrap; flex:1;">{{ $val }}</span>
                    @if($question->correct_answer === $key)
                        <i class="fas fa-check-circle" style="color:var(--success); flex-shrink:0; margin-top:2px;"></i>
                    @endif
                </div>
                @endif
            @endforeach

            @if($question->explanation)
            <div style="margin-top:14px; background:#EFF6FF; border:1px solid #BFDBFE; border-radius:8px; padding:12px 14px;">
                <div style="font-size:12px; font-weight:700; color:var(--primary); margin-bottom:4px;"><i class="fas fa-lightbulb"></i> Pembahasan</div>
                <div style="font-size:13px; line-height:1.6; color:var(--text-main); white-space:pre-wrap;">{{ $question->explanation }}</div>
            </div>
            @endif
        </div>
    </div>
    @empty
    <div style="text-align:center; padding:48px; color:var(--muted);">
        <i class="fas fa-bullseye" style="font-size:36px; opacity:0.2; display:block; margin-bottom:12px;"></i>
        Belum ada soal. Klik <strong>Tambah Soal</strong> di kanan atas.
    </div>
    @endforelse
</div>

{{-- ===================== MODAL TAMBAH SOAL ===================== --}}
<template x-teleport="body">
    <div class="modal-overlay" x-show="showAdd" x-cloak @keydown.escape.window="showAdd=false">
        <div class="modal-box" @click.outside="showAdd=false">
            <div class="modal-head">
                <div style="font-size:16px; font-weight:700;">Tambah Soal Baru</div>
                <button @click="showAdd=false" style="background:none; border:none; font-size:18px; cursor:pointer; color:var(--muted);">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.tryouts.questions.store', $tryout) }}"
                      x-data="{ correct: '{{ old('correct_answer','a') }}' }">
                    @csrf

                    <div class="opt-grid" style="margin-bottom:16px;">
                        <div>
                            <label class="field-label">Mata Pelajaran <span style="color:red;">*</span></label>
                            <select name="subject_id" class="field-input">
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id', $tryout->subject_id) == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="field-label">Tingkat Kesulitan</label>
                            <select name="difficulty" class="field-input">
                                <option value="mudah" {{ old('difficulty') === 'mudah' ? 'selected' : '' }}>Mudah</option>
                                <option value="sedang" {{ old('difficulty','sedang') === 'sedang' ? 'selected' : '' }}>Sedang</option>
                                <option value="sulit" {{ old('difficulty') === 'sulit' ? 'selected' : '' }}>Sulit</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom:16px;">
                        <label class="field-label">Teks Soal <span style="color:red;">*</span></label>
                        <textarea name="question_text" rows="4" class="field-input {{ $errors->has('question_text') ? 'is-invalid' : '' }}"
                                  placeholder="Tulis soal di sini... (boleh panjang, beberapa paragraf)">{{ old('question_text') }}</textarea>
                        @error('question_text') <div style="color:var(--danger); font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
                    </div>

                    {{-- Opsi jawaban: textarea agar muat jawaban panjang, klik kartu untuk set kunci --}}
                    <label class="field-label">Opsi Jawaban — klik radio di kiri untuk menandai kunci</label>
                    <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:16px;">
                        @foreach(['a'=>'A','b'=>'B','c'=>'C','d'=>'D','e'=>'E (opsional)'] as $key => $label)
                        <div style="display:flex; gap:10px; align-items:flex-start; padding:10px; border:1.5px solid var(--border); border-radius:8px;"
                             :style="correct==='{{ $key }}' ? 'border-color:var(--success); background:#ECFDF5;' : ''">
                            <label style="display:flex; flex-direction:column; align-items:center; gap:4px; cursor:pointer; padding-top:2px;">
                                <input type="radio" name="correct_answer" value="{{ $key }}" x-model="correct"
                                       {{ old('correct_answer','a') === $key ? 'checked' : '' }} style="cursor:pointer;">
                                <span style="font-size:12px; font-weight:700;">{{ strtoupper($key[0]) }}</span>
                            </label>
                            <textarea name="option_{{ $key }}" rows="1" class="field-input"
                                      {{ $key !== 'e' ? 'required' : '' }}
                                      placeholder="Jawaban {{ $label }}..."
                                      style="min-height:42px;">{{ old('option_'.$key) }}</textarea>
                        </div>
                        @endforeach
                    </div>

                    <div style="margin-bottom:20px;">
                        <label class="field-label">Pembahasan (opsional)</label>
                        <textarea name="explanation" rows="3" class="field-input" placeholder="Penjelasan jawaban...">{{ old('explanation') }}</textarea>
                    </div>

                    <div style="display:flex; gap:10px; justify-content:flex-end;">
                        <button type="button" @click="showAdd=false" class="btn btn-outline">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Soal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

</div>

@endsection
