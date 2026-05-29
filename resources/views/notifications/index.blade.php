@extends($layout)

@section('title', 'Notifikasi')
@php $subtitle = 'Semua pemberitahuan untukmu.'; @endphp

@section('content')

<div style="max-width:760px;">

    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
        <div>
            <h2 style="font-size:20px; font-weight:800;">Notifikasi</h2>
            <p style="font-size:13px; color:var(--text-muted); margin-top:2px;">
                {{ $notifications->total() }} notifikasi
            </p>
        </div>
        @if($notifications->total() > 0)
            <form action="{{ route('notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline" style="font-size:13px;">
                    <i class="fas fa-check-double"></i> Tandai semua dibaca
                </button>
            </form>
        @endif
    </div>

    @if(session('success'))
        <div class="card" style="background:#ECFDF5; border:1px solid #6EE7B7; color:#065F46; margin-bottom:16px; font-size:13px;">
            <i class="fas fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    {{-- List --}}
    @forelse($notifications as $notif)
        <a href="{{ route('notifications.read', $notif->id) }}"
           class="card"
           style="display:flex; gap:14px; align-items:flex-start; margin-bottom:10px; text-decoration:none; color:inherit;
                  {{ $notif->isUnread() ? 'border-left:3px solid '.$notif->color().'; background:#FBFDFF;' : 'opacity:0.85;' }}">
            <div style="width:40px; height:40px; border-radius:10px; flex-shrink:0; display:flex; align-items:center; justify-content:center;
                        background:{{ $notif->color() }}1A; color:{{ $notif->color() }};">
                <i class="fas {{ $notif->iconClass() }}"></i>
            </div>
            <div style="flex:1; min-width:0;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="font-size:14px; font-weight:700; color:var(--text-main);">{{ $notif->title }}</span>
                    @if($notif->isUnread())
                        <span style="width:7px; height:7px; border-radius:50%; background:{{ $notif->color() }}; flex-shrink:0;"></span>
                    @endif
                </div>
                @if($notif->body)
                    <p style="font-size:13px; color:var(--text-muted); margin-top:3px; line-height:1.5;">{{ $notif->body }}</p>
                @endif
                <div style="font-size:11px; color:var(--text-muted); margin-top:5px;">
                    {{ $notif->created_at->diffForHumans() }}
                </div>
            </div>
        </a>
    @empty
        <div class="card" style="text-align:center; padding:60px 20px;">
            <i class="fas fa-bell-slash" style="font-size:42px; opacity:0.2; display:block; margin-bottom:14px;"></i>
            <p style="font-weight:700; font-size:15px;">Belum ada notifikasi</p>
            <p style="font-size:13px; color:var(--text-muted); margin-top:4px;">Pemberitahuan akan muncul di sini.</p>
        </div>
    @endforelse

    @if($notifications->hasPages())
        <div style="margin-top:16px;">
            {{ $notifications->links() }}
        </div>
    @endif

</div>

@endsection
