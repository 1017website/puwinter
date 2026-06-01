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
        'course_id', 'mentor_id', 'subject_id', 'grade', 'grade_id', 'class_type', 'title', 'description',
        'scheduled_at', 'duration_minutes', 'zoom_link', 'zoom_meeting_id',
        'is_premium', 'status', 'recording_url', 'total_participants',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'is_premium'   => 'boolean',
    ];

    // Tipe live class
    public const TYPE_REGULAR = 'regular'; // live class umum (ikut grade + flag premium)
    public const TYPE_PRIVATE = 'private'; // privat/eksklusif — wajib premium

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

    public function scopeForUser($query, $user)
    {
        if (!$user || in_array($user->role, ['superadmin', 'admin', 'mentor']) || empty($user->grade_id)) {
            return $query;
        }
        return $query->where(function ($q) use ($user) {
            $q->whereNull('grade_id')->orWhere('grade_id', $user->grade_id);
        });
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('class_type', $type);
    }

    public function scopeRegularType($query)
    {
        return $query->where('class_type', self::TYPE_REGULAR);
    }

    public function scopePrivateType($query)
    {
        return $query->where('class_type', self::TYPE_PRIVATE);
    }

    // -------------------------------------------------------------------------
    // Access Helpers
    // -------------------------------------------------------------------------

    public function isPrivate(): bool
    {
        return $this->class_type === self::TYPE_PRIVATE;
    }

    /**
     * Private selalu wajib premium; regular ikut flag is_premium.
     */
    public function requiresPremium(): bool
    {
        return $this->isPrivate() ? true : (bool) $this->is_premium;
    }

    /**
     * Cek apakah $user boleh mengakses live class ini (grade + premium).
     */
    public function isAccessibleBy($user): bool
    {
        if (!$user) return false;
        if (in_array($user->role, ['superadmin', 'admin', 'mentor'])) return true;

        // Grade
        if ($this->grade_id !== null && (int) $user->grade_id !== (int) $this->grade_id) {
            return false;
        }

        // Premium
        if ($this->requiresPremium() && !$user->isPremium()) {
            return false;
        }

        return true;
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

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(LiveClassAttendance::class);
    }
}
