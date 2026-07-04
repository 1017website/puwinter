<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TryoutQuestion extends Model
{
    protected $fillable = [
        'tryout_id', 'subject_id', 'question_type', 'question_text', 'question_image',
        'option_a', 'option_b', 'option_c', 'option_d', 'option_e',
        'correct_answer', 'correct_answers', 'explanation', 'explanation_video_url',
        'difficulty', 'order', 'correct_rate', 'answered_count',
    ];

    protected $casts = [
        'correct_answers' => 'array',
    ];

    // Tipe soal
    public const TYPE_SINGLE   = 'single';   // pilihan ganda biasa (1 kunci)
    public const TYPE_MULTIPLE = 'multiple'; // multiple jawaban (>1 kunci)

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

    /**
     * Daftar kunci jawaban yang sudah dinormalisasi (lowercase) untuk tipe multiple.
     * @return array<int,string>
     */
    public function correctKeys(): array
    {
        if (!$this->isMultiple()) {
            return array_values(array_filter([strtolower((string) $this->correct_answer)]));
        }

        $keys = is_array($this->correct_answers) ? $this->correct_answers : [];
        $keys = array_values(array_unique(array_filter(
            array_map(fn($k) => strtolower((string) $k), $keys),
            fn($k) => $k !== ''
        )));

        // Fallback untuk data lama: multiple sudah dibuat, tapi kolom JSON belum terisi.
        if (empty($keys) && $this->correct_answer) {
            $keys = [strtolower((string) $this->correct_answer)];
        }

        return $keys;
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
     * Cek benar penuh untuk single maupun multiple.
     * Dipertahankan agar kode lama tetap kompatibel.
     */
    public function isCorrect(mixed $answer): bool
    {
        return $this->grade($answer, 1.0, 0.0)['status'] === 'correct';
    }

    /**
     * Penilaian satu soal, mendukung single & multiple.
     *
     * Aturan baru:
     * - Single answer   : benar = 1, salah/kosong = 0.
     * - Multiple answer : nilai proporsional sesuai jumlah kunci benar yang dipilih.
     *   Contoh 2 dari 4 kunci benar terpilih = 0.5.
     * - Tidak ada penalti minus. Opsi salah bernilai 0 dan tidak mengurangi skor.
     *
     * @return array{
     *   status:string,
     *   earned:float,
     *   max:float,
     *   selected:array<int,string>,
     *   correct_keys:array<int,string>,
     *   correct_selected:int,
     *   wrong_selected:int
     * }
     * status: 'correct' | 'partial' | 'wrong' | 'empty'
     */
    public function grade(mixed $userAnswer, float $fullPoint = 1.0, float $penaltyPerWrong = 0.0): array
    {
        $keys = $this->correctKeys();
        $totalKeys = max(1, count($keys));

        $emptyResult = [
            'status' => 'empty',
            'earned' => 0.0,
            'max' => $fullPoint,
            'selected' => [],
            'correct_keys' => $keys,
            'correct_selected' => 0,
            'wrong_selected' => 0,
        ];

        // ---- SINGLE ----
        if (!$this->isMultiple()) {
            if ($userAnswer === null || $userAnswer === '' || $userAnswer === []) {
                return $emptyResult;
            }

            $ans = is_array($userAnswer) ? (string) ($userAnswer[0] ?? '') : (string) $userAnswer;
            $ans = strtolower($ans);
            $isCorrect = in_array($ans, $keys, true);

            return [
                'status' => $isCorrect ? 'correct' : 'wrong',
                'earned' => $isCorrect ? $fullPoint : 0.0,
                'max' => $fullPoint,
                'selected' => $ans !== '' ? [$ans] : [],
                'correct_keys' => $keys,
                'correct_selected' => $isCorrect ? 1 : 0,
                'wrong_selected' => $isCorrect ? 0 : 1,
            ];
        }

        // ---- MULTIPLE (partial credit tanpa penalti minus) ----
        $picked = [];
        if (is_array($userAnswer)) {
            $picked = array_values(array_unique(array_filter(
                array_map(fn($k) => strtolower((string) $k), $userAnswer),
                fn($k) => $k !== ''
            )));
        } elseif ($userAnswer !== null && $userAnswer !== '') {
            $picked = [strtolower((string) $userAnswer)];
        }

        if (count($picked) === 0) {
            return $emptyResult;
        }

        $rightPicked = count(array_intersect($picked, $keys));
        $wrongPicked = count(array_diff($picked, $keys));

        // Nilai hanya dari jumlah kunci benar yang dipilih.
        // Opsi salah tidak mengurangi skor karena aturan baru salah = 0.
        $earned = round(($rightPicked / $totalKeys) * $fullPoint, 2);

        if ($earned >= $fullPoint) {
            $status = 'correct';
        } elseif ($earned > 0) {
            $status = 'partial';
        } else {
            $status = 'wrong';
        }

        return [
            'status' => $status,
            'earned' => $earned,
            'max' => $fullPoint,
            'selected' => $picked,
            'correct_keys' => $keys,
            'correct_selected' => $rightPicked,
            'wrong_selected' => $wrongPicked,
        ];
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
