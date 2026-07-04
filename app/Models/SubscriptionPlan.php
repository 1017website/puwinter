<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name', 'slug', 'tier', 'grade_id', 'duration_months', 'start_date', 'end_date',
        'quota', 'flyer_image', 'price', 'original_price',
        'is_popular', 'features', 'bonus', 'is_active', 'order',
    ];

    protected $casts = [
        'features'        => 'array',
        'is_popular'      => 'boolean',
        'is_active'       => 'boolean',
        'duration_months' => 'integer',
        'grade_id'        => 'integer',
        'start_date'      => 'date',
        'end_date'        => 'date',
        'quota'           => 'integer',
        'price'           => 'integer',
        'original_price'  => 'integer',
        'order'           => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }


    public function scopeForGrade($query, ?int $gradeId)
    {
        return $query->where(function ($q) use ($gradeId) {
            $q->whereNull('grade_id');
            if (!empty($gradeId)) {
                $q->orWhere('grade_id', $gradeId);
            }
        });
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isExclusive(): bool
    {
        return $this->tier === 'exclusive';
    }

    /**
     * Jumlah peserta BERBAYAR aktif pada program ini (untuk kuota).
     */
    public function paidCount(): int
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('program_enrollments')) {
            return 0;
        }
        return \App\Models\ProgramEnrollment::where('plan_id', $this->id)
            ->where('status', 'paid')
            ->where(function ($q) {
                $q->whereNull('paid_expires_at')->orWhere('paid_expires_at', '>', now());
            })
            ->count();
    }

    /**
     * Sisa kuota. null = tanpa batas.
     */
    public function remainingQuota(): ?int
    {
        if ($this->quota === null) {
            return null;
        }
        return max(0, (int) $this->quota - $this->paidCount());
    }

    /**
     * Apakah kuota berbayar sudah penuh.
     */
    public function isQuotaFull(): bool
    {
        $remaining = $this->remainingQuota();
        return $remaining !== null && $remaining <= 0;
    }

    /**
     * Label periode untuk ditampilkan (mis. "Agu 2026 - Okt 2026").
     */
    public function periodLabel(): ?string
    {
        if (!$this->start_date && !$this->end_date) {
            return null;
        }
        $start = $this->start_date?->translatedFormat('M Y');
        $end   = $this->end_date?->translatedFormat('M Y');
        if ($start && $end) {
            return $start . ' - ' . $end;
        }
        return $start ?: $end;
    }

    public function discountPercentage(): int
    {
        return (int) round((1 - $this->price / $this->original_price) * 100);
    }

    public function pricePerMonth(): int
    {
        return (int) round($this->price / $this->duration_months);
    }

    public function appliesToGrade(?int $gradeId): bool
    {
        return $this->grade_id === null || empty($gradeId) || (int) $this->grade_id === (int) $gradeId;
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function grade(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Grade::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }
}
