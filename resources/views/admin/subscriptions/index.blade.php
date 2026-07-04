@extends('admin.layouts.app')
@section('title', 'Manajemen Langganan')

@push('styles')
<style>[x-cloak]{display:none !important;}</style>
@endpush

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
        <div><div class="stat-value">Rp {{ number_format($stats['revenue']/1000000, 1) }}Jt</div><div class="stat-label">Total Revenue</div><div style="font-size:10.5px; color:var(--muted); margin-top:2px;">Benefit Affiliate: Rp {{ number_format(($stats['affiliate_rewards'] ?? 0), 0, ',', '.') }}</div></div>
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
                    <th>Program</th>
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
                    <td>
                        <div style="font-weight:700;">Rp {{ number_format($sub->total_amount ?? $sub->amount_paid, 0, ',', '.') }}</div>
                        @if($sub->unique_code)
                            <div style="font-size:11px; color:var(--muted);">Harga Rp {{ number_format($sub->amount_paid, 0, ',', '.') }} + kode {{ $sub->unique_code }}</div>
                        @endif
                        @if($sub->affiliateReferrer)
                            <div style="font-size:11px; color:var(--muted); margin-top:2px;">Pemilik kode: {{ $sub->affiliateReferrer->name }} ({{ $sub->affiliate_code }}) · Benefit Rp {{ number_format($sub->affiliate_reward_amount ?? 0, 0, ',', '.') }}</div>
                        @endif
                    </td>
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
                        <div style="display:flex; gap:4px; flex-wrap:wrap; align-items:center;">
                            @if($sub->payment_proof)
                            <a href="{{ asset('uploads/proofs/' . $sub->payment_proof) }}" target="_blank"
                               class="btn btn-outline btn-sm" title="Lihat bukti transfer">
                                <i class="fas fa-receipt"></i>
                            </a>
                            @endif

                            @if($sub->status === 'pending')
                            <form method="POST" action="{{ route('admin.subscriptions.activate', $sub) }}"
                                  onsubmit="return confirm('Aktifkan langganan ini? Pastikan pembayaran sudah masuk.')">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-primary btn-sm" title="Aktifkan / Validasi">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            @endif

                            @if($sub->status === 'active')
                            <div x-data="{ open:false }">
                                <button @click="open=true" class="btn btn-outline btn-sm">
                                    <i class="fas fa-plus"></i> Extend
                                </button>
                                {{-- Modal fixed: tidak terpotong oleh overflow tabel --}}
                                <template x-teleport="body">
                                    <div x-show="open" x-cloak @click.self="open=false"
                                         style="position:fixed; inset:0; background:rgba(15,23,42,0.45); display:flex; align-items:center; justify-content:center; z-index:1000;">
                                        <div @click.outside="open=false"
                                             style="background:#fff; border-radius:12px; padding:22px; width:320px; box-shadow:0 12px 40px rgba(0,0,0,0.2);">
                                            <div style="font-size:15px; font-weight:700; margin-bottom:4px;">Perpanjang Langganan</div>
                                            <div style="font-size:12.5px; color:var(--muted); margin-bottom:16px;">{{ $sub->user->name ?? '-' }} — {{ $sub->plan->name ?? '' }}</div>
                                            <form method="POST" action="{{ route('admin.subscriptions.extend', $sub) }}">
                                                @csrf @method('PATCH')
                                                <label style="font-size:12px; font-weight:600; display:block; margin-bottom:6px;">Durasi perpanjangan (bulan)</label>
                                                <input type="number" name="months" value="1" min="1" max="24"
                                                       style="width:100%; padding:9px 12px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; margin-bottom:16px;">
                                                <div style="display:flex; gap:8px;">
                                                    <button type="button" @click="open=false" class="btn btn-outline btn-sm" style="flex:1; justify-content:center;">Batal</button>
                                                    <button type="submit" class="btn btn-primary btn-sm" style="flex:1; justify-content:center;">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </template>
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
