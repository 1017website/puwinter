@props(['liveClass'])

@php
    $days = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
    $months = ['','JAN','FEB','MAR','APR','MEI','JUN','JUL','AGS','SEP','OKT','NOV','DES'];
    $day   = $days[$liveClass->scheduled_at->dayOfWeek];
    $date  = $liveClass->scheduled_at->day;
    $month = $months[$liveClass->scheduled_at->month];
    $time  = $liveClass->scheduled_at->format('H:i') . ' - ' .
             $liveClass->scheduled_at->addMinutes($liveClass->duration_minutes)->format('H:i') . ' WIB';
@endphp

<div style="display:flex; align-items:center; gap:12px; padding:12px 0; border-bottom:1px solid var(--border);">
    {{-- Date block --}}
    <div style="text-align:center; min-width:44px;">
        <div style="font-size:10px; font-weight:700; color:var(--primary); text-transform:uppercase;">{{ $day }}</div>
        <div style="font-size:20px; font-weight:800; color:var(--text-main); line-height:1;">{{ $date }}</div>
        <div style="font-size:10px; color:var(--text-muted); font-weight:600;">{{ $month }}</div>
    </div>

    {{-- Info --}}
    <div style="flex:1; min-width:0;">
        <div style="font-size:10px; font-weight:600; color:var(--primary); margin-bottom:2px;">
            {{ $liveClass->subject->name ?? '' }}
        </div>
        <div style="font-size:13px; font-weight:700; color:var(--text-main); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
            {{ $liveClass->title }}
        </div>
        <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">
            <i class="fas fa-clock" style="font-size:9px;"></i> {{ $time }}
            &nbsp;·&nbsp; {{ $liveClass->mentor->name ?? '' }}
        </div>
    </div>

    {{-- Button --}}
    <a href="{{ route('student.live.show', $liveClass->id) }}"
       style="padding:6px 12px; background:var(--primary-light); color:var(--primary); border-radius:6px; font-size:11px; font-weight:700; text-decoration:none; white-space:nowrap; transition:background 0.15s;"
       onmouseover="this.style.background='var(--primary)'; this.style.color='#fff';"
       onmouseout="this.style.background='var(--primary-light)'; this.style.color='var(--primary)';">
        Detail
    </a>
</div>
