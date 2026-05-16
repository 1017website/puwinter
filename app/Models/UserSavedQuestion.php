<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSavedQuestion extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'question_id', 'saved_at'];

    protected $casts = ['saved_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(TryoutQuestion::class, 'question_id');
    }
}
