<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            [
                'name'            => 'Rising Star',
                'slug'            => 'rising-star',
                'description'     => 'Naik 100 peringkat dalam satu hari',
                'color'           => '#F59E0B',
                'condition_type'  => 'rank_up',
                'condition_value' => 100,
            ],
            [
                'name'            => 'Consistent Learner',
                'slug'            => 'consistent-learner',
                'description'     => 'Belajar 20 hari berturut-turut',
                'color'           => '#10B981',
                'condition_type'  => 'streak',
                'condition_value' => 20,
            ],
            [
                'name'            => 'Tryout Master',
                'slug'            => 'tryout-master',
                'description'     => 'Mengerjakan 10 tryout',
                'color'           => '#6366F1',
                'condition_type'  => 'tryout_count',
                'condition_value' => 10,
            ],
            [
                'name'            => 'Top Performer',
                'slug'            => 'top-performer',
                'description'     => 'Masuk top 10% nasional tryout',
                'color'           => '#EF4444',
                'condition_type'  => 'rank_percentile',
                'condition_value' => 90,
            ],
            [
                'name'            => 'Maraton Belajar',
                'slug'            => 'maraton-belajar',
                'description'     => 'Total belajar 50 jam',
                'color'           => '#8B5CF6',
                'condition_type'  => 'study_hours',
                'condition_value' => 50,
            ],
            [
                'name'            => 'Tryout Warrior',
                'slug'            => 'tryout-warrior',
                'description'     => 'Mengerjakan tryout 30 hari berturut-turut',
                'color'           => '#F97316',
                'condition_type'  => 'tryout_streak',
                'condition_value' => 30,
            ],
        ];

        foreach ($achievements as $achievement) {
            DB::table('achievements')->insert(array_merge($achievement, [
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
