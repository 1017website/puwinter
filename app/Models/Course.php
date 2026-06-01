<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'subject_id', 'grade', 'grade_id', 'course_type', 'mentor_id', 'title', 'slug', 'description',
        'thumbnail', 'is_premium', 'is_published', 'total_modules', 'order',
    ];

    protected $casts = [
        'is_premium'   => 'boolean',
        'is_published' => 'boolean',
    ];

    // Tipe kelas
    public const TYPE_REGULAR = 'regular'; // ikut grade + flag is_premium
    public const TYPE_EXTRA   = 'extra';   // mis. TOEFL — bebas akses, tanpa premium, menu sendiri

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    public function scopeFree($query)
    {
        return $query->where('is_premium', false);
    }

    /**
     * Filter konten sesuai kelas (grade) user.
     * - Kelas EXTRA: selalu tampil untuk semua (lintas kelas, tanpa premium).
     * - Kelas REGULAR: ikut aturan grade.
     * Admin/mentor lihat semua; student hanya grade-nya + konten tanpa grade.
     */
    public function scopeForUser($query, $user)
    {
        if (!$user || in_array($user->role, ['superadmin', 'admin', 'mentor']) || empty($user->grade_id)) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            // Extra (TOEFL dsb) selalu lolos filter grade
            $q->where('course_type', self::TYPE_EXTRA)
                ->orWhereNull('grade_id')
                ->orWhere('grade_id', $user->grade_id);
        });
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('course_type', $type);
    }

    // Kelas reguler saja (untuk menu "Kelas" — Extra punya menu sendiri)
    public function scopeRegularType($query)
    {
        return $query->where('course_type', self::TYPE_REGULAR);
    }

    // Hanya Extra Class (untuk menu Extra Class)
    public function scopeExtraType($query)
    {
        return $query->where('course_type', self::TYPE_EXTRA);
    }

    // -------------------------------------------------------------------------
    // Access Helpers
    // -------------------------------------------------------------------------

    public function isExtra(): bool   { return $this->course_type === self::TYPE_EXTRA; }

    /**
     * Apakah kelas ini menuntut langganan premium?
     * - extra:   tidak pernah wajib premium.
     * - regular: ikut flag is_premium.
     */
    public function requiresPremium(): bool
    {
        return $this->isExtra() ? false : (bool) $this->is_premium;
    }

    /**
     * Apakah kelas ini terikat aturan grade siswa?
     * Extra bersifat lintas kelas, jadi tidak.
     */
    public function enforcesGrade(): bool
    {
        return $this->course_type !== self::TYPE_EXTRA;
    }

    /**
     * Cek apakah $user boleh mengakses kelas ini (grade + premium).
     */
    public function isAccessibleBy($user): bool
    {
        if (!$user) return false;
        if (in_array($user->role, ['superadmin', 'admin', 'mentor'])) return true;

        // Aturan kelas (grade) — kecuali extra
        if ($this->enforcesGrade() && $this->grade_id !== null) {
            if ((int) $user->grade_id !== (int) $this->grade_id) {
                return false;
            }
        }

        // Aturan premium
        if ($this->requiresPremium() && !$user->isPremium()) {
            return false;
        }

        return true;
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(CourseModule::class)->orderBy('order');
    }

    // Akses langsung ke semua materi tanpa lewat modul
    public function materials(): HasManyThrough
    {
        return $this->hasManyThrough(CourseMaterial::class, CourseModule::class, 'course_id', 'module_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(UserCourseEnrollment::class);
    }

    public function liveClasses(): HasMany
    {
        return $this->hasMany(LiveClass::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(StudentNote::class);
    }
}
