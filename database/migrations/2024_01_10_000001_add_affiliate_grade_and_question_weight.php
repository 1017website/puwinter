<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Revisi:
 * - Affiliate/referral siswa.
 * - Program dikaitkan ke kelas/grade tertentu.
 * - Bobot nilai custom per soal untuk mode regular.
 *
 * Idempotent agar aman dijalankan di hosting yang sudah punya sebagian kolom.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'affiliate_code')) {
                    $table->string('affiliate_code', 32)->nullable()->unique()->after('grade_locked');
                }
                if (!Schema::hasColumn('users', 'referred_by_user_id')) {
                    $table->foreignId('referred_by_user_id')->nullable()->after('affiliate_code')
                        ->constrained('users')->nullOnDelete();
                }
            });
        }



        if (Schema::hasTable('users') && Schema::hasColumn('users', 'affiliate_code')) {
            DB::table('users')
                ->where('role', 'student')
                ->whereNull('affiliate_code')
                ->orderBy('id')
                ->get(['id', 'name'])
                ->each(function ($user) {
                    $base = Str::upper(Str::slug((string) ($user->name ?: 'PW'), ''));
                    $base = preg_replace('/[^A-Z0-9]/', '', $base) ?: 'PW';
                    $base = Str::limit($base, 6, '');

                    do {
                        $code = $base . random_int(1000, 9999);
                    } while (DB::table('users')->where('affiliate_code', $code)->exists());

                    DB::table('users')->where('id', $user->id)->update([
                        'affiliate_code' => $code,
                        'updated_at' => now(),
                    ]);
                });
        }

        if (Schema::hasTable('subscription_plans')) {
            Schema::table('subscription_plans', function (Blueprint $table) {
                if (!Schema::hasColumn('subscription_plans', 'grade_id')) {
                    $table->foreignId('grade_id')->nullable()->after('tier')
                        ->constrained('grades')->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                if (!Schema::hasColumn('subscriptions', 'affiliate_referrer_id')) {
                    $table->foreignId('affiliate_referrer_id')->nullable()->after('plan_id')
                        ->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('subscriptions', 'affiliate_code')) {
                    $table->string('affiliate_code', 32)->nullable()->after('affiliate_referrer_id');
                }
                if (!Schema::hasColumn('subscriptions', 'affiliate_original_amount')) {
                    $table->unsignedInteger('affiliate_original_amount')->nullable()->after('total_amount');
                }
                if (!Schema::hasColumn('subscriptions', 'affiliate_discount_amount')) {
                    $table->unsignedInteger('affiliate_discount_amount')->default(0)->after('affiliate_original_amount');
                }
                if (!Schema::hasColumn('subscriptions', 'affiliate_reward_amount')) {
                    $table->unsignedInteger('affiliate_reward_amount')->default(0)->after('affiliate_discount_amount');
                }
            });
        }

        if (Schema::hasTable('tryout_questions')) {
            Schema::table('tryout_questions', function (Blueprint $table) {
                if (!Schema::hasColumn('tryout_questions', 'score_weight')) {
                    $table->decimal('score_weight', 8, 2)->default(1)->after('difficulty');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tryout_questions') && Schema::hasColumn('tryout_questions', 'score_weight')) {
            Schema::table('tryout_questions', fn (Blueprint $table) => $table->dropColumn('score_weight'));
        }

        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                foreach (['affiliate_reward_amount', 'affiliate_discount_amount', 'affiliate_original_amount', 'affiliate_code'] as $column) {
                    if (Schema::hasColumn('subscriptions', $column)) {
                        $table->dropColumn($column);
                    }
                }
                if (Schema::hasColumn('subscriptions', 'affiliate_referrer_id')) {
                    $table->dropConstrainedForeignId('affiliate_referrer_id');
                }
            });
        }

        if (Schema::hasTable('subscription_plans') && Schema::hasColumn('subscription_plans', 'grade_id')) {
            Schema::table('subscription_plans', fn (Blueprint $table) => $table->dropConstrainedForeignId('grade_id'));
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'referred_by_user_id')) {
                    $table->dropConstrainedForeignId('referred_by_user_id');
                }
                if (Schema::hasColumn('users', 'affiliate_code')) {
                    $table->dropUnique(['affiliate_code']);
                    $table->dropColumn('affiliate_code');
                }
            });
        }
    }
};
