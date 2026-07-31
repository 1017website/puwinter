<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class AppSetting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Ambil nilai setting. Aman dipanggil walau tabel belum termigrasi
     * (mengembalikan $default).
     */
    public static function get(string $key, $default = null)
    {
        if (! Schema::hasTable('app_settings')) {
            return $default;
        }

        $row = static::where('key', $key)->first();

        return $row ? $row->value : $default;
    }

    /**
     * Simpan / perbarui setting.
     */
    public static function set(string $key, $value): void
    {
        if (! Schema::hasTable('app_settings')) {
            return;
        }

        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * Konfigurasi affiliate/referral.
     * Benefit affiliate diberikan kepada pemilik kode/referrer ketika pembayaran divalidasi admin.
     * Key affiliate_discount_amount tetap dikembalikan 0 untuk kompatibilitas data lama.
     */
    public static function affiliateInfo(): array
    {
        return [
            'affiliate_discount_amount' => 0,
            'affiliate_reward_amount' => (int) static::get('affiliate_reward_amount', 0),
        ];
    }

    public static function studentTryoutEnabled(): bool
    {
        return filter_var(static::get('student_tryout_enabled', '1'), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Tentukan apakah siswa baru wajib memverifikasi alamat emailnya.
     * Default aktif untuk mempertahankan perilaku registrasi yang sudah ada.
     */
    public static function emailVerificationEnabled(): bool
    {
        return filter_var(static::get('email_verification_enabled', '1'), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Pengaturan yang dipakai landing page publik.
     */
    public static function frontendInfo(): array
    {
        return [
            'video_enabled' => filter_var(static::get('frontend_video_enabled', '0'), FILTER_VALIDATE_BOOLEAN),
            'video_url' => static::get('frontend_video_url', ''),
            'video_title' => static::get('frontend_video_title', 'Kenali Program Puwinter'),
            'video_description' => static::get('frontend_video_description', 'Lihat bagaimana Puwinter mendampingi proses belajar Bahasa Inggris siswa.'),
            'video_poster' => static::get('frontend_video_poster', ''),
            'seo_title' => static::get('seo_title', 'Puwinter — Platform Belajar Bahasa Inggris Terbaik Indonesia'),
            'seo_description' => static::get('seo_description', 'Belajar bahasa Inggris lebih cerdas bersama Puwinter. Kelas online, latihan, dan pembahasan bersama tutor terbaik.'),
            'seo_keywords' => static::get('seo_keywords', 'bimbel bahasa Inggris, TKA bahasa Inggris, literasi bahasa Inggris, grammar, TOEFL'),
            'seo_canonical_url' => static::get('seo_canonical_url', ''),
            'seo_robots' => static::get('seo_robots', 'index,follow'),
            'seo_og_title' => static::get('seo_og_title', ''),
            'seo_og_description' => static::get('seo_og_description', ''),
            'seo_og_image' => static::get('seo_og_image', ''),
            'google_tag_manager_id' => static::get('google_tag_manager_id', ''),
            'google_analytics_id' => static::get('google_analytics_id', ''),
            'google_ads_id' => static::get('google_ads_id', ''),
            'meta_pixel_id' => static::get('meta_pixel_id', ''),
        ];
    }

    /**
     * Ambil seluruh setting rekening transfer manual sekaligus.
     */
    public static function bankInfo(): array
    {
        return [
            'bank_name' => static::get('bank_name', ''),
            'bank_account' => static::get('bank_account', ''),
            'bank_holder' => static::get('bank_holder', ''),
            'payment_note' => static::get('payment_note', ''),
        ];
    }
}
