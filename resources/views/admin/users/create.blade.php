@extends('admin.layouts.app')
@section('title', 'Tambah User')

@section('content')

<div style="max-width:680px;">
    <div class="page-header">
        <div>
            <h2>Tambah User</h2>
            <p>Buat akun baru untuk siswa, mentor, atau admin.</p>
        </div>
        <div style="display:flex; gap:8px;">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div style="font-size:13px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:14px; padding-bottom:8px; border-bottom:1px solid var(--border);">
                Informasi Akun
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                <div class="form-group" style="grid-column:span 2;">
                    <label>Nama Lengkap <span style="color:red;">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Email <span style="color:red;">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Role <span style="color:red;">*</span></label>
                    <select name="role" class="form-control {{ $errors->has('role') ? 'is-invalid' : '' }}">
                        @foreach(['student','mentor','admin','superadmin'] as $role)
                        <option value="{{ $role }}" {{ old('role','student') === $role ? 'selected' : '' }}>
                            {{ ucfirst($role) }}
                        </option>
                        @endforeach
                    </select>
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>No. HP</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="08xx...">
                </div>

                <div class="form-group">
                    <label>Password <span style="color:red;">*</span></label>
                    <input type="password" name="password"
                           class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                           placeholder="Min. 8 karakter" autocomplete="new-password" required>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div style="font-size:13px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; margin:20px 0 14px; padding-bottom:8px; border-bottom:1px solid var(--border);">
                Informasi Sekolah <span style="font-weight:400; text-transform:none;">(opsional, untuk siswa)</span>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                <div class="form-group" style="grid-column:span 2;">
                    <label>Nama Sekolah</label>
                    <input type="text" name="school" value="{{ old('school') }}" class="form-control" placeholder="SMA Negeri...">
                </div>
                <div class="form-group">
                    <label>Kota</label>
                    <input type="text" name="city" value="{{ old('city') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label>Provinsi</label>
                    <input type="text" name="province" value="{{ old('province') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label>Kelas</label>
                    <select name="grade" class="form-control">
                        <option value="">Pilih kelas</option>
                        @foreach(['10','11','12'] as $g)
                        <option value="{{ $g }}" {{ (string) old('grade') === $g ? 'selected' : '' }}>Kelas {{ $g }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Status Akun</label>
                    <div style="display:flex; align-items:center; gap:10px; padding-top:8px;">
                        <label style="display:flex; align-items:center; gap:8px; font-size:13.5px; cursor:pointer; font-weight:400;">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                   style="width:16px; height:16px; accent-color:var(--primary);">
                            Akun Aktif
                        </label>
                    </div>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Buat User
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
