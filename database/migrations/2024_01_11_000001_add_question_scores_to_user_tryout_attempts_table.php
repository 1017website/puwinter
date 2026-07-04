<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_tryout_attempts', function (Blueprint $table) {
            if (!Schema::hasColumn('user_tryout_attempts', 'question_scores')) {
                $table->json('question_scores')->nullable()->after('answers');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_tryout_attempts', function (Blueprint $table) {
            if (Schema::hasColumn('user_tryout_attempts', 'question_scores')) {
                $table->dropColumn('question_scores');
            }
        });
    }
};
