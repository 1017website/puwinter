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
        'duration_minutes', 'total_questions',
        'is_premium', 'is_published', 'series', 'order',
    ];

    protected $casts = [
        'is_premium'   => 'boolean',
        'is_published' => 'boolean',
    ];

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
        if (!$user || in_array($user->role, ['superadmin', 'admin', 'mentor']) || empty($user->grade)) {
            return $query;
        }
        return $query->where(function ($q) use ($user) {
            $q->whereNull('grade')->orWhere('grade', (string) $user->grade);
        });
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

        // Grade (jika di-set)
        if ($this->grade_id !== null && (int) $user->grade_id !== (int) $this->grade_id) {
            return false;
        }

        return $user->canAccessContent($this->plan_id, $this->access_tier ?? 'both');
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
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
