@props(['message' => 'Konten Premium'])

<div style="position:relative; display:inline-block; width:100%;">
    {{-- Slot content --}}
    <div style="filter:blur(3px); pointer-events:none; user-select:none;">
        {{ $slot }}
    </div>

    {{-- Overlay --}}
    <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,0.7); backdrop-filter:blur(2px); border-radius:inherit;">
        <div style="text-align:center;">
            <div style="width:44px; height:44px; background:#1E293B; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 8px;">
                <i class="fas fa-lock" style="color:#fff; font-size:16px;"></i>
            </div>
            <div style="font-size:12px; font-weight:700; color:var(--text-main);">{{ $message }}</div>
            <a href="{{ route('upgrade.index') }}"
               style="display:inline-block; margin-top:8px; padding:6px 14px; background:var(--primary); color:#fff; border-radius:6px; font-size:11px; font-weight:700; text-decoration:none;">
                Upgrade Sekarang
            </a>
        </div>
    </div>
</div>
