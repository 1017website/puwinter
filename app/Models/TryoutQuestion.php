<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TryoutQuestion extends Model
{
    protected $fillable = [
        'tryout_id', 'subject_id', 'question_text', 'question_image',
        'option_a', 'option_b', 'option_c', 'option_d', 'option_e',
        'correct_answer', 'explanation', 'explanation_video_url',
        'difficulty', 'order', 'correct_rate', 'answered_count',
    ];

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeByDifficulty($query, string $difficulty)
    {
        return $query->where('difficulty', $difficulty);
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

    public function isCorrect(string $answer): bool
    {
        return strtolower($answer) === $this->correct_answer;
    }

    /**
     * Bobot kesulitan soal berbasis tingkat keberhasilan global.
     * correct_rate rendah (sedikit yang benar) => bobot tinggi.
     * Skala bobot 1.0 .. 2.0. Jika belum ada data, fallback ke difficulty.
     */
    public function difficultyWeight(): float
    {
        if ($this->answered_count > 0 && $this->correct_rate !== null) {
            // correct_rate 0%  => bobot 2.0 (sangat sulit)
            // correct_rate 100% => bobot 1.0 (sangat mudah)
            return round(1.0 + (1.0 - ((float) $this->correct_rate / 100)), 3);
        }

        // Fallback dari label difficulty bila belum ada statistik
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
