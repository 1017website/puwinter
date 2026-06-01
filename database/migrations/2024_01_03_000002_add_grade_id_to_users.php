<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah grade_id (FK ke master grades) + grade_locked ke users.
 * - grade_id: kelas siswa berbasis master (pengganti string `grade`).
 * - grade_locked: true setelah siswa memilih kelas saat daftar. Untuk ganti
 *   kelas, siswa harus request ke admin (lihat grade_change_requests).
 *
 * Kolom `grade` (string) lama TIDAK dihapus demi backward-compat; nilainya
 * disinkronkan ke grade_id berdasarkan code.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'grade_id')) {
                $table->foreignId('grade_id')->nullable()->after('grade')
                    ->constrained('grades')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'grade_locked')) {
                $table->boolean('grade_locked')->default(false)->after('grade_id');
            }
        });

        // Sinkron grade string lama -> grade_id sesuai code di master.
        $grades = DB::table('grades')->pluck('id', 'code'); // ['10' => 1, ...]
        foreach ($grades as $code => $id) {
            DB::table('users')
                ->where('grade', (string) $code)
                ->update(['grade_id' => $id, 'grade_locked' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'grade_id')) {
                $table->dropConstrainedForeignId('grade_id');
            }
            if (Schema::hasColumn('users', 'grade_locked')) {
                $table->dropColumn('grade_locked');
            }
        });
    }
};
