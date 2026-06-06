<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * - app_settings: key-value sederhana untuk konfigurasi editable admin
 *   (rekening transfer manual, dll).
 * - subscriptions: tambah kolom untuk alur transfer manual + bukti.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('app_settings')) {
            Schema::create('app_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscriptions', 'unique_code')) {
                $table->integer('unique_code')->nullable()->after('amount_paid');
            }
            if (!Schema::hasColumn('subscriptions', 'total_amount')) {
                $table->integer('total_amount')->nullable()->after('unique_code');
            }
            if (!Schema::hasColumn('subscriptions', 'payment_proof')) {
                $table->string('payment_proof')->nullable()->after('total_amount');
            }
            if (!Schema::hasColumn('subscriptions', 'proof_uploaded_at')) {
                $table->timestamp('proof_uploaded_at')->nullable()->after('payment_proof');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            foreach (['unique_code', 'total_amount', 'payment_proof', 'proof_uploaded_at'] as $col) {
                if (Schema::hasColumn('subscriptions', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::dropIfExists('app_settings');
    }
};
