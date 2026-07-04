@extends('admin.layouts.app')
@section('title', 'Log Email')

@section('content')
<div class="page-header">
    <div>
        <h2>Log Email</h2>
        <p>Pantau email yang dikirim sistem, status terkirim/gagal, dan response/error mailer.</p>
    </div>
</div>

<div class="stats-grid" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon" style="background:#EFF6FF;"><i class="fas fa-envelope" style="color:#2563EB;"></i></div>
        <div><div class="stat-value">{{ number_format($stats['total']) }}</div><div class="stat-label">Total Log</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#ECFDF5;"><i class="fas fa-check-circle" style="color:#10B981;"></i></div>
        <div><div class="stat-value">{{ number_format($stats['sent']) }}</div><div class="stat-label">Terkirim</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#FEF2F2;"><i class="fas fa-triangle-exclamation" style="color:#EF4444;"></i></div>
        <div><div class="stat-value">{{ number_format($stats['failed']) }}</div><div class="stat-label">Gagal</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#FFFBEB;"><i class="fas fa-clock" style="color:#F59E0B;"></i></div>
        <div><div class="stat-value">{{ number_format($stats['processing']) }}</div><div class="stat-label">Processing</div></div>
    </div>
</div>

<form method="GET" style="display:flex; gap:8px; margin-bottom:20px; flex-wrap:wrap;">
    <select name="status" class="form-control" style="width:160px;" onchange="this.form.submit()">
        <option value="">Semua Status</option>
        <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Terkirim</option>
        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Gagal</option>
        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
    </select>
    <select name="type" class="form-control" style="width:190px;" onchange="this.form.submit()">
        <option value="">Semua Tipe</option>
        <option value="email_verification" {{ request('type') === 'email_verification' ? 'selected' : '' }}>Verifikasi Email</option>
    </select>
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari email, nama, subject, response..."
           style="padding:8px 12px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; outline:none; width:320px; max-width:100%;">
    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
    @if(request()->hasAny(['status','type','search']))
        <a href="{{ route('admin.email-logs.index') }}" class="btn btn-outline btn-sm">Reset</a>
    @endif
</form>

<div class="card" style="padding:0;">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Penerima</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Mailer</th>
                    <th>Response / Error</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td style="font-size:12px; color:var(--muted); white-space:nowrap;">
                            <div>{{ $log->created_at?->format('d M Y') }}</div>
                            <div>{{ $log->created_at?->format('H:i:s') }}</div>
                            @if($log->source)
                                <div style="font-size:10.5px; color:#94A3B8; margin-top:3px;">{{ ucfirst($log->source) }}</div>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:700; font-size:13px;">{{ $log->to_name ?: ($log->user->name ?? '-') }}</div>
                            <div style="font-size:11.5px; color:var(--muted);">{{ $log->to_email }}</div>
                            @if($log->from_email)
                                <div style="font-size:10.5px; color:#94A3B8; margin-top:3px;">From: {{ $log->from_email }}</div>
                            @endif
                        </td>
                        <td style="font-size:13px;">
                            <div style="font-weight:600; color:#0F172A;">{{ $log->subject ?: '-' }}</div>
                            <div style="font-size:11px; color:var(--muted); margin-top:3px;">{{ str_replace('_', ' ', $log->type) }}</div>
                            @if($log->email_verification_id)
                                <div style="font-size:10.5px; color:#94A3B8; margin-top:3px;">Verification ID: {{ $log->email_verification_id }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $log->status === 'sent' ? 'badge-success' : ($log->status === 'failed' ? 'badge-danger' : 'badge-warning') }}">
                                {{ $log->status === 'sent' ? 'Terkirim' : ($log->status === 'failed' ? 'Gagal' : 'Processing') }}
                            </span>
                            @if($log->sent_at)
                                <div style="font-size:10.5px; color:var(--muted); margin-top:4px;">Sent: {{ $log->sent_at->format('H:i:s') }}</div>
                            @endif
                            @if($log->failed_at)
                                <div style="font-size:10.5px; color:var(--danger); margin-top:4px;">Failed: {{ $log->failed_at->format('H:i:s') }}</div>
                            @endif
                        </td>
                        <td style="font-size:12px; color:var(--muted);">
                            <div>{{ $log->mailer ?: '-' }}</div>
                            <div>{{ $log->transport ?: '-' }}</div>
                        </td>
                        <td style="font-size:12px; max-width:420px;">
                            <div style="white-space:normal; word-break:break-word; color:{{ $log->status === 'failed' ? 'var(--danger)' : '#334155' }};">
                                {{ $log->error_message ?: ($log->response ?: '-') }}
                            </div>
                            @if(!empty($log->payload['verify_url']))
                                <details style="margin-top:8px;">
                                    <summary style="cursor:pointer; color:var(--primary); font-weight:600;">Detail</summary>
                                    <div style="margin-top:6px; padding:8px; border-radius:8px; background:#F8FAFC; color:#64748B; font-size:11px; line-height:1.6;">
                                        <div style="word-break:break-all;">URL: {{ $log->payload['verify_url'] }}</div>
                                        @if(!empty($log->payload['expires_at']))
                                            <div>Expired: {{ $log->payload['expires_at'] }}</div>
                                        @endif
                                    </div>
                                </details>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:32px; color:var(--muted);">Belum ada log email.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top:18px;">
    {{ $logs->links() }}
</div>
@endsection
