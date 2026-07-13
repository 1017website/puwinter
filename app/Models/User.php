<?php

namespace App\Models;

use App\Services\EmailVerificationMailService;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'avatar', 'phone', 'phone_verified_at',
        'school', 'city', 'province', 'birth_date', 'grade', 'grade_id', 'grade_locked',
        'is_active', 'last_login_at', 'affiliate_code', 'referred_by_user_id', 'registration_code_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'birth_date' => 'date',
        'is_active' => 'boolean',
        'grade_locked' => 'boolean',
        'referred_by_user_id' => 'integer',
        'registration_code_id' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (($user->role ?? 'student') === 'student' && empty($user->affiliate_code)) {
                $user->affiliate_code = static::generateUniqueAffiliateCode($user->name ?: 'PW');
            }
        });
    }

    public static function generateUniqueAffiliateCode(string $name = 'PW'): string
    {
        $prefix = Str::upper(Str::slug($name, ''));
        $prefix = preg_replace('/[^A-Z0-9]/', '', $prefix) ?: 'PW';
        $prefix = Str::limit($prefix, 6, '');

        do {
            $code = $prefix.random_int(1000, 9999);
        } while (static::where('affiliate_code', $code)->exists());

        return $code;
    }

    public function ensureAffiliateCode(): string
    {
        if (empty($this->affiliate_code)) {
            $this->forceFill([
                'affiliate_code' => static::generateUniqueAffiliateCode($this->name ?: 'PW'),
            ])->save();
        }

        return (string) $this->affiliate_code;
    }

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

    /**
     * Tier premium aktif: 'exclusive' | 'regular' | null (free).
     * Staff (admin/mentor/superadmin) dianggap 'exclusive'.
     */
    public function premiumTier(): ?string
    {
        if (in_array($this->role, ['superadmin', 'admin', 'mentor'])) {
            return 'exclusive';
        }
        $sub = $this->activeSubscription();

        return $sub?->tier;
    }

    /**
     * Apakah user punya akses Exclusive (boleh ikut live class private/exclusive).
     */
    public function isExclusive(): bool
    {
        return $this->premiumTier() === 'exclusive';
    }

    // -------------------------------------------------------------------------
    // Program Access (akses per-program)
    // -------------------------------------------------------------------------

    /**
     * Apakah user TERDAFTAR pada sebuah program (plan), berapa pun statusnya.
     */
    public function isEnrolledInProgram(?int $planId): bool
    {
        if (in_array($this->role, ['superadmin', 'admin', 'mentor'])) {
            return true;
        }
        if ($planId === null) {
            return true; // konten tanpa program = umum
        }

        return $this->programEnrollments()->where('plan_id', $planId)->exists();
    }

    /**
     * Apakah user adalah peserta BERBAYAR aktif pada program (plan) tertentu.
     */
    public function hasPaidProgram(?int $planId): bool
    {
        if (in_array($this->role, ['superadmin', 'admin', 'mentor'])) {
            return true;
        }
        if ($planId === null) {
            return true;
        }
        $enr = $this->programEnrollments()->where('plan_id', $planId)->first();

        return $enr ? $enr->isPaidActive() : false;
    }

    /**
     * Cek akses konten berdasarkan program (plan_id) + access_tier konten.
     *
     * Aturan:
     * - Staff: selalu boleh.
     * - Konten tanpa plan_id: terbuka untuk umum (mengikuti aturan lama).
     * - Harus terdaftar di program-nya.
     * - access_tier 'free'/'both' : cukup terdaftar (free atau paid).
     * - access_tier 'paid'        : harus berbayar aktif.
     */
    public function canAccessContent(?int $planId, string $accessTier = 'both'): bool
    {
        if (in_array($this->role, ['superadmin', 'admin', 'mentor'])) {
            return true;
        }
        if ($planId === null) {
            return true;
        }
        if (! $this->isEnrolledInProgram($planId)) {
            return false;
        }
        if ($accessTier === 'paid') {
            return $this->hasPaidProgram($planId);
        }

        // free / both
        return true;
    }

    public function hasAccessTo(Course $course): bool
    {
        return $course->isAccessibleBy($this);
    }

    /**
     * Jumlah live class berbeda yang pernah diikuti (dihitung dari kehadiran unik).
     */
    public function liveClassesAttendedCount(): int
    {
        return LiveClassAttendance::where('user_id', $this->id)
            ->distinct('live_class_id')
            ->count('live_class_id');
    }

    /**
     * Apakah user free masih boleh ikut live class baru.
     * Aturan: user gratis dibatasi 1 live class seumur hidup.
     * Sudah pernah ikut live class $liveClassId -> tetap boleh (akses ulang).
     */
    public function canJoinFreeLiveClass(?int $liveClassId = null): bool
    {
        // Premium / staff: bebas.
        if ($this->isPremium()) {
            return true;
        }

        // Jika live class ini sudah pernah diikuti, akses ulang diperbolehkan.
        if ($liveClassId !== null) {
            $already = LiveClassAttendance::where('user_id', $this->id)
                ->where('live_class_id', $liveClassId)
                ->exists();
            if ($already) {
                return true;
            }
        }

        // Belum pernah ikut live class apa pun -> boleh 1x.
        return $this->liveClassesAttendedCount() < 1;
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

    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by_user_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by_user_id');
    }

    public function registrationCode(): BelongsTo
    {
        return $this->belongsTo(RegistrationCode::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function programEnrollments(): HasMany
    {
        return $this->hasMany(ProgramEnrollment::class);
    }

    public function grade(): BelongsTo
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

    /**
     * Override notifikasi verifikasi email bawaan Laravel agar memakai
     * pengiriman custom yang sekaligus mencatat status ke tabel email_logs.
     */
    public function sendEmailVerificationNotification(): void
    {
        app(EmailVerificationMailService::class)->send($this, null, 'system');
    }

    public function emailVerifications(): HasMany
    {
        return $this->hasMany(EmailVerification::class);
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->latest();
    }
}
