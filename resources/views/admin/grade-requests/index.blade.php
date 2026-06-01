@extends('admin.layouts.app')
@section('title', 'Permintaan Pindah Kelas')

@section('content')

<div class="page-header">
    <div>
        <h2>Permintaan Pindah Kelas</h2>
        <p>Tinjau dan proses permintaan pindah kelas dari siswa.</p>
    </div>
</div>

{{-- Filter status --}}
<div style="display:flex; gap:8px; margin-bottom:16px;">
    @php $tabs = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak']; @endphp
    @foreach($tabs as $key => $label)
        <a href="{{ route('admin.grade-requests.index', ['status' => $key]) }}"
           class="badge {{ $status === $key ? 'badge-primary' : 'badge-muted' }}"
           style="text-decoration:none; padding:7px 14px; font-size:12px;">
            {{ $label }}
            @if($key === 'pending' && $pendingCount > 0) ({{ $pendingCount }}) @endif
        </a>
    @endforeach
</div>

<div class="card" style="padding:0; overflow:hidden;">
    <table style="width:100%; border-collapse:collapse; font-size:13px;">
        <thead>
            <tr style="background:var(--bg); border-bottom:1px solid var(--border);">
                <th style="padding:12px 16px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted);">Siswa</th>
                <th style="padding:12px 16px; text-align:center; font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted);">Perpindahan</th>
                <th style="padding:12px 16px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted);">Alasan</th>
                <th style="padding:12px 16px; text-align:center; font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted);">Status</th>
                <th style="padding:12px 16px; text-align:right; font-size:11px; font-weight:700; text-transform:uppercase; color:var(--muted);">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $req)
            <tr style="border-bottom:1px solid var(--border);" x-data="{ open: false, action: '' }">
                <td style="padding:12px 16px;">
                    <div style="font-weight:700;">{{ $req->user->name }}</div>
                    <div style="font-size:11px; color:var(--muted);">{{ $req->user->email }}</div>
                    <div style="font-size:11px; color:var(--muted);">{{ $req->created_at->format('d M Y H:i') }}</div>
                </td>
                <td style="padding:12px 16px; text-align:center;">
                    <span style="font-weight:600;">{{ $req->fromGrade->name ?? '—' }}</span>
                    <i class="fas fa-arrow-right" style="margin:0 6px; color:var(--muted); font-size:10px;"></i>
                    <span style="font-weight:700; color:var(--primary);">{{ $req->toGrade->name ?? '—' }}</span>
                </td>
                <td style="padding:12px 16px; max-width:240px;">
                    <div style="font-size:12px; color:var(--text);">{{ $req->reason ?: '—' }}</div>
                    @if($req->admin_note)
                        <div style="font-size:11px; color:var(--muted); margin-top:4px;"><strong>Catatan admin:</strong> {{ $req->admin_note }}</div>
                    @endif
                </td>
                <td style="padding:12px 16px; text-align:center;">
                    @php $cls = ['pending'=>'badge-warning','approved'=>'badge-success','rejected'=>'badge-danger'][$req->status]; @endphp
                    <span class="badge {{ $cls }}" style="font-size:11px; padding:4px 10px;">{{ ucfirst($req->status) }}</span>
                </td>
                <td style="padding:12px 16px; text-align:right;">
                    @if($req->status === 'pending')
                        <div style="display:flex; gap:6px; justify-content:flex-end;">
                            <button type="button" @click="open=true; action='approve'" class="btn btn-primary btn-sm"><i class="fas fa-check"></i></button>
                            <button type="button" @click="open=true; action='reject'" class="btn btn-danger btn-sm"><i class="fas fa-times"></i></button>
                        </div>

                        {{-- Modal sederhana untuk catatan --}}
                        <div x-show="open" x-cloak
                             style="position:fixed; inset:0; background:rgba(0,0,0,.4); display:none; align-items:center; justify-content:center; z-index:50;"
                             :style="open ? 'display:flex;' : 'display:none;'"
                             @click.self="open=false">
                            <div class="card" style="width:380px; text-align:left;">
                                <div style="font-weight:700; margin-bottom:12px;"
                                     x-text="action === 'approve' ? 'Setujui Pindah Kelas' : 'Tolak Permintaan'"></div>
                                <form method="POST" :action="action === 'approve'
                                        ? '{{ route('admin.grade-requests.approve', $req) }}'
                                        : '{{ route('admin.grade-requests.reject', $req) }}'">
                                    @csrf @method('PATCH')
                                    <div class="form-group">
                                        <label>Catatan <span style="font-size:11px; color:var(--muted); font-weight:400;">(opsional)</span></label>
                                        <textarea name="admin_note" class="form-control" rows="3" placeholder="Catatan untuk siswa..."></textarea>
                                    </div>
                                    <div style="display:flex; gap:8px; justify-content:flex-end;">
                                        <button type="button" @click="open=false" class="btn btn-outline btn-sm">Batal</button>
                                        <button type="submit" class="btn btn-sm"
                                                :class="action === 'approve' ? 'btn-primary' : 'btn-danger'"
                                                x-text="action === 'approve' ? 'Setujui' : 'Tolak'"></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <span style="font-size:11px; color:var(--muted);">
                            {{ $req->processor->name ?? 'Admin' }}<br>
                            {{ optional($req->processed_at)->format('d M Y') }}
                        </span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="padding:60px; text-align:center; color:var(--muted);">
                    <i class="fas fa-inbox" style="font-size:32px; opacity:0.2; display:block; margin-bottom:10px;"></i>
                    Tidak ada permintaan.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px;">{{ $requests->links() }}</div>

@endsection
