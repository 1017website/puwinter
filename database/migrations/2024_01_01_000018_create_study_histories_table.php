<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('activity_type', ['material', 'tryout', 'live_class', 'pdf', 'pembahasan']);
            $table->morphs('reference');
            $table->integer('duration_seconds')->default(0);
            $table->decimal('score', 6, 2)->nullable();
            $table->string('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_histories');
    }
};
