<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * Seeder LENGKAP arsitektur akses per-program.
 *
 * Tiap program (subscription_plan) berisi:
 *  - 1 course + 2 modul + materi (campur free & paid)
 *  - 1 tryout + beberapa soal (single & multiple)
 *  - 1 kelas online (paid)
 *
 * Plus user default & student contoh yang terdaftar di beberapa program
 * (sebagian free, sebagian sudah paid).
 *
 * Idempotent: updateOrInsert by unique key; konten anak hanya dibuat bila course baru.
 *
 * Pakai: php artisan migrate:fresh
 *        php artisan db:seed --class=FullProgramSeeder
 */
class FullProgramSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $hasTier      = Schema::hasColumn('subscription_plans', 'tier');
        $hasSubTier   = Schema::hasColumn('subscriptions', 'tier');
        $hasQType     = Schema::hasColumn('tryout_questions', 'question_type');
        $hasPlanGrade = Schema::hasColumn('subscription_plans', 'grade_id');

        // =====================================================================
        // USERS
        // =====================================================================
        foreach ([
            ['name' => 'Super Admin',    'email' => 'superadmin@puwinter.com', 'role' => 'superadmin'],
            ['name' => 'Admin Puwinter', 'email' => 'admin@puwinter.com',      'role' => 'admin'],
            ['name' => 'Kak Farhan',     'email' => 'mentor@puwinter.com',     'role' => 'mentor', 'city' => 'Jombang', 'province' => 'Jawa Timur'],
        ] as $u) {
            DB::table('users')->updateOrInsert(
                ['email' => $u['email']],
                array_merge([
                    'name' => $u['name'], 'password' => Hash::make('password'), 'role' => $u['role'],
                    'email_verified_at' => $now, 'is_active' => true, 'updated_at' => $now, 'created_at' => $now,
                ], array_intersect_key($u, array_flip(['city', 'province'])))
            );
        }
        $mentorId = DB::table('users')->where('email', 'mentor@puwinter.com')->value('id');

        // =====================================================================
        // GRADES
        // =====================================================================
        foreach ([
            ['name' => 'Kelas 10', 'code' => '10', 'order' => 1],
            ['name' => 'Kelas 11', 'code' => '11', 'order' => 2],
            ['name' => 'Kelas 12', 'code' => '12', 'order' => 3],
        ] as $g) {
            DB::table('grades')->updateOrInsert(['code' => $g['code']],
                ['name' => $g['name'], 'order' => $g['order'], 'is_active' => true, 'updated_at' => $now, 'created_at' => $now]);
        }
        $gradeId = DB::table('grades')->pluck('id', 'code');

        // =====================================================================
        // SUBJECT
        // =====================================================================
        DB::table('subjects')->updateOrInsert(['slug' => 'bahasa-inggris'],
            ['name' => 'Bahasa Inggris', 'color' => '#D97706', 'order' => 1, 'is_active' => true, 'updated_at' => $now, 'created_at' => $now]);
        $subjectId = DB::table('subjects')->where('slug', 'bahasa-inggris')->value('id');

        // =====================================================================
        // PROGRAM DEFINITIONS
        // =====================================================================
        $programs = [
            [
                'slug' => 'grammar-reading-kelas-10', 'name' => 'Basic Grammar & Reading — Kelas 10',
                'price' => 135000, 'duration' => 6, 'tier' => 'regular', 'pertemuan' => 20,
                'start' => '2026-08-01', 'end' => '2026-12-31', 'quota' => null,
                'grade' => '10', 'course_slug' => 'basic-grammar-reading-kelas-10',
                'course_title' => 'Basic Grammar & Reading Text — Kelas 10',
            ],
            [
                'slug' => 'grammar-reading-kelas-11', 'name' => 'Basic Grammar & Reading — Kelas 11',
                'price' => 135000, 'duration' => 6, 'tier' => 'regular', 'pertemuan' => 20,
                'start' => '2026-08-01', 'end' => '2026-12-31', 'quota' => null,
                'grade' => '11', 'course_slug' => 'basic-grammar-reading-kelas-11',
                'course_title' => 'Basic Grammar & Reading Text — Kelas 11',
            ],
            [
                'slug' => 'tka-bahasa-inggris-2026', 'name' => 'TKA Bahasa Inggris Wajib 2026',
                'price' => 190000, 'duration' => 3, 'tier' => 'exclusive', 'pertemuan' => 24,
                'start' => '2026-08-01', 'end' => '2026-10-31', 'quota' => 30,
                'grade' => '12', 'course_slug' => 'tka-bahasa-inggris-wajib-2026',
                'course_title' => 'TKA Bahasa Inggris Wajib 2026',
            ],
            [
                'slug' => 'literasi-bahasa-inggris-utbk-2027', 'name' => 'Literasi Bhs Inggris UTBK SNBT 2027',
                'price' => 295000, 'duration' => 9, 'tier' => 'exclusive', 'pertemuan' => 36,
                'start' => '2026-08-01', 'end' => '2027-04-30', 'quota' => 25,
                'grade' => '12', 'course_slug' => 'literasi-bahasa-inggris-utbk-snbt-2027',
                'course_title' => 'Literasi Bahasa Inggris UTBK SNBT 2027',
            ],
        ];

        $order = 1;
        $planIdBySlug = [];
        foreach ($programs as $p) {
            // ---- PLAN (program) ----
            $planData = [
                'name' => $p['name'], 'duration_months' => $p['duration'],
                'price' => $p['price'], 'original_price' => $p['price'],
                'is_popular' => false, 'features' => json_encode([
                    $p['pertemuan'] . ' kali pertemuan',
                    'Kelas online tiap pekan (1x), 60 menit',
                    'Rekaman + link video tiap pertemuan',
                    'Materi PDF tiap selesai kelas',
                    'Online via Zoom Premium',
                ]),
                'bonus' => $p['pertemuan'] . ' kali pertemuan',
                'is_active' => true, 'order' => $order, 'updated_at' => $now, 'created_at' => $now,
            ];
            if ($hasTier) $planData['tier'] = $p['tier'];
            if ($hasPlanGrade) $planData['grade_id'] = $gradeId[$p['grade']] ?? null;
            if (Schema::hasColumn('subscription_plans', 'start_date')) {
                $planData['start_date'] = $p['start'] ?? null;
                $planData['end_date']   = $p['end'] ?? null;
                $planData['quota']      = $p['quota'] ?? null;
            }
            DB::table('subscription_plans')->updateOrInsert(['slug' => $p['slug']], $planData);
            $planId = DB::table('subscription_plans')->where('slug', $p['slug'])->value('id');
            $planIdBySlug[$p['slug']] = $planId;

            // ---- COURSE ----
            DB::table('courses')->updateOrInsert(['slug' => $p['course_slug']], [
                'subject_id' => $subjectId, 'grade' => $p['grade'], 'grade_id' => $gradeId[$p['grade']] ?? null,
                'course_type' => 'regular', 'plan_id' => $planId, 'access_tier' => 'both',
                'mentor_id' => $mentorId, 'title' => $p['course_title'],
                'description' => 'Kelas utama untuk program ' . $p['name'] . '.',
                'is_premium' => true, 'is_published' => true, 'total_modules' => 0,
                'order' => $order, 'updated_at' => $now, 'created_at' => $now,
            ]);
            $courseId = DB::table('courses')->where('slug', $p['course_slug'])->value('id');

            // ---- MODULES + MATERIALS (hanya jika belum ada modul utk course ini) ----
            $existingModules = DB::table('course_modules')->where('course_id', $courseId)->count();
            if ($existingModules === 0) {
                // Modul 1: Pengenalan (free)
                $mod1 = DB::table('course_modules')->insertGetId([
                    'course_id' => $courseId, 'title' => 'Modul 1 — Pengenalan', 'order' => 1,
                    'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
                ]);
                $this->material($mod1, 'Video Perkenalan Program', 'video', 'https://example.com/intro', 12, 'free', 1, $now);
                $this->material($mod1, 'Silabus & Panduan (PDF)', 'pdf', 'https://example.com/silabus.pdf', null, 'free', 2, $now);

                // Modul 2: Materi Inti (paid)
                $mod2 = DB::table('course_modules')->insertGetId([
                    'course_id' => $courseId, 'title' => 'Modul 2 — Materi Inti', 'order' => 2,
                    'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
                ]);
                $this->material($mod2, 'Video Materi Inti #1', 'video', 'https://example.com/inti-1', 45, 'paid', 1, $now);
                $this->material($mod2, 'Video Materi Inti #2', 'video', 'https://example.com/inti-2', 45, 'paid', 2, $now);
                $this->material($mod2, 'Rangkuman Materi (PDF)', 'pdf', 'https://example.com/rangkuman.pdf', null, 'paid', 3, $now);

                DB::table('courses')->where('id', $courseId)->update(['total_modules' => 2]);
            }

            // ---- TRYOUT + QUESTIONS (hanya jika belum ada) ----
            $tryoutSlug = 'tryout-' . $p['slug'];
            $existingTryout = DB::table('tryouts')->where('slug', $tryoutSlug)->exists();
            if (!$existingTryout) {
                $tryoutData = [
                    'title' => 'Tryout — ' . $p['name'], 'slug' => $tryoutSlug,
                    'subject_id' => $subjectId, 'grade' => $p['grade'], 'grade_id' => $gradeId[$p['grade']] ?? null,
                    'plan_id' => $planId, 'access_tier' => 'paid',
                    'description' => 'Tryout latihan untuk program ' . $p['name'] . '.',
                    'duration_minutes' => 60, 'total_questions' => 0,
                    'is_premium' => true, 'is_published' => true, 'order' => $order,
                    'created_at' => $now, 'updated_at' => $now,
                ];
                $tryoutId = DB::table('tryouts')->insertGetId($tryoutData);

                // Soal 1: single
                $this->questionSingle($tryoutId, $subjectId, $hasQType,
                    'Choose the correct sentence.',
                    ['She go to school.', 'She goes to school.', 'She going to school.', 'She gone to school.', null],
                    'b', 'mudah', 1, $now);
                // Soal 2: single
                $this->questionSingle($tryoutId, $subjectId, $hasQType,
                    'The synonym of "happy" is ...',
                    ['Sad', 'Angry', 'Glad', 'Tired', null],
                    'c', 'mudah', 2, $now);
                // Soal 3: multiple (jika kolom tersedia)
                $this->questionMultiple($tryoutId, $subjectId, $hasQType,
                    'Which of the following are nouns? (choose all that apply)',
                    ['Run', 'Book', 'Table', 'Quickly', 'Happiness'],
                    ['b', 'c', 'e'], 'sedang', 3, $now);

                DB::table('tryouts')->where('id', $tryoutId)->update([
                    'total_questions' => DB::table('tryout_questions')->where('tryout_id', $tryoutId)->count(),
                ]);
            }

            // ---- LIVE CLASS (hanya jika belum ada untuk program ini) ----
            $existingLive = DB::table('live_classes')->where('plan_id', $planId)->exists();
            if (!$existingLive) {
                DB::table('live_classes')->insert([
                    'course_id' => $courseId, 'mentor_id' => $mentorId, 'subject_id' => $subjectId,
                    'grade' => $p['grade'], 'grade_id' => $gradeId[$p['grade']] ?? null,
                    'class_type' => 'regular', 'plan_id' => $planId, 'access_tier' => 'paid',
                    'title' => 'Kelas Online Pekan 1 — ' . $p['name'],
                    'description' => 'Sesi live perdana program ' . $p['name'] . '.',
                    'scheduled_at' => now()->addDays(7)->setTime(19, 0),
                    'duration_minutes' => 60, 'zoom_link' => 'https://zoom.us/j/000000000',
                    'is_premium' => true, 'status' => 'scheduled', 'total_participants' => 0,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }

            $order++;
        }

        // =====================================================================
        // STUDENT contoh + enrollment campuran
        // =====================================================================
        DB::table('users')->updateOrInsert(['email' => 'student@puwinter.com'], [
            'name' => 'Aditya Pratama', 'password' => Hash::make('password'), 'role' => 'student',
            'email_verified_at' => $now, 'school' => 'SMA Negeri 1 Jombang',
            'city' => 'Jombang', 'province' => 'Jawa Timur',
            'grade' => '10', 'grade_id' => $gradeId['10'] ?? null,
            'is_active' => true, 'updated_at' => $now, 'created_at' => $now,
        ]);
        $studentId = DB::table('users')->where('email', 'student@puwinter.com')->value('id');

        if (Schema::hasTable('program_enrollments')) {
            // Terdaftar GRATIS di Grammar Kelas 10
            DB::table('program_enrollments')->updateOrInsert(
                ['user_id' => $studentId, 'plan_id' => $planIdBySlug['grammar-reading-kelas-10']],
                ['status' => 'free', 'enrolled_at' => $now, 'updated_at' => $now, 'created_at' => $now]
            );

            // Terdaftar BERBAYAR di TKA (sekaligus buat subscription aktif)
            $tkaPlanId = $planIdBySlug['tka-bahasa-inggris-2026'];
            $subData = [
                'user_id' => $studentId, 'plan_id' => $tkaPlanId, 'status' => 'active',
                'started_at' => $now, 'expired_at' => now()->addMonths(3),
                'payment_method' => 'manual_admin', 'amount_paid' => 190000,
                'updated_at' => $now, 'created_at' => $now,
            ];
            if ($hasSubTier) $subData['tier'] = 'exclusive';

            $existingSub = DB::table('subscriptions')
                ->where('user_id', $studentId)->where('plan_id', $tkaPlanId)
                ->where('status', 'active')->first();
            $subId = $existingSub->id ?? DB::table('subscriptions')->insertGetId($subData);

            DB::table('program_enrollments')->updateOrInsert(
                ['user_id' => $studentId, 'plan_id' => $tkaPlanId],
                [
                    'status' => 'paid', 'subscription_id' => $subId,
                    'paid_at' => $now, 'paid_expires_at' => now()->addMonths(3),
                    'enrolled_at' => $now, 'updated_at' => $now, 'created_at' => $now,
                ]
            );
        }

        $this->command?->info('FullProgramSeeder: user + 4 program lengkap (course/modul/materi/tryout/kelas online) berhasil di-seed.');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function material(int $moduleId, string $title, string $type, ?string $url, ?int $dur, string $tier, int $order, $now): void
    {
        DB::table('course_materials')->insert([
            'module_id' => $moduleId, 'title' => $title, 'type' => $type,
            'content_url' => $url, 'duration_minutes' => $dur,
            'is_premium' => $tier === 'paid', 'access_tier' => $tier,
            'is_locked' => false, 'order' => $order,
            'created_at' => $now, 'updated_at' => $now,
        ]);
    }

    private function questionSingle(int $tryoutId, int $subjectId, bool $hasQType, string $text, array $opts, string $correct, string $diff, int $order, $now): void
    {
        $row = [
            'tryout_id' => $tryoutId, 'subject_id' => $subjectId,
            'question_text' => $text,
            'option_a' => $opts[0], 'option_b' => $opts[1], 'option_c' => $opts[2],
            'option_d' => $opts[3], 'option_e' => $opts[4] ?? null,
            'correct_answer' => $correct, 'difficulty' => $diff, 'order' => $order,
            'created_at' => $now, 'updated_at' => $now,
        ];
        if ($hasQType) { $row['question_type'] = 'single'; }
        DB::table('tryout_questions')->insert($row);
    }

    private function questionMultiple(int $tryoutId, int $subjectId, bool $hasQType, string $text, array $opts, array $keys, string $diff, int $order, $now): void
    {
        // Jika kolom multiple belum ada (migrasi delta1 belum jalan), fallback ke single dgn kunci pertama.
        if (!$hasQType) {
            $this->questionSingle($tryoutId, $subjectId, false, $text, $opts, $keys[0], $diff, $order, $now);
            return;
        }
        DB::table('tryout_questions')->insert([
            'tryout_id' => $tryoutId, 'subject_id' => $subjectId,
            'question_type' => 'multiple',
            'question_text' => $text,
            'option_a' => $opts[0], 'option_b' => $opts[1], 'option_c' => $opts[2],
            'option_d' => $opts[3], 'option_e' => $opts[4] ?? null,
            'correct_answer' => $keys[0],
            'correct_answers' => json_encode($keys),
            'difficulty' => $diff, 'order' => $order,
            'created_at' => $now, 'updated_at' => $now,
        ]);
    }
}
