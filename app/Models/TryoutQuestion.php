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
            return [strtolower((string) $this->correct_answer)];
        }
        $keys = is_array($this->correct_answers) ? $this->correct_answers : [];
        return array_values(array_unique(array_map(fn($k) => strtolower((string) $k), $keys)));
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

    /**
     * Penilaian satu soal, mendukung single & multiple (partial credit).
     *
     * Input $userAnswer:
     *   - single   : string 'a'..'e' atau null
     *   - multiple : array ['a','c'] atau null/[]
     *
     * Rumus partial credit (multiple):
     *   skor = max(0, (benar_dipilih / total_kunci) * bobot_penuh
     *               - (salah_dipilih * penalti_per_salah))
     *
     * @return array{status:string, earned:float, max:float}
     *   status: 'correct' | 'partial' | 'wrong' | 'empty'
     *   earned: poin yang diraih (skala bobot_penuh)
     *   max   : poin maksimum soal (selalu = bobot_penuh)
     */
    public function grade(mixed $userAnswer, float $fullPoint = 4.0, float $penaltyPerWrong = 1.0): array
    {
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
            // salah: penalti seperti aturan lama (benar*4 - salah*1) ditangani di controller
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

        $earned = ($rightPicked / $totalKeys) * $fullPoint - ($wrongPicked * $penaltyPerWrong);
        $earned = max(0.0, round($earned, 2));

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
