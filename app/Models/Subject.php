<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = [
        'name', 'slug', 'icon', 'color', 'order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function liveClasses(): HasMany
    {
        return $this->hasMany(LiveClass::class);
    }

    public function tryouts(): HasMany
    {
        return $this->hasMany(Tryout::class);
    }

    public function leaderboardScores(): HasMany
    {
        return $this->hasMany(LeaderboardScore::class);
    }
}
