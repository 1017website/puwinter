@extends('layouts.student')

@section('title', 'Kelas Saya')

@php $subtitle = 'Kelola dan lanjutkan kelas yang sedang kamu ikuti.'; @endphp

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
    <div>
        <h2 style="font-size:22px; font-weight:800;">Kelas Saya</h2>
        <p style="font-size:13px; color:var(--text-muted); margin-top:2px;">Kelola dan lanjutkan kelas yang sedang kamu ikuti.</p>
    </div>
    <div style="display:flex; align-items:center; gap:12px;">
        <div style="font-size:13px; color:var(--text-muted);">
            Total Kelas: <strong style="color:var(--text-main);">{{ $enrollments->count() }}</strong>
        </div>
    </div>
</div>

{{-- Filter tabs --}}
<div style="display:flex; gap:4px; background:#fff; border:1px solid var(--border); border-radius:10px; padding:4px; width:fit-content; margin-bottom:24px;">
    @foreach(['semua' => 'Semua Kelas', 'aktif' => 'Aktif', 'selesai' => 'Selesai', 'arsip' => 'Arsip'] as $val => $label)
    <a href="{{ route('student.course.index', ['filter' => $val]) }}"
       style="padding:7px 16px; border-radius:7px; font-size:13px; font-weight:600; text-decoration:none;
              {{ $filter === $val ? 'background:var(--primary); color:#fff;' : 'color:var(--text-muted);' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

{{-- Grid kelas --}}
@if($enrollments->isEmpty())
    <div style="text-align:center; padding:80px 20px; color:var(--text-muted);">
        <i class="fas fa-book-open" style="font-size:48px; opacity:0.2; margin-bottom:16px; display:block;"></i>
        <p style="font-size:15px; font-weight:600;">Belum ada kelas</p>
        <p style="font-size:13px; margin-top:4px;">Mulai jelajahi kelas yang tersedia.</p>
        <a href="{{ route('student.course.explore') }}"
           class="btn btn-primary" style="margin-top:20px; display:inline-flex;">
            <i class="fas fa-compass"></i> Jelajahi Kelas
        </a>
    </div>
@else
    <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:16px;">
        @foreach($enrollments as $enrollment)
            <x-course-card :enrollment="$enrollment" />
        @endforeach
    </div>
@endif

@endsection
