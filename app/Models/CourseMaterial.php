<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CourseMaterial extends Model
{
    protected $fillable = [
        'module_id', 'title', 'type', 'content_url',
        'duration_minutes', 'is_premium', 'access_tier', 'is_locked', 'order',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'is_locked'  => 'boolean',
    ];

    /**
     * Akses materi: ikut program (plan) course induknya + access_tier materi.
     * Jika access_tier materi null, fallback ke access_tier course.
     */
    public function isAccessibleBy($user): bool
    {
        if (!$user) return false;
        if (in_array($user->role, ['superadmin', 'admin', 'mentor'])) return true;

        $course = $this->module?->course;
        if (!$course) {
            return true; // materi lepas tanpa course = umum
        }

        $tier = $this->access_tier ?: ($course->access_tier ?? 'both');
        return $user->canAccessContent($course->plan_id, $tier);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    public function scopeFree($query)
    {
        return $query->where('is_premium', false);
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }

    public function userProgress(): HasMany
    {
        return $this->hasMany(UserMaterialProgress::class, 'material_id');
    }

    public function savedByUsers(): HasMany
    {
        return $this->hasMany(UserSavedMaterial::class, 'material_id');
    }

    public function discussions(): MorphMany
    {
        return $this->morphMany(Discussion::class, 'discussable');
    }
}
