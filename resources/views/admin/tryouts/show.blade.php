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
    .passage-card{border:1px solid #BFDBFE; background:#EFF6FF; border-radius:12px; padding:14px; margin-bottom:12px;}
    .passage-title{font-size:13px; font-weight:800; color:#1D4ED8; margin-bottom:8px; display:flex; align-items:center; gap:8px;}
    .passage-text{font-size:13.5px; line-height:1.7; color:var(--text-main); white-space:pre-wrap;}
    .stimulus-image{max-width:100%; height:auto; border:1px solid var(--border); border-radius:10px; display:block; margin:10px 0; background:#fff;}
    .passage-list{display:grid; grid-template-columns:1fr; gap:12px;}
    .passage-admin-card{border:1px solid var(--border); border-radius:12px; padding:14px; background:#fff;}
    .passage-admin-head{display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:10px;}
    .muted-small{font-size:12px; color:var(--muted); line-height:1.5;}
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
    .matrix-editor{border:1px solid var(--border); border-radius:12px; overflow:hidden; margin-bottom:16px;}
    .matrix-row{display:grid; grid-template-columns:minmax(0,1fr) 160px 160px; gap:0; border-top:1px solid var(--border); align-items:stretch;}
    .matrix-row:first-child{border-top:none;}
    .matrix-head{background:#EFF6FF; color:#1D4ED8; font-weight:800; font-size:12px;}
    .matrix-cell{padding:10px 12px; border-left:1px solid var(--border); display:flex; align-items:center; gap:8px;}
    .matrix-cell:first-child{border-left:none;}
    .matrix-radio-label{justify-content:center; cursor:pointer; font-size:12.5px; font-weight:700;}
    .matrix-preview{width:100%; border-collapse:collapse; margin-bottom:12px; font-size:13px;}
    .matrix-preview th{background:#DBEAFE; color:#1E3A8A; padding:10px; border:1px solid #93C5FD; text-align:center;}
    .matrix-preview td{padding:10px; border:1px solid var(--border); vertical-align:middle;}
    .matrix-preview td:not(:first-child){text-align:center;}
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

<div x-data="{ showAdd: {{ $errors->any() ? 'true' : 'false' }}, showPassage:false }">

<div class="page-header">
    <div>
        <h2>{{ $tryout->title }}</h2>
        <p>{{ $tryout->questions->count() }} soal · {{ $tryout->duration_minutes }} menit · {{ $tryout->gradeLabel() }}
            @if($tryout->plan)
                · {{ $tryout->plan->name }}
            @endif
            · <span style="color:{{ $tryout->is_published ? 'var(--success)' : 'var(--muted)' }}; font-weight:600;">
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
        <button @click="showPassage=true" class="btn btn-outline">
            <i class="fas fa-book-open"></i> Tambah Soal Cerita
        </button>
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

{{-- Soal cerita / stimulus --}}
<div class="card" style="margin-bottom:16px;">
    <div style="font-size:15px; font-weight:700; margin-bottom:12px; display:flex; align-items:center; justify-content:space-between; gap:12px;">
        <span>Soal Cerita / Stimulus ({{ $tryout->passages->count() }})</span>
        <button @click="showPassage=true" class="btn btn-outline btn-sm"><i class="fas fa-plus"></i> Tambah Cerita</button>
    </div>
    <div class="muted-small" style="margin-bottom:14px;">
        Gunakan bagian ini untuk teks panjang, cerita, tabel, atau infografik seperti contoh PDF TKA: satu cerita/stimulus dapat dipakai oleh banyak soal.
    </div>

    @if($tryout->passages->count())
        <div class="passage-list">
            @foreach($tryout->passages as $passage)
                <div class="passage-admin-card" x-data="{ edit:false }">
                    <div class="passage-admin-head">
                        <div style="min-width:0;">
                            <div style="font-weight:800; font-size:14px; color:var(--text-main);">
                                {{ $passage->order }}. {{ $passage->title ?: 'Tanpa Judul' }}
                            </div>
                            <div class="muted-small">
                                Dipakai oleh {{ $passage->questions->count() }} soal
                                @if($passage->source) · Sumber: {{ $passage->source }} @endif
                            </div>
                        </div>
                        <div style="display:flex; gap:8px; align-items:center; flex-shrink:0;">
                            <button type="button" class="btn btn-outline btn-sm" @click="edit=!edit">
                                <i class="fas fa-pen"></i> <span x-text="edit ? 'Tutup' : 'Edit'"></span>
                            </button>
                            <form method="POST" action="{{ route('admin.tryouts.passages.destroy', $passage) }}" onsubmit="return confirm('Hapus soal cerita ini? Soal yang memakai cerita ini tidak ikut terhapus, hanya dilepas dari cerita.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>

                    <div x-show="!edit">
                        @if($passage->passage_image)
                            <img src="{{ asset($passage->passage_image) }}" alt="Gambar stimulus {{ $passage->title }}" class="stimulus-image" style="max-height:260px; object-fit:contain;">
                        @endif
                        @if($passage->passage_text)
                            <div class="passage-text">{{ Str::limit($passage->passage_text, 420) }}</div>
                        @endif
                    </div>

                    <div x-show="edit" x-cloak style="margin-top:14px; border-top:1px dashed var(--border); padding-top:14px;">
                        <form method="POST" action="{{ route('admin.tryouts.passages.update', $passage) }}" enctype="multipart/form-data">
                            @csrf @method('PUT')

                            <div style="display:grid; grid-template-columns:120px minmax(0,1fr); gap:14px; margin-bottom:14px;">
                                <div>
                                    <label class="field-label">Urutan <span style="color:red;">*</span></label>
                                    <input type="number" name="order" min="1" class="field-input" value="{{ $passage->order }}">
                                </div>
                                <div>
                                    <label class="field-label">Judul Cerita / Stimulus</label>
                                    <input type="text" name="title" class="field-input" value="{{ $passage->title }}" placeholder="Judul cerita/stimulus">
                                </div>
                            </div>

                            <div style="margin-bottom:14px;">
                                <label class="field-label">Teks Cerita / Stimulus</label>
                                <textarea name="passage_text" rows="7" class="field-input" placeholder="Tulis cerita panjang, bacaan, tabel dalam teks, atau stimulus...">{{ $passage->passage_text }}</textarea>
                                <div class="answer-helper">Boleh dikosongkan jika stimulus masih memakai gambar.</div>
                            </div>

                            <div style="display:grid; grid-template-columns:minmax(0,1fr) minmax(0,1fr); gap:14px; margin-bottom:14px;">
                                <div>
                                    <label class="field-label">Gambar Stimulus / Infografik</label>
                                    @if($passage->passage_image)
                                        <img src="{{ asset($passage->passage_image) }}" alt="Gambar stimulus saat ini" class="stimulus-image" style="max-height:160px; object-fit:contain; margin-bottom:8px;">
                                        <label style="font-size:12.5px; color:var(--muted); display:flex; gap:8px; align-items:center; margin-bottom:8px;">
                                            <input type="checkbox" name="remove_image" value="1">
                                            Hapus gambar saat ini
                                        </label>
                                    @endif
                                    <input type="file" name="passage_image" class="field-input" accept="image/jpeg,image/png,image/webp">
                                    <div class="answer-helper">Upload gambar baru jika ingin mengganti. Format jpg, png, webp maksimal 4 MB.</div>
                                </div>
                                <div>
                                    <label class="field-label">Sumber (opsional)</label>
                                    <input type="text" name="source" class="field-input" value="{{ $passage->source }}" placeholder="URL/sumber adaptasi jika ada">
                                </div>
                            </div>

                            <div style="display:flex; gap:10px; justify-content:flex-end; flex-wrap:wrap;">
                                <button type="button" @click="edit=false" class="btn btn-outline">Batal</button>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align:center; padding:28px; color:var(--muted); border:1px dashed var(--border); border-radius:12px; background:var(--bg);">
            Belum ada soal cerita/stimulus. Klik <strong>Tambah Soal Cerita</strong> untuk membuat teks atau upload gambar stimulus.
        </div>
    @endif
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
                        @if($question->passage)
                            <span class="badge" style="background:#DBEAFE; color:#1D4ED8;"><i class="fas fa-book-open"></i> {{ Str::limit($question->passage->title ?: 'Soal Cerita', 28) }}</span>
                        @endif
                        @if($question->question_image)
                            <span class="badge" style="background:#F0FDF4; color:#047857;"><i class="fas fa-image"></i> Gambar</span>
                        @endif
                        @if($question->isMatrix())
                            <span class="badge" style="background:#FEF3C7; color:#92400E;">Kategori</span>
                            <span style="font-size:11.5px; color:var(--muted);">· {{ count($question->options()) }} baris kategori</span>
                        @elseif($question->isMultiple())
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
            @if($question->passage)
                <div class="passage-card" style="margin-top:14px;">
                    <div class="passage-title"><i class="fas fa-book-open"></i> {{ $question->passage->title ?: 'Soal Cerita / Stimulus' }}</div>
                    @if($question->passage->passage_image)
                        <img src="{{ asset($question->passage->passage_image) }}" alt="Gambar stimulus" class="stimulus-image" style="max-height:320px; object-fit:contain;">
                    @endif
                    @if($question->passage->passage_text)
                        <div class="passage-text">{{ $question->passage->passage_text }}</div>
                    @endif
                    @if($question->passage->source)
                        <div class="muted-small" style="margin-top:8px;">Sumber: {{ $question->passage->source }}</div>
                    @endif
                </div>
            @endif
            @if($question->question_image)
                <img src="{{ asset($question->question_image) }}" alt="Gambar soal" class="stimulus-image" style="max-height:320px; object-fit:contain;">
            @endif
            <div style="font-size:14px; line-height:1.7; color:var(--text-main); margin:14px 0 16px; white-space:pre-wrap;">{{ $question->question_text }}</div>

            @if($question->isMatrix())
                @php
                    $columns = $question->matrixColumns();
                    $matrixKeys = $question->matrixCorrectAnswers();
                @endphp
                <table class="matrix-preview">
                    <thead>
                        <tr>
                            <th style="text-align:left; width:55%;">Pernyataan / Teknik</th>
                            @foreach($columns as $columnLabel)
                                <th>{{ $columnLabel }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($question->options() as $rowKey => $rowText)
                            <tr>
                                <td style="white-space:pre-wrap;">{{ $rowText }}</td>
                                @foreach($columns as $columnKey => $columnLabel)
                                    <td>
                                        @if(($matrixKeys[$rowKey] ?? null) === $columnKey)
                                            <i class="fas fa-check-circle" style="color:var(--success);"></i>
                                        @else
                                            <span style="color:var(--muted);">—</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
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
            @endif

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

{{-- ===================== MODAL TAMBAH SOAL CERITA / STIMULUS ===================== --}}
<template x-teleport="body">
    <div class="modal-overlay" x-show="showPassage" x-cloak @keydown.escape.window="showPassage=false">
        <div class="modal-box" style="max-width:900px;" @click.outside="showPassage=false">
            <div class="modal-head">
                <div class="modal-title-wrap">
                    <div style="font-size:16px; font-weight:700;">Tambah Soal Cerita / Stimulus</div>
                    <span style="font-size:12px; color:var(--muted); background:var(--bg); border:1px solid var(--border); border-radius:999px; padding:5px 10px;">
                        No. {{ old('passage_order', $tryout->passages->max('order') + 1) }}
                    </span>
                </div>
                <button @click="showPassage=false" style="background:none; border:none; font-size:18px; cursor:pointer; color:var(--muted);">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.tryouts.passages.store', $tryout) }}" enctype="multipart/form-data">
                    @csrf
                    <div style="display:grid; grid-template-columns:120px minmax(0,1fr); gap:14px; margin-bottom:16px;">
                        <div>
                            <label class="field-label">Urutan <span style="color:red;">*</span></label>
                            <input type="number" name="order" min="1" class="field-input" value="{{ old('passage_order', $tryout->passages->max('order') + 1) }}">
                        </div>
                        <div>
                            <label class="field-label">Judul Cerita / Stimulus</label>
                            <input type="text" name="title" class="field-input" value="{{ old('title') }}" placeholder="Contoh: The Lion and the Mouse / Effective Study Technique">
                        </div>
                    </div>

                    <div style="margin-bottom:16px;">
                        <label class="field-label">Teks Cerita / Stimulus</label>
                        <textarea name="passage_text" rows="8" class="field-input" placeholder="Tulis cerita panjang, bacaan, tabel dalam teks, atau stimulus yang akan dipakai beberapa soal...">{{ old('passage_text') }}</textarea>
                        <div class="answer-helper">Boleh dikosongkan jika stimulus hanya berupa gambar/infografik.</div>
                    </div>

                    <div style="display:grid; grid-template-columns:minmax(0,1fr) minmax(0,1fr); gap:14px; margin-bottom:20px;">
                        <div>
                            <label class="field-label">Gambar Stimulus / Infografik</label>
                            <input type="file" name="passage_image" class="field-input" accept="image/jpeg,image/png,image/webp">
                            <div class="answer-helper">Format jpg, png, webp. Maksimal 4 MB.</div>
                        </div>
                        <div>
                            <label class="field-label">Sumber (opsional)</label>
                            <input type="text" name="source" class="field-input" value="{{ old('source') }}" placeholder="URL/sumber adaptasi jika ada">
                        </div>
                    </div>

                    <div style="display:flex; gap:10px; justify-content:flex-end;">
                        <button type="button" @click="showPassage=false" class="btn btn-outline">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Soal Cerita</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

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
                <form method="POST" action="{{ route('admin.tryouts.questions.store', $tryout) }}" enctype="multipart/form-data"
                      @submit="if (!validateQuestionForm($event)) $event.preventDefault()"
                      x-data="{
                        qtype: '{{ old('question_type','single') }}',
                        correct: '{{ old('correct_answer','a') }}',
                        multi: {{ \Illuminate\Support\Js::from(array_values((array) old('correct_answers', []))) }},
                        matrixCol1: {{ \Illuminate\Support\Js::from(old('matrix_columns.col_1', 'Time Management')) }},
                        matrixCol2: {{ \Illuminate\Support\Js::from(old('matrix_columns.col_2', 'Self Management')) }},
                        matrixRows: {
                            a: {{ \Illuminate\Support\Js::from(old('option_a', '')) }},
                            b: {{ \Illuminate\Support\Js::from(old('option_b', '')) }},
                            c: {{ \Illuminate\Support\Js::from(old('option_c', '')) }},
                            d: {{ \Illuminate\Support\Js::from(old('option_d', '')) }},
                            e: {{ \Illuminate\Support\Js::from(old('option_e', '')) }}
                        },
                        toggleMulti(k){ this.multi.includes(k) ? this.multi=this.multi.filter(x=>x!==k) : this.multi.push(k) },
                        isKey(k){ return this.qtype==='multiple' ? this.multi.includes(k) : (this.qtype==='single' ? this.correct===k : false) },
                        optionFilled(form, key){ return ((form.querySelector('[name=option_'+key+']')?.value || '').trim().length > 0) },
                        validateQuestionForm(event){
                            const form = event.target;
                            if (this.qtype === 'single') {
                                if (!this.optionFilled(form, this.correct)) {
                                    alert('Kunci jawaban yang dipilih harus memiliki teks opsi jawaban.');
                                    return false;
                                }
                            }
                            if (this.qtype === 'multiple') {
                                const validKeys = this.multi.filter(k => this.optionFilled(form, k));
                                if (validKeys.length < 2) {
                                    alert('Soal multiple jawaban minimal punya 2 kunci jawaban yang opsinya sudah diisi.');
                                    return false;
                                }
                            }
                            if (this.qtype === 'matrix') {
                                const filledRows = Object.entries(this.matrixRows).filter(([k, v]) => (v || '').trim().length > 0).map(([k]) => k);
                                if (filledRows.length < 2) {
                                    alert('Soal kategori minimal punya 2 baris/pernyataan. Isi minimal baris A dan B.');
                                    return false;
                                }
                                const missingKeys = filledRows.filter(k => !form.querySelector(`input[name='correct_matrix_answers[${k}]']:checked`));
                                if (missingKeys.length) {
                                    alert('Kunci kategori wajib dipilih untuk baris: ' + missingKeys.map(k => k.toUpperCase()).join(', '));
                                    return false;
                                }
                            }
                            return true;
                        }
                      }">
                    @csrf

                    @if($errors->any())
                        <div style="border:1px solid #FCA5A5; background:#FEF2F2; color:#991B1B; border-radius:10px; padding:12px 14px; margin-bottom:16px; font-size:13px; line-height:1.55;">
                            <div style="font-weight:800; margin-bottom:6px;"><i class="fas fa-triangle-exclamation"></i> Periksa input soal terlebih dahulu:</div>
                            <ul style="margin:0; padding-left:18px;">
                                @foreach($errors->all() as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

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
                        <label class="field-label">Soal Cerita / Stimulus</label>
                        <select name="passage_id" class="field-input">
                            <option value="">Tidak memakai soal cerita</option>
                            @foreach($tryout->passages as $passage)
                                <option value="{{ $passage->id }}" {{ old('passage_id') == $passage->id ? 'selected' : '' }}>
                                    {{ $passage->order }}. {{ $passage->title ?: Str::limit($passage->passage_text, 60) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="answer-helper">Pilih jika soal ini menggunakan bacaan/gambar yang sama dengan soal lain.</div>
                    </div>

                    <div style="margin-bottom:16px;">
                        <label class="field-label">Tipe Soal <span style="color:red;">*</span></label>
                        <select name="question_type" x-model="qtype" class="field-input">
                            <option value="single">Pilihan Ganda (1 jawaban)</option>
                            <option value="multiple">Multiple Jawaban (beberapa benar)</option>
                            <option value="matrix">Pilihan Ganda Kompleks - Kategori</option>
                        </select>
                        <div x-show="qtype==='multiple'" style="font-size:11.5px; color:var(--muted); margin-top:6px;">
                            <i class="fas fa-info-circle"></i> Pilih minimal 2 kunci. Penilaian partial credit: nilai proporsional dari bobot soal sesuai jumlah kunci benar yang dipilih, tanpa penalti opsi salah.
                        </div>
                        <div x-show="qtype==='matrix'" x-cloak style="font-size:11.5px; color:var(--muted); margin-top:6px;">
                            <i class="fas fa-table-list"></i> Cocok untuk soal tabel kategori seperti Time Management / Self Management. Nilai partial dihitung per baris yang benar.
                        </div>
                    </div>

                    <div style="margin-bottom:16px;">
                        <label class="field-label">Teks Soal <span style="color:red;">*</span></label>
                        <textarea name="question_text" rows="4" class="field-input {{ $errors->has('question_text') ? 'is-invalid' : '' }}"
                                  placeholder="Tulis soal di sini... (boleh panjang, beberapa paragraf)">{{ old('question_text') }}</textarea>
                        @error('question_text') <div style="color:var(--danger); font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
                    </div>

                    <div style="margin-bottom:16px;">
                        <label class="field-label">Gambar Soal (opsional)</label>
                        <input type="file" name="question_image" class="field-input" accept="image/jpeg,image/png,image/webp">
                        <div class="answer-helper">Gunakan untuk soal yang punya gambar/tabel khusus. Format jpg, png, webp maksimal 4 MB.</div>
                    </div>

                    {{-- Opsi jawaban biasa --}}
                    <div x-show="qtype!=='matrix'">
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
                                          :disabled="qtype==='matrix'"
                                          :required="qtype !== 'matrix' && '{{ $key }}' !== 'e'"
                                          placeholder="Tulis jawaban {{ $label }}...">{{ old('option_'.$key) }}</textarea>
                                <label class="answer-key-btn" :class="isKey('{{ $key }}') ? 'active' : ''">
                                    {{-- single --}}
                                    <input type="radio" name="correct_answer" value="{{ $key }}" x-model="correct"
                                           x-show="qtype==='single'"
                                           :disabled="qtype!=='single'"
                                           {{ old('correct_answer','a') === $key ? 'checked' : '' }}>
                                    {{-- multiple --}}
                                    <input type="checkbox" name="correct_answers[]" value="{{ $key }}"
                                           x-show="qtype==='multiple'" x-cloak
                                           :disabled="qtype!=='multiple'"
                                           :checked="multi.includes('{{ $key }}')"
                                           @change="toggleMulti('{{ $key }}')">
                                    <span x-show="!isKey('{{ $key }}')">Jadikan Kunci</span>
                                    <span x-show="isKey('{{ $key }}')"><i class="fas fa-check"></i> Kunci</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @error('correct_answers') <div style="color:var(--danger); font-size:12px; margin-top:-8px; margin-bottom:12px;">{{ $message }}</div> @enderror
                    </div>

                    {{-- Opsi kategori / matrix --}}
                    <div x-show="qtype==='matrix'" x-cloak>
                        <label class="field-label">Kolom Kategori</label>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
                            <input type="text" name="matrix_columns[col_1]" class="field-input" :disabled="qtype!=='matrix'" :required="qtype==='matrix'"
                                   x-model="matrixCol1" placeholder="Contoh: Time Management">
                            <input type="text" name="matrix_columns[col_2]" class="field-input" :disabled="qtype!=='matrix'" :required="qtype==='matrix'"
                                   x-model="matrixCol2" placeholder="Contoh: Self Management">
                        </div>
                        <div class="answer-helper" style="margin-bottom:10px;">
                            Isi pernyataan/baris di kolom kiri, lalu pilih kategori yang menjadi kunci pada tiap baris. Minimal baris A dan B wajib diisi.
                        </div>
                        <div class="matrix-editor">
                            <div class="matrix-row matrix-head">
                                <div class="matrix-cell">Pernyataan / Teknik</div>
                                <div class="matrix-cell" style="justify-content:center;">
                                    <span x-text="matrixCol1 || 'Kategori 1'"></span>
                                </div>
                                <div class="matrix-cell" style="justify-content:center;">
                                    <span x-text="matrixCol2 || 'Kategori 2'"></span>
                                </div>
                            </div>
                            @foreach(['a'=>'A','b'=>'B','c'=>'C (opsional)','d'=>'D (opsional)','e'=>'E (opsional)'] as $key => $label)
                                <div class="matrix-row">
                                    <div class="matrix-cell">
                                        <span class="answer-letter" style="width:34px;height:34px;font-size:12px;">{{ strtoupper($key) }}</span>
                                        <textarea name="option_{{ $key }}" rows="2" class="field-input answer-textarea"
                                                  :disabled="qtype!=='matrix'"
                                                  :required="qtype==='matrix' && ['a','b'].includes('{{ $key }}')"
                                                  x-model="matrixRows['{{ $key }}']"
                                                  placeholder="Tulis baris {{ $label }}..."></textarea>
                                    </div>
                                    <label class="matrix-cell matrix-radio-label">
                                        <input type="radio" name="correct_matrix_answers[{{ $key }}]" value="col_1"
                                               :disabled="qtype!=='matrix'"
                                               :required="qtype==='matrix' && (matrixRows['{{ $key }}'] || '').trim().length > 0"
                                               {{ old('correct_matrix_answers.'.$key) === 'col_1' ? 'checked' : '' }}>
                                        Kunci
                                    </label>
                                    <label class="matrix-cell matrix-radio-label">
                                        <input type="radio" name="correct_matrix_answers[{{ $key }}]" value="col_2"
                                               :disabled="qtype!=='matrix'"
                                               :required="qtype==='matrix' && (matrixRows['{{ $key }}'] || '').trim().length > 0"
                                               {{ old('correct_matrix_answers.'.$key) === 'col_2' ? 'checked' : '' }}>
                                        Kunci
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('correct_matrix_answers') <div style="color:var(--danger); font-size:12px; margin-top:-8px; margin-bottom:12px;">{{ $message }}</div> @enderror
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
