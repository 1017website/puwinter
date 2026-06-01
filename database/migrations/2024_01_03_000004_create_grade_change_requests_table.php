<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Permintaan pindah kelas. Siswa mengajukan, admin approve/reject.
 * Saat approve: users.grade_id diperbarui ke grade tujuan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_grade_id')->nullable()->constrained('grades')->nullOnDelete();
            $table->foreignId('to_grade_id')->constrained('grades')->cascadeOnDelete();
            $table->text('reason')->nullable();              // alasan siswa
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->text('admin_note')->nullable();          // catatan admin saat proses
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_change_requests');
    }
};
