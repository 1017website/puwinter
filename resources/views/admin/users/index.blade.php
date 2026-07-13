@extends('admin.layouts.app')
@section('title', 'Manajemen User')

@section('content')

<div class="page-header">
    <div>
        <h2>Manajemen User</h2>
        <p>Kelola semua akun user platform.</p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ route('admin.users.export', request()->query()) }}" class="btn btn-outline">
            <i class="fas fa-file-excel" style="color:#16A34A;"></i> Export Excel
        </a>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="fas fa-user-plus"></i> Tambah User</a>
    </div>
</div>

{{-- Filter tabs --}}
<div style="display:flex; gap:12px; margin-bottom:20px; flex-wrap:wrap; align-items:center;">
    <div style="display:flex; gap:2px; background:#fff; border:1px solid var(--border); border-radius:8px; padding:3px;">
        @foreach([''=>'Semua ('.$totalStats['all'].')','student'=>'Student ('.$totalStats['student'].')','mentor'=>'Mentor ('.$totalStats['mentor'].')','admin,superadmin'=>'Admin ('.$totalStats['admin'].')'] as $val => $label)
        <a href="{{ route('admin.users.index', array_merge(request()->query(), ['role'=>$val])) }}"
           style="padding:6px 12px; border-radius:6px; font-size:12.5px; font-weight:600; text-decoration:none;
                  {{ request('role','') === $val ? 'background:var(--primary); color:#fff;' : 'color:var(--muted);' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('admin.users.index') }}" style="display:flex; gap:8px; margin-left:auto;">
        @if(request('role')) <input type="hidden" name="role" value="{{ request('role') }}"> @endif
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama, email, sekolah..."
               style="padding:7px 12px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; width:240px; outline:none;">
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
        @if(request('search'))
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm">Reset</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="card" style="padding:0;">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Sekolah / Kota</th>
                    <th>Kelas</th>
                    <th>Tryout</th>
                    <th>Bergabung</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="width:34px; height:34px; border-radius:50%; background:var(--primary); display:flex; align-items:center; justify-content:center; color:#fff; font-size:13px; font-weight:700; flex-shrink:0;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600; font-size:13.5px;">{{ $user->name }}</div>
                                <div style="font-size:11.5px; color:var(--muted);">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ match($user->role) { 'superadmin'=>'badge-danger','admin'=>'badge-warning','mentor'=>'badge-primary',default=>'badge-gray' } }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>
                        @if($user->subscriptions->isNotEmpty())
                            <span class="badge badge-success"><i class="fas fa-crown" style="font-size:9px;"></i> Premium</span>
                        @elseif($user->is_active)
                            <span class="badge badge-gray">Gratis</span>
                        @else
                            <span class="badge badge-danger">Nonaktif</span>
                        @endif
                    </td>
                    <td style="font-size:12.5px; color:var(--muted);">
                        {{ $user->school ? Str::limit($user->school, 25) : '-' }}
                        @if($user->city) <br><span style="font-size:11px;">{{ $user->city }}</span> @endif
                    </td>
                    <td style="text-align:center; font-weight:700;">{{ $user->enrollments_count }}</td>
                    <td style="text-align:center; font-weight:700;">{{ $user->tryout_attempts_count }}</td>
                    <td style="font-size:12px; color:var(--muted);">{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <div style="display:flex; gap:4px;">
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline btn-sm" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline btn-sm" title="Edit">
                                <i class="fas fa-pen"></i>
                            </a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-outline' : 'btn-primary' }}" title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="fas {{ $user->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:40px; color:var(--muted);">
                        <i class="fas fa-users" style="font-size:32px; opacity:0.2; display:block; margin-bottom:10px;"></i>
                        Tidak ada user ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:12px 20px;">
        {{ $users->links() }}
    </div>
</div>

@endsection
