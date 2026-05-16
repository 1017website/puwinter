<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            ['name' => 'Matematika TPS',                   'slug' => 'matematika-tps',  'color' => '#2563EB', 'order' => 1],
            ['name' => 'Penalaran Umum',                   'slug' => 'penalaran-umum',  'color' => '#7C3AED', 'order' => 2],
            ['name' => 'Bahasa Indonesia',                 'slug' => 'bahasa-indonesia','color' => '#059669', 'order' => 3],
            ['name' => 'Bahasa Inggris',                   'slug' => 'bahasa-inggris',  'color' => '#D97706', 'order' => 4],
            ['name' => 'Pengetahuan Kuantitatif',          'slug' => 'pengetahuan-kuantitatif', 'color' => '#DC2626', 'order' => 5],
            ['name' => 'Pengetahuan dan Pemahaman Umum',   'slug' => 'ppu',             'color' => '#0891B2', 'order' => 6],
            ['name' => 'Fisika',                           'slug' => 'fisika',          'color' => '#4F46E5', 'order' => 7],
            ['name' => 'TKA Saintek',                      'slug' => 'tka-saintek',     'color' => '#BE185D', 'order' => 8],
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->insert(array_merge($subject, [
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
