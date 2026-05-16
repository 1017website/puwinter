<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            'Akses semua kelas',
            'Live class eksklusif',
            'Tryout tanpa batas',
            'Materi premium',
            'Pembahasan video tutor',
            'Analisis belajar detail',
            'Tanpa iklan',
        ];

        $plans = [
            [
                'name'           => 'Paket 1 Bulan',
                'slug'           => '1-bulan',
                'duration_months'=> 1,
                'price'          => 89000,
                'original_price' => 129000,
                'is_popular'     => false,
                'bonus'          => null,
                'order'          => 1,
            ],
            [
                'name'           => 'Paket 6 Bulan',
                'slug'           => '6-bulan',
                'duration_months'=> 6,
                'price'          => 249000,
                'original_price' => 498000,
                'is_popular'     => true,
                'bonus'          => '1x Konsultasi Belajar Personal',
                'order'          => 2,
            ],
            [
                'name'           => 'Paket 12 Bulan',
                'slug'           => '12-bulan',
                'duration_months'=> 12,
                'price'          => 399000,
                'original_price' => 798000,
                'is_popular'     => false,
                'bonus'          => '2x Konsultasi Belajar Personal',
                'order'          => 3,
            ],
        ];

        foreach ($plans as $plan) {
            DB::table('subscription_plans')->insert(array_merge($plan, [
                'features'   => json_encode($features),
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
