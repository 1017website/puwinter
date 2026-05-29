<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $table = 'app_notifications';

    protected $fillable = [
        'user_id', 'type', 'title', 'body', 'url', 'icon', 'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    /** Ikon default berdasarkan type bila kolom icon kosong. */
    public function iconClass(): string
    {
        if (!empty($this->icon)) {
            return $this->icon;
        }

        return match ($this->type) {
            'success' => 'fa-circle-check',
            'warning' => 'fa-triangle-exclamation',
            'tryout'  => 'fa-clipboard-list',
            'live'    => 'fa-video',
            'payment' => 'fa-credit-card',
            default   => 'fa-bell',
        };
    }

    /** Warna aksen berdasarkan type. */
    public function color(): string
    {
        return match ($this->type) {
            'success' => '#10B981',
            'warning' => '#F59E0B',
            'tryout'  => '#7C3AED',
            'live'    => '#DC2626',
            'payment' => '#2563EB',
            default   => '#64748B',
        };
    }

    /**
     * Helper ringkas untuk membuat notifikasi dari mana saja.
     * Contoh: Notification::notify($userId, 'tryout', 'Tryout selesai', '...', route(...));
     */
    public static function notify(int $userId, string $type, string $title, ?string $body = null, ?string $url = null, ?string $icon = null): self
    {
        return static::create([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'body'    => $body,
            'url'     => $url,
            'icon'    => $icon,
        ]);
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
