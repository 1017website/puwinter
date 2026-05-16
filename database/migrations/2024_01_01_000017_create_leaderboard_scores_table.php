<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leaderboard_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('total_score', 8, 2)->default(0);
            $table->decimal('percentile', 5, 2)->nullable();
            $table->integer('rank_global')->nullable();
            $table->integer('rank_school')->nullable();
            $table->integer('rank_city')->nullable();
            $table->integer('rank_province')->nullable();
            $table->unique(['user_id', 'subject_id']);
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaderboard_scores');
    }
};
