<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Koreksi konsep:
 * - PRIVATE class kini bagian dari LIVE CLASS (bukan course). Tambah `class_type`
 *   enum 'regular' | 'private' di live_classes. Private wajib premium.
 * - course_type pada courses disederhanakan menjadi 'regular' | 'extra' saja.
 *   Data course lama bertipe 'private' dialihkan ke 'regular'.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1) live_classes.class_type
        if (Schema::hasTable('live_classes') && !Schema::hasColumn('live_classes', 'class_type')) {
            Schema::table('live_classes', function (Blueprint $t) {
                $t->enum('class_type', ['regular', 'private'])
                    ->default('regular')
                    ->after('subject_id')
                    ->index();
            });
        }

        // 2) Alihkan course bertipe 'private' (jika ada) ke 'regular' sebelum ubah enum.
        if (Schema::hasTable('courses') && Schema::hasColumn('courses', 'course_type')) {
            DB::table('courses')->where('course_type', 'private')->update(['course_type' => 'regular']);

            // Persempit enum menjadi regular/extra. MySQL: ubah definisi kolom.
            // Pakai statement mentah agar kompatibel; aman karena nilai sudah dibersihkan.
            try {
                DB::statement("ALTER TABLE courses MODIFY COLUMN course_type ENUM('regular','extra') NOT NULL DEFAULT 'regular'");
            } catch (\Throwable $e) {
                // Jika driver tidak mendukung MODIFY (mis. SQLite), abaikan — nilai 'private'
                // sudah tidak ada sehingga aplikasi tetap konsisten.
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('live_classes') && Schema::hasColumn('live_classes', 'class_type')) {
            Schema::table('live_classes', fn(Blueprint $t) => $t->dropColumn('class_type'));
        }

        if (Schema::hasTable('courses') && Schema::hasColumn('courses', 'course_type')) {
            try {
                DB::statement("ALTER TABLE courses MODIFY COLUMN course_type ENUM('regular','extra','private') NOT NULL DEFAULT 'regular'");
            } catch (\Throwable $e) {
                // no-op
            }
        }
    }
};
