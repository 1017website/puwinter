<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * - Tambah grade_id ke courses / live_classes / tryouts (pengganti string grade).
 * - Tambah course_type ke courses:
 *     regular  = kelas reguler (ikut grade + flag is_premium seperti biasa)
 *     extra    = kelas ekstra (mis. TOEFL) — bebas akses, TANPA premium, lintas kelas
 *     private  = kelas privat/eksklusif — WAJIB premium
 */
return new class extends Migration
{
    public function up(): void
    {
        $gradeIdByCode = DB::table('grades')->pluck('id', 'code');

        foreach (['courses', 'live_classes', 'tryouts'] as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            Schema::table($table, function (Blueprint $t) use ($table) {
                if (!Schema::hasColumn($table, 'grade_id')) {
                    $t->foreignId('grade_id')->nullable()->after('grade')
                        ->constrained('grades')->nullOnDelete();
                }
            });

            // Sinkron grade string lama -> grade_id
            foreach ($gradeIdByCode as $code => $id) {
                DB::table($table)
                    ->where('grade', (string) $code)
                    ->update(['grade_id' => $id]);
            }
        }

        // course_type khusus tabel courses
        if (Schema::hasTable('courses') && !Schema::hasColumn('courses', 'course_type')) {
            Schema::table('courses', function (Blueprint $t) {
                $t->enum('course_type', ['regular', 'extra', 'private'])
                    ->default('regular')
                    ->after('grade_id')
                    ->index();
            });
        }
    }

    public function down(): void
    {
        foreach (['courses', 'live_classes', 'tryouts'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'grade_id')) {
                Schema::table($table, fn(Blueprint $t) => $t->dropConstrainedForeignId('grade_id'));
            }
        }
        if (Schema::hasTable('courses') && Schema::hasColumn('courses', 'course_type')) {
            Schema::table('courses', fn(Blueprint $t) => $t->dropColumn('course_type'));
        }
    }
};
