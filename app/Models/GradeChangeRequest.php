<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeChangeRequest extends Model
{
    protected $fillable = [
        'user_id', 'from_grade_id', 'to_grade_id', 'reason',
        'status', 'admin_note', 'processed_by', 'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fromGrade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'from_grade_id');
    }

    public function toGrade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'to_grade_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
