@extends('layouts.student')
@section('title', 'Bank Soal')
@php $subtitle = 'Latihan soal dari semua tryout yang tersedia.'; @endphp

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
    <div>
        <h2 style="font-size:22px; font-weight:800;">Bank Soal</h2>
        <p style="font-size:13px; color:var(--text-muted); margin-top:2px;">Latihan soal dari semua tryout yang tersedia.</p>
    </div>
</div>

{{-- Filter --}}
<form method="GET" style="display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap; align-items:center;">
    <div style="display:flex; gap:4px; background:#fff; border:1px solid var(--border); border-radius:10px; padding:4px;">
        @foreach(['semua'=>'Semua','disimpan'=>'Disimpan'] as $val=>$label)
        <button type="submit" name="filter" value="{{ $val }}"
            style="padding:7px 14px; border-radius:7px; font-size:13px; font-weight:600; border:none; cursor:pointer; font-family:inherit;
                   {{ $filter===$val ? 'background:var(--primary);color:#fff;' : 'background:transparent;color:var(--text-muted);' }}">
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
    <select name="difficulty" onchange="this.form.submit()" style="padding:8px 12px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; background:#fff; outline:none;">
        <option value="">Semua Tingkat</option>
        @foreach(['mudah'=>'Mudah','sedang'=>'Sedang','sulit'=>'Sulit'] as $val=>$label)
            <option value="{{ $val }}" {{ $difficulty===$val ? 'selected':'' }}>{{ $label }}</option>
        @endforeach
    </select>
</form>

{{-- Stats --}}
<div style="display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap;">
    <div class="card" style="padding:12px 18px; display:flex; align-items:center; gap:10px;">
        <i class="fas fa-list" style="color:var(--primary);"></i>
        <span style="font-size:13px; font-weight:700;">{{ $questions->total() }} soal</span>
    </div>
    <div class="card" style="padding:12px 18px; display:flex; align-items:center; gap:10px;">
        <i class="fas fa-bookmark" style="color:#F59E0B;"></i>
        <span style="font-size:13px; font-weight:700;">{{ $savedIds->count() }} disimpan</span>
    </div>
</div>

@if($questions->isEmpty())
    <div class="card" style="text-align:center; padding:60px; color:var(--text-muted);">
        <i class="fas fa-database" style="font-size:40px; opacity:0.2; display:block; margin-bottom:12px;"></i>
        <p style="font-size:14px; font-weight:600;">Belum ada soal tersedia.</p>
    </div>
@else
    <div style="display:flex; flex-direction:column; gap:12px;">
        @foreach($questions as $q)
        @php $saved = $savedIds->contains($q->id); @endphp
        <div class="card" style="padding:18px 20px;" x-data="{ showAnswer: false }">
            <div style="display:flex; align-items:flex-start; gap:14px;">
                {{-- Nomor --}}
                <div style="width:32px; height:32px; border-radius:8px; background:var(--primary); display:flex; align-items:center; justify-content:center; color:#fff; font-size:12px; font-weight:800; flex-shrink:0;">
                    {{ ($questions->currentPage()-1)*$questions->perPage() + $loop->iteration }}
                </div>
                <div style="flex:1; min-width:0;">
                    {{-- Meta --}}
                    <div style="display:flex; gap:6px; margin-bottom:8px; flex-wrap:wrap;">
                        @if($q->subject)
                            <span class="badge badge-primary" style="font-size:10px;">{{ $q->subject->name }}</span>
                        @endif
                        <span class="badge {{ match($q->difficulty) { 'mudah'=>'badge-success','sedang'=>'badge-warning',default=>'badge-danger' } }}" style="font-size:10px;">
                            {{ ucfirst($q->difficulty) }}
                        </span>
                        @if($q->tryout)
                            <span style="font-size:10px; color:var(--text-muted);">{{ $q->tryout->title }}</span>
                        @endif
                    </div>

                    {{-- Soal --}}
                    <div style="font-size:13.5px; line-height:1.6; margin-bottom:12px;">{{ $q->question_text }}</div>

                    {{-- Opsi --}}
                    <div style="display:flex; flex-direction:column; gap:6px; margin-bottom:12px;">
                        @foreach($q->options() as $key => $opt)
                        <div style="display:flex; align-items:flex-start; gap:8px; padding:8px 12px; border-radius:7px;
                                    background:{{ $key===$q->correct_answer ? '#ECFDF5' : '#F8FAFC' }};
                                    border:1px solid {{ $key===$q->correct_answer ? '#6EE7B7' : 'var(--border)' }};">
                            <span style="font-weight:800; font-size:13px; color:{{ $key===$q->correct_answer ? 'var(--success)' : 'var(--text-muted)' }}; min-width:16px;">{{ strtoupper($key) }}.</span>
                            <span style="font-size:13px; line-height:1.5;">{{ $opt }}</span>
                            @if($key===$q->correct_answer)
                                <i class="fas fa-check-circle" style="color:var(--success); margin-left:auto; flex-shrink:0;"></i>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    {{-- Pembahasan toggle --}}
                    @if($q->explanation)
                    <div>
                        <button @click="showAnswer=!showAnswer"
                                style="font-size:12px; color:var(--primary); font-weight:600; background:none; border:none; cursor:pointer; padding:0; font-family:inherit; display:flex; align-items:center; gap:5px;">
                            <i class="fas fa-lightbulb"></i>
                            <span x-text="showAnswer ? 'Sembunyikan Pembahasan' : 'Lihat Pembahasan'">Lihat Pembahasan</span>
                        </button>
                        <div x-show="showAnswer" style="margin-top:8px; padding:12px; background:#FFFBEB; border:1px solid #FCD34D; border-radius:8px; font-size:13px; color:#92400E; line-height:1.6;">
                            {{ $q->explanation }}
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Simpan --}}
                <form method="POST" action="{{ route('student.bank.toggle-save', $q->id) }}" style="flex-shrink:0;">
                    @csrf
                    <button type="submit" title="{{ $saved ? 'Hapus simpanan':'Simpan soal' }}"
                            style="width:34px; height:34px; border-radius:8px; border:1px solid var(--border); background:{{ $saved ? '#FFFBEB':' #fff' }}; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all 0.15s;">
                        <i class="fas fa-bookmark" style="font-size:14px; color:{{ $saved ? '#F59E0B':'var(--text-muted)' }};"></i>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <div style="margin-top:20px;">{{ $questions->appends(request()->query())->links() }}</div>
@endif

@endsection
