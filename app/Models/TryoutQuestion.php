<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TryoutQuestion extends Model
{
    protected $fillable = [
        'tryout_id', 'passage_id', 'subject_id', 'question_type', 'question_text', 'question_image',
        'option_a', 'option_b', 'option_c', 'option_d', 'option_e',
        'correct_answer', 'correct_answers', 'matrix_columns', 'explanation', 'explanation_video_url',
        'difficulty', 'score_weight', 'order', 'correct_rate', 'answered_count',
    ];

    protected $casts = [
        'correct_answers' => 'array',
        'matrix_columns'  => 'array',
        'score_weight'    => 'float',
    ];

    // Tipe soal
    public const TYPE_SINGLE   = 'single';   // pilihan ganda biasa (1 kunci)
    public const TYPE_MULTIPLE = 'multiple'; // multiple jawaban (>1 kunci)
    public const TYPE_MATRIX   = 'matrix';   // pilihan ganda kompleks kategori/tabel

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeByDifficulty($query, string $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    // -------------------------------------------------------------------------
    // Tipe Soal Helpers
    // -------------------------------------------------------------------------

    public function isMultiple(): bool
    {
        return $this->question_type === self::TYPE_MULTIPLE;
    }

    public function isMatrix(): bool
    {
        return $this->question_type === self::TYPE_MATRIX;
    }

    /**
     * Daftar kunci jawaban yang sudah dinormalisasi (lowercase) untuk tipe single/multiple.
     * Untuk tipe matrix, gunakan matrixCorrectAnswers() karena bentuknya row => kategori.
     * @return array<int,string>
     */
    public function correctKeys(): array
    {
        if ($this->isMatrix()) {
            return array_map(
                fn($row, $column) => strtoupper((string) $row) . ': ' . $this->matrixColumnLabel((string) $column),
                array_keys($this->matrixCorrectAnswers()),
                array_values($this->matrixCorrectAnswers())
            );
        }

        if (!$this->isMultiple()) {
            return [strtolower((string) $this->correct_answer)];
        }

        $keys = is_array($this->correct_answers) ? $this->correct_answers : [];
        return array_values(array_unique(array_map(fn($k) => strtolower((string) $k), $keys)));
    }

    /**
     * Label kolom untuk soal kategori/matrix.
     * Format disimpan sebagai JSON: {"col_1":"Time Management", "col_2":"Self Management"}.
     * @return array<string,string>
     */
    public function matrixColumns(): array
    {
        $columns = is_array($this->matrix_columns) ? $this->matrix_columns : [];

        $normalized = [];
        foreach ($columns as $key => $label) {
            $key = (string) $key;
            if (!str_starts_with($key, 'col_')) {
                $position = is_numeric($key) ? ((int) $key + 1) : (count($normalized) + 1);
                $key = 'col_' . $position;
            }
            $label = trim((string) $label);
            if ($label !== '') {
                $normalized[$key] = $label;
            }
        }

        return $normalized ?: [
            'col_1' => 'Kategori 1',
            'col_2' => 'Kategori 2',
        ];
    }

    public function matrixColumnLabel(?string $columnKey): string
    {
        if (!$columnKey) {
            return '—';
        }

        return $this->matrixColumns()[$columnKey] ?? $columnKey;
    }

    /**
     * Kunci soal matrix: [rowKey => columnKey], contoh ['a' => 'col_2'].
     * @return array<string,string>
     */
    public function matrixCorrectAnswers(): array
    {
        $answers = is_array($this->correct_answers) ? $this->correct_answers : [];
        $rows = array_keys($this->options());
        $columns = array_keys($this->matrixColumns());
        $normalized = [];

        foreach ($answers as $rowKey => $columnKey) {
            $rowKey = strtolower((string) $rowKey);
            $columnKey = (string) $columnKey;
            if (in_array($rowKey, $rows, true) && in_array($columnKey, $columns, true)) {
                $normalized[$rowKey] = $columnKey;
            }
        }

        return $normalized;
    }

    public function answerLabel(mixed $answer): string
    {
        if ($this->isMatrix()) {
            $answerMap = is_array($answer) ? $answer : [];
            $parts = [];
            foreach ($this->options() as $rowKey => $rowText) {
                $columnKey = $answerMap[$rowKey] ?? null;
                if ($columnKey) {
                    $parts[] = strtoupper((string) $rowKey) . ': ' . $this->matrixColumnLabel((string) $columnKey);
                }
            }
            return $parts ? implode(', ', $parts) : '—';
        }

        if (is_array($answer)) {
            return count($answer) ? strtoupper(implode(', ', array_values($answer))) : '—';
        }

        return ($answer !== null && $answer !== '') ? strtoupper((string) $answer) : '—';
    }

    public function correctAnswerLabel(): string
    {
        if ($this->isMatrix()) {
            $parts = [];
            foreach ($this->matrixCorrectAnswers() as $rowKey => $columnKey) {
                $parts[] = strtoupper((string) $rowKey) . ': ' . $this->matrixColumnLabel((string) $columnKey);
            }
            return $parts ? implode(', ', $parts) : '—';
        }

        return strtoupper(implode(', ', $this->correctKeys()));
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function options(): array
    {
        return array_filter([
            'a' => $this->option_a,
            'b' => $this->option_b,
            'c' => $this->option_c,
            'd' => $this->option_d,
            'e' => $this->option_e,
        ]);
    }

    /**
     * Untuk tipe single: cek kecocokan persis.
     * (Dipertahankan agar kode lama tetap kompatibel.)
     */
    public function isCorrect(string $answer): bool
    {
        return strtolower($answer) === $this->correct_answer;
    }

    public function scoreWeight(): float
    {
        $weight = (float) ($this->score_weight ?? 1);
        return $weight > 0 ? $weight : 1.0;
    }

    /**
     * Penilaian satu soal, mendukung single & multiple (partial credit).
     *
     * Input $userAnswer:
     *   - single   : string 'a'..'e' atau null
     *   - multiple : array ['a','c'] atau null/[]
     *
     * Rumus partial credit (multiple):
     *   skor = benar_dipilih / total_kunci
     *
     * Contoh: kunci benar 4 opsi, siswa memilih 2 opsi benar => 0.5 poin.
     * Opsi salah tidak mengurangi poin, hanya tidak menambah nilai.
     *
     * @return array{status:string, earned:float, max:float}
     *   status: 'correct' | 'partial' | 'wrong' | 'empty'
     *   earned: poin yang diraih (skala bobot_penuh)
     *   max   : poin maksimum soal (selalu = bobot_penuh)
     */
    public function grade(mixed $userAnswer, float $fullPoint = 1.0, float $penaltyPerWrong = 0.0): array
    {
        // ---- MATRIX / KATEGORI ----
        if ($this->isMatrix()) {
            $keys = $this->matrixCorrectAnswers();
            $totalRows = max(1, count($keys));
            $picked = is_array($userAnswer) ? $userAnswer : [];
            $picked = array_filter($picked, fn($value) => $value !== null && $value !== '');

            if (count($picked) === 0) {
                return ['status' => 'empty', 'earned' => 0.0, 'max' => $fullPoint];
            }

            $right = 0;
            foreach ($keys as $rowKey => $columnKey) {
                if (($picked[$rowKey] ?? null) === $columnKey) {
                    $right++;
                }
            }

            $earned = max(0.0, min($fullPoint, round(($right / $totalRows) * $fullPoint, 2)));
            $status = $right === $totalRows
                ? 'correct'
                : ($earned > 0 ? 'partial' : 'wrong');

            return ['status' => $status, 'earned' => $earned, 'max' => $fullPoint];
        }

        $keys = $this->correctKeys();
        $totalKeys = max(1, count($keys));

        // ---- SINGLE ----
        if (!$this->isMultiple()) {
            if ($userAnswer === null || $userAnswer === '' || $userAnswer === []) {
                return ['status' => 'empty', 'earned' => 0.0, 'max' => $fullPoint];
            }
            $ans = is_array($userAnswer) ? (string) ($userAnswer[0] ?? '') : (string) $userAnswer;
            $ans = strtolower($ans);
            if (in_array($ans, $keys, true)) {
                return ['status' => 'correct', 'earned' => $fullPoint, 'max' => $fullPoint];
            }
            // Salah/kosong tidak mengurangi nilai.
            return ['status' => 'wrong', 'earned' => 0.0, 'max' => $fullPoint];
        }

        // ---- MULTIPLE (partial credit) ----
        $picked = [];
        if (is_array($userAnswer)) {
            $picked = array_values(array_unique(array_map(fn($k) => strtolower((string) $k), $userAnswer)));
        } elseif ($userAnswer !== null && $userAnswer !== '') {
            $picked = [strtolower((string) $userAnswer)];
        }

        if (count($picked) === 0) {
            return ['status' => 'empty', 'earned' => 0.0, 'max' => $fullPoint];
        }

        $rightPicked = count(array_intersect($picked, $keys)); // opsi benar yang dipilih
        $wrongPicked = count(array_diff($picked, $keys));       // opsi salah yang dipilih

        $earned = ($rightPicked / $totalKeys) * $fullPoint;

        if ($penaltyPerWrong > 0) {
            $earned -= ($wrongPicked * $penaltyPerWrong);
        }

        $earned = max(0.0, min($fullPoint, round($earned, 2)));

        // status untuk statistik
        if ($rightPicked === $totalKeys && $wrongPicked === 0) {
            $status = 'correct';
        } elseif ($earned > 0) {
            $status = 'partial';
        } else {
            $status = 'wrong';
        }

        return ['status' => $status, 'earned' => $earned, 'max' => $fullPoint];
    }

    /**
     * Bobot kesulitan soal berbasis tingkat keberhasilan global.
     * correct_rate rendah (sedikit yang benar) => bobot tinggi.
     * Skala bobot 1.0 .. 2.0. Jika belum ada data, fallback ke difficulty.
     */
    public function difficultyWeight(): float
    {
        if ($this->answered_count > 0 && $this->correct_rate !== null) {
            return round(1.0 + (1.0 - ((float) $this->correct_rate / 100)), 3);
        }

        return match ($this->difficulty) {
            'sulit'  => 1.7,
            'sedang' => 1.3,
            default  => 1.0,
        };
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function tryout(): BelongsTo
    {
        return $this->belongsTo(Tryout::class);
    }

    public function passage(): BelongsTo
    {
        return $this->belongsTo(TryoutPassage::class, 'passage_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function savedByUsers(): HasMany
    {
        return $this->hasMany(UserSavedQuestion::class, 'question_id');
    }

    public function discussions(): MorphMany
    {
        return $this->morphMany(Discussion::class, 'discussable');
    }
}
