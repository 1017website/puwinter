@extends('admin.layouts.app')
@section('title', 'Live Class')

@section('content')

<div class="page-header">
    <div>
        <h2>Live Class</h2>
        <p>Kelola jadwal, status, dan rekaman live class.</p>
    </div>
    <a href="{{ route('admin.live-classes.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Buat Live Class
    </a>
</div>

{{-- Filter --}}
<div class="card" style="padding:14px 16px; margin-bottom:20px;">
    <form method="GET" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul..."
               class="form-control" style="width:240px; font-size:13px;">
        <select name="status" class="form-control" style="width:160px; font-size:13px;">
            <option value="">Semua Status</option>
            <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
            <option value="live"      {{ request('status') === 'live'      ? 'selected' : '' }}>Live</option>
            <option value="ended"     {{ request('status') === 'ended'     ? 'selected' : '' }}>Selesai</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        @if(request()->anyFilled(['search','status']))
            <a href="{{ route('admin.live-classes.index') }}" class="btn btn-outline btn-sm">Reset</a>
        @endif
    </form>
</div>

{{-- Stats --}}
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:20px;">
    @foreach([
        ['label'=>'Total',      'value'=>\App\Models\LiveClass::count(),                                       'color'=>'blue',   'icon'=>'fa-video'],
        ['label'=>'Sedang Live','value'=>\App\Models\LiveClass::where('status','live')->count(),                'color'=>'danger', 'icon'=>'fa-circle'],
        ['label'=>'Terjadwal',  'value'=>\App\Models\LiveClass::where('status','scheduled')->count(),          'color'=>'yellow', 'icon'=>'fa-calendar'],
        ['label'=>'Ada Rekaman','value'=>\App\Models\LiveClass::where('status','ended')->whereNotNull('recording_url')->count(), 'color'=>'green', 'icon'=>'fa-film'],
    ] as $s)
    <div class="card" style="padding:14px 16px; display:flex; align-items:center; gap:12px;">
        <div style="width:38px; height:38px; border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0;
            {{ match($s['color']) { 'blue'=>'background:#EFF6FF; color:#2563EB;', 'danger'=>'background:#FEF2F2; color:#EF4444;', 'yellow'=>'background:#FFFBEB; color:#F59E0B;', 'green'=>'background:#ECFDF5; color:#10B981;', default=>'' } }}">
            <i class="fas {{ $s['icon'] }}" style="font-size:15px;"></i>
        </div>
        <div>
            <div style="font-size:20px; font-weight:800;">{{ $s['value'] }}</div>
            <div style="font-size:11px; color:var(--muted);">{{ $s['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Table --}}
<div class="card" style="padding:0; overflow:hidden;">
    <table style="width:100%; border-collapse:collapse; font-size:13px;">
        <thead>
            <tr style="background:var(--bg); border-bottom:1px solid var(--border);">
                <th style="padding:12px 16px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--muted);">Judul</th>
                <th style="padding:12px 16px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--muted);">Jadwal</th>
                <th style="padding:12px 16px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--muted);">Mentor</th>
                <th style="padding:12px 16px; text-align:center; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--muted);">Status</th>
                <th style="padding:12px 16px; text-align:center; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--muted);">Rekaman</th>
                <th style="padding:12px 16px; text-align:right; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--muted);">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($liveClasses as $lc)
            <tr style="border-bottom:1px solid var(--border); transition:background 0.1s;" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">
                <td style="padding:12px 16px;">
                    <div style="font-weight:700; margin-bottom:3px;">{{ $lc->title }}</div>
                    <div style="font-size:11px; color:var(--muted); display:flex; gap:8px; flex-wrap:wrap;">
                        @if($lc->subject) <span>{{ $lc->subject->name }}</span> @endif
                        @if($lc->is_premium) <span style="color:#D97706; font-weight:600;"><i class="fas fa-crown" style="font-size:9px;"></i> Premium</span> @endif
                        <span>{{ $lc->duration_minutes }} mnt</span>
                    </div>
                </td>
                <td style="padding:12px 16px; color:var(--muted); font-size:12px;">
                    {{ $lc->scheduled_at->format('d M Y') }}<br>
                    <strong style="color:var(--text);">{{ $lc->scheduled_at->format('H:i') }} WIB</strong>
                </td>
                <td style="padding:12px 16px; font-size:12px; color:var(--muted);">
                    {{ $lc->mentor->name ?? '-' }}
                </td>
                <td style="padding:12px 16px; text-align:center;">
                    @php
                        $badge = match($lc->status) {
                            'live'      => ['class'=>'badge-danger',   'label'=>'LIVE'],
                            'scheduled' => ['class'=>'badge-primary',  'label'=>'Terjadwal'],
                            'ended'     => ['class'=>'badge-success',  'label'=>'Selesai'],
                            'cancelled' => ['class'=>'badge-warning',  'label'=>'Dibatalkan'],
                            default     => ['class'=>'badge-primary',  'label'=>$lc->status],
                        };
                    @endphp
                    <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                </td>
                <td style="padding:12px 16px; text-align:center;">
                    @if($lc->recording_url)
                        <i class="fas fa-check-circle" style="color:var(--success); font-size:16px;" title="Ada rekaman"></i>
                    @else
                        <i class="fas fa-times-circle" style="color:var(--border); font-size:16px;" title="Belum ada rekaman"></i>
                    @endif
                </td>
                <td style="padding:12px 16px; text-align:right;">
                    <div style="display:flex; gap:6px; justify-content:flex-end; align-items:center;">
                        {{-- Quick status buttons --}}
                        @if($lc->status === 'scheduled')
                            <form method="POST" action="{{ route('admin.live-classes.set-status', $lc) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="live">
                                <button type="submit" class="btn btn-danger btn-sm" title="Mulai Live">
                                    <i class="fas fa-play"></i> Go Live
                                </button>
                            </form>
                        @elseif($lc->status === 'live')
                            <form method="POST" action="{{ route('admin.live-classes.set-status', $lc) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="ended">
                                <button type="submit" class="btn btn-outline btn-sm" title="Akhiri Live">
                                    <i class="fas fa-stop"></i> End
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('admin.live-classes.edit', $lc) }}" class="btn btn-outline btn-sm">
                            <i class="fas fa-pen"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.live-classes.destroy', $lc) }}" onsubmit="return confirm('Hapus live class ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="padding:60px; text-align:center; color:var(--muted);">
                    <i class="fas fa-video" style="font-size:32px; opacity:0.2; display:block; margin-bottom:10px;"></i>
                    Belum ada live class.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($liveClasses->hasPages())
        <div style="padding:14px 16px; border-top:1px solid var(--border);">
            {{ $liveClasses->links() }}
        </div>
    @endif
</div>

@endsection
