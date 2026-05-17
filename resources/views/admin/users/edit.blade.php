@extends('admin.layouts.app')
@section('title', 'Edit User — '.$user->name)

@section('content')

<div style="max-width:680px;">
    <div class="page-header">
        <div>
            <h2>Edit User</h2>
            <p>{{ $user->email }}</p>
        </div>
        <div style="display:flex; gap:8px;">
            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PUT')

            <div style="font-size:13px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:14px; padding-bottom:8px; border-bottom:1px solid var(--border);">
                Informasi Akun
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                <div class="form-group" style="grid-column:span 2;">
                    <label>Nama Lengkap <span style="color:red;">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Email <span style="color:red;">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Role <span style="color:red;">*</span></label>
                    <select name="role" class="form-control" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                        @foreach(['student','mentor','admin','superadmin'] as $role)
                        <option value="{{ $role }}" {{ old('role', $user->role) === $role ? 'selected' : '' }}>
                            {{ ucfirst($role) }}
                        </option>
                        @endforeach
                    </select>
                    @if($user->id === auth()->id())
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        <div style="font-size:11.5px; color:var(--muted); margin-top:4px;">Tidak bisa mengubah role akun sendiri.</div>
                    @endif
                </div>

                <div class="form-group">
                    <label>No. HP</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control" placeholder="08xx...">
                </div>

                <div class="form-group">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}" class="form-control">
                </div>
            </div>

            <div style="font-size:13px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; margin:20px 0 14px; padding-bottom:8px; border-bottom:1px solid var(--border);">
                Informasi Sekolah
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                <div class="form-group" style="grid-column:span 2;">
                    <label>Nama Sekolah</label>
                    <input type="text" name="school" value="{{ old('school', $user->school) }}" class="form-control" placeholder="SMA Negeri...">
                </div>
                <div class="form-group">
                    <label>Kota</label>
                    <input type="text" name="city" value="{{ old('city', $user->city) }}" class="form-control">
                </div>
                <div class="form-group">
                    <label>Provinsi</label>
                    <input type="text" name="province" value="{{ old('province', $user->province) }}" class="form-control">
                </div>
                <div class="form-group">
                    <label>Kelas</label>
                    <select name="grade" class="form-control">
                        <option value="">Pilih kelas</option>
                        @foreach(['X IPA','X IPS','XI IPA','XI IPS','XII IPA','XII IPS','Alumni'] as $g)
                        <option value="{{ $g }}" {{ old('grade', $user->grade) === $g ? 'selected' : '' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Status Akun</label>
                    <div style="display:flex; align-items:center; gap:10px; padding-top:8px;">
                        <label style="display:flex; align-items:center; gap:8px; font-size:13.5px; cursor:pointer; font-weight:400;">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                   style="width:16px; height:16px; accent-color:var(--primary);">
                            Akun Aktif
                        </label>
                    </div>
                </div>
            </div>

            <div style="font-size:13px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; margin:20px 0 14px; padding-bottom:8px; border-bottom:1px solid var(--border);">
                Ganti Password
            </div>

            <div class="form-group">
                <label>Password Baru <span style="font-weight:400; color:var(--muted);">(kosongkan jika tidak ingin mengubah)</span></label>
                <input type="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                       placeholder="Min. 8 karakter" autocomplete="new-password">
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:8px;">
                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
