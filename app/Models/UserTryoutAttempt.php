<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTryoutAttempt extends Model
{
    protected $fillable = [
        'user_id', 'tryout_id', 'started_at', 'submitted_at',
        'answers', 'score', 'correct_count', 'wrong_count',
        'empty_count', 'rank_at_submit', 'weighted_score', 'tab_switch_count',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'submitted_at' => 'datetime',
        'answers'      => 'array',
        'score'          => 'float',
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

    public function answerFor(int $questionId): ?string
    {
        return $this->answers[$questionId] ?? null;
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
