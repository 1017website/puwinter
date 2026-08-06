<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LiveClass extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id', 'mentor_id', 'subject_id', 'grade', 'grade_id', 'class_type', 'plan_id', 'access_tier', 'title', 'description',
        'scheduled_at', 'duration_minutes', 'zoom_link', 'zoom_meeting_id',
        'is_premium', 'status', 'recording_url', 'total_participants',
    ];

    protected $casts = [
        'scheduled_at'       => 'datetime',
        'is_premium'         => 'boolean',
        'duration_minutes'   => 'integer',
        'total_participants' => 'integer',
    ];

    // Tipe live class
    public const TYPE_REGULAR = 'regular'; // live class umum (ikut grade + flag premium)
    public const TYPE_PRIVATE = 'private'; // privat/eksklusif — wajib premium

    // Alasan akses. Dipakai controller (pesan error) dan blade (label tombol)
    // dari satu sumber yang sama, supaya keduanya tidak pernah bertolak belakang.
    public const ACCESS_OK              = 'ok';
    public const ACCESS_WRONG_GRADE     = 'wrong_grade';
    public const ACCESS_NOT_ENROLLED    = 'not_enrolled';
    public const ACCESS_NEEDS_PAID      = 'needs_paid';
    public const ACCESS_NEEDS_EXCLUSIVE = 'needs_exclusive';
    public const ACCESS_DENIED          = 'denied';

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')
                     ->where('scheduled_at', '>', now())
                     ->orderBy('scheduled_at');
    }

    public function scopeLive($query)
    {
        return $query->where('status', 'live');
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    public function scopeForUser($query, $user)
    {
        if (!$user || in_array($user->role, ['superadmin', 'admin', 'mentor']) || empty($user->grade_id)) {
            return $query;
        }
        return $query->where(function ($q) use ($user) {
            $q->whereNull('grade_id')->orWhere('grade_id', $user->grade_id);
        });
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('class_type', $type);
    }

    public function scopeRegularType($query)
    {
        return $query->where('class_type', self::TYPE_REGULAR);
    }

    public function scopePrivateType($query)
    {
        return $query->where('class_type', self::TYPE_PRIVATE);
    }

    // -------------------------------------------------------------------------
    // Access Helpers
    // -------------------------------------------------------------------------

    public function isPrivate(): bool
    {
        return $this->class_type === self::TYPE_PRIVATE;
    }

    /**
     * Private selalu wajib premium; regular ikut flag is_premium.
     */
    public function requiresPremium(): bool
    {
        return $this->isPrivate() ? true : (bool) $this->is_premium;
    }

    /**
     * Tier akses konten ini: 'free' | 'paid' | 'both'. Default 'paid'
     * (live class umumnya manfaat utama membayar program).
     */
    public function accessTier(): string
    {
        return $this->access_tier ?: 'paid';
    }

    /**
     * Cek apakah $user boleh mengakses live class ini (grade + program + premium).
     */
    public function isAccessibleBy($user): bool
    {
        return $this->accessStatusFor($user) === self::ACCESS_OK;
    }

    /**
     * Alasan spesifik kenapa $user boleh / tidak boleh masuk.
     * Selalu pakai ini (bukan pesan generik) supaya siswa tahu apa yang harus
     * dilakukan: daftar program, bayar program, ganti kelas, atau upgrade.
     */
    public function accessStatusFor($user): string
    {
        if (! $user) {
            return self::ACCESS_DENIED;
        }
        if (in_array($user->role, ['superadmin', 'admin', 'mentor'])) {
            return self::ACCESS_OK;
        }

        // Grade. Siswa yang grade_id-nya belum terisi jangan diblokir —
        // konsisten dengan scopeForUser() dan User::canAccessGradeId().
        if ($this->grade_id !== null
            && ! empty($user->grade_id)
            && (int) $user->grade_id !== (int) $this->grade_id) {
            return self::ACCESS_WRONG_GRADE;
        }

        // Live class private/exclusive: wajib premium tier EXCLUSIVE.
        if ($this->isPrivate() && ! $user->isExclusive()) {
            return self::ACCESS_NEEDS_EXCLUSIVE;
        }

        // Akses per-program (plan_id + access_tier).
        if ($this->plan_id !== null) {
            if (! $user->isEnrolledInProgram($this->plan_id)) {
                // Tier gratis: enrollment cuma formalitas dan dibuat otomatis
                // saat siswa membuka kelas — lihat autoEnroll().
                return $this->canAutoEnroll($user) ? self::ACCESS_OK : self::ACCESS_NOT_ENROLLED;
            }
            if ($this->accessTier() === 'paid' && ! $user->hasPaidProgram($this->plan_id)) {
                return self::ACCESS_NEEDS_PAID;
            }
        }

        return self::ACCESS_OK;
    }

    /**
     * Apakah $user bisa didaftarkan otomatis ke program kelas online ini.
     * Hanya untuk tier gratis dan hanya jika program memang berlaku untuk
     * kelasnya — setara siswa menekan "Daftar" di halaman Program, jadi tidak
     * melonggarkan konten berbayar.
     */
    public function canAutoEnroll($user): bool
    {
        if (! $user || $this->isPrivate() || $this->plan_id === null) {
            return false;
        }
        if (! in_array($this->accessTier(), ['free', 'both'], true)) {
            return false;
        }
        $plan = $this->plan;

        return $plan !== null && $plan->is_active && $plan->appliesToGrade($user->grade_id);
    }

    /**
     * Apakah halaman detail program kelas online ini boleh dibuka $user.
     * ProgramController::show() menolak (403) program yang bukan untuk kelas
     * siswa, jadi CTA "Daftar Program" harus jatuh ke daftar program.
     */
    public function programPageOpenableBy($user): bool
    {
        $plan = $this->plan;

        return $plan !== null && $plan->appliesToGrade($user->grade_id ?? null);
    }

    /**
     * Daftarkan $user (gratis) ke program kelas online ini. Idempoten.
     */
    public function autoEnroll($user): bool
    {
        if (! $this->canAutoEnroll($user)) {
            return false;
        }

        ProgramEnrollment::firstOrCreate(
            ['user_id' => $user->id, 'plan_id' => $this->plan_id],
            ['status' => ProgramEnrollment::STATUS_FREE, 'enrolled_at' => now()]
        );

        return true;
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isLive(): bool
    {
        return $this->status === 'live';
    }

    /**
     * Nama kelas/tingkat sasaran, atau null kalau berlaku untuk semua kelas.
     *
     * Catatan: JANGAN pakai $liveClass->grade->name — tabel live_classes masih
     * punya kolom legacy `grade` (string) yang menutupi relasi grade(), jadi
     * properti itu mengembalikan isi kolom, bukan model Grade.
     */
    public function gradeName(): ?string
    {
        if ($this->grade_id === null) {
            return null;
        }

        return $this->grade()->value('name');
    }

    public function hasRecording(): bool
    {
        return $this->recording_url !== null;
    }

    public function formattedSchedule(): string
    {
        return $this->scheduled_at->translatedFormat('l, d F Y • H:i') . ' WIB';
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(LiveClassAttendance::class);
    }
}
