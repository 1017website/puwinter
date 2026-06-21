<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ARSITEKTUR AKSES PER-PROGRAM
 *
 * Konsep:
 * - "Program" = subscription_plans (1 plan = 1 program).
 * - Siswa MENDAFTAR program (gratis dulu) -> tercatat di program_enrollments
 *   dengan status 'free'. Setelah membayar -> status 'paid'.
 * - Tiap konten (course / tryout / live_class / material) menempel pada 1 program
 *   (plan_id) dan punya access_tier:
 *       free  = bisa diakses peserta gratis & berbayar
 *       paid  = hanya peserta berbayar
 *       both  = sama seperti free (eksplisit "untuk semua")
 *   (free & both sama-sama terbuka utk peserta program; 'paid' yang membatasi.)
 * - Siswa boleh terdaftar di BANYAK program sekaligus.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ---------------------------------------------------------------------
        // 1) program_enrollments: pendaftaran siswa ke sebuah program (plan)
        // ---------------------------------------------------------------------
        if (!Schema::hasTable('program_enrollments')) {
            Schema::create('program_enrollments', function (Blueprint $t) {
                $t->id();
                $t->foreignId('user_id')->constrained()->cascadeOnDelete();
                $t->foreignId('plan_id')->constrained('subscription_plans')->cascadeOnDelete();
                $t->enum('status', ['free', 'paid'])->default('free')->index();
                $t->foreignId('subscription_id')->nullable()
                    ->constrained('subscriptions')->nullOnDelete(); // sumber status paid
                $t->timestamp('enrolled_at')->useCurrent();
                $t->timestamp('paid_at')->nullable();
                $t->timestamp('paid_expires_at')->nullable(); // akhir masa berbayar (ikut subscription)
                $t->timestamps();
                $t->unique(['user_id', 'plan_id']);
            });
        }

        // ---------------------------------------------------------------------
        // 2) plan_id + access_tier pada konten
        // ---------------------------------------------------------------------
        foreach (['courses', 'tryouts', 'live_classes'] as $table) {
            if (!Schema::hasTable($table)) continue;
            Schema::table($table, function (Blueprint $t) use ($table) {
                if (!Schema::hasColumn($table, 'plan_id')) {
                    $t->foreignId('plan_id')->nullable()
                        ->constrained('subscription_plans')->nullOnDelete();
                }
                if (!Schema::hasColumn($table, 'access_tier')) {
                    $t->enum('access_tier', ['free', 'paid', 'both'])
                        ->default('both')->index();
                }
            });
        }

        // ---------------------------------------------------------------------
        // 3) access_tier pada materi (course_materials sudah punya is_premium;
        //    access_tier lebih ekspresif & jadi sumber kebenaran utama).
        // ---------------------------------------------------------------------
        if (Schema::hasTable('course_materials') && !Schema::hasColumn('course_materials', 'access_tier')) {
            Schema::table('course_materials', function (Blueprint $t) {
                $t->enum('access_tier', ['free', 'paid', 'both'])
                    ->default('both')->after('is_premium')->index();
            });
        }
    }

    public function down(): void
    {
        foreach (['courses', 'tryouts', 'live_classes'] as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    if (Schema::hasColumn($table, 'plan_id')) {
                        $t->dropConstrainedForeignId('plan_id');
                    }
                    if (Schema::hasColumn($table, 'access_tier')) {
                        $t->dropColumn('access_tier');
                    }
                });
            }
        }
        if (Schema::hasTable('course_materials') && Schema::hasColumn('course_materials', 'access_tier')) {
            Schema::table('course_materials', fn(Blueprint $t) => $t->dropColumn('access_tier'));
        }
        Schema::dropIfExists('program_enrollments');
    }
};
