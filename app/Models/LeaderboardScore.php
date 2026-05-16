<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaderboardScore extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'subject_id', 'total_score', 'percentile',
        'rank_global', 'rank_school', 'rank_city', 'rank_province',
    ];

    protected $casts = [
        'total_score' => 'float',
        'percentile'  => 'float',
        'updated_at'  => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
