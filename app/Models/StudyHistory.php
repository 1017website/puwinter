<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StudyHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'activity_type', 'reference_id', 'reference_type',
        'duration_seconds', 'score', 'notes', 'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'score'      => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function durationFormatted(): string
    {
        $minutes = (int) ($this->duration_seconds / 60);
        $hours   = (int) ($minutes / 60);
        $mins    = $minutes % 60;

        if ($hours > 0) {
            return "{$hours} jam {$mins} menit";
        }
        return "{$mins} menit";
    }
}
