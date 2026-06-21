<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Revisi Paket Program:
 * - start_date / end_date : periode program (mis. Agustus-Oktober 2026).
 * - quota                 : batas peserta BERBAYAR (nullable = tanpa batas).
 * - flyer_image           : path gambar pamflet untuk landing page.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_plans', function (Blueprint $t) {
            if (!Schema::hasColumn('subscription_plans', 'start_date')) {
                $t->date('start_date')->nullable()->after('duration_months');
            }
            if (!Schema::hasColumn('subscription_plans', 'end_date')) {
                $t->date('end_date')->nullable()->after('start_date');
            }
            if (!Schema::hasColumn('subscription_plans', 'quota')) {
                // null = tanpa batas; angka = kuota peserta berbayar.
                $t->unsignedInteger('quota')->nullable()->after('end_date');
            }
            if (!Schema::hasColumn('subscription_plans', 'flyer_image')) {
                $t->string('flyer_image')->nullable()->after('quota');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $t) {
            foreach (['start_date', 'end_date', 'quota', 'flyer_image'] as $col) {
                if (Schema::hasColumn('subscription_plans', $col)) {
                    $t->dropColumn($col);
                }
            }
        });
    }
};
