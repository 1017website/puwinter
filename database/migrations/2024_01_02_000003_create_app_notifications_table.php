<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel notifikasi sederhana (in-app).
 * Dipakai untuk bell di topbar student & admin.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('app_notifications')) {
            return;
        }

        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 50)->default('info'); // info | success | warning | tryout | live | payment
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('url')->nullable();           // tautan tujuan saat diklik
            $table->string('icon', 50)->nullable();      // ikon FontAwesome opsional, mis. "fa-trophy"
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_notifications');
    }
};
