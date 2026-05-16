@props([
    'enrollment',
])

@php
    $course   = $enrollment->course;
    $progress = $enrollment->progress_percentage ?? 0;
    $colors   = ['#2563EB','#7C3AED','#059669','#D97706','#DC2626','#0891B2'];
    $color    = $colors[$course->subject_id % count($colors)] ?? '#2563EB';
@endphp

<div style="background: {{ $color }}; border-radius: 12px; padding: 18px; color: #fff; position: relative; overflow: hidden; min-height: 160px; display: flex; flex-direction: column; justify-content: space-between;">
    {{-- Decorative circle --}}
    <div style="position:absolute; top:-20px; right:-20px; width:100px; height:100px; background:rgba(255,255,255,0.1); border-radius:50%;"></div>
    <div style="position:absolute; bottom:-30px; right:20px; width:70px; height:70px; background:rgba(255,255,255,0.08); border-radius:50%;"></div>

    <div>
        <span style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:1px; background:rgba(255,255,255,0.2); padding:3px 8px; border-radius:20px;">TPS</span>
        <h3 style="font-size:15px; font-weight:800; margin-top:8px; line-height:1.3;">{{ $course->title }}</h3>
    </div>

    <div>
        <div style="display:flex; justify-content:space-between; font-size:11px; margin-bottom:6px; opacity:0.9;">
            <span>Progress</span>
            <span>{{ $progress }}%</span>
        </div>
        <div style="height:5px; background:rgba(255,255,255,0.3); border-radius:99px; overflow:hidden;">
            <div style="height:100%; width:{{ $progress }}%; background:#fff; border-radius:99px;"></div>
        </div>
        <div style="font-size:11px; margin-top:6px; opacity:0.8;">
            {{ $enrollment->course->modules->sum(fn($m) => $m->materials->count()) }} Materi
        </div>
        <a href="{{ route('student.course.show', $course->slug) }}"
           style="display:flex; align-items:center; justify-content:center; gap:6px; margin-top:10px; padding:8px; background:rgba(255,255,255,0.2); border-radius:8px; color:#fff; text-decoration:none; font-size:12px; font-weight:600; border:1px solid rgba(255,255,255,0.3); transition:background 0.15s;"
           onmouseover="this.style.background='rgba(255,255,255,0.3)'"
           onmouseout="this.style.background='rgba(255,255,255,0.2)'">
            Lanjutkan Belajar <i class="fas fa-play" style="font-size:10px;"></i>
        </a>
    </div>
</div>
