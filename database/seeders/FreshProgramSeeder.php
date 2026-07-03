<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * Seeder FRESH untuk lingkungan testing.
 *
 * Mengisi:
 *  - User default (superadmin, admin, mentor, student)
 *  - Grades 10/11/12
 *  - Subject Bahasa Inggris
 *  - 4 program Puwinter (course + program) sesuai pamflet
 *
 * TIDAK mengikutkan data demo lama (subject TPS, plan 1/6/12 bulan, achievement).
 * Idempotent: aman dijalankan berulang (updateOrInsert by unique key).
 *
 * Pakai bersama: php artisan migrate:fresh --seeder=FreshProgramSeeder
 * atau          php artisan db:seed --class=FreshProgramSeeder
 */
class FreshProgramSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $hasTier = Schema::hasColumn('subscription_plans', 'tier');

        // =====================================================================
        // 1) USERS
        // =====================================================================
        $users = [
            ['name' => 'Super Admin',     'email' => 'superadmin@puwinter.com', 'role' => 'superadmin'],
            ['name' => 'Admin Puwinter',  'email' => 'admin@puwinter.com',      'role' => 'admin'],
            ['name' => 'Kak Farhan',      'email' => 'mentor@puwinter.com',     'role' => 'mentor', 'city' => 'Jombang', 'province' => 'Jawa Timur'],
        ];
        foreach ($users as $u) {
            DB::table('users')->updateOrInsert(
                ['email' => $u['email']],
                array_merge([
                    'name'              => $u['name'],
                    'password'          => Hash::make('password'),
                    'role'              => $u['role'],
                    'email_verified_at' => $now,
                    'is_active'         => true,
                    'updated_at'        => $now,
                    'created_at'        => $now,
                ], array_intersect_key($u, array_flip(['city', 'province'])))
            );
        }

        // =====================================================================
        // 2) GRADES
        // =====================================================================
        foreach ([
            ['name' => 'Kelas 10', 'code' => '10', 'order' => 1],
            ['name' => 'Kelas 11', 'code' => '11', 'order' => 2],
            ['name' => 'Kelas 12', 'code' => '12', 'order' => 3],
        ] as $g) {
            DB::table('grades')->updateOrInsert(
                ['code' => $g['code']],
                ['name' => $g['name'], 'order' => $g['order'], 'is_active' => true, 'updated_at' => $now, 'created_at' => $now]
            );
        }
        $gradeId = DB::table('grades')->pluck('id', 'code');

        // =====================================================================
        // 3) SUBJECT
        // =====================================================================
        DB::table('subjects')->updateOrInsert(
            ['slug' => 'bahasa-inggris'],
            ['name' => 'Bahasa Inggris', 'color' => '#D97706', 'order' => 1, 'is_active' => true, 'updated_at' => $now, 'created_at' => $now]
        );
        $subjectId = DB::table('subjects')->where('slug', 'bahasa-inggris')->value('id');

        // =====================================================================
        // 4) MENTOR id
        // =====================================================================
        $mentorId = DB::table('users')->where('email', 'mentor@puwinter.com')->value('id');

        // =====================================================================
        // 5) PROGRAM (course + plan)
        // =====================================================================
        $programs = [
            [
                'course_slug' => 'basic-grammar-reading-kelas-10',
                'title'       => 'Basic Grammar & Reading Text — Kelas 10',
                'grade_code'  => '10',
                'plan_slug'   => 'grammar-reading-kelas-10',
                'plan_name'   => 'Basic Grammar & Reading — Kelas 10',
                'price'       => 135000, 'duration' => 6, 'tier' => 'regular', 'pertemuan' => 20,
                'desc' => 'Program bimbel online Basic Grammar & Reading Text untuk Kelas 10. '
                        . 'Menguatkan Materi Bahasa Inggris Sekolah agar siswa lebih SIAP menghadapi Ulangan, Asesmen, dan Ujian.',
                'features' => [
                    'Program Semester 1: Agustus-Desember 2026',
                    'Program Semester 2: Januari-Mei 2027',
                    '20 kali pertemuan per semester',
                    'Kelas online tiap pekan (1x), durasi 60 menit',
                    'Rekaman + link video tiap pertemuan',
                    'Materi PDF tiap selesai kelas',
                    'Online via Zoom Premium',
                ],
            ],
            [
                'course_slug' => 'basic-grammar-reading-kelas-11',
                'title'       => 'Basic Grammar & Reading Text — Kelas 11',
                'grade_code'  => '11',
                'plan_slug'   => 'grammar-reading-kelas-11',
                'plan_name'   => 'Basic Grammar & Reading — Kelas 11',
                'price'       => 135000, 'duration' => 6, 'tier' => 'regular', 'pertemuan' => 20,
                'desc' => 'Program bimbel online Basic Grammar & Reading Text untuk Kelas 11. '
                        . 'Menguatkan Materi Bahasa Inggris Sekolah agar siswa lebih SIAP menghadapi Ulangan, Asesmen, dan Ujian.',
                'features' => [
                    'Program Semester 1: Agustus-Desember 2026',
                    'Program Semester 2: Januari-Mei 2027',
                    '20 kali pertemuan per semester',
                    'Kelas online tiap pekan (1x), durasi 60 menit',
                    'Rekaman + link video tiap pertemuan',
                    'Materi PDF tiap selesai kelas',
                    'Online via Zoom Premium',
                ],
            ],
            [
                'course_slug' => 'tka-bahasa-inggris-wajib-2026',
                'title'       => 'TKA Bahasa Inggris Wajib 2026',
                'grade_code'  => '12',
                'plan_slug'   => 'tka-bahasa-inggris-2026',
                'plan_name'   => 'TKA Bahasa Inggris Wajib 2026',
                'price'       => 190000, 'duration' => 3, 'tier' => 'exclusive', 'pertemuan' => 24,
                'desc' => 'Program bimbel online TKA BHS INGGRIS WAJIB. Siapkan Reading Text TKA sejak sekarang '
                        . 'untuk meraih hasil terbaik di TKA 2026. Periode Agustus-Oktober 2026.',
                'features' => [
                    'Periode: Agustus-Oktober 2026',
                    '24 kali pertemuan',
                    'Kelas online tiap pekan (1x), durasi 60 menit',
                    'Rekaman + link video tiap pertemuan',
                    'Materi PDF tiap selesai kelas',
                    'Online via Zoom Premium',
                ],
            ],
            [
                'course_slug' => 'literasi-bahasa-inggris-utbk-snbt-2027',
                'title'       => 'Literasi Bahasa Inggris UTBK SNBT 2027',
                'grade_code'  => '12',
                'plan_slug'   => 'literasi-bahasa-inggris-utbk-2027',
                'plan_name'   => 'Literasi Bhs Inggris UTBK SNBT 2027',
                'price'       => 295000, 'duration' => 9, 'tier' => 'exclusive', 'pertemuan' => 36,
                'desc' => 'Program bimbel online Literasi Bahasa Inggris UTBK SNBT. Latihan memahami pertanyaan kritis: '
                        . 'Topic, Main Idea, Stated/Unstated Detail Question, Inference, Vocabulary, dan Paragraf Rumpang. '
                        . 'Periode Agustus 2026-April 2027.',
                'features' => [
                    'Periode: Agustus 2026-April 2027',
                    '36 kali pertemuan',
                    'Kelas online tiap pekan (1x), durasi 60 menit',
                    'Rekaman + link video tiap pertemuan',
                    'Materi PDF tiap selesai kelas',
                    'Online via Zoom Premium',
                ],
            ],
        ];

        $order = 1;
        $firstPlanId = null;
        foreach ($programs as $p) {
            DB::table('courses')->updateOrInsert(
                ['slug' => $p['course_slug']],
                [
                    'subject_id'    => $subjectId,
                    'grade'         => $p['grade_code'],
                    'grade_id'      => $gradeId[$p['grade_code']] ?? null,
                    'course_type'   => 'regular',
                    'mentor_id'     => $mentorId,
                    'title'         => $p['title'],
                    'description'   => $p['desc'],
                    'is_premium'    => true,
                    'is_published'  => true,
                    'total_modules' => 0,
                    'order'         => $order,
                    'updated_at'    => $now,
                    'created_at'    => $now,
                ]
            );

            $planData = [
                'name'            => $p['plan_name'],
                'duration_months' => $p['duration'],
                'price'           => $p['price'],
                'original_price'  => $p['price'],
                'is_popular'      => false,
                'features'        => json_encode($p['features']),
                'bonus'           => $p['pertemuan'] . ' kali pertemuan',
                'is_active'       => true,
                'order'           => $order,
                'updated_at'      => $now,
                'created_at'      => $now,
            ];
            if ($hasTier) {
                $planData['tier'] = $p['tier'];
            }

            DB::table('subscription_plans')->updateOrInsert(['slug' => $p['plan_slug']], $planData);

            if ($firstPlanId === null) {
                $firstPlanId = DB::table('subscription_plans')->where('slug', $p['plan_slug'])->value('id');
            }
            $order++;
        }

        // =====================================================================
        // 6) STUDENT contoh (subscribe ke program pertama agar bisa uji premium)
        // =====================================================================
        DB::table('users')->updateOrInsert(
            ['email' => 'student@puwinter.com'],
            [
                'name'              => 'Aditya Pratama',
                'password'          => Hash::make('password'),
                'role'              => 'student',
                'email_verified_at' => $now,
                'school'            => 'SMA Negeri 1 Jombang',
                'city'              => 'Jombang',
                'province'          => 'Jawa Timur',
                'grade'             => '10',
                'grade_id'          => $gradeId['10'] ?? null,
                'is_active'         => true,
                'updated_at'        => $now,
                'created_at'        => $now,
            ]
        );
        $studentId = DB::table('users')->where('email', 'student@puwinter.com')->value('id');

        // Hanya beri subscription jika belum punya yang aktif, agar idempotent.
        $hasActive = DB::table('subscriptions')
            ->where('user_id', $studentId)
            ->where('status', 'active')
            ->exists();

        if (!$hasActive && $firstPlanId) {
            $subData = [
                'user_id'        => $studentId,
                'plan_id'        => $firstPlanId,
                'status'         => 'active',
                'started_at'     => $now,
                'expired_at'     => now()->addMonths(6),
                'payment_method' => 'manual_admin',
                'amount_paid'    => 135000,
                'updated_at'     => $now,
                'created_at'     => $now,
            ];
            if (Schema::hasColumn('subscriptions', 'tier')) {
                $subData['tier'] = 'regular';
            }
            DB::table('subscriptions')->insert($subData);
        }

        $this->command?->info('FreshProgramSeeder: user default + 4 program berhasil di-seed.');
    }
}
