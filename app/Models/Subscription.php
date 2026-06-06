<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    protected $fillable = [
        'user_id', 'plan_id', 'status',
        'started_at', 'expired_at', 'payment_method',
        'midtrans_order_id', 'midtrans_snap_token', 'amount_paid',
        'unique_code', 'total_amount', 'payment_proof', 'proof_uploaded_at',
    ];

    protected $casts = [
        'started_at'        => 'datetime',
        'expired_at'        => 'datetime',
        'proof_uploaded_at' => 'datetime',
        'amount_paid'       => 'integer',
        'unique_code'       => 'integer',
        'total_amount'      => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('expired_at', '>', now());
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expired_at->isFuture();
    }

    public function daysRemaining(): int
    {
        return max(0, now()->diffInDays($this->expired_at, false));
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

    public function logs(): HasMany
    {
        return $this->hasMany(PaymentLog::class);
    }
}
