<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * REVISI 2 & 3:
 *
 * 1) Premium Tier (regular | exclusive)
 *    - subscription_plans.tier : tier paket yang dijual.
 *    - subscriptions.tier      : tier yang sedang aktif (di-copy dari plan saat aktivasi).
 *    Pembeda: Premium Exclusive boleh akses live class bertipe 'private' (exclusive),
 *    Premium Regular tidak.
 *
 * 2) Tipe Soal Multiple Jawaban
 *    - tryout_questions.question_type : 'single' (existing) | 'multiple'.
 *    - tryout_questions.correct_answers : JSON daftar kunci untuk tipe multiple,
 *      mis. ["a","c","d"]. Untuk single tetap pakai kolom correct_answer lama.
 */
return new class extends Migration
{
    public function up(): void
    {
        // --- Tier pada plan ---
        if (Schema::hasTable('subscription_plans') && !Schema::hasColumn('subscription_plans', 'tier')) {
            Schema::table('subscription_plans', function (Blueprint $t) {
                $t->enum('tier', ['regular', 'exclusive'])
                    ->default('regular')
                    ->after('slug')
                    ->index();
            });
        }

        // --- Tier pada subscription aktif ---
        if (Schema::hasTable('subscriptions') && !Schema::hasColumn('subscriptions', 'tier')) {
            Schema::table('subscriptions', function (Blueprint $t) {
                $t->enum('tier', ['regular', 'exclusive'])
                    ->default('regular')
                    ->after('plan_id')
                    ->index();
            });

            // Backfill: samakan tier subscription lama dengan tier plan-nya.
            try {
                DB::statement("
                    UPDATE subscriptions s
                    JOIN subscription_plans p ON p.id = s.plan_id
                    SET s.tier = p.tier
                ");
            } catch (\Throwable $e) {
                // SQLite / driver lain: abaikan, default 'regular' sudah aman.
            }
        }

        // --- Tipe soal pada tryout_questions ---
        Schema::table('tryout_questions', function (Blueprint $t) {
            if (!Schema::hasColumn('tryout_questions', 'question_type')) {
                $t->enum('question_type', ['single', 'multiple'])
                    ->default('single')
                    ->after('subject_id')
                    ->index();
            }
            if (!Schema::hasColumn('tryout_questions', 'correct_answers')) {
                // daftar kunci untuk tipe 'multiple' (JSON array of keys)
                $t->json('correct_answers')->nullable()->after('correct_answer');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('subscription_plans', 'tier')) {
            Schema::table('subscription_plans', fn(Blueprint $t) => $t->dropColumn('tier'));
        }
        if (Schema::hasColumn('subscriptions', 'tier')) {
            Schema::table('subscriptions', fn(Blueprint $t) => $t->dropColumn('tier'));
        }
        Schema::table('tryout_questions', function (Blueprint $t) {
            if (Schema::hasColumn('tryout_questions', 'question_type')) {
                $t->dropColumn('question_type');
            }
            if (Schema::hasColumn('tryout_questions', 'correct_answers')) {
                $t->dropColumn('correct_answers');
            }
        });
    }
};
