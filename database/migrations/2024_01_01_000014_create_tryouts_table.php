<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tryouts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description')->nullable();
            $table->integer('duration_minutes')->default(90);
            $table->integer('total_questions')->default(0);
            $table->boolean('is_premium')->default(false);
            $table->boolean('is_published')->default(false);
            $table->string('series')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tryouts');
    }
};
