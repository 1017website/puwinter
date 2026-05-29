<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sistem skor berbobot kesulitan (IRT sederhana).
 *
 * - tryout_questions.correct_rate  : cache % peserta yang menjawab benar soal ini
 *                                    (0..100). Makin kecil = soal makin sulit.
 * - tryout_questions.answered_count: jumlah peserta yang sudah pernah menjawab.
 * - user_tryout_attempts.weighted_score : skor berbobot kesulitan (ditampilkan
 *                                         sebagai info; ranking tetap pakai `score`).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tryout_questions', function (Blueprint $t) {
            if (!Schema::hasColumn('tryout_questions', 'correct_rate')) {
                $t->decimal('correct_rate', 5, 2)->nullable()->after('difficulty');
            }
            if (!Schema::hasColumn('tryout_questions', 'answered_count')) {
                $t->unsignedInteger('answered_count')->default(0)->after('correct_rate');
            }
        });

        Schema::table('user_tryout_attempts', function (Blueprint $t) {
            if (!Schema::hasColumn('user_tryout_attempts', 'weighted_score')) {
                $t->decimal('weighted_score', 8, 2)->nullable()->after('score');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tryout_questions', function (Blueprint $t) {
            $t->dropColumn(['correct_rate', 'answered_count']);
        });
        Schema::table('user_tryout_attempts', function (Blueprint $t) {
            $t->dropColumn('weighted_score');
        });
    }
};
