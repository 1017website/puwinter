<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Master Kelas (grade). Menggantikan hardcode '10' / '11' / '12'.
 * Admin dapat menambah/menyunting kelas dari panel tanpa ubah kode.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // mis. "Kelas 10", "Alumni"
            $table->string('code')->unique();        // mis. "10", "11", "12", "alumni"
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed awal dari nilai lama agar data existing tetap konsisten.
        $now = now();
        DB::table('grades')->insert([
            ['name' => 'Kelas 10', 'code' => '10', 'order' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kelas 11', 'code' => '11', 'order' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kelas 12', 'code' => '12', 'order' => 3, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
