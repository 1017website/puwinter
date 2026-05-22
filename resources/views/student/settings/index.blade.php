@extends('layouts.student')
@section('title', 'Pengaturan')
@php $subtitle = 'Kelola informasi akun dan keamanan kamu.'; @endphp

@section('content')

<div style="margin-bottom:24px;">
    <h2 style="font-size:22px; font-weight:800;">Pengaturan</h2>
    <p style="font-size:13px; color:var(--text-muted); margin-top:2px;">Kelola informasi akun dan keamanan kamu.</p>
</div>

<div style="display:grid; grid-template-columns:1fr 300px; gap:20px; align-items:flex-start;">

    <div style="display:flex; flex-direction:column; gap:16px;">

        {{-- Profil --}}
        <div class="card">
            <div style="font-size:14px; font-weight:700; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid var(--border);">
                <i class="fas fa-user" style="color:var(--primary); margin-right:7px;"></i> Informasi Profil
            </div>
            <form method="POST" action="{{ route('student.settings.profile') }}">
                @csrf
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                    <div class="form-group" style="grid-column:span 2;">
                        <label>Nama Lengkap <span style="color:var(--danger);">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                        @error('name') <div style="font-size:11px; color:var(--danger); margin-top:3px;">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>Nomor HP</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control" placeholder="08xx...">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}" class="form-control">
                    </div>
                    <div class="form-group" style="grid-column:span 2;">
                        <label>Asal Sekolah</label>
                        <input type="text" name="school" value="{{ old('school', $user->school) }}" class="form-control" placeholder="SMA Negeri...">
                    </div>
                    <div class="form-group">
                        <label>Kota</label>
                        <input type="text" name="city" value="{{ old('city', $user->city) }}" class="form-control" placeholder="Surabaya">
                    </div>
                    <div class="form-group">
                        <label>Provinsi</label>
                        <input type="text" name="province" value="{{ old('province', $user->province) }}" class="form-control" placeholder="Jawa Timur">
                    </div>
                    <div class="form-group">
                        <label>Kelas</label>
                        <select name="grade" class="form-control">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach(['X IPA','X IPS','XI IPA','XI IPS','XII IPA','XII IPS','Lulus/Gap Year'] as $g)
                                <option value="{{ $g }}" {{ old('grade', $user->grade) === $g ? 'selected':'' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="margin-top:16px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Profil
                    </button>
                </div>
            </form>
        </div>

        {{-- Password --}}
        <div class="card">
            <div style="font-size:14px; font-weight:700; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid var(--border);">
                <i class="fas fa-lock" style="color:var(--primary); margin-right:7px;"></i> Ubah Password
            </div>
            <form method="POST" action="{{ route('student.settings.password') }}">
                @csrf
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <div class="form-group">
                        <label>Password Saat Ini <span style="color:var(--danger);">*</span></label>
                        <input type="password" name="current_password" class="form-control" required>
                        @error('current_password') <div style="font-size:11px; color:var(--danger); margin-top:3px;">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>Password Baru <span style="color:var(--danger);">*</span></label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                        @error('password') <div style="font-size:11px; color:var(--danger); margin-top:3px;">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password Baru <span style="color:var(--danger);">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                <div style="margin-top:16px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key"></i> Ubah Password
                    </button>
                </div>
            </form>
        </div>

    </div>

    {{-- Sidebar Info --}}
    <div style="position:sticky; top:80px; display:flex; flex-direction:column; gap:14px;">

        {{-- Avatar / Info --}}
        <div class="card" style="text-align:center; padding:24px;">
            <div style="width:72px; height:72px; border-radius:50%; background:var(--primary); display:flex; align-items:center; justify-content:center; margin:0 auto 12px; font-size:28px; font-weight:800; color:#fff;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div style="font-size:15px; font-weight:800; margin-bottom:2px;">{{ $user->name }}</div>
            <div style="font-size:12px; color:var(--text-muted);">{{ $user->email }}</div>
            <div style="margin-top:10px;">
                @if($user->isPremium())
                    <span class="badge badge-premium"><i class="fas fa-crown" style="font-size:10px;"></i> Member Premium</span>
                @else
                    <span class="badge badge-primary">Akun Gratis</span>
                @endif
            </div>
        </div>

        {{-- Status Langganan --}}
        <div class="card">
            <div style="font-size:13px; font-weight:700; margin-bottom:12px;">Status Langganan</div>
            @if($activeSubscription)
                <div style="background:#ECFDF5; border:1px solid #6EE7B7; border-radius:8px; padding:12px; margin-bottom:10px;">
                    <div style="font-size:12px; font-weight:700; color:#065F46; margin-bottom:3px;">
                        <i class="fas fa-check-circle" style="margin-right:4px;"></i> Aktif — {{ $activeSubscription->plan->name ?? 'Premium' }}
                    </div>
                    <div style="font-size:11px; color:#047857;">
                        Berlaku hingga: <strong>{{ $activeSubscription->expired_at->format('d M Y') }}</strong>
                    </div>
                    <div style="font-size:11px; color:#047857; margin-top:2px;">
                        Sisa: <strong>{{ $activeSubscription->daysRemaining() }} hari</strong>
                    </div>
                </div>
                <a href="{{ route('upgrade.index') }}" class="btn btn-outline" style="width:100%; justify-content:center; font-size:12px;">
                    <i class="fas fa-refresh"></i> Perpanjang
                </a>
            @else
                <div style="text-align:center; padding:12px; color:var(--text-muted);">
                    <i class="fas fa-crown" style="font-size:24px; opacity:0.2; display:block; margin-bottom:8px;"></i>
                    <p style="font-size:12px; margin-bottom:12px;">Kamu belum berlangganan Premium.</p>
                    <a href="{{ route('upgrade.index') }}" class="btn btn-premium" style="width:100%; justify-content:center; font-size:12px;">
                        <i class="fas fa-crown"></i> Upgrade Premium
                    </a>
                </div>
            @endif
        </div>

    </div>
</div>

@endsection
