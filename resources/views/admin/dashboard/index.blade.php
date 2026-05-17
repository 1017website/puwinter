@extends('admin.layouts.app')
@section('title', 'Dashboard Admin')

@section('content')

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#EFF6FF;"><i class="fas fa-users" style="color:#2563EB;"></i></div>
        <div>
            <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
            <div class="stat-label">Total Student</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#FFFBEB;"><i class="fas fa-crown" style="color:#F59E0B;"></i></div>
        <div>
            <div class="stat-value">{{ number_format($stats['total_premium']) }}</div>
            <div class="stat-label">Member Premium</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#ECFDF5;"><i class="fas fa-money-bill-wave" style="color:#10B981;"></i></div>
        <div>
            <div class="stat-value">Rp {{ number_format($stats['revenue_month']/1000, 0) }}K</div>
            <div class="stat-label">Revenue Bulan Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#FEF2F2;"><i class="fas fa-clock" style="color:#EF4444;"></i></div>
        <div>
            <div class="stat-value">{{ $stats['pending_payments'] }}</div>
            <div class="stat-label">Pembayaran Pending</div>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 360px; gap:20px;">

    {{-- LEFT --}}
    <div>
        {{-- Revenue Chart --}}
        <div class="card" style="margin-bottom:20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <div style="font-size:15px; font-weight:700;">Revenue 7 Hari Terakhir</div>
            </div>
            <div style="height:200px; display:flex; align-items:flex-end; gap:8px;">
                @php $maxRevenue = max(array_column($chartData, 'total')) ?: 1; @endphp
                @foreach($chartData as $day)
                <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:6px;">
                    <div style="font-size:10px; color:var(--muted);">
                        Rp{{ number_format($day['total']/1000, 0) }}K
                    </div>
                    <div style="width:100%; background:#2563EB; border-radius:4px 4px 0 0; transition:height 0.3s;"
                         style="height:{{ max(4, ($day['total']/$maxRevenue)*160) }}px;"
                         data-height="{{ max(4, ($day['total']/$maxRevenue)*160) }}">
                    </div>
                    <div style="font-size:10px; color:var(--muted);">{{ $day['date'] }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Transaksi Terbaru --}}
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <div style="font-size:15px; font-weight:700;">Transaksi Terbaru</div>
                <a href="{{ route('admin.subscriptions.index') }}" style="font-size:12px; color:var(--primary); text-decoration:none; font-weight:600;">Lihat Semua</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Paket</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTransactions as $sub)
                        <tr>
                            <td>
                                <div style="font-weight:600; font-size:13px;">{{ $sub->user->name ?? '-' }}</div>
                                <div style="font-size:11px; color:var(--muted);">{{ $sub->user->email ?? '' }}</div>
                            </td>
                            <td>{{ $sub->plan->name ?? '-' }}</td>
                            <td style="font-weight:700;">Rp {{ number_format($sub->amount_paid, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge {{ match($sub->status) { 'active'=>'badge-success', 'pending'=>'badge-warning', default=>'badge-danger' } }}">
                                    {{ ucfirst($sub->status) }}
                                </span>
                            </td>
                            <td style="font-size:12px; color:var(--muted);">{{ $sub->created_at->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- RIGHT --}}
    <div>
        {{-- Stats tambahan --}}
        <div class="card" style="margin-bottom:20px;">
            <div style="font-size:14px; font-weight:700; margin-bottom:14px;">Ringkasan Platform</div>
            @foreach([
                ['label'=>'Total Kelas', 'value'=>$stats['total_courses'], 'icon'=>'fa-book-open', 'color'=>'#2563EB'],
                ['label'=>'Total Tryout', 'value'=>$stats['total_tryouts'], 'icon'=>'fa-bullseye', 'color'=>'#7C3AED'],
                ['label'=>'User Baru Hari Ini', 'value'=>$stats['new_users_today'], 'icon'=>'fa-user-plus', 'color'=>'#10B981'],
                ['label'=>'Tryout Berlangsung', 'value'=>$stats['active_attempts'], 'icon'=>'fa-pen-to-square', 'color'=>'#F59E0B'],
            ] as $s)
            <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid var(--border);">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="width:32px; height:32px; border-radius:8px; background:{{ $s['color'] }}20; display:flex; align-items:center; justify-content:center;">
                        <i class="fas {{ $s['icon'] }}" style="color:{{ $s['color'] }}; font-size:13px;"></i>
                    </div>
                    <span style="font-size:13px; color:var(--muted);">{{ $s['label'] }}</span>
                </div>
                <strong style="font-size:15px;">{{ number_format($s['value']) }}</strong>
            </div>
            @endforeach
        </div>

        {{-- User Terbaru --}}
        <div class="card" style="margin-bottom:20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                <div style="font-size:14px; font-weight:700;">User Terbaru</div>
                <a href="{{ route('admin.users.index') }}" style="font-size:12px; color:var(--primary); text-decoration:none; font-weight:600;">Lihat Semua</a>
            </div>
            @foreach($recentUsers as $user)
            <div style="display:flex; align-items:center; gap:10px; padding:8px 0; border-bottom:1px solid var(--border);">
                <div style="width:32px; height:32px; border-radius:50%; background:var(--primary); display:flex; align-items:center; justify-content:center; color:#fff; font-size:12px; font-weight:700; flex-shrink:0;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="font-size:13px; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $user->name }}</div>
                    <div style="font-size:11px; color:var(--muted);">{{ $user->created_at->diffForHumans() }}</div>
                </div>
                <span class="badge badge-gray" style="font-size:10px;">{{ $user->role }}</span>
            </div>
            @endforeach
        </div>

        {{-- Tryout Terpopuler --}}
        <div class="card">
            <div style="font-size:14px; font-weight:700; margin-bottom:14px;">Tryout Terpopuler</div>
            @foreach($popularTryouts as $i => $tryout)
            <div style="display:flex; align-items:center; gap:10px; padding:8px 0; border-bottom:1px solid var(--border);">
                <div style="width:24px; height:24px; border-radius:6px; background:{{ ['#2563EB','#7C3AED','#10B981','#F59E0B','#EF4444'][$i] }}20; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:800; color:{{ ['#2563EB','#7C3AED','#10B981','#F59E0B','#EF4444'][$i] }}; flex-shrink:0;">
                    {{ $i + 1 }}
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="font-size:13px; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $tryout->title }}</div>
                    <div style="font-size:11px; color:var(--muted);">{{ $tryout->attempts_count }} percobaan</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>

@push('scripts')
<script>
    // Animate bar chart
    document.querySelectorAll('[data-height]').forEach(el => {
        const h = el.getAttribute('data-height');
        el.style.height = h + 'px';
    });
</script>
@endpush

@endsection
