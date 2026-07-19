<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('frontend_visits', function (Blueprint $table) {
            $table->id();
            $table->uuid('visitor_id')->index();
            $table->string('session_id', 100)->nullable()->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_hash', 64)->nullable();
            $table->string('path', 255)->default('/')->index();
            $table->string('route_name', 100)->nullable();
            $table->text('referrer')->nullable();
            $table->string('referrer_domain', 255)->nullable()->index();
            $table->string('device', 30)->default('Desktop')->index();
            $table->string('browser', 50)->nullable();
            $table->string('operating_system', 50)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['created_at', 'visitor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('frontend_visits');
    }
};
