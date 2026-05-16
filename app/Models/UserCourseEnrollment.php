<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCourseEnrollment extends Model
{
    protected $fillable = [
        'user_id', 'course_id', 'enrolled_at', 'completed_at',
        'progress_percentage', 'last_accessed_at',
    ];

    protected $casts = [
        'enrolled_at'         => 'datetime',
        'completed_at'        => 'datetime',
        'last_accessed_at'    => 'datetime',
        'progress_percentage' => 'integer',
    ];

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('completed_at');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }
}
