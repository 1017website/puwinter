@extends('admin.layouts.app')
@section('title', 'Manajemen Tryout')

@section('content')

<div class="page-header">
    <div>
        <h2>Manajemen Tryout</h2>
        <p>Buat dan kelola tryout UTBK.</p>
    </div>
    <a href="{{ route('admin.tryouts.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Buat Tryout
    </a>
</div>

{{-- Filter --}}
<form method="GET" style="display:flex; gap:8px; margin-bottom:20px; flex-wrap:wrap;">
    <select name="subject_id" class="form-control" style="width:200px;" onchange="this.form.submit()">
        <option value="">Semua Mapel</option>
        @foreach($subjects as $subject)
        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
            {{ $subject->name }}
        </option>
        @endforeach
    </select>
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari tryout..."
           style="padding:8px 12px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; outline:none; width:240px;">
    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
    @if(request()->hasAny(['search','subject_id']))
        <a href="{{ route('admin.tryouts.index') }}" class="btn btn-outline btn-sm">Reset</a>
    @endif
</form>

<div class="card" style="padding:0;">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Mapel</th>
                    <th>Soal</th>
                    <th>Durasi</th>
                    <th>Peserta</th>
                    <th>Tipe</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tryouts as $tryout)
                <tr>
                    <td>
                        <div style="font-weight:600; font-size:13.5px;">{{ $tryout->title }}</div>
                        @if($tryout->series)
                            <div style="font-size:11px; color:var(--muted);">{{ $tryout->series }}</div>
                        @endif
                    </td>
                    <td style="font-size:13px;">{{ $tryout->subject->name ?? 'Semua' }}</td>
                    <td style="text-align:center; font-weight:700;">{{ $tryout->total_questions }}</td>
                    <td style="font-size:13px;">{{ $tryout->duration_minutes }} mnt</td>
                    <td style="text-align:center; font-weight:700;">{{ number_format($tryout->attempts_count) }}</td>
                    <td>
                        <span class="badge {{ $tryout->is_premium ? 'badge-warning' : 'badge-success' }}">
                            {{ $tryout->is_premium ? 'Premium' : 'Gratis' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $tryout->is_published ? 'badge-success' : 'badge-gray' }}">
                            {{ $tryout->is_published ? 'Publik' : 'Draft' }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex; gap:4px;">
                            <a href="{{ route('admin.tryouts.show', $tryout) }}" class="btn btn-outline btn-sm">
                                <i class="fas fa-list"></i> Soal
                            </a>
                            <a href="{{ route('admin.tryouts.edit', $tryout) }}" class="btn btn-outline btn-sm">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.tryouts.toggle-publish', $tryout) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $tryout->is_published ? 'btn-outline' : 'btn-primary' }}">
                                    <i class="fas {{ $tryout->is_published ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:40px; color:var(--muted);">
                        Belum ada tryout.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:12px 20px;">{{ $tryouts->links() }}</div>
</div>

@endsection
