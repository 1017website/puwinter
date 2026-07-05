<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tryout_passages')) {
            Schema::create('tryout_passages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tryout_id')->constrained()->cascadeOnDelete();
                $table->string('title')->nullable();
                $table->longText('passage_text')->nullable();
                $table->string('passage_image')->nullable();
                $table->string('source')->nullable();
                $table->unsignedInteger('order')->default(0);
                $table->timestamps();

                $table->index(['tryout_id', 'order']);
            });
        }

        if (Schema::hasTable('tryout_questions') && !Schema::hasColumn('tryout_questions', 'passage_id')) {
            Schema::table('tryout_questions', function (Blueprint $table) {
                $table->foreignId('passage_id')
                    ->nullable()
                    ->after('tryout_id')
                    ->constrained('tryout_passages')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tryout_questions') && Schema::hasColumn('tryout_questions', 'passage_id')) {
            Schema::table('tryout_questions', function (Blueprint $table) {
                $table->dropConstrainedForeignId('passage_id');
            });
        }

        Schema::dropIfExists('tryout_passages');
    }
};
