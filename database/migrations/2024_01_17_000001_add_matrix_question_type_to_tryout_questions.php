<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tryout_questions')) {
            return;
        }

        if (Schema::hasColumn('tryout_questions', 'question_type')) {
            try {
                DB::statement("ALTER TABLE tryout_questions MODIFY question_type ENUM('single','multiple','matrix') NOT NULL DEFAULT 'single'");
            } catch (\Throwable $e) {
                // Untuk database non-MySQL saat development/test, abaikan perubahan enum.
                // Kolom tetap bisa dipakai sebagai string pada DB yang tidak membatasi enum.
            }
        }

        Schema::table('tryout_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('tryout_questions', 'matrix_columns')) {
                $table->json('matrix_columns')->nullable()->after('correct_answers');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('tryout_questions')) {
            return;
        }

        if (Schema::hasColumn('tryout_questions', 'matrix_columns')) {
            Schema::table('tryout_questions', function (Blueprint $table) {
                $table->dropColumn('matrix_columns');
            });
        }

        if (Schema::hasColumn('tryout_questions', 'question_type')) {
            DB::table('tryout_questions')
                ->where('question_type', 'matrix')
                ->update(['question_type' => 'multiple']);

            try {
                DB::statement("ALTER TABLE tryout_questions MODIFY question_type ENUM('single','multiple') NOT NULL DEFAULT 'single'");
            } catch (\Throwable $e) {
                // Abaikan pada database non-MySQL.
            }
        }
    }
};
