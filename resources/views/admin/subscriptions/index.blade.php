@extends('admin.layouts.app')
@section('title', 'Manajemen Langganan')

@section('content')

<div class="page-header">
    <div>
        <h2>Manajemen Langganan</h2>
        <p>Pantau dan kelola semua transaksi langganan premium.</p>
    </div>
</div>

{{-- Stats --}}
<div class="stats-grid" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:#ECFDF5;"><i class="fas fa-check-circle" style="color:#10B981;"></i></div>
        <div><div class="stat-value">{{ number_format($stats['active']) }}</div><div class="stat-label">Aktif</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#FFFBEB;"><i class="fas fa-clock" style="color:#F59E0B;"></i></div>
        <div><div class="stat-value">{{ number_format($stats['pending']) }}</div><div class="stat-label">Pending</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#EFF6FF;"><i class="fas fa-list" style="color:#2563EB;"></i></div>
        <div><div class="stat-value">{{ number_format($stats['total']) }}</div><div class="stat-label">Total</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#F5F3FF;"><i class="fas fa-money-bill-wave" style="color:#7C3AED;"></i></div>
        <div><div class="stat-value">Rp {{ number_format($stats['revenue']/1000000, 1) }}Jt</div><div class="stat-label">Total Revenue</div></div>
    </div>
</div>

{{-- Filter --}}
<form method="GET" style="display:flex; gap:8px; margin-bottom:20px;">
    <select name="status" class="form-control" style="width:160px;" onchange="this.form.submit()">
        <option value="">Semua Status</option>
        <option value="active"    {{ request('status')==='active'    ? 'selected' : '' }}>Aktif</option>
        <option value="pending"   {{ request('status')==='pending'   ? 'selected' : '' }}>Pending</option>
        <option value="expired"   {{ request('status')==='expired'   ? 'selected' : '' }}>Expired</option>
        <option value="cancelled" {{ request('status')==='cancelled' ? 'selected' : '' }}>Dibatalkan</option>
    </select>
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
           style="padding:8px 12px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; outline:none; width:240px;">
    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
    @if(request()->hasAny(['status','search']))
        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline btn-sm">Reset</a>
    @endif
</form>

<div class="card" style="padding:0;">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Paket</th>
                    <th>Nominal</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th>Berlaku Hingga</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscriptions as $sub)
                <tr>
                    <td>
                        <div style="font-weight:600; font-size:13.5px;">{{ $sub->user->name ?? '-' }}</div>
                        <div style="font-size:11.5px; color:var(--muted);">{{ $sub->user->email ?? '' }}</div>
                    </td>
                    <td style="font-size:13px; font-weight:600;">{{ $sub->plan->name ?? '-' }}</td>
                    <td style="font-weight:700;">Rp {{ number_format($sub->amount_paid, 0, ',', '.') }}</td>
                    <td style="font-size:12px; text-transform:uppercase; color:var(--muted);">
                        {{ str_replace('_', ' ', $sub->payment_method ?? '-') }}
                    </td>
                    <td>
                        <span class="badge {{ match($sub->status) { 'active'=>'badge-success','pending'=>'badge-warning','cancelled'=>'badge-danger',default=>'badge-gray' } }}">
                            {{ ucfirst($sub->status) }}
                        </span>
                    </td>
                    <td style="font-size:12.5px;">
                        @if($sub->expired_at)
                            <span style="color:{{ $sub->expired_at->isFuture() ? 'var(--success)' : 'var(--danger)' }}; font-weight:600;">
                                {{ $sub->expired_at->format('d M Y') }}
                            </span>
                        @else — @endif
                    </td>
                    <td style="font-size:12px; color:var(--muted);">{{ $sub->created_at->format('d M Y') }}</td>
                    <td>
                        <div style="display:flex; gap:4px; flex-wrap:wrap;">
                            @if($sub->status === 'pending')
                            <form method="POST" action="{{ route('admin.subscriptions.activate', $sub) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-primary btn-sm" title="Aktifkan">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            @endif

                            @if($sub->status === 'active')
                            <div x-data="{ open:false }" style="position:relative;">
                                <button @click="open=!open" class="btn btn-outline btn-sm">
                                    <i class="fas fa-plus"></i> Extend
                                </button>
                                <div x-show="open" @click.outside="open=false"
                                     style="position:absolute; right:0; top:calc(100%+4px); background:#fff; border:1px solid var(--border); border-radius:8px; padding:12px; width:200px; z-index:50; box-shadow:0 4px 16px rgba(0,0,0,0.1);">
                                    <form method="POST" action="{{ route('admin.subscriptions.extend', $sub) }}">
                                        @csrf @method('PATCH')
                                        <label style="font-size:12px; font-weight:600; display:block; margin-bottom:6px;">Perpanjang (bulan)</label>
                                        <input type="number" name="months" value="1" min="1" max="24" class="form-control" style="margin-bottom:8px;">
                                        <button type="submit" class="btn btn-primary btn-sm" style="width:100%; justify-content:center;">Simpan</button>
                                    </form>
                                </div>
                            </div>
                            @endif

                            @if(!in_array($sub->status, ['cancelled']))
                            <form method="POST" action="{{ route('admin.subscriptions.cancel', $sub) }}" onsubmit="return confirm('Batalkan subscription ini?')">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-danger btn-sm" title="Batalkan">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:40px; color:var(--muted);">
                        Tidak ada transaksi ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:12px 20px;">{{ $subscriptions->links() }}</div>
</div>

@endsection
