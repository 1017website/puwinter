<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TryoutPassage extends Model
{
    protected $fillable = [
        'tryout_id',
        'title',
        'passage_text',
        'passage_image',
        'source',
        'order',
    ];

    public function tryout(): BelongsTo
    {
        return $this->belongsTo(Tryout::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(TryoutQuestion::class, 'passage_id')->orderBy('order');
    }

    public function hasContent(): bool
    {
        return filled($this->passage_text) || filled($this->passage_image);
    }
}
