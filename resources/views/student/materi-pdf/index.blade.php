@extends('layouts.student')
@section('title', 'Materi PDF')
@php $subtitle = 'Kumpulan materi PDF untuk belajar kapan saja.'; @endphp

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
    <div>
        <h2 style="font-size:22px; font-weight:800;">Materi PDF</h2>
        <p style="font-size:13px; color:var(--text-muted); margin-top:2px;">Kumpulan materi PDF untuk belajar kapan saja.</p>
    </div>
</div>

{{-- Filter --}}
<form method="GET" class="student-filter-form" style="display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap; align-items:center;">
    <div class="student-filter-scroll" style="display:flex; gap:4px; background:#fff; border:1px solid var(--border); border-radius:10px; padding:4px;">
        @foreach(['semua'=>'Semua','disimpan'=>'Disimpan'] as $val=>$label)
        <button type="submit" name="filter" value="{{ $val }}"
            style="padding:7px 14px; border-radius:7px; font-size:13px; font-weight:600; border:none; cursor:pointer; font-family:inherit;
                   {{ $filter===$val ? 'background:var(--primary);color:#fff;':'background:transparent;color:var(--text-muted);' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>
    <select name="subject_id" onchange="this.form.submit()" style="padding:8px 12px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; background:#fff; outline:none;">
        <option value="">Semua Mapel</option>
        @foreach($subjects as $s)
            <option value="{{ $s->id }}" {{ $subjectId==$s->id ? 'selected':'' }}>{{ $s->name }}</option>
        @endforeach
    </select>
</form>

@if($materials->isEmpty())
    <div class="card" style="text-align:center; padding:60px; color:var(--text-muted);">
        <i class="fas fa-file-pdf" style="font-size:40px; color:#DC2626; opacity:0.2; display:block; margin-bottom:12px;"></i>
        <p style="font-size:14px; font-weight:600;">Belum ada materi PDF tersedia.</p>
        @if(!auth()->user()->isPremium())
            <p style="font-size:12px; margin-top:6px;">Upgrade Premium untuk akses lebih banyak materi.</p>
            <a href="{{ route('upgrade.index') }}" class="btn btn-premium" style="margin-top:16px; display:inline-flex;">
                <i class="fas fa-crown"></i> Upgrade Premium
            </a>
        @endif
    </div>
@else
    <div class="student-card-grid student-grid-3" style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px;">
        @foreach($materials as $material)
        @php
            $saved   = $savedIds->contains($material->id);
            $subject = $material->module->course->subject ?? null;
            $course  = $material->module->course ?? null;
            $locked  = $material->is_premium && !auth()->user()->isPremium();
        @endphp
        <div class="card" style="padding:0; overflow:hidden; {{ $locked ? 'opacity:0.75;':'' }}">
            {{-- Header merah PDF --}}
            <div style="background:linear-gradient(135deg,#991B1B,#DC2626); padding:20px; position:relative; overflow:hidden;">
                <div style="position:absolute; top:-15px; right:-15px; width:80px; height:80px; background:rgba(255,255,255,0.08); border-radius:50%;"></div>
                <i class="fas fa-file-pdf" style="font-size:36px; color:rgba(255,255,255,0.9);"></i>
                @if($material->is_premium)
                    <div style="position:absolute; top:10px; right:10px;">
                        <span style="background:rgba(245,158,11,0.9); color:#fff; font-size:10px; font-weight:700; padding:2px 7px; border-radius:20px;">
                            <i class="fas fa-crown" style="font-size:9px;"></i> Premium
                        </span>
                    </div>
                @endif
            </div>
            <div style="padding:14px 16px;">
                @if($subject)
                    <span class="badge badge-primary" style="font-size:10px; margin-bottom:6px; display:inline-block;">{{ $subject->name }}</span>
                @endif
                <div style="font-size:13.5px; font-weight:700; margin-bottom:4px; line-height:1.3;">{{ $material->title }}</div>
                <div style="font-size:11px; color:var(--text-muted); margin-bottom:12px;">
                    {{ $course ? Str::limit($course->title, 35) : '-' }}
                </div>
                <div style="display:flex; gap:8px;">
                    @if($locked)
                        <a href="{{ route('upgrade.index') }}" class="btn btn-premium" style="flex:1; justify-content:center; font-size:12px; padding:8px;">
                            <i class="fas fa-crown"></i> Upgrade
                        </a>
                    @elseif($material->content_url)
                        <a href="{{ $material->content_url }}" target="_blank" class="btn btn-primary" style="flex:1; justify-content:center; font-size:12px; padding:8px;">
                            <i class="fas fa-eye"></i> Buka PDF
                        </a>
                    @else
                        <div class="btn btn-outline" style="flex:1; justify-content:center; font-size:12px; padding:8px; opacity:0.5; cursor:default;">
                            Belum tersedia
                        </div>
                    @endif
                    <form method="POST" action="{{ route('student.pdf.toggle-save', $material->id) }}">
                        @csrf
                        <button type="submit" title="{{ $saved ? 'Hapus':'Simpan' }}"
                                style="width:38px; height:38px; border-radius:8px; border:1px solid var(--border); background:{{ $saved ? '#FFFBEB':'#fff' }}; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-bookmark" style="font-size:14px; color:{{ $saved ? '#F59E0B':'var(--text-muted)' }};"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div style="margin-top:20px;">{{ $materials->appends(request()->query())->links() }}</div>
@endif

@endsection
