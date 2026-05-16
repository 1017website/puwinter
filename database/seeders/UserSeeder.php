<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Superadmin
        DB::table('users')->insert([
            'name'              => 'Super Admin',
            'email'             => 'superadmin@puwinter.com',
            'password'          => Hash::make('password'),
            'role'              => 'superadmin',
            'email_verified_at' => now(),
            'is_active'         => true,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // 2. Admin
        DB::table('users')->insert([
            'name'              => 'Admin Puwinter',
            'email'             => 'admin@puwinter.com',
            'password'          => Hash::make('password'),
            'role'              => 'admin',
            'email_verified_at' => now(),
            'is_active'         => true,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // 3. Mentor
        DB::table('users')->insert([
            'name'              => 'Kak Farhan',
            'email'             => 'mentor@puwinter.com',
            'password'          => Hash::make('password'),
            'role'              => 'mentor',
            'email_verified_at' => now(),
            'city'              => 'Bandung',
            'province'          => 'Jawa Barat',
            'is_active'         => true,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // 4. Student (premium aktif)
        $studentId = DB::table('users')->insertGetId([
            'name'              => 'Aditya Pratama',
            'email'             => 'student@puwinter.com',
            'password'          => Hash::make('password'),
            'role'              => 'student',
            'email_verified_at' => now(),
            'school'            => 'SMA Negeri 1 Jakarta',
            'city'              => 'Jakarta Selatan',
            'province'          => 'DKI Jakarta',
            'grade'             => 'XII IPA',
            'birth_date'        => '2006-05-12',
            'is_active'         => true,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // Beri subscription aktif ke student
        $planId = DB::table('subscription_plans')->where('slug', '6-bulan')->value('id');

        DB::table('subscriptions')->insert([
            'user_id'        => $studentId,
            'plan_id'        => $planId,
            'status'         => 'active',
            'started_at'     => now(),
            'expired_at'     => now()->addMonths(6),
            'payment_method' => 'transfer_bank',
            'amount_paid'    => 249000,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }
}
