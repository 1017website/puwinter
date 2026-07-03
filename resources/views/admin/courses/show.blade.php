@extends('admin.layouts.app')
@section('title', 'Kelola Kelas — '.$course->title)

@section('content')

<div class="page-header">
    <div>
        <h2>{{ $course->title }}</h2>
        <p>{{ $course->subject->name ?? '' }} · {{ $course->mentor->name ?? '' }} ·
            <span style="color:{{ $course->is_published ? 'var(--success)' : 'var(--muted)' }}; font-weight:600;">
                {{ $course->is_published ? 'Dipublikasikan' : 'Draft' }}
            </span>
        </p>
    </div>
    <div style="display:flex; gap:8px;">
        <form method="POST" action="{{ route('admin.courses.toggle-publish', $course) }}">
            @csrf @method('PATCH')
            <button type="submit" class="btn {{ $course->is_published ? 'btn-outline' : 'btn-primary' }}">
                <i class="fas {{ $course->is_published ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                {{ $course->is_published ? 'Sembunyikan' : 'Publikasikan' }}
            </button>
        </form>
        <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-outline">
            <i class="fas fa-pen"></i> Edit
        </a>
        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

{{-- Stats --}}
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:24px;">
    @foreach([
        ['label'=>'Total Modul',  'value'=>$course->modules->count()],
        ['label'=>'Total Materi', 'value'=>$course->modules->sum(fn($m)=>$m->materials->count())],
        ['label'=>'Total Peserta','value'=>number_format($course->enrollments->count())],
        ['label'=>'Tipe',         'value'=>$course->is_premium ? 'Premium' : 'Gratis'],
    ] as $s)
    <div class="card" style="padding:14px; text-align:center;">
        <div style="font-size:20px; font-weight:800;">{{ $s['value'] }}</div>
        <div style="font-size:12px; color:var(--muted); margin-top:3px;">{{ $s['label'] }}</div>
    </div>
    @endforeach
</div>

<div style="display:grid; grid-template-columns:1fr 340px; gap:20px; align-items:start;">

    {{-- LEFT: Daftar Modul + Materi --}}
    <div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <div style="font-size:15px; font-weight:700;">Modul & Materi</div>
        </div>

        @forelse($course->modules as $module)
        <div class="card" style="margin-bottom:14px; padding:0; overflow:hidden;">
            {{-- Module header --}}
            <div style="padding:12px 16px; background:var(--bg); display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid var(--border);">
                <div style="display:flex; align-items:center; gap:10px;">
                    <i class="fas fa-folder" style="color:var(--primary);"></i>
                    <span style="font-size:14px; font-weight:700;">{{ $module->title }}</span>
                    <span style="font-size:11px; color:var(--muted);">{{ $module->materials->count() }} materi</span>
                </div>
                <form method="POST" action="{{ route('admin.courses.modules.destroy', $module) }}" onsubmit="return confirm('Hapus modul dan semua materinya?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                </form>
            </div>

            {{-- Materials --}}
            @foreach($module->materials as $material)
            <div style="padding:10px 16px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid var(--border);">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="width:28px; height:28px; border-radius:6px; background:{{ ['video'=>'#EFF6FF','pdf'=>'#FEF2F2','quiz'=>'#FFFBEB','live_class'=>'#ECFDF5'][$material->type] ?? '#F1F5F9' }}; display:flex; align-items:center; justify-content:center;">
                        <i class="fas {{ ['video'=>'fa-play-circle','pdf'=>'fa-file-pdf','quiz'=>'fa-question-circle','live_class'=>'fa-video'][$material->type] ?? 'fa-file' }}"
                           style="font-size:12px; color:{{ ['video'=>'#2563EB','pdf'=>'#EF4444','quiz'=>'#F59E0B','live_class'=>'#10B981'][$material->type] ?? '#64748B' }};"></i>
                    </div>
                    <div>
                        <div style="font-size:13px; font-weight:600;">{{ $material->title }}</div>
                        <div style="font-size:11px; color:var(--muted);">
                            {{ strtoupper($material->type) }}
                            @if($material->duration_minutes) · {{ $material->duration_minutes }} mnt @endif
                            @if($material->is_premium) · <span style="color:#D97706;">Premium</span> @endif
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.courses.materials.destroy', $material) }}" onsubmit="return confirm('Hapus materi ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                </form>
            </div>
            @endforeach

            {{-- Tambah materi ke modul ini --}}
            <div style="padding:10px 16px;" x-data="{ open:false }">
                <button @click="open=!open" style="font-size:12px; color:var(--primary); font-weight:600; background:transparent; border:none; cursor:pointer; display:flex; align-items:center; gap:6px; font-family:inherit;">
                    <i class="fas fa-plus"></i> Tambah Materi
                </button>
                <div x-show="open" style="margin-top:10px; padding:14px; background:var(--bg); border-radius:8px;">
                    <form method="POST" action="{{ route('admin.courses.materials.store', $module) }}">
                        @csrf
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                            <div class="form-group" style="grid-column:span 2; margin-bottom:8px;">
                                <input type="text" name="title" class="form-control" placeholder="Judul materi..." required style="font-size:12.5px;">
                            </div>
                            <div class="form-group" style="margin-bottom:8px;">
                                <select name="type" class="form-control" style="font-size:12.5px;">
                                    <option value="video">Video</option>
                                    <option value="pdf">PDF</option>
                                    <option value="quiz">Quiz</option>
                                    <option value="live_class">Kelas Online</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom:8px;">
                                <input type="number" name="duration_minutes" class="form-control" placeholder="Durasi (menit)" style="font-size:12.5px;">
                            </div>
                            <div class="form-group" style="grid-column:span 2; margin-bottom:8px;">
                                <input type="url" name="content_url" class="form-control" placeholder="URL konten (https://...)" style="font-size:12.5px;">
                            </div>
                            <div class="form-group" style="grid-column:span 2; margin-bottom:8px;">
                                <select name="access_tier" class="form-control" style="font-size:12.5px;">
                                    <option value="both">Akses: Semua peserta (gratis & berbayar)</option>
                                    <option value="free">Akses: Hanya gratis</option>
                                    <option value="paid">Akses: Hanya BERBAYAR</option>
                                </select>
                            </div>
                            <div style="grid-column:span 2; display:flex; align-items:center; justify-content:flex-end;">
                                <input type="hidden" name="is_premium" value="0">
                                <button type="submit" class="btn btn-primary btn-sm">Simpan Materi</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="card" style="text-align:center; padding:40px; color:var(--muted);">
            <i class="fas fa-folder-open" style="font-size:32px; opacity:0.2; display:block; margin-bottom:10px;"></i>
            Belum ada modul. Tambahkan modul di sebelah kanan.
        </div>
        @endforelse
    </div>

    {{-- RIGHT: Form tambah modul --}}
    <div style="position:sticky; top:80px;">
        <div class="card">
            <div style="font-size:14px; font-weight:700; margin-bottom:14px;">Tambah Modul Baru</div>
            <form method="POST" action="{{ route('admin.courses.modules.store', $course) }}">
                @csrf
                <div class="form-group">
                    <label>Nama Modul</label>
                    <input type="text" name="title" class="form-control" placeholder="Bab 1: Pengantar..." required>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                    <i class="fas fa-plus"></i> Tambah Modul
                </button>
            </form>
        </div>
    </div>

</div>

@endsection
