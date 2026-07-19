<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FrontendVisit extends Model
{
    protected $fillable = [
        'visitor_id',
        'session_id',
        'user_id',
        'ip_hash',
        'path',
        'route_name',
        'referrer',
        'referrer_domain',
        'device',
        'browser',
        'operating_system',
        'user_agent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
