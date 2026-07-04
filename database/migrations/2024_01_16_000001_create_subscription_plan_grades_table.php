<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Satu program bisa berlaku untuk banyak kelas.
     *
     * Catatan kompatibilitas:
     * - subscription_plans.grade_id lama tetap dibiarkan sebagai fallback.
     * - Data lama yang punya grade_id otomatis dimasukkan ke pivot ini.
     * - Program tanpa baris pivot dan grade_id NULL berarti berlaku untuk Semua Kelas.
     */
    public function up(): void
    {
        if (!Schema::hasTable('subscription_plan_grades')) {
            Schema::create('subscription_plan_grades', function (Blueprint $table) {
                $table->id();
                $table->foreignId('subscription_plan_id')
                    ->constrained('subscription_plans')
                    ->cascadeOnDelete();
                $table->foreignId('grade_id')
                    ->constrained('grades')
                    ->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['subscription_plan_id', 'grade_id'], 'spg_plan_grade_unique');
            });
        }

        // Backfill pilihan kelas lama dari subscription_plans.grade_id ke pivot.
        if (Schema::hasTable('subscription_plans') && Schema::hasColumn('subscription_plans', 'grade_id')) {
            DB::table('subscription_plans')
                ->whereNotNull('grade_id')
                ->select(['id', 'grade_id'])
                ->orderBy('id')
                ->chunkById(100, function ($plans) {
                    foreach ($plans as $plan) {
                        DB::table('subscription_plan_grades')->updateOrInsert(
                            [
                                'subscription_plan_id' => $plan->id,
                                'grade_id' => $plan->grade_id,
                            ],
                            [
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plan_grades');
    }
};
