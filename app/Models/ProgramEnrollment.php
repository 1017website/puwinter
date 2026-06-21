<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramEnrollment extends Model
{
    protected $fillable = [
        'user_id', 'plan_id', 'status', 'subscription_id',
        'enrolled_at', 'paid_at', 'paid_expires_at',
    ];

    protected $casts = [
        'enrolled_at'     => 'datetime',
        'paid_at'         => 'datetime',
        'paid_expires_at' => 'datetime',
    ];

    public const STATUS_FREE = 'free';
    public const STATUS_PAID = 'paid';

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Apakah status berbayar masih berlaku (paid & belum kedaluwarsa).
     */
    public function isPaidActive(): bool
    {
        if ($this->status !== self::STATUS_PAID) {
            return false;
        }
        if ($this->paid_expires_at === null) {
            return true; // tanpa batas waktu
        }
        return $this->paid_expires_at->isFuture();
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
