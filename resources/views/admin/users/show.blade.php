@extends('admin.layouts.app')
@section('title', 'Detail User — '.$user->name)

@section('content')

<div class="page-header">
    <div>
        <h2>{{ $user->name }}</h2>
        <p>Detail lengkap akun dan aktivitas user.</p>
    </div>
    <div style="display:flex; gap:8px;">
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
            <i class="fas fa-pen"></i> Edit User
        </a>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div style="display:grid; grid-template-columns:320px 1fr; gap:20px; align-items:start;">

    {{-- LEFT: Profil --}}
    <div>
        {{-- Kartu profil --}}
        <div class="card" style="margin-bottom:16px; text-align:center;">
            <div style="width:72px; height:72px; border-radius:50%; background:var(--primary); display:flex; align-items:center; justify-content:center; color:#fff; font-size:28px; font-weight:800; margin:0 auto 14px;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div style="font-size:17px; font-weight:800; margin-bottom:4px;">{{ $user->name }}</div>
            <div style="font-size:13px; color:var(--muted); margin-bottom:10px;">{{ $user->email }}</div>
            <div style="display:flex; justify-content:center; gap:8px; flex-wrap:wrap;">
                <span class="badge {{ match($user->role) { 'superadmin'=>'badge-danger','admin'=>'badge-warning','mentor'=>'badge-primary',default=>'badge-gray' } }}">
                    {{ ucfirst($user->role) }}
                </span>
                @if($user->isPremium())
                    <span class="badge badge-success"><i class="fas fa-crown" style="font-size:9px;"></i> Premium</span>
                @endif
                <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }}">
                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
        </div>

        {{-- Info detail --}}
        <div class="card" style="margin-bottom:16px;">
            <div style="font-size:13px; font-weight:700; margin-bottom:12px;">Informasi Akun</div>
            @foreach([
                ['label'=>'Sekolah',       'value'=>$user->school ?? '-'],
                ['label'=>'Kota',          'value'=>$user->city ?? '-'],
                ['label'=>'Provinsi',      'value'=>$user->province ?? '-'],
                ['label'=>'Kelas',         'value'=>$user->grade ?? '-'],
                ['label'=>'No. HP',        'value'=>$user->phone ?? '-'],
                ['label'=>'Tgl Lahir',     'value'=>$user->birth_date?->format('d M Y') ?? '-'],
                ['label'=>'Bergabung',     'value'=>$user->created_at->format('d M Y')],
                ['label'=>'Login Terakhir','value'=>$user->last_login_at?->diffForHumans() ?? 'Belum pernah'],
                ['label'=>'Email Verified','value'=>$user->email_verified_at ? 'Terverifikasi' : 'Belum'],
            ] as $info)
            <div style="display:flex; justify-content:space-between; padding:7px 0; border-bottom:1px solid var(--border); font-size:12.5px;">
                <span style="color:var(--muted);">{{ $info['label'] }}</span>
                <strong style="text-align:right; max-width:60%;">{{ $info['value'] }}</strong>
            </div>
            @endforeach
        </div>

        {{-- Statistik --}}
        <div class="card" style="margin-bottom:16px;">
            <div style="font-size:13px; font-weight:700; margin-bottom:12px;">Statistik Belajar</div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                @foreach([
                    ['label'=>'Kelas Diikuti',   'value'=>$user->enrollments->count()],
                    ['label'=>'Tryout Dikerjakan','value'=>$user->tryoutAttempts->count()],
                    ['label'=>'Badge Diraih',     'value'=>$user->achievements->count()],
                    ['label'=>'Langganan',        'value'=>$user->subscriptions->count()],
                ] as $s)
                <div style="background:var(--bg); border-radius:8px; padding:12px; text-align:center;">
                    <div style="font-size:20px; font-weight:800; color:var(--text);">{{ $s['value'] }}</div>
                    <div style="font-size:10.5px; color:var(--muted); margin-top:2px;">{{ $s['label'] }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Grant Premium --}}
        @if(!$user->isPremium() || $user->role === 'student')
        <div class="card" style="background:#FFFBEB; border-color:#FCD34D;">
            <div style="font-size:13px; font-weight:700; margin-bottom:10px; color:#92400E;">
                <i class="fas fa-crown"></i> Grant Premium Manual
            </div>
            <form method="POST" action="{{ route('admin.users.grant-premium', $user) }}">
                @csrf
                <select name="plan_id" class="form-control" style="margin-bottom:8px; font-size:12.5px;">
                    @foreach(\App\Models\SubscriptionPlan::active()->get() as $plan)
                    <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm" style="background:#F59E0B; color:#fff; width:100%; justify-content:center;">
                    <i class="fas fa-crown"></i> Berikan Premium
                </button>
            </form>
        </div>
        @endif
    </div>

    {{-- RIGHT: Aktivitas --}}
    <div>

        {{-- Subscription history --}}
        <div class="card" style="margin-bottom:16px;">
            <div style="font-size:14px; font-weight:700; margin-bottom:14px;">Riwayat Langganan</div>
            @if($user->subscriptions->isEmpty())
                <div style="text-align:center; padding:20px; color:var(--muted); font-size:13px;">Belum pernah berlangganan.</div>
            @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Program</th>
                            <th>Nominal</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th>Berlaku</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user->subscriptions->sortByDesc('created_at') as $sub)
                        <tr>
                            <td style="font-weight:600; font-size:13px;">{{ $sub->plan->name ?? '-' }}</td>
                            <td>Rp {{ number_format($sub->amount_paid, 0, ',', '.') }}</td>
                            <td style="font-size:12px; text-transform:uppercase; color:var(--muted);">
                                {{ str_replace('_', ' ', $sub->payment_method ?? '-') }}
                            </td>
                            <td>
                                <span class="badge {{ match($sub->status) { 'active'=>'badge-success','pending'=>'badge-warning',default=>'badge-danger' } }}">
                                    {{ ucfirst($sub->status) }}
                                </span>
                            </td>
                            <td style="font-size:12px;">
                                @if($sub->expired_at)
                                    {{ $sub->started_at?->format('d M Y') }} — {{ $sub->expired_at->format('d M Y') }}
                                @else — @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- Kelas yang diikuti --}}
        <div class="card" style="margin-bottom:16px;">
            <div style="font-size:14px; font-weight:700; margin-bottom:14px;">Kelas yang Diikuti</div>
            @if($user->enrollments->isEmpty())
                <div style="text-align:center; padding:20px; color:var(--muted); font-size:13px;">Belum mengikuti kelas.</div>
            @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Kelas</th><th>Progress</th><th>Terakhir Akses</th></tr>
                    </thead>
                    <tbody>
                        @foreach($user->enrollments->sortByDesc('last_accessed_at') as $enrollment)
                        <tr>
                            <td>
                                <div style="font-weight:600; font-size:13px;">{{ $enrollment->course->title ?? '-' }}</div>
                                <div style="font-size:11px; color:var(--muted);">{{ $enrollment->course->subject->name ?? '' }}</div>
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <div style="flex:1; height:6px; background:#E2E8F0; border-radius:99px; overflow:hidden; min-width:80px;">
                                        <div style="height:100%; width:{{ $enrollment->progress_percentage }}%; background:var(--primary); border-radius:99px;"></div>
                                    </div>
                                    <span style="font-size:11px; font-weight:600; color:var(--muted);">{{ $enrollment->progress_percentage }}%</span>
                                </div>
                            </td>
                            <td style="font-size:12px; color:var(--muted);">
                                {{ $enrollment->last_accessed_at?->diffForHumans() ?? '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- Riwayat tryout --}}
        <div class="card">
            <div style="font-size:14px; font-weight:700; margin-bottom:14px;">Riwayat Tryout</div>
            @if($user->tryoutAttempts->isEmpty())
                <div style="text-align:center; padding:20px; color:var(--muted); font-size:13px;">Belum pernah mengerjakan tryout.</div>
            @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Tryout</th><th>Skor</th><th>Benar/Partial/Salah/Kosong</th><th>Peringkat</th><th>Waktu</th></tr>
                    </thead>
                    <tbody>
                        @foreach($user->tryoutAttempts->whereNotNull('submitted_at')->sortByDesc('submitted_at')->take(10) as $attempt)
                        <tr>
                            <td style="font-size:13px; font-weight:600;">{{ $attempt->tryout->title ?? '-' }}</td>
                            @php
                                $maxAttemptScore = max(1, (int) ($attempt->tryout->total_questions ?? 0));
                                $attemptPct = $maxAttemptScore > 0 ? min(100, max(0, (($attempt->score ?? 0) / $maxAttemptScore) * 100)) : 0;
                            @endphp
                            <td style="font-weight:800; color:{{ $attemptPct >= 70 ? 'var(--success)' : ($attemptPct >= 50 ? 'var(--warning)' : 'var(--danger)') }};">
                                {{ $attempt->formattedScore($maxAttemptScore) }}
                            </td>
                            <td style="font-size:12px;">
                                <span style="color:var(--success);">{{ $attempt->correct_count }}</span> /
                                <span style="color:var(--warning);">{{ $attempt->partialCount() }}</span> /
                                <span style="color:var(--danger);">{{ $attempt->wrong_count }}</span> /
                                <span style="color:var(--muted);">{{ $attempt->empty_count }}</span>
                            </td>
                            <td style="font-weight:700;">#{{ $attempt->rank_at_submit ?? '-' }}</td>
                            <td style="font-size:12px; color:var(--muted);">{{ $attempt->submitted_at?->format('d M Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

    </div>
</div>

@endsection
