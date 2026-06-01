@extends('layouts.student')
@section('title', 'Extra Class')
@php $subtitle = 'Kelas tambahan seperti TOEFL — bebas diakses tanpa premium.'; @endphp

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
    <div>
        <h2 style="font-size:22px; font-weight:800;">Extra Class</h2>
        <p style="font-size:13px; color:var(--text-muted); margin-top:2px;">
            {{ $courses->total() }} kelas tambahan tersedia &middot; bebas untuk semua siswa
        </p>
    </div>
</div>

<div style="background:#ECFDF5; border:1px solid #A7F3D0; color:#065F46; padding:12px 16px; border-radius:10px; margin-bottom:20px; font-size:12.5px;">
    <i class="fas fa-unlock"></i>
    Extra Class (mis. TOEFL) bisa kamu ikuti tanpa langganan premium dan lintas kelas.
</div>

{{-- Filter cari --}}
<form method="GET" style="display:flex; gap:10px; margin-bottom:24px; flex-wrap:wrap; align-items:center;">
    <input type="text" name="search" value="{{ $search }}" placeholder="Cari extra class..."
           style="padding:8px 14px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; outline:none; width:240px;"
           onkeydown="if(event.key==='Enter')this.form.submit()">
    @if($search)
        <a href="{{ route('student.extra.index') }}" style="font-size:12.5px; color:var(--text-muted);">Reset</a>
    @endif
</form>

@if($courses->isEmpty())
    <div class="card" style="text-align:center; padding:50px; color:var(--text-muted);">
        <i class="fas fa-language" style="font-size:34px; opacity:0.2; display:block; margin-bottom:12px;"></i>
        <p style="font-size:14px; font-weight:600;">Belum ada extra class.</p>
    </div>
@else
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:16px;">
        @foreach($courses as $course)
            <div class="card" style="padding:0; overflow:hidden; display:flex; flex-direction:column;">
                <div style="height:120px; background:linear-gradient(135deg,#059669,#10B981); position:relative; display:flex; align-items:center; justify-content:center;">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}"
                             style="width:100%; height:100%; object-fit:cover;">
                    @else
                        <i class="fas fa-language" style="font-size:38px; color:rgba(255,255,255,0.85);"></i>
                    @endif
                    <span class="badge" style="position:absolute; top:10px; left:10px; background:rgba(255,255,255,0.9); color:#065F46; font-size:10px; font-weight:700;">
                        <i class="fas fa-unlock" style="font-size:9px;"></i> Extra
                    </span>
                </div>
                <div style="padding:16px; flex:1; display:flex; flex-direction:column;">
                    <h4 style="font-size:14px; font-weight:700; margin-bottom:4px;">{{ $course->title }}</h4>
                    <div style="font-size:12px; color:var(--text-muted); margin-bottom:12px;">
                        <i class="fas fa-user-tie" style="margin-right:3px;"></i>{{ $course->mentor->name ?? '-' }}
                        @if($course->subject)
                            &middot; {{ $course->subject->name }}
                        @endif
                    </div>
                    <div style="margin-top:auto; display:flex; align-items:center; justify-content:space-between;">
                        <span style="font-size:11.5px; color:var(--text-muted);">
                            <i class="fas fa-users" style="margin-right:3px;"></i>{{ $course->enrollments_count }} siswa
                        </span>
                        <a href="{{ route('student.course.show', $course->slug) }}"
                           class="btn btn-primary" style="font-size:12px; padding:7px 14px;">
                            @if($enrolledIds->contains($course->id))
                                <i class="fas fa-play"></i> Lanjutkan
                            @else
                                <i class="fas fa-arrow-right"></i> Ikuti
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div style="margin-top:24px;">{{ $courses->appends(request()->query())->links() }}</div>
@endif

@endsection
