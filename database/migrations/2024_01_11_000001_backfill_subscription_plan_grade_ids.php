<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Tidak lagi mengisi kelas program berdasarkan nama/slug.
 *
 * Kelas program sekarang murni mengikuti pilihan admin di field
 * subscription_plans.grade_id pada menu Admin > Program.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Sengaja dikosongkan.
    }

    public function down(): void
    {
        // Sengaja dikosongkan.
    }
};
