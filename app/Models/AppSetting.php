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
        if (!Schema::hasTable('app_settings')) {
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
        if (!Schema::hasTable('app_settings')) {
            return;
        }

        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * Ambil seluruh setting rekening transfer manual sekaligus.
     */
    public static function bankInfo(): array
    {
        return [
            'bank_name'      => static::get('bank_name', ''),
            'bank_account'   => static::get('bank_account', ''),
            'bank_holder'    => static::get('bank_holder', ''),
            'payment_note'   => static::get('payment_note', ''),
        ];
    }
}
