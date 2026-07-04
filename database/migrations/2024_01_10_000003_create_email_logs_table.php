<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('email_logs')) {
            Schema::create('email_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('email_verification_id')->nullable()->constrained('email_verifications')->nullOnDelete();
                $table->string('type')->default('email_verification');
                $table->string('source')->nullable(); // register, resend, system, dll.
                $table->string('mailer')->nullable();
                $table->string('transport')->nullable();
                $table->string('from_email')->nullable();
                $table->string('from_name')->nullable();
                $table->string('to_email');
                $table->string('to_name')->nullable();
                $table->string('subject')->nullable();
                $table->string('status')->default('processing')->index(); // processing, sent, failed
                $table->text('response')->nullable();
                $table->text('error_message')->nullable();
                $table->longText('error_trace')->nullable();
                $table->json('payload')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamp('failed_at')->nullable();
                $table->timestamps();

                $table->index(['type', 'source']);
                $table->index('to_email');
                $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
