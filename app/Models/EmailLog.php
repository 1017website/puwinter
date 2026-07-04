<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLog extends Model
{
    protected $fillable = [
        'user_id',
        'email_verification_id',
        'type',
        'source',
        'mailer',
        'transport',
        'from_email',
        'from_name',
        'to_email',
        'to_name',
        'subject',
        'status',
        'response',
        'error_message',
        'error_trace',
        'payload',
        'sent_at',
        'failed_at',
    ];

    protected $casts = [
        'payload'   => 'array',
        'sent_at'   => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function emailVerification(): BelongsTo
    {
        return $this->belongsTo(EmailVerification::class);
    }

    public function markSent(?string $response = null, array $payload = []): void
    {
        $this->update([
            'status'     => 'sent',
            'response'   => $response,
            'payload'    => array_merge($this->payload ?? [], $payload),
            'sent_at'    => now(),
            'failed_at'  => null,
        ]);
    }

    public function markFailed(\Throwable $exception, array $payload = []): void
    {
        $this->update([
            'status'        => 'failed',
            'response'      => $exception->getMessage(),
            'error_message' => $exception->getMessage(),
            'error_trace'   => mb_substr($exception->getTraceAsString(), 0, 65000),
            'payload'       => array_merge($this->payload ?? [], $payload),
            'failed_at'     => now(),
        ]);
    }
}
