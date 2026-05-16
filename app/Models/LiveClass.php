<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LiveClass extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id', 'mentor_id', 'subject_id', 'title', 'description',
        'scheduled_at', 'duration_minutes', 'zoom_link', 'zoom_meeting_id',
        'is_premium', 'status', 'recording_url', 'total_participants',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'is_premium'   => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')
                     ->where('scheduled_at', '>', now())
                     ->orderBy('scheduled_at');
    }

    public function scopeLive($query)
    {
        return $query->where('status', 'live');
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isLive(): bool
    {
        return $this->status === 'live';
    }

    public function hasRecording(): bool
    {
        return $this->recording_url !== null;
    }

    public function formattedSchedule(): string
    {
        return $this->scheduled_at->translatedFormat('l, d F Y • H:i') . ' WIB';
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(LiveClassAttendance::class);
    }
}
