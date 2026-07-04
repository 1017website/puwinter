<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    /**
     * Filter program berdasarkan kelas siswa.
     *
     * - Program tanpa grade di pivot dan grade_id legacy kosong = Semua Kelas.
     * - Program dengan salah satu grade di pivot = tampil hanya untuk kelas tersebut.
     * - grade_id legacy tetap dibaca sebagai fallback untuk data lama.
     */
    public function scopeForGrade($query, ?int $gradeId)
    {
        return $query->where(function ($q) use ($gradeId) {
            $q->where(function ($allClasses) {
                $allClasses->whereNull('grade_id')
                    ->whereDoesntHave('grades');
            });

            if (!empty($gradeId)) {
                $q->orWhereHas('grades', function ($grades) use ($gradeId) {
                    $grades->where('grades.id', $gradeId);
                })->orWhere('grade_id', $gradeId);
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

    /**
     * Label kelas sesuai pilihan admin di menu Program.
     */
    public function gradeLabel(): string
    {
        $grades = $this->relationLoaded('grades')
            ? $this->grades
            : $this->grades()->get();

        if ($grades->isNotEmpty()) {
            return $grades->pluck('name')->implode(', ');
        }

        return $this->grade?->name ?? 'Semua Kelas';
    }

    /**
     * Program tersedia untuk siswa jika:
     * - tidak ada kelas khusus = umum / semua kelas, atau
     * - kelas siswa ada di salah satu kelas program.
     */
    public function appliesToGrade(?int $gradeId): bool
    {
        $selectedGradeIds = $this->relationLoaded('grades')
            ? $this->grades->pluck('id')
            : $this->grades()->pluck('grades.id');

        if ($selectedGradeIds->isNotEmpty()) {
            return !empty($gradeId) && $selectedGradeIds
                ->map(fn ($id) => (int) $id)
                ->contains((int) $gradeId);
        }

        if (empty($this->grade_id)) {
            return true;
        }

        return !empty($gradeId) && (int) $this->grade_id === (int) $gradeId;
    }

    /**
     * ID kelas untuk form admin. Menggabungkan pivot baru + grade_id lama sebagai fallback.
     */
    public function gradeIdsForForm(): array
    {
        $ids = $this->relationLoaded('grades')
            ? $this->grades->pluck('id')->all()
            : $this->grades()->pluck('grades.id')->all();

        if (empty($ids) && !empty($this->grade_id)) {
            $ids = [(int) $this->grade_id];
        }

        return array_values(array_unique(array_map('intval', $ids)));
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * Relasi lama 1 kelas, dipertahankan untuk kompatibilitas data lama.
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Grade::class);
    }

    /**
     * Relasi baru: 1 program bisa berlaku untuk banyak kelas.
     */
    public function grades(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Grade::class,
            'subscription_plan_grades',
            'subscription_plan_id',
            'grade_id'
        )->withTimestamps()->orderBy('grades.order');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }
}
