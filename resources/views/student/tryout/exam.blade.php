<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $tryout->title }} — Puwinter</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #F8FAFC; min-height: 100vh; }

        /* TOPBAR */
        .exam-topbar {
            background: #0F172A;
            color: #fff;
            padding: 0 24px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .exam-title {
            font-size: 14px;
            font-weight: 700;
        }

        .exam-meta {
            font-size: 12px;
            color: #64748B;
            margin-top: 2px;
        }

        /* Timer */
        .timer-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.08);
            border-radius: 10px;
            padding: 8px 16px;
        }

        .timer-display {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 2px;
            font-variant-numeric: tabular-nums;
        }

        .timer-display.warning { color: #FBBF24; }
        .timer-display.danger  { color: #F87171; animation: blink 1s infinite; }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* LAYOUT */
        .exam-body {
            display: grid;
            grid-template-columns: 1fr 280px;
            gap: 0;
            min-height: calc(100vh - 60px);
        }

        /* LEFT — Question Area */
        .question-area {
            padding: 32px;
            overflow-y: auto;
        }

        .question-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .question-badge {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .q-number {
            width: 40px; height: 40px;
            background: #2563EB;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px;
            font-weight: 800;
            color: #fff;
        }

        .q-info { font-size: 12px; color: #64748B; }

        .q-difficulty {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .diff-mudah  { background: #ECFDF5; color: #059669; }
        .diff-sedang { background: #FFFBEB; color: #D97706; }
        .diff-sulit  { background: #FEF2F2; color: #DC2626; }

        .question-text {
            font-size: 16px;
            line-height: 1.75;
            color: #1E293B;
            margin-bottom: 28px;
            font-weight: 500;
        }

        .passage-card {
            background: #FFFFFF;
            border: 1.5px solid #BFDBFE;
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 22px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
        }

        .passage-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #EFF6FF;
            color: #1D4ED8;
            border-radius: 999px;
            padding: 5px 11px;
            font-size: 11px;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .passage-title {
            text-align: center;
            font-size: 16px;
            font-weight: 800;
            color: #0F172A;
            margin-bottom: 12px;
        }

        .passage-text {
            font-size: 14.5px;
            line-height: 1.8;
            color: #334155;
            white-space: pre-wrap;
        }

        .stimulus-image {
            display: block;
            max-width: 100%;
            height: auto;
            max-height: 560px;
            object-fit: contain;
            border-radius: 12px;
            border: 1px solid #E2E8F0;
            background: #fff;
            margin: 12px auto 16px;
        }

        .question-image-wrap {
            margin-bottom: 18px;
        }

        /* Answer Options */
        .options-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 32px;
        }

        .option-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 14px 18px;
            border: 1.5px solid #E2E8F0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.15s;
            background: #fff;
        }

        .option-item:hover {
            border-color: #2563EB;
            background: #EFF6FF;
        }

        .option-item.selected {
            border-color: #2563EB;
            background: #EFF6FF;
        }

        .option-item input[type="radio"] { display: none; }

        .option-key {
            width: 30px; height: 30px;
            border-radius: 50%;
            border: 2px solid #E2E8F0;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #64748B;
            flex-shrink: 0;
            transition: all 0.15s;
        }

        .option-item.selected .option-key {
            background: #2563EB;
            border-color: #2563EB;
            color: #fff;
        }

        .option-text {
            font-size: 14px;
            color: #1E293B;
            line-height: 1.6;
            padding-top: 4px;
        }

        .matrix-answer-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #D7E7FF;
            margin-bottom: 32px;
        }

        .matrix-answer-table th {
            background: #DBEAFE;
            color: #1E3A8A;
            border: 1px solid #93C5FD;
            padding: 14px 12px;
            font-size: 13px;
            text-align: center;
            font-weight: 800;
        }

        .matrix-answer-table th:first-child,
        .matrix-answer-table td:first-child { text-align: left; }

        .matrix-answer-table td {
            border: 1px solid #E2E8F0;
            padding: 14px 12px;
            font-size: 14px;
            color: #334155;
            vertical-align: middle;
        }

        .matrix-choice {
            width: 34px;
            height: 34px;
            border: 2px solid #CBD5E1;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .15s;
            background:#fff;
        }

        .matrix-choice:hover,
        .matrix-choice.selected {
            border-color: #2563EB;
            background: #EFF6FF;
        }

        .matrix-choice.selected::after {
            content: '';
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #2563EB;
            display: block;
        }

        /* Nav buttons */
        .question-nav-btns {
            display: flex;
            gap: 10px;
            justify-content: space-between;
        }

        .btn-nav {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            border: 1.5px solid #E2E8F0;
            background: #fff;
            color: #1E293B;
            transition: all 0.15s;
        }

        .btn-nav:hover { border-color: #2563EB; color: #2563EB; }

        .btn-nav-primary {
            background: #2563EB;
            border-color: #2563EB;
            color: #fff;
        }

        .btn-nav-primary:hover { background: #1D4ED8; border-color: #1D4ED8; color: #fff; }

        .btn-flag {
            background: transparent;
            border-color: #F59E0B;
            color: #F59E0B;
        }

        .btn-flag.flagged {
            background: #FEF3C7;
        }

        /* RIGHT — Sidebar Navigation */
        .exam-sidebar {
            background: #fff;
            border-left: 1px solid #E2E8F0;
            padding: 20px;
            overflow-y: auto;
            height: calc(100vh - 60px);
            position: sticky;
            top: 60px;
        }

        .sidebar-title {
            font-size: 13px;
            font-weight: 700;
            color: #1E293B;
            margin-bottom: 14px;
        }

        /* Number grid */
        .number-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 6px;
            margin-bottom: 20px;
        }

        .num-btn {
            aspect-ratio: 1;
            border-radius: 8px;
            border: 1.5px solid #E2E8F0;
            background: #fff;
            font-size: 12px;
            font-weight: 700;
            color: #64748B;
            cursor: pointer;
            transition: all 0.15s;
            display: flex; align-items: center; justify-content: center;
            font-family: inherit;
        }

        .num-btn:hover { border-color: #2563EB; color: #2563EB; }
        .num-btn.answered { background: #2563EB; border-color: #2563EB; color: #fff; }
        .num-btn.flagged  { background: #FEF3C7; border-color: #F59E0B; color: #D97706; }
        .num-btn.current  { box-shadow: 0 0 0 3px rgba(37,99,235,0.25); }

        /* Legend */
        .legend {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 20px;
            padding: 12px;
            background: #F8FAFC;
            border-radius: 8px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #64748B;
        }

        .legend-dot {
            width: 14px; height: 14px;
            border-radius: 4px;
            border: 1.5px solid #E2E8F0;
            flex-shrink: 0;
        }

        /* Progress summary */
        .exam-summary {
            padding: 12px;
            background: #F8FAFC;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 12px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            color: #64748B;
        }

        .summary-row strong { color: #1E293B; }

        /* Submit button */
        .btn-submit-exam {
            width: 100%;
            padding: 13px;
            background: #10B981;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit-exam:hover { background: #059669; }

        /* Modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(4px);
            z-index: 200;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
        }

        .modal-overlay.show {
            opacity: 1;
            pointer-events: all;
        }

        .modal-box {
            background: #fff;
            border-radius: 16px;
            padding: 32px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            transform: scale(0.95);
            transition: transform 0.2s;
        }

        .modal-overlay.show .modal-box { transform: scale(1); }
    </style>
</head>
<body>

    {{-- Banner peringatan saat siswa meninggalkan jendela tryout --}}
    <div id="leave-warning" style="display:none; position:fixed; top:0; left:0; right:0; z-index:9999;
         background:#DC2626; color:#fff; padding:12px 20px; text-align:center; font-size:14px; font-weight:600;
         box-shadow:0 2px 12px rgba(0,0,0,0.25);">
        <i class="fas fa-triangle-exclamation" style="margin-right:6px;"></i>
        Kamu meninggalkan jendela tryout! Pelanggaran tercatat (<span id="leave-count">0</span>x).
        Tetaplah di halaman ini selama mengerjakan.
        <button onclick="document.getElementById('leave-warning').style.display='none';"
                style="margin-left:12px; background:rgba(255,255,255,0.2); border:none; color:#fff;
                       padding:4px 12px; border-radius:6px; cursor:pointer; font-weight:700;">Mengerti</button>
    </div>

{{-- ======================================================================== --}}
{{-- EXAM TOPBAR                                                                --}}
{{-- ======================================================================== --}}
<div class="exam-topbar">
    <div>
        <div class="exam-title">{{ $tryout->title }}</div>
        <div class="exam-meta">{{ $tryout->subject->name ?? 'Semua Mapel' }} · {{ $tryout->total_questions }} Soal</div>
    </div>

    <div class="timer-box">
        <i class="fas fa-clock" style="color:#64748B; font-size:16px;"></i>
        <div class="timer-display" id="timer">
            {{ sprintf('%02d:%02d', intdiv($tryout->duration_minutes, 60), $tryout->duration_minutes % 60) }}:00
        </div>
    </div>

    <div style="display:flex; align-items:center; gap:12px;">
        <div style="font-size:12px; color:#64748B;">
            Attempt #{{ $attempt->id }}
        </div>
        <button onclick="showSubmitModal()"
            style="padding:8px 16px; background:#EF4444; color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:700; font-family:inherit; cursor:pointer;">
            <i class="fas fa-paper-plane"></i> Submit
        </button>
    </div>
</div>

{{-- ======================================================================== --}}
{{-- EXAM BODY                                                                  --}}
{{-- ======================================================================== --}}
<div class="exam-body">

    {{-- QUESTION AREA --}}
    <div class="question-area" id="question-area">
        @foreach($tryout->questions as $index => $question)
        <div class="question-wrapper" id="q-{{ $question->id }}" style="{{ $index === 0 ? '' : 'display:none;' }}">

            <div class="question-header">
                <div class="question-badge">
                    <div class="q-number">{{ $index + 1 }}</div>
                    <div>
                        <div style="font-size:14px; font-weight:700; color:#1E293B;">Soal {{ $index + 1 }} dari {{ $tryout->total_questions }}</div>
                        <div class="q-info">{{ $question->subject->name ?? '' }}</div>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <span class="q-difficulty diff-{{ $question->difficulty }}">
                        {{ ucfirst($question->difficulty) }}
                    </span>
                    <button class="btn-nav btn-flag" id="flag-btn-{{ $question->id }}"
                            onclick="toggleFlag({{ $question->id }})">
                        <i class="fas fa-flag"></i> Tandai
                    </button>
                </div>
            </div>

            @if($question->passage)
                <div class="passage-card">
                    <div class="passage-label"><i class="fas fa-book-open"></i> Soal Cerita / Stimulus</div>
                    @if($question->passage->title)
                        <div class="passage-title">{{ $question->passage->title }}</div>
                    @endif
                    @if($question->passage->passage_image)
                        <img src="{{ asset($question->passage->passage_image) }}" alt="Gambar stimulus" class="stimulus-image">
                    @endif
                    @if($question->passage->passage_text)
                        <div class="passage-text">{{ $question->passage->passage_text }}</div>
                    @endif
                    @if($question->passage->source)
                        <div style="font-size:12px; color:#64748B; margin-top:12px;">Sumber: {{ $question->passage->source }}</div>
                    @endif
                </div>
            @endif

            @if($question->question_image)
                <div class="question-image-wrap">
                    <img src="{{ asset($question->question_image) }}" alt="Gambar soal" class="stimulus-image">
                </div>
            @endif

            {{-- Question Text --}}
            <div class="question-text">
                @if($question->isMatrix())
                    <span style="display:inline-block; background:#FEF3C7; color:#92400E; font-size:11px; font-weight:700; padding:3px 10px; border-radius:999px; margin-bottom:8px;">
                        <i class="fas fa-table-list"></i> Pilih satu kategori pada setiap baris
                    </span><br>
                @elseif($question->isMultiple())
                    <span style="display:inline-block; background:#EEF2FF; color:#4F46E5; font-size:11px; font-weight:700; padding:3px 10px; border-radius:999px; margin-bottom:8px;">
                        <i class="fas fa-list-check"></i> Pilih beberapa jawaban
                    </span><br>
                @endif
                {!! nl2br(e($question->question_text)) !!}
            </div>

            {{-- Options --}}
            @if($question->isMatrix())
                @php $columns = $question->matrixColumns(); @endphp
                <table class="matrix-answer-table">
                    <thead>
                        <tr>
                            <th style="width:56%;">Pernyataan / Teknik</th>
                            @foreach($columns as $columnLabel)
                                <th>{{ $columnLabel }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($question->options() as $rowKey => $rowText)
                            <tr>
                                <td>{{ $rowText }}</td>
                                @foreach($columns as $columnKey => $columnLabel)
                                    <td style="text-align:center;">
                                        <label class="matrix-choice"
                                               id="opt-{{ $question->id }}-{{ $rowKey }}-{{ $columnKey }}"
                                               data-matrix-option="{{ $question->id }}-{{ $rowKey }}"
                                               onclick="event.preventDefault(); selectMatrixAnswer({{ $question->id }}, '{{ $rowKey }}', '{{ $columnKey }}', this)">
                                            <input type="radio" name="answer_{{ $question->id }}[{{ $rowKey }}]" value="{{ $columnKey }}" style="display:none;">
                                        </label>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="options-list">
                    @foreach($question->options() as $key => $text)
                    <label class="option-item" id="opt-{{ $question->id }}-{{ $key }}"
                           onclick="event.preventDefault(); selectAnswer({{ $question->id }}, '{{ $key }}', this, {{ $question->isMultiple() ? 'true' : 'false' }})">
                        @if($question->isMultiple())
                            <input type="checkbox" name="answer_{{ $question->id }}[]" value="{{ $key }}" style="display:none;">
                        @else
                            <input type="radio" name="answer_{{ $question->id }}" value="{{ $key }}">
                        @endif
                        <div class="option-key">{{ strtoupper($key) }}</div>
                        <div class="option-text">{{ $text }}</div>
                    </label>
                    @endforeach
                </div>
            @endif

            {{-- Nav buttons --}}
            <div class="question-nav-btns">
                <button class="btn-nav" onclick="goToQuestion({{ $index - 1 }})"
                        {{ $index === 0 ? 'disabled style=opacity:0.4;cursor:not-allowed' : '' }}>
                    <i class="fas fa-arrow-left"></i> Sebelumnya
                </button>
                <div style="display:flex; gap:8px;">
                    @if($index < $tryout->total_questions - 1)
                    <button class="btn-nav btn-nav-primary" onclick="goToQuestion({{ $index + 1 }})">
                        Selanjutnya <i class="fas fa-arrow-right"></i>
                    </button>
                    @else
                    <button class="btn-nav btn-nav-primary" onclick="showSubmitModal()"
                            style="background:#10B981; border-color:#10B981;">
                        <i class="fas fa-paper-plane"></i> Selesai & Submit
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- SIDEBAR --}}
    <div class="exam-sidebar">
        <div class="sidebar-title">Navigasi Soal</div>

        {{-- Summary --}}
        <div class="exam-summary">
            <div class="summary-row">
                <span>Dijawab</span>
                <strong id="count-answered">0</strong>
            </div>
            <div class="summary-row">
                <span>Ditandai</span>
                <strong id="count-flagged" style="color:#D97706;">0</strong>
            </div>
            <div class="summary-row">
                <span>Belum dijawab</span>
                <strong id="count-empty" style="color:#EF4444;">{{ $tryout->total_questions }}</strong>
            </div>
        </div>

        {{-- Number grid --}}
        <div class="number-grid" id="number-grid">
            @foreach($tryout->questions as $index => $question)
            <button class="num-btn {{ $index === 0 ? 'current' : '' }}"
                    id="num-{{ $question->id }}"
                    onclick="goToQuestion({{ $index }})">
                {{ $index + 1 }}
            </button>
            @endforeach
        </div>

        {{-- Legend --}}
        <div class="legend">
            <div class="legend-item">
                <div class="legend-dot" style="background:#2563EB; border-color:#2563EB;"></div>
                Sudah dijawab
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background:#FEF3C7; border-color:#F59E0B;"></div>
                Ditandai
            </div>
            <div class="legend-item">
                <div class="legend-dot"></div>
                Belum dijawab
            </div>
        </div>

        <button class="btn-submit-exam" onclick="showSubmitModal()">
            <i class="fas fa-paper-plane"></i> Submit Jawaban
        </button>
    </div>
</div>

{{-- ======================================================================== --}}
{{-- SUBMIT MODAL                                                               --}}
{{-- ======================================================================== --}}
<div class="modal-overlay" id="submit-modal">
    <div class="modal-box">
        <div style="width:64px; height:64px; background:#ECFDF5; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
            <i class="fas fa-paper-plane" style="font-size:24px; color:#10B981;"></i>
        </div>
        <h3 style="font-size:18px; font-weight:800; color:#1E293B; margin-bottom:8px;">Submit Tryout?</h3>
        <p style="font-size:13.5px; color:#64748B; margin-bottom:8px;">
            Pastikan semua soal sudah dijawab sebelum submit.
        </p>
        <div style="background:#F8FAFC; border-radius:8px; padding:12px; margin-bottom:20px; text-align:left;">
            <div style="display:flex; justify-content:space-between; font-size:13px; padding:4px 0;">
                <span style="color:#64748B;">Dijawab</span>
                <strong id="modal-answered" style="color:#10B981;">0</strong>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:13px; padding:4px 0;">
                <span style="color:#64748B;">Belum dijawab</span>
                <strong id="modal-empty" style="color:#EF4444;">{{ $tryout->total_questions }}</strong>
            </div>
        </div>
        <div style="display:flex; gap:10px;">
            <button onclick="closeSubmitModal()"
                style="flex:1; padding:11px; border:1.5px solid #E2E8F0; border-radius:8px; font-size:13.5px; font-weight:600; font-family:inherit; cursor:pointer; background:#fff; color:#1E293B;">
                Kembali
            </button>
            <button onclick="submitExam()"
                style="flex:1; padding:11px; background:#10B981; color:#fff; border:none; border-radius:8px; font-size:13.5px; font-weight:700; font-family:inherit; cursor:pointer;">
                Ya, Submit!
            </button>
        </div>
    </div>
</div>

{{-- ======================================================================== --}}
{{-- SUBMIT FORM (hidden)                                                       --}}
{{-- ======================================================================== --}}
<form id="exam-form" method="POST" action="{{ route('student.tryout.submit', $attempt->id) }}" style="display:none;">
    @csrf
    <input type="hidden" name="tab_switch_count" id="tab-switch-input" value="0">
    <div id="answer-inputs"></div>
</form>

<script>
    // =========================================================================
    // STATE
    // =========================================================================
    const TOTAL        = {{ $tryout->total_questions }};
    const DURATION     = {{ $tryout->duration_minutes * 60 }}; // seconds
    const ATTEMPT_ID   = {{ $attempt->id }};
    const STORAGE_KEY  = 'puwinter_exam_{{ $attempt->id }}';

    const questions = @json($tryout->questions->pluck('id')->values());

    // Peta tipe soal: single | multiple | matrix.
    const questionTypes = @json($tryout->questions->mapWithKeys(fn($q) => [$q->id => $q->question_type ?? 'single']));

    let currentIndex = 0;
    let answers  = {}; // single: { qId: 'a' } | multiple: { qId: ['a','c'] } | matrix: { qId: {a:'col_1'} }
    let flagged  = {}; // { questionId: true }
    let timeLeft = DURATION;
    let leaveCount = 0; // jumlah kali meninggalkan jendela tryout

    // =========================================================================
    // INIT — restore from localStorage
    // =========================================================================
    function init() {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved) {
            const data = JSON.parse(saved);
            answers  = data.answers  || {};
            flagged  = data.flagged  || {};
            timeLeft = data.timeLeft || DURATION;
            leaveCount = data.leaveCount || 0;
        }
        // tampilkan counter awal bila ada
        const lc = document.getElementById('leave-count'); if (lc) lc.textContent = leaveCount;

        // Restore UI
        Object.entries(answers).forEach(([qId, val]) => {
            if (val && typeof val === 'object' && !Array.isArray(val)) {
                Object.entries(val).forEach(([rowKey, columnKey]) => {
                    document.getElementById(`opt-${qId}-${rowKey}-${columnKey}`)?.classList.add('selected');
                });
            } else {
                const vals = Array.isArray(val) ? val : [val];
                vals.forEach(v => {
                    document.getElementById(`opt-${qId}-${v}`)?.classList.add('selected');
                });
            }
            if (hasAnswer(qId)) {
                document.getElementById(`num-${qId}`)?.classList.add('answered');
            }
        });

        Object.keys(flagged).forEach(qId => {
            document.getElementById(`flag-btn-${qId}`)?.classList.add('flagged');
            const numBtn = document.getElementById(`num-${qId}`);
            if (numBtn) { numBtn.classList.remove('answered'); numBtn.classList.add('flagged'); }
        });

        updateCounts();
        startTimer();
    }

    // =========================================================================
    // SAVE STATE
    // =========================================================================
    function saveState() {
        localStorage.setItem(STORAGE_KEY, JSON.stringify({ answers, flagged, timeLeft, leaveCount }));
    }

    // =========================================================================
    // NAVIGATION
    // =========================================================================
    function goToQuestion(index) {
        if (index < 0 || index >= TOTAL) return;

        // Hide current
        document.getElementById(`q-${questions[currentIndex]}`)?.style.setProperty('display', 'none');
        document.getElementById(`num-${questions[currentIndex]}`)?.classList.remove('current');

        currentIndex = index;

        // Show new
        document.getElementById(`q-${questions[currentIndex]}`)?.style.setProperty('display', 'block');
        const numBtn = document.getElementById(`num-${questions[currentIndex]}`);
        numBtn?.classList.add('current');

        // Scroll number into view
        numBtn?.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }

    function hasAnswer(qId) {
        const val = answers[qId];
        if (Array.isArray(val)) return val.length > 0;
        if (val && typeof val === 'object') return Object.keys(val).length > 0;
        return !!val;
    }

    // =========================================================================
    // SELECT ANSWER
    // =========================================================================
    function selectAnswer(qId, val, el, multi) {
        if (multi) {
            // toggle pilihan
            let arr = Array.isArray(answers[qId]) ? answers[qId] : [];
            if (arr.includes(val)) {
                arr = arr.filter(v => v !== val);
                el.classList.remove('selected');
            } else {
                arr.push(val);
                el.classList.add('selected');
            }
            if (arr.length > 0) {
                answers[qId] = arr;
            } else {
                delete answers[qId];
            }
        } else {
            // single: ganti pilihan
            ['a','b','c','d','e'].forEach(k => {
                document.getElementById(`opt-${qId}-${k}`)?.classList.remove('selected');
            });
            el.classList.add('selected');
            answers[qId] = val;
        }

        const numBtn = document.getElementById(`num-${qId}`);
        const hasAns = hasAnswer(qId);
        if (numBtn && !flagged[qId]) {
            numBtn.classList.remove('flagged');
            numBtn.classList.toggle('answered', hasAns);
        }

        saveState();
        updateCounts();
    }

    function selectMatrixAnswer(qId, rowKey, columnKey, el) {
        const rowSelector = `[data-matrix-option="${qId}-${rowKey}"]`;
        document.querySelectorAll(rowSelector).forEach(item => item.classList.remove('selected'));
        el.classList.add('selected');

        const current = (answers[qId] && typeof answers[qId] === 'object' && !Array.isArray(answers[qId])) ? answers[qId] : {};
        current[rowKey] = columnKey;
        answers[qId] = current;

        const numBtn = document.getElementById(`num-${qId}`);
        if (numBtn && !flagged[qId]) {
            numBtn.classList.remove('flagged');
            numBtn.classList.add('answered');
        }

        saveState();
        updateCounts();
    }

    // =========================================================================
    // FLAG
    // =========================================================================
    function toggleFlag(qId) {
        const btn    = document.getElementById(`flag-btn-${qId}`);
        const numBtn = document.getElementById(`num-${qId}`);

        if (flagged[qId]) {
            delete flagged[qId];
            btn?.classList.remove('flagged');
            if (hasAnswer(qId)) numBtn?.classList.add('answered');
            numBtn?.classList.remove('flagged');
        } else {
            flagged[qId] = true;
            btn?.classList.add('flagged');
            numBtn?.classList.remove('answered');
            numBtn?.classList.add('flagged');
        }

        saveState();
        updateCounts();
    }

    // =========================================================================
    // COUNTS
    // =========================================================================
    function updateCounts() {
        const answered = Object.keys(answers).length;
        const fl       = Object.keys(flagged).length;
        const empty    = TOTAL - answered;

        document.getElementById('count-answered').textContent = answered;
        document.getElementById('count-flagged').textContent  = fl;
        document.getElementById('count-empty').textContent    = empty;
        document.getElementById('modal-answered').textContent = answered;
        document.getElementById('modal-empty').textContent    = empty;
    }

    // =========================================================================
    // TIMER
    // =========================================================================
    function startTimer() {
        updateTimerDisplay();
        const interval = setInterval(() => {
            timeLeft--;
            saveState();
            updateTimerDisplay();

            if (timeLeft <= 0) {
                clearInterval(interval);
                alert('Waktu habis! Jawaban kamu akan otomatis disubmit.');
                submitExam();
            }
        }, 1000);
    }

    function updateTimerDisplay() {
        const h = Math.floor(timeLeft / 3600);
        const m = Math.floor((timeLeft % 3600) / 60);
        const s = timeLeft % 60;

        const display = h > 0
            ? `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`
            : `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;

        const el = document.getElementById('timer');
        el.textContent = display;

        el.className = 'timer-display';
        if (timeLeft <= 60)  el.classList.add('danger');
        else if (timeLeft <= 300) el.classList.add('warning');
    }

    // =========================================================================
    // MODAL
    // =========================================================================
    function showSubmitModal() {
        updateCounts();
        document.getElementById('submit-modal').classList.add('show');
    }

    function closeSubmitModal() {
        document.getElementById('submit-modal').classList.remove('show');
    }

    // =========================================================================
    // SUBMIT
    // =========================================================================
    function submitExam() {
        const form   = document.getElementById('exam-form');
        const inputs = document.getElementById('answer-inputs');
        inputs.innerHTML = '';

        Object.entries(answers).forEach(([qId, val]) => {
            if (Array.isArray(val)) {
                // multiple: kirim answers[qId][] = a, c, ...
                val.forEach(v => {
                    const input = document.createElement('input');
                    input.type  = 'hidden';
                    input.name  = `answers[${qId}][]`;
                    input.value = v;
                    inputs.appendChild(input);
                });
            } else if (val && typeof val === 'object') {
                // matrix: kirim answers[qId][row] = col_1/col_2
                Object.entries(val).forEach(([rowKey, columnKey]) => {
                    const input = document.createElement('input');
                    input.type  = 'hidden';
                    input.name  = `answers[${qId}][${rowKey}]`;
                    input.value = columnKey;
                    inputs.appendChild(input);
                });
            } else {
                const input = document.createElement('input');
                input.type  = 'hidden';
                input.name  = `answers[${qId}]`;
                input.value = val;
                inputs.appendChild(input);
            }
        });

        // sertakan jumlah pelanggaran meninggalkan jendela
        const tsi = document.getElementById('tab-switch-input');
        if (tsi) tsi.value = leaveCount;

        localStorage.removeItem(STORAGE_KEY);
        form.submit();
    }

    // =========================================================================
    // ANTI-TINGGALKAN JENDELA — deteksi pindah tab / minimize / blur
    // =========================================================================
    let lastLeaveAt = 0;
    function registerLeave() {
        // debounce: blur + visibilitychange bisa menyala bersamaan, hitung 1 saja
        const now = Date.now();
        if (now - lastLeaveAt < 800) return;
        lastLeaveAt = now;

        leaveCount++;
        saveState();

        const lc = document.getElementById('leave-count');
        if (lc) lc.textContent = leaveCount;
        const banner = document.getElementById('leave-warning');
        if (banner) banner.style.display = 'block';
    }

    // Pindah tab atau minimize window
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'hidden') registerLeave();
    });
    // Klik ke aplikasi lain (alt-tab) tanpa mengubah visibility
    window.addEventListener('blur', registerLeave);

        // =========================================================================
    // WARN BEFORE LEAVE
    // =========================================================================
    window.addEventListener('beforeunload', (e) => {
        e.preventDefault();
        e.returnValue = 'Tryout sedang berlangsung. Yakin ingin meninggalkan halaman?';
    });

    // Remove warn before actual submit
    document.getElementById('exam-form').addEventListener('submit', () => {
        window.removeEventListener('beforeunload', () => {});
    });

    // =========================================================================
    // KEYBOARD SHORTCUTS
    // =========================================================================
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight') goToQuestion(currentIndex + 1);
        if (e.key === 'ArrowLeft')  goToQuestion(currentIndex - 1);
        if (['a','b','c','d','e'].includes(e.key)) {
            const qId  = questions[currentIndex];
            const opt  = document.getElementById(`opt-${qId}-${e.key}`);
            if (opt) selectAnswer(qId, e.key, opt, questionTypes[qId] === 'multiple');
        }
    });

    // Start
    init();
</script>

</body>
</html>
