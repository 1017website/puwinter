<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan kolom `grade` (kelas: 10 / 11 / 12) ke konten.
 * NULL = berlaku untuk semua kelas.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['courses', 'live_classes', 'tryouts'] as $table) {
            if (Schema::hasColumn($table, 'grade')) {
                continue;
            }
            Schema::table($table, function (Blueprint $t) {
                // enum nullable; null artinya semua kelas
                $t->enum('grade', ['10', '11', '12'])->nullable()->after('subject_id')->index();
            });
        }
    }

    public function down(): void
    {
        foreach (['courses', 'live_classes', 'tryouts'] as $table) {
            if (!Schema::hasColumn($table, 'grade')) {
                continue;
            }
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn('grade');
            });
        }
    }
};
