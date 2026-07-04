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
    .modal-overlay{position:fixed; inset:0; background:rgba(15,23,42,0.5); z-index:1000; display:flex; align-items:flex-start; justify-content:center; padding:24px 16px; overflow-y:auto;}
    .modal-box{background:#fff; border-radius:14px; width:100%; max-width:1180px; box-shadow:0 16px 50px rgba(0,0,0,0.25);}
    .modal-head{padding:18px 24px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; gap:16px;}
    .modal-title-wrap{display:flex; align-items:center; gap:10px; flex-wrap:wrap;}
    .modal-content{padding:24px;}
    .question-meta-grid{display:grid; grid-template-columns:130px 150px minmax(0,1fr); gap:14px; margin-bottom:16px;}
    .opt-grid{display:grid; grid-template-columns:1fr 1fr; gap:14px;}
    .field-label{font-size:12.5px; font-weight:600; display:block; margin-bottom:6px;}
    .field-input{width:100%; padding:10px 12px; border:1px solid var(--border); border-radius:8px; font-size:13.5px; font-family:inherit; outline:none; resize:vertical;}
    .field-input:focus{border-color:var(--primary); box-shadow:0 0 0 3px rgba(37,99,235,.08);}
    .answer-helper{font-size:12px; color:var(--muted); margin-top:4px; line-height:1.5;}
    .answer-list{display:grid; grid-template-columns:1fr; gap:10px; margin-bottom:16px;}
    .answer-item{display:grid; grid-template-columns:56px minmax(0,1fr) 150px; gap:12px; align-items:stretch; padding:12px; border:1.5px solid var(--border); border-radius:12px; background:#fff; transition:border-color .15s ease, background .15s ease, box-shadow .15s ease;}
    .answer-item.active{border-color:var(--success); background:#ECFDF5; box-shadow:0 8px 24px rgba(16,185,129,.08);}
    .answer-letter{width:44px; height:44px; border-radius:10px; background:var(--bg); color:var(--text-main); display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:800;}
    .answer-item.active .answer-letter{background:var(--success); color:#fff;}
    .answer-key-btn{height:100%; min-height:44px; border:1px solid var(--border); border-radius:10px; background:#fff; display:flex; align-items:center; justify-content:center; gap:8px; padding:10px 12px; cursor:pointer; font-size:12.5px; font-weight:700; color:var(--text-main); user-select:none;}
    .answer-key-btn.active{border-color:var(--success); color:var(--success); background:#F0FDF4;}
    .answer-key-btn input{cursor:pointer; margin:0;}
    .answer-textarea{min-height:52px; height:52px;}
    @media(max-width:900px){
        .modal-box{max-width:96vw;}
        .question-meta-grid{grid-template-columns:1fr;}
        .opt-grid{grid-template-columns:1fr;}
        .answer-item{grid-template-columns:48px minmax(0,1fr);}
        .answer-key-btn{grid-column:1 / -1; justify-content:flex-start; height:auto;}
    }
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
        <a href="{{ route('admin.tryout-results.index', ['tryout_id' => $tryout->id]) }}" class="btn btn-outline">
            <i class="fas fa-chart-column"></i> Hasil Siswa
        </a>
        @if($tryout->isIrt())
            <form method="POST" action="{{ route('admin.tryouts.calibrate-irt', $tryout) }}" onsubmit="return confirm('Kalibrasi ulang IRT untuk tryout ini? Skor IRT dan ranking peserta akan diperbarui.')">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-outline">
                    <i class="fas fa-scale-balanced"></i> {{ $tryout->irt_calibrated ? 'Kalibrasi Ulang IRT' : 'Kalibrasi IRT' }}
                </button>
            </form>
        @endif
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
                        <span class="badge badge-gray">Bobot: {{ rtrim(rtrim(number_format($question->scoreWeight(), 2, ',', '.'), '0'), ',') }}</span>
                        <span style="font-size:11.5px; color:var(--muted);">{{ $question->subject->name ?? '' }}</span>
                        @if($question->isMultiple())
                            <span class="badge" style="background:#EEF2FF; color:#4F46E5;">Multiple</span>
                            <span style="font-size:11.5px; color:var(--muted);">· Kunci: <strong style="color:var(--success);">{{ strtoupper(implode(', ', $question->correctKeys())) }}</strong></span>
                        @else
                            <span style="font-size:11.5px; color:var(--muted);">· Kunci: <strong style="color:var(--success);">{{ strtoupper($question->correct_answer) }}</strong></span>
                        @endif
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

            @php $qKeys = $question->correctKeys(); @endphp
            @foreach($opts as $key => $val)
                @if(!is_null($val) && $val !== '')
                <div class="opt-row {{ in_array($key, $qKeys) ? 'correct' : '' }}">
                    <span class="opt-key">{{ strtoupper($key) }}</span>
                    <span style="white-space:pre-wrap; flex:1;">{{ $val }}</span>
                    @if(in_array($key, $qKeys))
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
                <div class="modal-title-wrap">
                    <div style="font-size:16px; font-weight:700;">Tambah Soal Baru</div>
                    <span style="font-size:12px; color:var(--muted); background:var(--bg); border:1px solid var(--border); border-radius:999px; padding:5px 10px;">
                        No. {{ old('order', $tryout->questions->max('order') + 1) }}
                    </span>
                </div>
                <button @click="showAdd=false" style="background:none; border:none; font-size:18px; cursor:pointer; color:var(--muted);">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.tryouts.questions.store', $tryout) }}"
                      x-data="{
                        qtype: '{{ old('question_type','single') }}',
                        correct: '{{ old('correct_answer','a') }}',
                        multi: {{ collect((array) old('correct_answers', []))->map(fn($k)=>"'".$k."'")->implode(',') ? '['.collect((array) old('correct_answers', []))->map(fn($k)=>"'".$k."'")->implode(',').']' : '[]' }},
                        toggleMulti(k){ this.multi.includes(k) ? this.multi=this.multi.filter(x=>x!==k) : this.multi.push(k) },
                        isKey(k){ return this.qtype==='multiple' ? this.multi.includes(k) : this.correct===k }
                      }">
                    @csrf

                    <div class="question-meta-grid">
                        <div>
                            <label class="field-label">Nomor Soal <span style="color:red;">*</span></label>
                            <input type="number" name="order" min="1" class="field-input"
                                   value="{{ old('order', $tryout->questions->max('order') + 1) }}"
                                   placeholder="No.">
                            <div class="answer-helper">Urutan tampil soal.</div>
                        </div>

                        <div>
                            <label class="field-label">Bobot Nilai <span style="color:red;">*</span></label>
                            <input type="number" name="score_weight" min="0.01" step="0.01" class="field-input"
                                   value="{{ old('score_weight', 1) }}"
                                   placeholder="1">
                            <div class="answer-helper">Nilai penuh soal ini.</div>
                        </div>
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
                    </div>

                    <div style="margin-bottom:16px;">
                        <label class="field-label">Tipe Soal <span style="color:red;">*</span></label>
                        <select name="question_type" x-model="qtype" class="field-input">
                            <option value="single">Pilihan Ganda (1 jawaban)</option>
                            <option value="multiple">Multiple Jawaban (beberapa benar)</option>
                        </select>
                        <div x-show="qtype==='multiple'" style="font-size:11.5px; color:var(--muted); margin-top:6px;">
                            <i class="fas fa-info-circle"></i> Pilih minimal 2 kunci. Penilaian partial credit: nilai proporsional dari bobot soal sesuai jumlah kunci benar yang dipilih, tanpa penalti opsi salah.
                        </div>
                    </div>

                    <div style="margin-bottom:16px;">
                        <label class="field-label">Teks Soal <span style="color:red;">*</span></label>
                        <textarea name="question_text" rows="4" class="field-input {{ $errors->has('question_text') ? 'is-invalid' : '' }}"
                                  placeholder="Tulis soal di sini... (boleh panjang, beberapa paragraf)">{{ old('question_text') }}</textarea>
                        @error('question_text') <div style="color:var(--danger); font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
                    </div>

                    {{-- Opsi jawaban: input jawaban dan tombol kunci dipisah agar lebih mudah diisi --}}
                    <label class="field-label">Opsi Jawaban</label>
                    <div class="answer-helper" style="margin-bottom:10px;">
                        <span x-show="qtype==='single'">Isi jawaban di kolom tengah, lalu pilih tombol <strong>Kunci</strong> pada jawaban yang benar.</span>
                        <span x-show="qtype==='multiple'" x-cloak>Isi jawaban di kolom tengah, lalu centang semua tombol <strong>Kunci</strong> yang benar. Minimal 2 kunci.</span>
                    </div>
                    <div class="answer-list">
                        @foreach(['a'=>'A','b'=>'B','c'=>'C','d'=>'D','e'=>'E (opsional)'] as $key => $label)
                        <div class="answer-item" :class="isKey('{{ $key }}') ? 'active' : ''">
                            <div class="answer-letter">{{ strtoupper($key[0]) }}</div>
                            <textarea name="option_{{ $key }}" rows="2" class="field-input answer-textarea"
                                      {{ $key !== 'e' ? 'required' : '' }}
                                      placeholder="Tulis jawaban {{ $label }}...">{{ old('option_'.$key) }}</textarea>
                            <label class="answer-key-btn" :class="isKey('{{ $key }}') ? 'active' : ''">
                                {{-- single --}}
                                <input type="radio" name="correct_answer" value="{{ $key }}" x-model="correct"
                                       x-show="qtype==='single'"
                                       {{ old('correct_answer','a') === $key ? 'checked' : '' }}>
                                {{-- multiple --}}
                                <input type="checkbox" name="correct_answers[]" value="{{ $key }}"
                                       x-show="qtype==='multiple'" x-cloak
                                       :checked="multi.includes('{{ $key }}')"
                                       @change="toggleMulti('{{ $key }}')">
                                <span x-show="!isKey('{{ $key }}')">Jadikan Kunci</span>
                                <span x-show="isKey('{{ $key }}')"><i class="fas fa-check"></i> Kunci</span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @error('correct_answers') <div style="color:var(--danger); font-size:12px; margin-top:-8px; margin-bottom:12px;">{{ $message }}</div> @enderror

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
