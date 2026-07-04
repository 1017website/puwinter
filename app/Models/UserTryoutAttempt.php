<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTryoutAttempt extends Model
{
    protected $fillable = [
        'user_id', 'tryout_id', 'started_at', 'submitted_at',
        'answers', 'question_scores', 'score', 'correct_count', 'wrong_count',
        'empty_count', 'rank_at_submit', 'weighted_score', 'tab_switch_count',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'submitted_at' => 'datetime',
        'answers'         => 'array',
        'question_scores' => 'array',
        'score'           => 'float',
        'weighted_score' => 'float',
    ];

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeSubmitted($query)
    {
        return $query->whereNotNull('submitted_at');
    }

    public function scopeInProgress($query)
    {
        return $query->whereNull('submitted_at');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isSubmitted(): bool
    {
        return $this->submitted_at !== null;
    }

    public function duration(): int
    {
        $end = $this->submitted_at ?? now();
        return (int) $this->started_at->diffInMinutes($end);
    }

    public function answerFor(int $questionId): mixed
    {
        return $this->answers[$questionId] ?? null;
    }

    public function questionScoreFor(int $questionId): ?array
    {
        $scores = $this->question_scores ?? [];
        return $scores[$questionId] ?? $scores[(string) $questionId] ?? null;
    }

    public function partialCount(): int
    {
        return collect($this->question_scores ?? [])
            ->where('status', 'partial')
            ->count();
    }

    public function formattedScore(?int $max = null): string
    {
        $score = rtrim(rtrim(number_format((float) $this->score, 2, '.', ''), '0'), '.');
        return $max ? $score . '/' . $max : $score;
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tryout(): BelongsTo
    {
        return $this->belongsTo(Tryout::class);
    }
}
