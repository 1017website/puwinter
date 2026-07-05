<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tryout extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'subject_id', 'grade', 'grade_id', 'plan_id', 'access_tier', 'description',
        'duration_minutes', 'total_questions', 'scoring_mode', 'irt_calibrated',
        'is_premium', 'is_published', 'series', 'order',
    ];

    protected $casts = [
        'is_premium'     => 'boolean',
        'is_published'   => 'boolean',
        'irt_calibrated' => 'boolean',
    ];

    // Mode penilaian
    public const SCORING_REGULAR = 'regular';
    public const SCORING_IRT     = 'irt';

    public function isIrt(): bool
    {
        return $this->scoring_mode === self::SCORING_IRT;
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderBy('order');
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    public function scopeFree($query)
    {
        return $query->where('is_premium', false);
    }

    public function scopeForUser($query, $user)
    {
        if (!$user || in_array($user->role, ['superadmin', 'admin', 'mentor'])) {
            return $query;
        }

        $userGradeId = $user->grade_id ? (int) $user->grade_id : null;
        $legacyGrade = filled($user->grade) ? (string) $user->grade : null;

        // Jika siswa belum punya kelas, jangan blokir agar data lama tetap aman.
        if (!$userGradeId && !$legacyGrade) {
            return $query;
        }

        return $query->where(function ($q) use ($userGradeId, $legacyGrade) {
            $q->where(function ($allGrades) {
                $allGrades->whereNull('grade_id')->whereNull('grade');
            });

            if ($userGradeId) {
                $q->orWhere('grade_id', $userGradeId);
            }

            if ($legacyGrade) {
                $q->orWhere('grade', $legacyGrade);
            }
        });
    }

    public function gradeLabel(): string
    {
        $grade = $this->relationLoaded('gradeLevel')
            ? $this->getRelation('gradeLevel')
            : $this->gradeLevel()->first();

        if ($grade) {
            return $grade->name;
        }

        $legacyGrade = $this->getAttribute('grade');
        return $legacyGrade ? 'Kelas ' . $legacyGrade : 'Semua Kelas';
    }


    public function gradeIdForForm(): ?int
    {
        if (!empty($this->grade_id)) {
            return (int) $this->grade_id;
        }

        $legacyGrade = $this->getAttribute('grade');
        if (!$legacyGrade) {
            return null;
        }

        return Grade::where('code', (string) $legacyGrade)->value('id');
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    /**
     * Cek akses tryout berdasarkan grade + program (plan_id) + access_tier.
     */
    public function isAccessibleBy($user): bool
    {
        if (!$user) return false;
        if (in_array($user->role, ['superadmin', 'admin', 'mentor'])) return true;

        // Kelas/grade dari Master Kelas (grade_id), dengan fallback field lama grade.
        if (!$user->canAccessGradeId($this->grade_id)) {
            return false;
        }

        if ($this->grade_id === null && filled($this->grade) && filled($user->grade) && (string) $this->grade !== (string) $user->grade) {
            return false;
        }

        return $user->canAccessContent($this->plan_id, $this->access_tier ?? 'both');
    }

    /**
     * Relasi ke Master Kelas. Dipisah dari nama `grade` karena field legacy
     * `tryouts.grade` masih ada dan dapat bentrok dengan relasi Eloquent.
     */
    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function grade(): BelongsTo
    {
        return $this->gradeLevel();
    }

    public function passages(): HasMany
    {
        return $this->hasMany(TryoutPassage::class)->orderBy('order');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(TryoutQuestion::class)->orderBy('order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(UserTryoutAttempt::class);
    }
}
