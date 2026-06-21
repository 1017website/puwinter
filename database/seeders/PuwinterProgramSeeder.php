<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Seeder konten resmi Puwinter sesuai 5 pamflet:
 *
 *  1. Basic Grammar & Reading Text — Kelas 10  (135.000 / semester, 20x)
 *  2. Basic Grammar & Reading Text — Kelas 11  (135.000 / semester, 20x)
 *  3. TKA Bahasa Inggris Wajib 2026            (190.000, 24x, Agu-Okt 2026)
 *  4. Literasi Bhs Inggris UTBK SNBT 2027      (295.000, 36x, Agu 2026-Apr 2027)
 *
 * Pendekatan: tiap program = 1 paket harga (subscription_plan) + 1 course.
 * Seeder idempotent (aman dijalankan berulang) — pakai updateOrInsert by slug.
 */
class PuwinterProgramSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // ---------------------------------------------------------------------
        // 1) GRADES — pastikan kelas 10 & 11 ada (kelas 12 utk TKA/UTBK)
        // ---------------------------------------------------------------------
        $grades = [
            ['name' => 'Kelas 10', 'code' => '10', 'order' => 1],
            ['name' => 'Kelas 11', 'code' => '11', 'order' => 2],
            ['name' => 'Kelas 12', 'code' => '12', 'order' => 3],
        ];
        foreach ($grades as $g) {
            DB::table('grades')->updateOrInsert(
                ['code' => $g['code']],
                ['name' => $g['name'], 'order' => $g['order'], 'is_active' => true, 'updated_at' => $now, 'created_at' => $now]
            );
        }
        $gradeId = DB::table('grades')->pluck('id', 'code'); // ['10'=>id, ...]

        // ---------------------------------------------------------------------
        // 2) SUBJECT — Bahasa Inggris (semua program berbasis Bahasa Inggris)
        // ---------------------------------------------------------------------
        DB::table('subjects')->updateOrInsert(
            ['slug' => 'bahasa-inggris'],
            ['name' => 'Bahasa Inggris', 'color' => '#D97706', 'order' => 4, 'is_active' => true, 'updated_at' => $now, 'created_at' => $now]
        );
        $subjectId = DB::table('subjects')->where('slug', 'bahasa-inggris')->value('id');

        // ---------------------------------------------------------------------
        // 3) MENTOR default
        // ---------------------------------------------------------------------
        $mentorId = DB::table('users')->where('email', 'mentor@puwinter.com')->value('id');
        if (!$mentorId) {
            // fallback: admin / superadmin pertama agar foreign key tetap valid
            $mentorId = DB::table('users')
                ->whereIn('role', ['mentor', 'admin', 'superadmin'])
                ->orderBy('id')
                ->value('id');
        }

        // Kolom opsional (tier) — cek agar seeder tetap jalan walau migrasi tier belum ada.
        $hasTier = \Illuminate\Support\Facades\Schema::hasColumn('subscription_plans', 'tier');

        // ---------------------------------------------------------------------
        // 4) DEFINISI PROGRAM (course + plan)
        // ---------------------------------------------------------------------
        $programs = [
            [
                'course_slug'  => 'basic-grammar-reading-kelas-10',
                'title'        => 'Basic Grammar & Reading Text — Kelas 10',
                'grade_code'   => '10',
                'plan_slug'    => 'grammar-reading-kelas-10',
                'plan_name'    => 'Basic Grammar & Reading — Kelas 10',
                'price'        => 135000,
                'duration'     => 6, // 1 semester
                'tier'         => 'regular',
                'pertemuan'    => 20,
                'desc' => "Program bimbel online Basic Grammar & Reading Text untuk Kelas 10. "
                        . "Dirancang khusus untuk menguatkan Materi Bahasa Inggris Sekolah, membantu siswa lebih SIAP "
                        . "menghadapi Ulangan, Asesmen, dan Ujian.",
                'features' => [
                    'Paket Semester 1: Agustus-Desember 2026',
                    'Paket Semester 2: Januari-Mei 2027',
                    '20 kali pertemuan per semester',
                    'Live class tiap pekan (1x), durasi 60 menit',
                    'Rekaman + link video tiap pertemuan',
                    'Materi PDF tiap selesai kelas',
                    'Online via Zoom Premium',
                ],
            ],
            [
                'course_slug'  => 'basic-grammar-reading-kelas-11',
                'title'        => 'Basic Grammar & Reading Text — Kelas 11',
                'grade_code'   => '11',
                'plan_slug'    => 'grammar-reading-kelas-11',
                'plan_name'    => 'Basic Grammar & Reading — Kelas 11',
                'price'        => 135000,
                'duration'     => 6,
                'tier'         => 'regular',
                'pertemuan'    => 20,
                'desc' => "Program bimbel online Basic Grammar & Reading Text untuk Kelas 11. "
                        . "Dirancang khusus untuk menguatkan Materi Bahasa Inggris Sekolah, membantu siswa lebih SIAP "
                        . "menghadapi Ulangan, Asesmen, dan Ujian.",
                'features' => [
                    'Paket Semester 1: Agustus-Desember 2026',
                    'Paket Semester 2: Januari-Mei 2027',
                    '20 kali pertemuan per semester',
                    'Live class tiap pekan (1x), durasi 60 menit',
                    'Rekaman + link video tiap pertemuan',
                    'Materi PDF tiap selesai kelas',
                    'Online via Zoom Premium',
                ],
            ],
            [
                'course_slug'  => 'tka-bahasa-inggris-wajib-2026',
                'title'        => 'TKA Bahasa Inggris Wajib 2026',
                'grade_code'   => '12',
                'plan_slug'    => 'tka-bahasa-inggris-2026',
                'plan_name'    => 'TKA Bahasa Inggris Wajib 2026',
                'price'        => 190000,
                'duration'     => 3, // Agustus-Oktober 2026
                'tier'         => 'exclusive',
                'pertemuan'    => 24,
                'desc' => "Program bimbel online TKA BHS INGGRIS WAJIB. Siapkan Reading Text TKA sejak sekarang "
                        . "untuk meraih hasil terbaik di TKA 2026. Periode Agustus-Oktober 2026.",
                'features' => [
                    'Periode: Agustus-Oktober 2026',
                    '24 kali pertemuan',
                    'Live class tiap pekan (1x), durasi 60 menit',
                    'Rekaman + link video tiap pertemuan',
                    'Materi PDF tiap selesai kelas',
                    'Online via Zoom Premium',
                ],
            ],
            [
                'course_slug'  => 'literasi-bahasa-inggris-utbk-snbt-2027',
                'title'        => 'Literasi Bahasa Inggris UTBK SNBT 2027',
                'grade_code'   => '12',
                'plan_slug'    => 'literasi-bahasa-inggris-utbk-2027',
                'plan_name'    => 'Literasi Bhs Inggris UTBK SNBT 2027',
                'price'        => 295000,
                'duration'     => 9, // Agustus 2026 - April 2027
                'tier'         => 'exclusive',
                'pertemuan'    => 36,
                'desc' => "Program bimbel online Literasi Bahasa Inggris UTBK SNBT. Latihan memahami pertanyaan kritis: "
                        . "Topic, Main Idea, Stated Detail Question, Unstated Detail Question, Inference, Vocabulary, "
                        . "dan Paragraf Rumpang. Periode Agustus 2026-April 2027.",
                'features' => [
                    'Periode: Agustus 2026-April 2027',
                    '36 kali pertemuan',
                    'Live class tiap pekan (1x), durasi 60 menit',
                    'Rekaman + link video tiap pertemuan',
                    'Materi PDF tiap selesai kelas',
                    'Online via Zoom Premium',
                ],
            ],
        ];

        $order = 1;
        foreach ($programs as $p) {
            // ---- COURSE ----
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

            // ---- PLAN (paket harga per-program) ----
            $planData = [
                'name'            => $p['plan_name'],
                'duration_months' => $p['duration'],
                'price'           => $p['price'],
                'original_price'  => $p['price'], // tanpa coret-harga
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

            DB::table('subscription_plans')->updateOrInsert(
                ['slug' => $p['plan_slug']],
                $planData
            );

            $order++;
        }

        $this->command?->info('PuwinterProgramSeeder: 4 program (course + plan) berhasil di-seed.');
    }
}
