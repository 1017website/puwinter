@extends('admin.layouts.app')
@section('title', 'Visitor')

@section('content')
<div class="page-header" style="align-items:flex-end;gap:16px;">
    <div>
        <h2>Visitor Frontend</h2>
        <p>Statistik kunjungan landing page tanpa menyimpan alamat IP asli.</p>
    </div>
    <form method="GET" style="display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap;">
        <div><label style="font-size:11px;color:var(--muted);display:block;margin-bottom:4px;">Dari</label><input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="form-control" style="padding:7px 9px;font-size:12px;"></div>
        <div><label style="font-size:11px;color:var(--muted);display:block;margin-bottom:4px;">Sampai</label><input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="form-control" style="padding:7px 9px;font-size:12px;"></div>
        <button class="btn btn-primary btn-sm" type="submit"><i class="fas fa-filter"></i> Terapkan</button>
    </form>
</div>

<div class="stats-grid">
    @foreach([
        ['label'=>'Page Views','value'=>number_format($stats['page_views']),'icon'=>'fa-eye','color'=>'#2563EB','bg'=>'#EFF6FF'],
        ['label'=>'Visitor Unik','value'=>number_format($stats['unique_visitors']),'icon'=>'fa-users','color'=>'#7C3AED','bg'=>'#F5F3FF'],
        ['label'=>'Views Hari Ini','value'=>number_format($stats['today_views']),'icon'=>'fa-calendar-day','color'=>'#059669','bg'=>'#ECFDF5'],
        ['label'=>'View / Visitor','value'=>number_format($stats['views_per_visitor'],1),'icon'=>'fa-arrow-trend-up','color'=>'#D97706','bg'=>'#FFFBEB'],
    ] as $item)
    <div class="stat-card">
        <div class="stat-icon" style="background:{{ $item['bg'] }};"><i class="fas {{ $item['icon'] }}" style="color:{{ $item['color'] }};"></i></div>
        <div><div class="stat-value">{{ $item['value'] }}</div><div class="stat-label">{{ $item['label'] }}</div></div>
    </div>
    @endforeach
</div>

<div class="card" style="margin-bottom:20px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
        <div><div style="font-size:15px;font-weight:700;">Tren Kunjungan</div><div style="font-size:11px;color:var(--muted);margin-top:2px;">Page views dan visitor unik per hari</div></div>
        <div style="display:flex;gap:13px;font-size:11px;color:var(--muted);"><span><i class="fas fa-circle" style="color:#2563EB;font-size:8px;"></i> Views</span><span><i class="fas fa-circle" style="color:#A78BFA;font-size:8px;"></i> Visitor</span></div>
    </div>
    @php $maxChart = max(1, ...array_column($daily, 'views')); @endphp
    <div style="height:230px;display:flex;align-items:flex-end;gap:clamp(2px,1vw,10px);overflow-x:auto;padding-top:20px;">
        @foreach($daily as $index => $day)
        <div title="{{ $day['label'] }}: {{ $day['views'] }} views, {{ $day['visitors'] }} visitor" style="min-width:14px;flex:1;height:100%;display:flex;flex-direction:column;justify-content:flex-end;align-items:center;gap:4px;">
            <div style="font-size:9px;color:var(--muted);">{{ $day['views'] ?: '' }}</div>
            <div style="height:{{ max(2, ($day['views'] / $maxChart) * 170) }}px;width:100%;max-width:24px;background:linear-gradient(180deg,#2563EB,#93C5FD);border-radius:4px 4px 0 0;position:relative;">
                <span style="position:absolute;left:25%;right:25%;bottom:0;height:{{ $day['views'] ? max(2, ($day['visitors'] / $maxChart) * 170) : 0 }}px;background:#A78BFA;border-radius:3px 3px 0 0;"></span>
            </div>
            @if(count($daily) <= 31 || $index % 5 === 0)<div style="font-size:9px;color:var(--muted);white-space:nowrap;transform:rotate(-35deg);transform-origin:center;min-height:18px;">{{ $day['label'] }}</div>@else<div style="min-height:18px;"></div>@endif
        </div>
        @endforeach
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:20px;margin-bottom:20px;">
    <div class="card">
        <div style="font-size:14px;font-weight:700;margin-bottom:14px;">Halaman Teratas</div>
        @forelse($topPages as $page)
        <div style="display:flex;justify-content:space-between;gap:12px;padding:9px 0;border-bottom:1px solid var(--border);font-size:12px;"><span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#334155;">{{ $page->path }}</span><strong>{{ number_format($page->total) }}</strong></div>
        @empty<p style="font-size:12px;color:var(--muted);">Belum ada data.</p>@endforelse
    </div>
    <div class="card">
        <div style="font-size:14px;font-weight:700;margin-bottom:14px;">Sumber Kunjungan</div>
        @forelse($referrers as $source)
        <div style="display:flex;justify-content:space-between;gap:12px;padding:9px 0;border-bottom:1px solid var(--border);font-size:12px;"><span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#334155;">{{ $source->source }}</span><strong>{{ number_format($source->total) }}</strong></div>
        @empty<p style="font-size:12px;color:var(--muted);">Belum ada data.</p>@endforelse
    </div>
    <div class="card">
        <div style="font-size:14px;font-weight:700;margin-bottom:14px;">Perangkat</div>
        @forelse($devices as $device)
        @php $percent = $stats['page_views'] ? round(($device->total / $stats['page_views']) * 100) : 0; @endphp
        <div style="margin-bottom:13px;"><div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:5px;"><span>{{ $device->device }}</span><strong>{{ $percent }}%</strong></div><div style="height:6px;background:#E2E8F0;border-radius:10px;overflow:hidden;"><div style="height:100%;width:{{ $percent }}%;background:#7C3AED;border-radius:10px;"></div></div></div>
        @empty<p style="font-size:12px;color:var(--muted);">Belum ada data.</p>@endforelse
    </div>
</div>

<div class="card">
    <div style="font-size:14px;font-weight:700;margin-bottom:14px;">Kunjungan Terbaru</div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Waktu</th><th>Visitor</th><th>Halaman</th><th>Sumber</th><th>Perangkat</th><th>Browser / OS</th></tr></thead>
            <tbody>
                @forelse($recentVisits as $visit)
                <tr>
                    <td style="white-space:nowrap;"><strong style="font-size:12px;">{{ $visit->created_at->format('d M Y') }}</strong><div style="font-size:10.5px;color:var(--muted);">{{ $visit->created_at->format('H:i:s') }}</div></td>
                    <td><code style="font-size:10.5px;background:#F1F5F9;padding:3px 5px;border-radius:4px;">{{ substr($visit->visitor_id, 0, 8) }}</code></td>
                    <td style="font-size:12px;">{{ $visit->path }}</td>
                    <td style="font-size:12px;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $visit->referrer_domain ?: 'Langsung' }}</td>
                    <td><span class="badge badge-gray">{{ $visit->device }}</span></td>
                    <td style="font-size:11px;color:var(--muted);">{{ $visit->browser }} / {{ $visit->operating_system }}</td>
                </tr>
                @empty<tr><td colspan="6" style="padding:30px;text-align:center;color:var(--muted);">Belum ada kunjungan pada periode ini.</td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @if($recentVisits->hasPages())
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:15px;font-size:12px;color:var(--muted);">
        <span>Halaman {{ $recentVisits->currentPage() }} dari {{ $recentVisits->lastPage() }}</span>
        <div style="display:flex;gap:7px;">@if($recentVisits->onFirstPage())<span class="btn btn-sm" style="opacity:.45;">Sebelumnya</span>@else<a class="btn btn-sm" style="border:1px solid var(--border);" href="{{ $recentVisits->previousPageUrl() }}">Sebelumnya</a>@endif @if($recentVisits->hasMorePages())<a class="btn btn-sm" style="border:1px solid var(--border);" href="{{ $recentVisits->nextPageUrl() }}">Berikutnya</a>@else<span class="btn btn-sm" style="opacity:.45;">Berikutnya</span>@endif</div>
    </div>
    @endif
</div>
@endsection
