<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mencatat berapa kali siswa meninggalkan jendela tryout (pindah tab/minimize).
 * Dipakai sebagai indikator integritas pengerjaan.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('user_tryout_attempts', 'tab_switch_count')) {
            return;
        }

        Schema::table('user_tryout_attempts', function (Blueprint $table) {
            $table->unsignedSmallInteger('tab_switch_count')->default(0)->after('weighted_score');
        });
    }

    public function down(): void
    {
        Schema::table('user_tryout_attempts', function (Blueprint $table) {
            $table->dropColumn('tab_switch_count');
        });
    }
};
