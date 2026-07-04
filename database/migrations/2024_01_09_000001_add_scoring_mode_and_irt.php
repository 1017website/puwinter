<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dua tipe penilaian tryout: 'regular' (existing +4/-1) & 'irt'.
 *
 * - tryouts.scoring_mode    : mode penilaian yang dipakai tryout ini.
 * - tryouts.irt_calibrated  : true bila bobot IRT sudah dihitung (setelah tryout ditutup).
 * - tryout_questions.irt_b      : parameter kesulitan Rasch, b = ln((1-p)/p).
 * - tryout_questions.irt_weight : bobot poin ternormalisasi 0..100 (dipakai untuk skor IRT).
 * - user_tryout_attempts.irt_score : skor akhir mode IRT (skala 0..100).
 *
 * Idempotent (aman dijalankan ulang di shared hosting via /system/maintenance).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tryouts', function (Blueprint $t) {
            if (!Schema::hasColumn('tryouts', 'scoring_mode')) {
                $t->enum('scoring_mode', ['regular', 'irt'])
                    ->default('regular')
                    ->after('total_questions')
                    ->index();
            }
            if (!Schema::hasColumn('tryouts', 'irt_calibrated')) {
                $t->boolean('irt_calibrated')->default(false)->after('scoring_mode');
            }
        });

        Schema::table('tryout_questions', function (Blueprint $t) {
            if (!Schema::hasColumn('tryout_questions', 'irt_b')) {
                $t->float('irt_b')->nullable()->after('correct_rate');
            }
            if (!Schema::hasColumn('tryout_questions', 'irt_weight')) {
                $t->float('irt_weight')->nullable()->after('irt_b');
            }
        });

        Schema::table('user_tryout_attempts', function (Blueprint $t) {
            if (!Schema::hasColumn('user_tryout_attempts', 'irt_score')) {
                $t->decimal('irt_score', 8, 2)->nullable()->after('weighted_score');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tryouts', function (Blueprint $t) {
            if (Schema::hasColumn('tryouts', 'scoring_mode')) {
                $t->dropColumn('scoring_mode');
            }
            if (Schema::hasColumn('tryouts', 'irt_calibrated')) {
                $t->dropColumn('irt_calibrated');
            }
        });

        Schema::table('tryout_questions', function (Blueprint $t) {
            foreach (['irt_b', 'irt_weight'] as $col) {
                if (Schema::hasColumn('tryout_questions', $col)) {
                    $t->dropColumn($col);
                }
            }
        });

        Schema::table('user_tryout_attempts', function (Blueprint $t) {
            if (Schema::hasColumn('user_tryout_attempts', 'irt_score')) {
                $t->dropColumn('irt_score');
            }
        });
    }
};
