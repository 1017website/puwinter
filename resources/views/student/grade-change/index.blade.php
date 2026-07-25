@extends('layouts.student')
@section('title', 'Pindah Kelas')
@php $subtitle = 'Ajukan permintaan pindah kelas ke admin.'; @endphp

@section('content')

<div style="max-width:720px;">

    @if(session('success'))
        <div style="background:#ECFDF5; border:1px solid #A7F3D0; color:#065F46; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:13px;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background:#FEF2F2; border:1px solid #FECACA; color:#991B1B; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:13px;">
            {{ session('error') }}
        </div>
    @endif

    {{-- Kelas saat ini --}}
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:24px; margin-bottom:16px;">
        <div style="font-size:12px; font-weight:600; color:#64748B; text-transform:uppercase; letter-spacing:0.4px; margin-bottom:6px;">Kelas Saat Ini</div>
        <div style="font-size:20px; font-weight:800; color:#1E293B;">
            {{ auth()->user()->grade?->name ?? 'Belum diatur' }}
        </div>
    </div>

    {{-- Form pengajuan --}}
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:24px; margin-bottom:16px;">
        <div style="font-size:14px; font-weight:700; color:#1E293B; margin-bottom:18px;">Ajukan Pindah Kelas</div>

        @if($pending)
            <div style="background:#FFFBEB; border:1px solid #FDE68A; color:#92400E; padding:14px 16px; border-radius:10px; font-size:13px;">
                <i class="fas fa-clock"></i>
                Kamu masih punya permintaan yang sedang diproses admin. Tunggu hingga selesai sebelum mengajukan lagi.
            </div>
        @else
            <form method="POST" action="{{ route('student.grade-change.store') }}">
                @csrf
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:12px; font-weight:600; color:#64748B; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.4px;">Kelas Tujuan</label>
                    <select name="to_grade_id" required
                            style="width:100%; padding:10px 14px; border:1.5px solid #E2E8F0; border-radius:8px; font-size:13.5px; background:#fff; box-sizing:border-box;">
                        <option value="">Pilih kelas tujuan</option>
                        @foreach($grades as $g)
                            <option value="{{ $g->id }}" {{ old('to_grade_id') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                        @endforeach
                    </select>
                    @error('to_grade_id') <div style="font-size:11px; color:#EF4444; margin-top:4px;">{{ $message }}</div> @enderror
                </div>
                <div style="margin-bottom:18px;">
                    <label style="display:block; font-size:12px; font-weight:600; color:#64748B; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.4px;">Alasan <span style="font-weight:400; text-transform:none;">(opsional)</span></label>
                    <textarea name="reason" rows="3" placeholder="Contoh: naik kelas / salah pilih saat daftar"
                              style="width:100%; padding:10px 14px; border:1.5px solid #E2E8F0; border-radius:8px; font-size:13.5px; font-family:inherit; box-sizing:border-box;">{{ old('reason') }}</textarea>
                    @error('reason') <div style="font-size:11px; color:#EF4444; margin-top:4px;">{{ $message }}</div> @enderror
                </div>
                <button type="submit"
                        style="background:var(--primary); color:#fff; border:none; padding:11px 20px; border-radius:9px; font-size:13.5px; font-weight:600; cursor:pointer;">
                    <i class="fas fa-paper-plane"></i> Kirim Permintaan
                </button>
            </form>
        @endif
    </div>

    {{-- Riwayat --}}
    <div style="background:#fff; border:1px solid #E2E8F0; border-radius:14px; padding:24px;">
        <div style="font-size:14px; font-weight:700; color:#1E293B; margin-bottom:16px;">Riwayat Permintaan</div>
        @forelse($history as $req)
            <div class="student-program-row" style="display:flex; justify-content:space-between; align-items:center; padding:12px 0; border-bottom:1px solid #F1F5F9; gap:10px;">
                <div>
                    <div style="font-size:13.5px; font-weight:600; color:#1E293B;">
                        {{ $req->fromGrade->name ?? '—' }} <i class="fas fa-arrow-right" style="font-size:10px; color:#94A3B8; margin:0 4px;"></i> {{ $req->toGrade->name ?? '—' }}
                    </div>
                    <div style="font-size:11px; color:#94A3B8;">{{ $req->created_at->format('d M Y H:i') }}</div>
                    @if($req->admin_note)
                        <div style="font-size:11.5px; color:#64748B; margin-top:3px;">Catatan: {{ $req->admin_note }}</div>
                    @endif
                </div>
                @php
                    $map = ['pending'=>['#FFFBEB','#92400E','Menunggu'],'approved'=>['#ECFDF5','#065F46','Disetujui'],'rejected'=>['#FEF2F2','#991B1B','Ditolak']];
                    [$bg,$fg,$lbl] = $map[$req->status];
                @endphp
                <span style="background:{{ $bg }}; color:{{ $fg }}; font-size:11px; font-weight:600; padding:4px 10px; border-radius:999px;">{{ $lbl }}</span>
            </div>
        @empty
            <div style="text-align:center; color:#94A3B8; padding:30px; font-size:13px;">Belum ada permintaan pindah kelas.</div>
        @endforelse
    </div>

</div>

@endsection
