<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Konversi data grade siswa LAMA (format "X IPA", "XI IPS", dst) ke format
 * baru "10" / "11" / "12" agar cocok dengan filter konten per kelas.
 *
 * Tanpa konversi ini, siswa lama (mis. grade "X IPA") tidak akan pernah
 * cocok dengan konten grade "10" sehingga selalu ditolak 403.
 */
return new class extends Migration
{
    public function up(): void
    {
        $map = [
            'X IPA'   => '10', 'X IPS'   => '10', 'X'   => '10',
            'XI IPA'  => '11', 'XI IPS'  => '11', 'XI'  => '11',
            'XII IPA' => '12', 'XII IPS' => '12', 'XII' => '12',
            // "Lulus/Gap Year" / "Alumni" tidak dipetakan ke kelas manapun → dikosongkan
            'Lulus/Gap Year' => null,
            'Alumni'         => null,
        ];

        foreach ($map as $old => $new) {
            DB::table('users')->where('grade', $old)->update(['grade' => $new]);
        }

        // Pengaman: nilai grade apa pun yang BUKAN 10/11/12 dan tidak kosong
        // dikosongkan agar tidak menyebabkan blokir tak terduga.
        DB::table('users')
            ->whereNotNull('grade')
            ->whereNotIn('grade', ['10', '11', '12', ''])
            ->update(['grade' => null]);
    }

    public function down(): void
    {
        // Konversi tidak dapat dikembalikan secara akurat (banyak-ke-satu).
        // Sengaja dibiarkan no-op.
    }
};
