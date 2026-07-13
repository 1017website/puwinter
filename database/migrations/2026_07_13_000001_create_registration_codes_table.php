<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('registration_codes')) {
            Schema::create('registration_codes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code', 32)->unique();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('expires_at')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'registration_code_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('registration_code_id')->nullable()->after('referred_by_user_id')
                    ->constrained('registration_codes')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'registration_code_id')) {
            Schema::table('users', fn (Blueprint $table) => $table->dropConstrainedForeignId('registration_code_id'));
        }

        Schema::dropIfExists('registration_codes');
    }
};
