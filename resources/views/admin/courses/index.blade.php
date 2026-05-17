@extends('admin.layouts.app')
@section('title', 'Manajemen Kelas')

@section('content')

<div class="page-header">
    <div>
        <h2>Manajemen Kelas</h2>
        <p>Kelola semua kelas dan materi pembelajaran.</p>
    </div>
    <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Buat Kelas
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
    <select name="status" class="form-control" style="width:160px;" onchange="this.form.submit()">
        <option value="">Semua Status</option>
        <option value="published" {{ request('status')==='published' ? 'selected' : '' }}>Dipublikasikan</option>
        <option value="draft"     {{ request('status')==='draft'     ? 'selected' : '' }}>Draft</option>
    </select>
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul kelas..."
           style="padding:8px 12px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; outline:none; width:240px;">
    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
    @if(request()->hasAny(['search','subject_id','status']))
        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline btn-sm">Reset</a>
    @endif
</form>

<div class="card" style="padding:0;">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Kelas</th>
                    <th>Mapel</th>
                    <th>Mentor</th>
                    <th>Modul</th>
                    <th>Peserta</th>
                    <th>Tipe</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courses as $course)
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="width:40px; height:40px; border-radius:8px; background:linear-gradient(135deg,#1E293B,#2563EB); flex-shrink:0; display:flex; align-items:center; justify-content:center;">
                                <i class="fas fa-book-open" style="color:rgba(255,255,255,0.7); font-size:14px;"></i>
                            </div>
                            <div>
                                <div style="font-weight:600; font-size:13.5px;">{{ $course->title }}</div>
                                <div style="font-size:11px; color:var(--muted);">{{ Str::limit($course->description, 50) }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px;">{{ $course->subject->name ?? '-' }}</td>
                    <td style="font-size:13px;">{{ $course->mentor->name ?? '-' }}</td>
                    <td style="text-align:center; font-weight:700;">{{ $course->modules_count }}</td>
                    <td style="text-align:center; font-weight:700;">{{ number_format($course->enrollments_count) }}</td>
                    <td>
                        <span class="badge {{ $course->is_premium ? 'badge-warning' : 'badge-success' }}">
                            {{ $course->is_premium ? 'Premium' : 'Gratis' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $course->is_published ? 'badge-success' : 'badge-gray' }}">
                            {{ $course->is_published ? 'Publik' : 'Draft' }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex; gap:4px;">
                            <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-outline btn-sm" title="Kelola Materi">
                                <i class="fas fa-list"></i>
                            </a>
                            <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-outline btn-sm" title="Edit">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.courses.toggle-publish', $course) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $course->is_published ? 'btn-outline' : 'btn-primary' }}" title="{{ $course->is_published ? 'Sembunyikan' : 'Publikasikan' }}">
                                    <i class="fas {{ $course->is_published ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" onsubmit="return confirm('Hapus kelas ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:40px; color:var(--muted);">
                        <i class="fas fa-book-open" style="font-size:32px; opacity:0.2; display:block; margin-bottom:10px;"></i>
                        Belum ada kelas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:12px 20px;">{{ $courses->links() }}</div>
</div>

@endsection
