@props([
    'icon'    => 'fa-chart-bar',
    'color'   => 'blue',
    'value'   => '0',
    'label'   => '',
    'suffix'  => '',
])

<div class="stat-card">
    <div class="stat-icon {{ $color }}">
        <i class="fas {{ $icon }}"></i>
    </div>
    <div>
        <div class="stat-value">{{ $value }}<small style="font-size:12px; font-weight:500; color:var(--text-muted);">{{ $suffix }}</small></div>
        <div class="stat-label">{{ $label }}</div>
    </div>
</div>
