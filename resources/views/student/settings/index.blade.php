@extends('layouts.student')
@section('title', 'Pengaturan')
@php $subtitle = 'Kelola informasi akun dan keamanan kamu.'; @endphp

@push('styles')
<style>
.settings-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #64748B;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.settings-input {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid #E2E8F0;
    border-radius: 8px;
    font-size: 13.5px;
    font-family: inherit;
    color: #1E293B;
    background: #fff;
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
    box-sizing: border-box;
}
.settings-input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
}
.settings-input::placeholder { color: #94A3B8; }
.settings-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748B' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 36px;
}
.settings-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
.settings-field { display: flex; flex-direction: column; }
.settings-field.full { grid-column: span 2; }
.field-error { font-size: 11px; color: #EF4444; margin-top: 4px; }
.settings-section {
    background: #fff;
    border: 1px solid #E2E8F0;
    border-radius: 14px;
    padding: 24px;
    margin-bottom: 16px;
}
.settings-section-title {
    font-size: 14px;
    font-weight: 700;
    color: #1E293B;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 1px solid #F1F5F9;
    display: flex;
    align-items: center;
    gap: 8px;
}
.settings-section-title i { color: var(--primary); }
.settings-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    font-family: inherit;
    transition: opacity 0.15s;
}
.settings-btn:hover { opacity: 0.88; }
.settings-btn-primary { background: var(--primary); color: #fff; }
.settings-btn-outline { background: #fff; color: #475569; border: 1.5px solid #E2E8F0; }

@media (max-width: 768px) {
    .settings-layout { grid-template-columns: 1fr !important; }
    .settings-row { grid-template-columns: 1fr !important; }
    .settings-field.full { grid-column: span 1 !important; }
}
</style>
@endpush

@section('content')

<div style="margin-bottom:24px;">
    <h2 style="font-size:22px; font-weight:800;">Pengaturan</h2>
    <p style="font-size:13px; color:var(--text-muted); margin-top:2px;">Kelola informasi akun dan keamanan kamu.</p>
</div>

<div class="settings-layout" style="display:grid; grid-template-columns:1fr 280px; gap:20px; align-items:flex-start;">

    {{-- ===== KIRI: Form ===== --}}
    <div>

        {{-- Profil --}}
        <div class="settings-section">
            <div class="settings-section-title">
                <i class="fas fa-user"></i> Informasi Profil
            </div>
            <form method="POST" action="{{ route('student.settings.profile') }}">
                @csrf

                {{-- Nama --}}
                <div style="margin-bottom:16px;">
                    <label class="settings-label">Nama Lengkap <span style="color:#EF4444;">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="settings-input" required placeholder="Nama lengkap kamu">
                    @error('name') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                {{-- HP + Tanggal Lahir --}}
                <div class="settings-row" style="margin-bottom:16px;">
                    <div class="settings-field">
                        <label class="settings-label">Nomor HP</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                               class="settings-input" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="settings-field">
                        <label class="settings-label">Tanggal Lahir</label>
                        <input type="date" name="birth_date"
                               value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}"
                               class="settings-input">
                    </div>
                </div>

                {{-- Asal Sekolah --}}
                <div style="margin-bottom:16px;">
                    <label class="settings-label">Asal Sekolah</label>
                    <input type="text" name="school" value="{{ old('school', $user->school) }}"
                           class="settings-input" placeholder="SMA Negeri 1 ...">
                </div>

                {{-- Kota + Provinsi --}}
                <div class="settings-row" style="margin-bottom:16px;">
                    <div class="settings-field">
                        <label class="settings-label">Kota</label>
                        <input type="text" name="city" value="{{ old('city', $user->city) }}"
                               class="settings-input" placeholder="Surabaya">
                    </div>
                    <div class="settings-field">
                        <label class="settings-label">Provinsi</label>
                        <input type="text" name="province" value="{{ old('province', $user->province) }}"
                               class="settings-input" placeholder="Jawa Timur">
                    </div>
                </div>

                {{-- Kelas --}}
                <div style="margin-bottom:24px;">
                    <label class="settings-label">Kelas</label>
                    <select name="grade" class="settings-input settings-select">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach(['10','11','12'] as $g)
                            <option value="{{ $g }}" {{ (string) old('grade', $user->grade) === $g ? 'selected' : '' }}>
                                Kelas {{ $g }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="settings-btn settings-btn-primary">
                    <i class="fas fa-save"></i> Simpan Profil
                </button>
            </form>
        </div>

        {{-- Ubah Password --}}
        <div class="settings-section">
            <div class="settings-section-title">
                <i class="fas fa-lock"></i> Ubah Password
            </div>
            <form method="POST" action="{{ route('student.settings.password') }}">
                @csrf

                <div style="margin-bottom:16px;">
                    <label class="settings-label">Password Saat Ini <span style="color:#EF4444;">*</span></label>
                    <input type="password" name="current_password" class="settings-input" required
                           placeholder="••••••••">
                    @error('current_password') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div class="settings-row" style="margin-bottom:24px;">
                    <div class="settings-field">
                        <label class="settings-label">Password Baru <span style="color:#EF4444;">*</span></label>
                        <input type="password" name="password" class="settings-input" required
                               minlength="8" placeholder="Min. 8 karakter">
                        @error('password') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="settings-field">
                        <label class="settings-label">Konfirmasi Password <span style="color:#EF4444;">*</span></label>
                        <input type="password" name="password_confirmation" class="settings-input" required
                               placeholder="Ulangi password baru">
                    </div>
                </div>

                <button type="submit" class="settings-btn settings-btn-primary">
                    <i class="fas fa-key"></i> Ubah Password
                </button>
            </form>
        </div>

    </div>

    {{-- ===== KANAN: Info sidebar ===== --}}
    <div style="position:sticky; top:80px; display:flex; flex-direction:column; gap:14px;">

        {{-- Avatar --}}
        <div class="settings-section" style="text-align:center; padding:28px 20px; margin-bottom:0;">
            <div style="width:76px; height:76px; border-radius:50%; background:linear-gradient(135deg,#2563EB,#7C3AED);
                        display:flex; align-items:center; justify-content:center; margin:0 auto 14px;
                        font-size:30px; font-weight:800; color:#fff; box-shadow:0 4px 14px rgba(37,99,235,0.35);">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div style="font-size:16px; font-weight:800; color:#1E293B; margin-bottom:3px;">{{ $user->name }}</div>
            <div style="font-size:12px; color:#64748B; margin-bottom:12px;">{{ $user->email }}</div>
            @if($user->isPremium())
                <span style="display:inline-flex; align-items:center; gap:5px; background:linear-gradient(135deg,#FEF3C7,#FDE68A); color:#92400E; font-size:11px; font-weight:700; padding:4px 12px; border-radius:20px; border:1px solid #FCD34D;">
                    <i class="fas fa-crown" style="font-size:10px; color:#F59E0B;"></i> Member Premium
                </span>
            @else
                <span style="display:inline-flex; align-items:center; gap:5px; background:#EFF6FF; color:#2563EB; font-size:11px; font-weight:700; padding:4px 12px; border-radius:20px; border:1px solid #BFDBFE;">
                    Akun Gratis
                </span>
            @endif

            @if($user->grade)
                <div style="margin-top:10px; font-size:12px; color:#64748B;">
                    <i class="fas fa-graduation-cap" style="margin-right:4px;"></i>{{ $user->grade }}
                </div>
            @endif
            @if($user->school)
                <div style="margin-top:4px; font-size:11px; color:#94A3B8;">{{ Str::limit($user->school, 30) }}</div>
            @endif
        </div>

        {{-- Status Langganan --}}
        <div class="settings-section" style="margin-bottom:0;">
            <div style="font-size:13px; font-weight:700; color:#1E293B; margin-bottom:14px;">
                <i class="fas fa-credit-card" style="color:var(--primary); margin-right:6px;"></i>Status Langganan
            </div>

            @if($activeSubscription)
                <div style="background:linear-gradient(135deg,#ECFDF5,#D1FAE5); border:1px solid #6EE7B7; border-radius:10px; padding:14px; margin-bottom:12px;">
                    <div style="display:flex; align-items:center; gap:6px; margin-bottom:8px;">
                        <div style="width:8px; height:8px; border-radius:50%; background:#10B981;"></div>
                        <span style="font-size:12px; font-weight:700; color:#065F46;">Aktif — {{ $activeSubscription->plan->name ?? 'Premium' }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:11px; color:#047857; margin-bottom:4px;">
                        <span>Berlaku hingga</span>
                        <strong>{{ $activeSubscription->expired_at->format('d M Y') }}</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; font-size:11px; color:#047857;">
                        <span>Sisa waktu</span>
                        <strong>{{ $activeSubscription->daysRemaining() }} hari</strong>
                    </div>
                    {{-- Progress bar sisa hari --}}
                    @php
                        $totalDays = $activeSubscription->plan->duration_months * 30;
                        $remainPct = min(100, round(($activeSubscription->daysRemaining() / $totalDays) * 100));
                    @endphp
                    <div style="margin-top:10px; height:4px; background:rgba(255,255,255,0.5); border-radius:99px; overflow:hidden;">
                        <div style="height:100%; width:{{ $remainPct }}%; background:#10B981; border-radius:99px;"></div>
                    </div>
                </div>
                <a href="{{ route('upgrade.index') }}"
                   style="display:flex; align-items:center; justify-content:center; gap:6px; padding:9px; background:#fff; color:#475569; border:1.5px solid #E2E8F0; border-radius:8px; font-size:12px; font-weight:700; text-decoration:none; transition:border-color 0.15s;"
                   onmouseover="this.style.borderColor='var(--primary)'; this.style.color='var(--primary)'"
                   onmouseout="this.style.borderColor='#E2E8F0'; this.style.color='#475569'">
                    <i class="fas fa-rotate-right"></i> Perpanjang
                </a>
            @else
                <div style="text-align:center; padding:16px 8px;">
                    <div style="width:44px; height:44px; border-radius:50%; background:#FEF3C7; display:flex; align-items:center; justify-content:center; margin:0 auto 10px;">
                        <i class="fas fa-crown" style="color:#F59E0B; font-size:20px;"></i>
                    </div>
                    <p style="font-size:12px; color:#64748B; margin-bottom:14px; line-height:1.5;">
                        Upgrade Premium untuk akses semua kelas, tryout, dan kelas online tanpa batas.
                    </p>
                    <a href="{{ route('upgrade.index') }}"
                       style="display:flex; align-items:center; justify-content:center; gap:6px; padding:10px; background:linear-gradient(135deg,#F59E0B,#EF4444); color:#fff; border-radius:8px; font-size:12px; font-weight:700; text-decoration:none;">
                        <i class="fas fa-crown"></i> Upgrade Premium
                    </a>
                </div>
            @endif
        </div>

    </div>

</div>

@endsection
