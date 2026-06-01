<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'avatar', 'phone', 'phone_verified_at',
        'school', 'city', 'province', 'birth_date', 'grade', 'grade_id', 'grade_locked',
        'is_active', 'last_login_at',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at'  => 'datetime',
        'phone_verified_at'  => 'datetime',
        'last_login_at'      => 'datetime',
        'birth_date'         => 'date',
        'is_active'          => 'boolean',
        'grade_locked'       => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Role Helpers
    // -------------------------------------------------------------------------

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['superadmin', 'admin']);
    }

    public function isMentor(): bool
    {
        return $this->role === 'mentor';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    // -------------------------------------------------------------------------
    // Premium Helpers
    // -------------------------------------------------------------------------

    public function isPremium(): bool
    {
        // superadmin, admin, mentor selalu dianggap premium
        if (in_array($this->role, ['superadmin', 'admin', 'mentor'])) {
            return true;
        }

        return $this->activeSubscription() !== null;
    }

    public function activeSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('expired_at', '>', now())
            ->latest('started_at')
            ->first();
    }

    public function hasAccessTo(Course $course): bool
    {
        return $course->isAccessibleBy($this);
    }

    // -------------------------------------------------------------------------
    // Grade / Kelas Helpers
    // -------------------------------------------------------------------------

    /**
     * Apakah user boleh mengakses konten dengan grade_id tertentu.
     * - Admin / mentor / superadmin: bebas semua.
     * - Konten grade_id NULL = berlaku semua kelas.
     * - Student tanpa grade_id terisi: jangan blokir (fallback aman).
     */
    public function canAccessGradeId(?int $gradeId): bool
    {
        if (in_array($this->role, ['superadmin', 'admin', 'mentor'])) {
            return true;
        }
        if ($gradeId === null) {
            return true;
        }
        if (empty($this->grade_id)) {
            return true;
        }
        return (int) $this->grade_id === (int) $gradeId;
    }

    /**
     * Apakah siswa punya request pindah kelas yang masih pending.
     */
    public function hasPendingGradeChange(): bool
    {
        return $this->gradeChangeRequests()->where('status', 'pending')->exists();
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function grade(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function gradeChangeRequests(): HasMany
    {
        return $this->hasMany(GradeChangeRequest::class)->latest();
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(UserCourseEnrollment::class);
    }

    public function materialProgress(): HasMany
    {
        return $this->hasMany(UserMaterialProgress::class);
    }

    public function tryoutAttempts(): HasMany
    {
        return $this->hasMany(UserTryoutAttempt::class);
    }

    public function studyHistories(): HasMany
    {
        return $this->hasMany(StudyHistory::class);
    }

    public function leaderboardScores(): HasMany
    {
        return $this->hasMany(LeaderboardScore::class);
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }

    public function discussions(): HasMany
    {
        return $this->hasMany(Discussion::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(StudentNote::class);
    }

    public function savedMaterials(): HasMany
    {
        return $this->hasMany(UserSavedMaterial::class);
    }

    public function savedQuestions(): HasMany
    {
        return $this->hasMany(UserSavedQuestion::class);
    }

    // Mentor: kelas yang dia ampu
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'mentor_id');
    }

    public function liveClasses(): HasMany
    {
        return $this->hasMany(LiveClass::class, 'mentor_id');
    }

    public function emailVerifications(): HasMany
    {
        return $this->hasMany(EmailVerification::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->latest();
    }
}
