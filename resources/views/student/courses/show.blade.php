@extends('layouts.student')

@section('title', $course->title)

@php $subtitle = 'Detail kelas dan daftar materi pembelajaran.'; @endphp

@section('content')

{{-- Breadcrumb --}}
<div style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text-muted); margin-bottom:20px;">
    <a href="{{ route('student.course.index') }}" style="color:var(--primary); text-decoration:none; font-weight:600;">Kelas Saya</a>
    <i class="fas fa-chevron-right" style="font-size:10px;"></i>
    <span style="color:var(--text-main); font-weight:600;">{{ $course->title }}</span>
</div>

{{-- Header Kelas --}}
<div class="card" style="margin-bottom:20px;">
    <div style="display:flex; gap:20px; align-items:flex-start;">
        {{-- Thumbnail / placeholder --}}
        <div style="width:120px; height:80px; border-radius:10px; background:linear-gradient(135deg,#2563EB,#7C3AED); flex-shrink:0; display:flex; align-items:center; justify-content:center; overflow:hidden;">
            @if($course->thumbnail)
                <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="" style="width:100%; height:100%; object-fit:cover;">
            @else
                <i class="fas fa-book-open" style="font-size:28px; color:rgba(255,255,255,0.8);"></i>
            @endif
        </div>

        <div style="flex:1; min-width:0;">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px; flex-wrap:wrap;">
                @if($course->subject)
                    <span class="badge badge-primary">{{ $course->subject->name }}</span>
                @endif
                @if($course->is_premium)
                    <span class="badge badge-premium"><i class="fas fa-crown" style="font-size:10px;"></i> Premium</span>
                @else
                    <span class="badge badge-success">Gratis</span>
                @endif
            </div>
            <h2 style="font-size:18px; font-weight:800; margin-bottom:6px;">{{ $course->title }}</h2>
            @if($course->description)
                <p style="font-size:13px; color:var(--text-muted); line-height:1.5; margin-bottom:10px;">{{ $course->description }}</p>
            @endif
            <div style="display:flex; align-items:center; gap:16px; font-size:12px; color:var(--text-muted);">
                @if($course->mentor)
                    <span><i class="fas fa-user-tie" style="margin-right:4px;"></i>{{ $course->mentor->name }}</span>
                @endif
                <span><i class="fas fa-layer-group" style="margin-right:4px;"></i>{{ $course->modules->count() }} Modul</span>
                <span><i class="fas fa-play-circle" style="margin-right:4px;"></i>{{ $course->materials()->count() }} Materi</span>
            </div>
        </div>

        {{-- Progress --}}
        <div style="text-align:center; flex-shrink:0; min-width:100px;">
            <div style="font-size:28px; font-weight:800; color:var(--primary);">{{ $progressPercentage }}%</div>
            <div style="font-size:11px; color:var(--text-muted); margin-bottom:6px;">Selesai</div>
            <div class="progress-bar" style="width:100px;">
                <div class="progress-bar-fill" style="width:{{ $progressPercentage }}%;"></div>
            </div>
        </div>
    </div>
</div>

{{-- Daftar Modul & Materi --}}
<div style="display:flex; flex-direction:column; gap:12px;">
    @forelse($course->modules as $module)
        @php
            $moduleMaterials = $module->materials;
            $moduleCompleted = $moduleMaterials->filter(fn($m) => in_array($m->id, $completedMaterialIds))->count();
            $moduleTotal     = $moduleMaterials->count();
        @endphp

        <div class="card" style="padding:0; overflow:hidden;">
            {{-- Header modul --}}
            <div style="padding:14px 18px; background:#F8FAFC; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; cursor:pointer;"
                 onclick="toggleModule({{ $module->id }})">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="width:32px; height:32px; background:var(--primary-light); border-radius:8px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-folder" style="color:var(--primary); font-size:14px;"></i>
                    </div>
                    <div>
                        <div style="font-size:14px; font-weight:700;">{{ $module->title }}</div>
                        <div style="font-size:11px; color:var(--text-muted);">{{ $moduleCompleted }}/{{ $moduleTotal }} materi selesai</div>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:10px;">
                    @if($moduleTotal > 0)
                        <div style="font-size:12px; font-weight:700; color:{{ $moduleCompleted === $moduleTotal ? 'var(--success)' : 'var(--primary)' }};">
                            {{ $moduleTotal > 0 ? round($moduleCompleted / $moduleTotal * 100) : 0 }}%
                        </div>
                    @endif
                    <i class="fas fa-chevron-down module-chevron-{{ $module->id }}" style="font-size:12px; color:var(--text-muted); transition:transform 0.2s;"></i>
                </div>
            </div>

            {{-- Daftar materi --}}
            <div id="module-{{ $module->id }}" style="display:block;">
                @forelse($moduleMaterials as $material)
                    @php
                        $isDone   = in_array($material->id, $completedMaterialIds);
                        $isLocked = ($material->is_locked || $material->is_premium) && !auth()->user()->isPremium();

                        $typeIcon  = match($material->type) {
                            'video'      => 'fa-play-circle',
                            'pdf'        => 'fa-file-pdf',
                            'quiz'       => 'fa-question-circle',
                            'live_class' => 'fa-video',
                            default      => 'fa-file',
                        };
                        $typeColor = match($material->type) {
                            'video'      => '#2563EB',
                            'pdf'        => '#DC2626',
                            'quiz'       => '#7C3AED',
                            'live_class' => '#059669',
                            default      => '#64748B',
                        };
                        $typeLabel = match($material->type) {
                            'video'      => 'Video',
                            'pdf'        => 'PDF',
                            'quiz'       => 'Quiz',
                            'live_class' => 'Live Class',
                            default      => 'Materi',
                        };
                    @endphp

                    <div style="display:flex; align-items:center; gap:14px; padding:12px 18px; border-bottom:1px solid var(--border); transition:background 0.1s; {{ $isLocked ? 'opacity:0.6;' : '' }}"
                         onmouseover="if(!{{ $isLocked ? 'true' : 'false' }}) this.style.background='#F8FAFC'"
                         onmouseout="this.style.background='transparent'">

                        {{-- Status icon --}}
                        <div style="width:24px; height:24px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;
                                    {{ $isDone ? 'background:var(--success);' : 'border:2px solid var(--border);' }}">
                            @if($isDone)
                                <i class="fas fa-check" style="font-size:11px; color:#fff;"></i>
                            @endif
                        </div>

                        {{-- Type icon --}}
                        <div style="width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; background:{{ $typeColor }}18;">
                            <i class="fas {{ $typeIcon }}" style="font-size:15px; color:{{ $typeColor }};"></i>
                        </div>

                        {{-- Info --}}
                        <div style="flex:1; min-width:0;">
                            <div style="font-size:13.5px; font-weight:600; margin-bottom:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $material->title }}
                            </div>
                            <div style="display:flex; align-items:center; gap:8px; font-size:11px; color:var(--text-muted);">
                                <span style="color:{{ $typeColor }}; font-weight:600;">{{ $typeLabel }}</span>
                                @if($material->duration_minutes)
                                    <span>· {{ $material->duration_minutes }} menit</span>
                                @endif
                                @if($material->is_premium)
                                    <span class="badge badge-premium" style="font-size:10px; padding:1px 6px;"><i class="fas fa-crown" style="font-size:9px;"></i> Premium</span>
                                @endif
                            </div>
                        </div>

                        {{-- Action --}}
                        @if($isLocked)
                            <a href="{{ route('upgrade.index') }}"
                               style="display:flex; align-items:center; gap:6px; padding:7px 12px; background:#FEF3C7; color:#D97706; border-radius:7px; font-size:12px; font-weight:600; text-decoration:none; flex-shrink:0;">
                                <i class="fas fa-lock" style="font-size:11px;"></i> Upgrade
                            </a>
                        @else
                            <a href="{{ route('student.course.material.show', [$course->slug, $material->id]) }}"
                               style="display:flex; align-items:center; gap:6px; padding:7px 14px; {{ $isDone ? 'background:#ECFDF5; color:var(--success);' : 'background:var(--primary); color:#fff;' }} border-radius:7px; font-size:12px; font-weight:600; text-decoration:none; flex-shrink:0; transition:opacity 0.15s;"
                               onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                                @if($isDone)
                                    <i class="fas fa-redo" style="font-size:11px;"></i> Ulangi
                                @else
                                    <i class="fas fa-play" style="font-size:11px;"></i> Mulai
                                @endif
                            </a>
                        @endif
                    </div>
                @empty
                    <div style="padding:20px 18px; font-size:13px; color:var(--text-muted);">
                        <i class="fas fa-inbox" style="margin-right:6px;"></i> Belum ada materi di modul ini.
                    </div>
                @endforelse
            </div>
        </div>
    @empty
        <div class="card" style="text-align:center; padding:60px 20px; color:var(--text-muted);">
            <i class="fas fa-folder-open" style="font-size:40px; opacity:0.2; margin-bottom:12px; display:block;"></i>
            <p style="font-size:14px; font-weight:600;">Belum ada modul</p>
            <p style="font-size:12px; margin-top:4px;">Materi sedang disiapkan oleh mentor.</p>
        </div>
    @endforelse
</div>

@endsection

@push('scripts')
<script>
function toggleModule(id) {
    const el      = document.getElementById('module-' + id);
    const chevron = document.querySelector('.module-chevron-' + id);
    const isOpen  = el.style.display !== 'none';
    el.style.display      = isOpen ? 'none' : 'block';
    chevron.style.transform = isOpen ? 'rotate(-90deg)' : 'rotate(0deg)';
}
</script>
@endpush
