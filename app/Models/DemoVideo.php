<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DemoVideo extends Model
{
    public const GRADE_LEVELS = [7, 8, 9, 10, 11, 12];

    protected $fillable = [
        'grade_level',
        'title',
        'description',
        'video_url',
        'poster_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'grade_level' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function playerData(): array
    {
        $url = trim($this->video_url);

        if (preg_match('~(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{6,})~', $url, $matches)) {
            return [
                'type' => 'embed',
                'provider' => 'youtube',
                'url' => 'https://www.youtube-nocookie.com/embed/'.$matches[1]
                    .'?rel=0&modestbranding=1&iv_load_policy=3&fs=0&disablekb=1&color=white&controls=1&playsinline=1&enablejsapi=0',
            ];
        }

        if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $url, $matches)) {
            return [
                'type' => 'embed',
                'provider' => 'vimeo',
                'url' => 'https://player.vimeo.com/video/'.$matches[1].'?title=0&byline=0&portrait=0&dnt=1',
            ];
        }

        return ['type' => 'file', 'provider' => 'file', 'url' => $url];
    }
}
