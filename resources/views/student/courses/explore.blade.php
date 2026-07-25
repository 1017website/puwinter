@extends('layouts.student')
@section('title', 'Jelajahi Kelas')
@php $subtitle = 'Temukan kelas yang sesuai dengan kebutuhanmu.'; @endphp

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
    <div>
        <h2 style="font-size:22px; font-weight:800;">Jelajahi Kelas</h2>
        <p style="font-size:13px; color:var(--text-muted); margin-top:2px;">
            {{ $courses->total() }} kelas tersedia
        </p>
    </div>
    <a href="{{ route('student.course.index') }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Kelas Saya
    </a>
</div>

{{-- Filter --}}
<form method="GET" class="student-filter-form" style="display:flex; gap:10px; margin-bottom:24px; flex-wrap:wrap; align-items:center;">
    <input type="text" name="search" value="{{ $search }}" placeholder="Cari kelas..."
           style="padding:8px 14px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; outline:none; width:220px;"
           onkeydown="if(event.key==='Enter')this.form.submit()">

    <select name="subject_id" onchange="this.form.submit()"
            style="padding:8px 12px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; background:#fff; outline:none;">
        <option value="">Semua Mapel</option>
        @foreach($subjects as $s)
            <option value="{{ $s->id }}" {{ $subjectId==$s->id ? 'selected':'' }}>{{ $s->name }}</option>
        @endforeach
    </select>

    <div class="student-filter-scroll" style="display:flex; gap:4px; background:#fff; border:1px solid var(--border); border-radius:10px; padding:4px;">
        @foreach([''=> 'Semua', 'gratis'=>'Gratis', 'premium'=>'Premium'] as $val=>$label)
        <button type="submit" name="type" value="{{ $val }}"
            style="padding:7px 14px; border-radius:7px; font-size:13px; font-weight:600; border:none; cursor:pointer; font-family:inherit;
                   {{ $type===$val ? 'background:var(--primary);color:#fff;':'background:transparent;color:var(--text-muted);' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    @if($search || $subjectId || $type)
        <a href="{{ route('student.course.explore') }}"
           style="font-size:13px; color:var(--text-muted); text-decoration:none; padding:8px 12px; border:1px solid var(--border); border-radius:8px; background:#fff;">
            <i class="fas fa-xmark"></i> Reset
        </a>
    @endif
</form>

{{-- Grid --}}
@if($courses->isEmpty())
    <div class="card" style="text-align:center; padding:60px; color:var(--text-muted);">
        <i class="fas fa-magnifying-glass" style="font-size:40px; opacity:0.2; display:block; margin-bottom:12px;"></i>
        <p style="font-size:14px; font-weight:600;">Kelas tidak ditemukan.</p>
        @if($search)
            <p style="font-size:12px; margin-top:4px;">Coba kata kunci lain.</p>
        @endif
    </div>
@else
    <div class="student-card-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(min(280px, 100%), 1fr)); gap:16px;">
        @foreach($courses as $course)
        @php
            $enrolled = $enrolledIds->contains($course->id);
            $canAccess = $course->isAccessibleBy(auth()->user());
            $needsProgramEnrollment = $course->plan_id
                && !auth()->user()->isEnrolledInProgram($course->plan_id);
            $isPaidOnly = $course->access_tier === 'paid';
            $colors   = ['#2563EB','#7C3AED','#059669','#D97706','#DC2626','#0891B2'];
            $color    = $colors[$loop->index % count($colors)];
        @endphp
        <div class="card" style="padding:0; overflow:hidden; display:flex; flex-direction:column; transition:transform 0.2s, box-shadow 0.2s;"
             onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,0.1)'"
             onmouseout="this.style.transform=''; this.style.boxShadow=''">

            {{-- Thumbnail --}}
            <div style="height:120px; background:linear-gradient(135deg, {{ $color }}, {{ $color }}cc); position:relative; overflow:hidden;">
                @if($course->thumbnail)
                    <img src="{{ asset('storage/'.$course->thumbnail) }}" alt=""
                         style="width:100%; height:100%; object-fit:cover;">
                @else
                    <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-book-open" style="font-size:36px; color:rgba(255,255,255,0.4);"></i>
                    </div>
                @endif
                <div style="position:absolute; top:10px; left:10px; display:flex; gap:6px;">
                    @if($isPaidOnly)
                        <span style="background:rgba(245,158,11,0.9); color:#fff; font-size:10px; font-weight:700; padding:2px 8px; border-radius:20px;">
                            <i class="fas fa-crown" style="font-size:9px;"></i> Premium
                        </span>
                    @else
                        <span style="background:rgba(5,150,105,0.9); color:#fff; font-size:10px; font-weight:700; padding:2px 8px; border-radius:20px;">
                            Gratis
                        </span>
                    @endif
                    @if($enrolled)
                        <span style="background:rgba(37,99,235,0.9); color:#fff; font-size:10px; font-weight:700; padding:2px 8px; border-radius:20px;">
                            <i class="fas fa-check" style="font-size:9px;"></i> Diikuti
                        </span>
                    @endif
                </div>
            </div>

            <div style="padding:14px 16px; flex:1; display:flex; flex-direction:column;">
                @if($course->subject)
                    <span class="badge badge-primary" style="font-size:10px; margin-bottom:7px; display:inline-block; width:fit-content;">
                        {{ $course->subject->name }}
                    </span>
                @endif
                <div style="font-size:14px; font-weight:700; margin-bottom:5px; line-height:1.3; flex:1;">
                    {{ $course->title }}
                </div>
                <div style="font-size:11px; color:var(--text-muted); margin-bottom:12px; display:flex; gap:12px; flex-wrap:wrap;">
                    @if($course->mentor)
                        <span><i class="fas fa-user-tie" style="margin-right:3px;"></i>{{ $course->mentor->name }}</span>
                    @endif
                    <span><i class="fas fa-users" style="margin-right:3px;"></i>{{ $course->enrollments_count }} peserta</span>
                </div>

                @if($needsProgramEnrollment)
                    <a href="{{ route('student.program.show', $course->plan_id) }}"
                       style="display:flex; align-items:center; justify-content:center; gap:6px; padding:9px; background:#F1F5F9; color:var(--text-main); border-radius:8px; font-size:12px; font-weight:700; text-decoration:none;">
                        <i class="fas fa-user-plus"></i> Daftar Program
                    </a>
                @elseif(!$canAccess && $isPaidOnly)
                    <a href="{{ route('upgrade.index') }}"
                       style="display:flex; align-items:center; justify-content:center; gap:6px; padding:9px; background:linear-gradient(135deg,#F59E0B,#EF4444); color:#fff; border-radius:8px; font-size:12px; font-weight:700; text-decoration:none;">
                        <i class="fas fa-crown"></i> Upgrade untuk Akses
                    </a>
                @else
                    <a href="{{ route('student.course.show', $course->slug) }}"
                       style="display:flex; align-items:center; justify-content:center; gap:6px; padding:9px; background:{{ $enrolled ? 'var(--primary)':'#F1F5F9' }}; color:{{ $enrolled ? '#fff':'var(--text-main)' }}; border-radius:8px; font-size:12px; font-weight:700; text-decoration:none; transition:opacity 0.15s;"
                       onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                        @if($enrolled)
                            <i class="fas fa-play"></i> Lanjutkan Belajar
                        @else
                            <i class="fas fa-plus"></i> Mulai Kelas
                        @endif
                    </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div style="margin-top:24px;">{{ $courses->appends(request()->query())->links() }}</div>
@endif

@endsection
