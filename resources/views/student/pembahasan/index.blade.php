@extends('layouts.student')
@section('title', 'Pembahasan')
@php $subtitle = 'Review semua soal yang pernah kamu kerjakan.'; @endphp

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
    <div>
        <h2 style="font-size:22px; font-weight:800;">Pembahasan</h2>
        <p style="font-size:13px; color:var(--text-muted); margin-top:2px;">Review semua soal yang pernah kamu kerjakan.</p>
    </div>
</div>

{{-- Stats --}}
<div class="stats-row cols-4" style="margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-list-check"></i></div>
        <div><div class="stat-value">{{ $stats['total'] }}</div><div class="stat-label">Total Soal Dikerjakan</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div><div class="stat-value">{{ $stats['benar'] }}</div><div class="stat-label">Benar</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow"><i class="fas fa-star-half-stroke"></i></div>
        <div><div class="stat-value">{{ $stats['partial'] ?? 0 }}</div><div class="stat-label">Partial</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#FEF2F2; color:#EF4444;"><i class="fas fa-times-circle"></i></div>
        <div><div class="stat-value">{{ $stats['salah'] }}</div><div class="stat-label">Salah</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow"><i class="fas fa-minus-circle"></i></div>
        <div><div class="stat-value">{{ $stats['kosong'] }}</div><div class="stat-label">Tidak Dijawab</div></div>
    </div>
</div>

{{-- Filter --}}
<form method="GET" style="display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap; align-items:center;">
    <div style="display:flex; gap:4px; background:#fff; border:1px solid var(--border); border-radius:10px; padding:4px;">
        @foreach(['semua'=>'Semua','benar'=>'Benar','salah'=>'Salah'] as $val=>$label)
        <button type="submit" name="filter" value="{{ $val }}"
            style="padding:7px 14px; border-radius:7px; font-size:13px; font-weight:600; border:none; cursor:pointer; font-family:inherit;
                   {{ $filter===$val ? 'background:var(--primary);color:#fff;':'background:transparent;color:var(--text-muted);' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>
    <select name="subject_id" onchange="this.form.submit()" style="padding:8px 12px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; background:#fff; outline:none;">
        <option value="">Semua Mapel</option>
        @foreach($subjects as $s)
            <option value="{{ $s->id }}" {{ $subjectId==$s->id ? 'selected':'' }}>{{ $s->name }}</option>
        @endforeach
    </select>
</form>

@if($paginator->isEmpty())
    <div class="card" style="text-align:center; padding:60px; color:var(--text-muted);">
        <i class="fas fa-lightbulb" style="font-size:40px; opacity:0.2; display:block; margin-bottom:12px;"></i>
        <p style="font-size:14px; font-weight:600;">Belum ada soal yang dikerjakan.</p>
        <p style="font-size:12px; margin-top:4px;">Selesaikan tryout untuk melihat pembahasan di sini.</p>
        <a href="{{ route('student.tryout.index') }}" class="btn btn-primary" style="margin-top:16px; display:inline-flex;">
            Mulai Tryout
        </a>
    </div>
@else
    <div style="display:flex; flex-direction:column; gap:12px;">
        @foreach($paginator as $item)
        @php
            $q          = $item['question'];
            $userAnswer = $item['user_answer'];
            $isCorrect  = $item['is_correct'];
            $status     = $item['status'] ?? ($isCorrect ? 'correct' : ($userAnswer ? 'wrong' : 'empty'));
            $score      = (float) ($item['score'] ?? 0);
            $correctKeys = $item['correct_keys'] ?? $q->correctKeys();
            $attempt    = $item['attempt'];
            $formatAnswer = function ($answer) {
                if (is_array($answer)) {
                    $items = array_filter(array_map(fn($v) => strtoupper((string) $v), $answer));
                    return count($items) ? implode(', ', $items) : '—';
                }
                return $answer ? strtoupper((string) $answer) : '—';
            };
            $answerLabel = $formatAnswer($userAnswer);
            $correctLabel = $formatAnswer($correctKeys);
        @endphp
        <div class="card" style="padding:18px 20px; border-left:4px solid {{ $status === 'correct' ? 'var(--success)' : ($status === 'partial' ? 'var(--warning)' : ($status === 'wrong' ? 'var(--danger)' : 'var(--border)')) }};" x-data="{ showExplain: false }">
            <div style="display:flex; align-items:flex-start; gap:14px;">
                <div style="width:30px; height:30px; border-radius:50%; background:{{ $status === 'correct' ? '#ECFDF5' : ($status === 'partial' ? '#FFFBEB' : ($status === 'wrong' ? '#FEF2F2' : '#F1F5F9')) }}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    @if($status === 'correct')
                        <i class="fas fa-check" style="font-size:13px; color:var(--success);"></i>
                    @elseif($status === 'partial')
                        <i class="fas fa-star-half-stroke" style="font-size:13px; color:var(--warning);"></i>
                    @elseif($status === 'wrong')
                        <i class="fas fa-times" style="font-size:13px; color:var(--danger);"></i>
                    @else
                        <i class="fas fa-minus" style="font-size:13px; color:var(--text-muted);"></i>
                    @endif
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="display:flex; gap:6px; margin-bottom:8px; flex-wrap:wrap;">
                        @if($q->subject)
                            <span class="badge badge-primary" style="font-size:10px;">{{ $q->subject->name }}</span>
                        @endif
                        <span class="badge {{ match($q->difficulty) { 'mudah'=>'badge-success','sedang'=>'badge-warning',default=>'badge-danger' } }}" style="font-size:10px;">{{ ucfirst($q->difficulty) }}</span>
                        <span style="font-size:10px; color:var(--text-muted);">{{ $attempt->tryout->title ?? '' }}</span>
                    </div>
                    <div style="font-size:13.5px; line-height:1.6; margin-bottom:10px;">{{ $q->question_text }}</div>
                    <div style="display:flex; gap:16px; font-size:12px; flex-wrap:wrap;">
                        <span>Jawaban kamu: <strong style="color:{{ $status === 'correct' ? 'var(--success)' : ($status === 'partial' ? 'var(--warning)' : 'var(--danger)') }};">{{ $answerLabel }}</strong></span>
                        <span>Jawaban benar: <strong style="color:var(--success);">{{ $correctLabel }}</strong></span>
                        <span>Nilai: <strong>{{ rtrim(rtrim(number_format($score, 2, '.', ''), '0'), '.') }}/1</strong></span>
                    </div>
                    @if($q->explanation)
                    <div style="margin-top:10px;">
                        <button @click="showExplain=!showExplain"
                                style="font-size:12px; color:var(--primary); font-weight:600; background:none; border:none; cursor:pointer; padding:0; font-family:inherit; display:flex; align-items:center; gap:5px;">
                            <i class="fas fa-lightbulb"></i>
                            <span x-text="showExplain ? 'Sembunyikan':'Lihat Pembahasan'">Lihat Pembahasan</span>
                        </button>
                        <div x-show="showExplain" style="margin-top:8px; padding:12px; background:#FFFBEB; border:1px solid #FCD34D; border-radius:8px; font-size:13px; color:#92400E; line-height:1.6;">
                            {{ $q->explanation }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div style="margin-top:20px;">{{ $paginator->appends(request()->query())->links() }}</div>
@endif

@endsection
