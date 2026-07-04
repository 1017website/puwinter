<?php

namespace Database\Seeders;

use App\Models\Tryout;
use App\Models\TryoutQuestion;
use App\Models\User;
use App\Models\UserTryoutAttempt;
use App\Services\IrtScoringService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Seeder data simulasi untuk menguji penilaian IRT di Tryout.
 *
 * Cara pakai:
 *   php artisan db:seed --class=IrtTryoutTestSeeder
 *
 * Yang dibuat:
 *   - Subject Bahasa Inggris
 *   - Grade Kelas 12
 *   - 1 tryout mode IRT + 12 soal campuran single/multiple
 *   - 40 siswa dummy + attempt selesai dengan variasi jawaban
 *   - Kalibrasi IRT otomatis + rescore ranking seluruh attempt
 *
 * Login siswa dummy:
 *   irt.student.01@puwinter.test s/d irt.student.40@puwinter.test
 *   password: password
 */
class IrtTryoutTestSeeder extends Seeder
{
    private const TRYOUT_SLUG = 'simulasi-irt-bahasa-inggris-2026';
    private const STUDENT_EMAIL_DOMAIN = 'puwinter.test';

    public function run(): void
    {
        mt_srand(20260702);

        $now = now();

        $gradeId = $this->ensureGrade($now);
        $subjectId = $this->ensureSubject($now);
        $tryout = $this->resetTryout($subjectId, $gradeId, $now);
        $questions = $this->seedQuestions($tryout, $subjectId);
        $students = $this->seedStudents($gradeId, $now);

        foreach ($students as $index => $student) {
            $answers = $this->generateAnswers($questions, $index + 1);
            $scoreData = $this->calculateRegularScore($questions, $answers);

            UserTryoutAttempt::create([
                'user_id' => $student->id,
                'tryout_id' => $tryout->id,
                'started_at' => $now->copy()->subDays(2)->addMinutes(($index + 1) * 7),
                'submitted_at' => $now->copy()->subDays(2)->addMinutes(($index + 1) * 7 + 54 + ($index % 18)),
                'answers' => $answers,
                'score' => $scoreData['score'],
                'correct_count' => $scoreData['correct'],
                'wrong_count' => $scoreData['wrong'],
                'empty_count' => $scoreData['empty'],
                'weighted_score' => 0,
                'tab_switch_count' => $this->tabSwitchCount($index + 1),
            ]);
        }

        $irt = app(IrtScoringService::class);
        $irt->calibrate($tryout->fresh('questions'));
        $irt->rescoreAll($tryout->fresh('questions'));
        $this->refreshWeightedScores($tryout->fresh('questions'));

        $this->command?->info('IrtTryoutTestSeeder selesai.');
        $this->command?->line('Tryout: ' . $tryout->title);
        $this->command?->line('Siswa dummy: irt.student.01@puwinter.test s/d irt.student.40@puwinter.test');
        $this->command?->line('Password semua siswa dummy: password');
    }

    private function ensureGrade($now): ?int
    {
        if (!Schema::hasTable('grades')) {
            return null;
        }

        DB::table('grades')->updateOrInsert(
            ['code' => '12'],
            [
                'name' => 'Kelas 12',
                'order' => 3,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        return (int) DB::table('grades')->where('code', '12')->value('id');
    }

    private function ensureSubject($now): int
    {
        DB::table('subjects')->updateOrInsert(
            ['slug' => 'bahasa-inggris'],
            [
                'name' => 'Bahasa Inggris',
                'color' => '#D97706',
                'order' => 4,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        return (int) DB::table('subjects')->where('slug', 'bahasa-inggris')->value('id');
    }

    private function resetTryout(int $subjectId, ?int $gradeId, $now): Tryout
    {
        $tryout = Tryout::withTrashed()->where('slug', self::TRYOUT_SLUG)->first();

        if ($tryout && method_exists($tryout, 'trashed') && $tryout->trashed()) {
            $tryout->restore();
        }

        if (!$tryout) {
            $tryout = new Tryout(['slug' => self::TRYOUT_SLUG]);
        }

        $payload = [
            'title' => 'Simulasi IRT Bahasa Inggris 2026',
            'slug' => self::TRYOUT_SLUG,
            'subject_id' => $subjectId,
            'grade' => '12',
            'description' => 'Data dummy untuk mengetes kalibrasi IRT, skor regular, skor IRT, ranking, dan halaman hasil tryout admin.',
            'duration_minutes' => 90,
            'total_questions' => 12,
            'scoring_mode' => Tryout::SCORING_IRT,
            'irt_calibrated' => false,
            'is_premium' => false,
            'is_published' => true,
            'series' => 'Seeder Test IRT',
            'order' => 1,
            'updated_at' => $now,
            'created_at' => $tryout->exists ? $tryout->created_at : $now,
        ];

        if (Schema::hasColumn('tryouts', 'grade_id')) {
            $payload['grade_id'] = $gradeId;
        }
        if (Schema::hasColumn('tryouts', 'plan_id')) {
            $payload['plan_id'] = null;
        }
        if (Schema::hasColumn('tryouts', 'access_tier')) {
            $payload['access_tier'] = 'both';
        }

        $tryout->forceFill($payload)->save();

        DB::table('user_tryout_attempts')->where('tryout_id', $tryout->id)->delete();
        DB::table('tryout_questions')->where('tryout_id', $tryout->id)->delete();

        return $tryout->fresh();
    }

    /**
     * @return \Illuminate\Support\Collection<int, TryoutQuestion>
     */
    private function seedQuestions(Tryout $tryout, int $subjectId)
    {
        $items = [
            [
                'type' => 'single', 'difficulty' => 'mudah', 'key' => 'b',
                'text' => 'Choose the sentence with the correct simple present tense.',
                'options' => ['a' => 'She go to school every day.', 'b' => 'She goes to school every day.', 'c' => 'She going to school every day.', 'd' => 'She gone to school every day.', 'e' => 'She is go to school every day.'],
                'explanation' => 'Subject she memakai verb+s/es pada simple present: she goes.',
            ],
            [
                'type' => 'single', 'difficulty' => 'mudah', 'key' => 'c',
                'text' => 'Find the synonym of “increase” in the context of academic reading.',
                'options' => ['a' => 'reduce', 'b' => 'avoid', 'c' => 'rise', 'd' => 'hide', 'e' => 'ignore'],
                'explanation' => 'Increase berarti bertambah atau naik, sehingga sinonim paling dekat adalah rise.',
            ],
            [
                'type' => 'single', 'difficulty' => 'sedang', 'key' => 'a',
                'text' => 'The phrase “on the other hand” is commonly used to introduce ....',
                'options' => ['a' => 'contrast', 'b' => 'cause', 'c' => 'example', 'd' => 'sequence', 'e' => 'definition'],
                'explanation' => 'On the other hand memberi sinyal perbandingan atau pertentangan gagasan.',
            ],
            [
                'type' => 'single', 'difficulty' => 'sedang', 'key' => 'd',
                'text' => 'Which sentence uses present perfect correctly?',
                'options' => ['a' => 'I have finish the report.', 'b' => 'I has finished the report.', 'c' => 'I finished have the report.', 'd' => 'I have finished the report.', 'e' => 'I am have finished the report.'],
                'explanation' => 'Present perfect memakai have/has + past participle: have finished.',
            ],
            [
                'type' => 'single', 'difficulty' => 'sedang', 'key' => 'e',
                'text' => 'In reading comprehension, “main idea” refers to ....',
                'options' => ['a' => 'one small detail', 'b' => 'the writer’s address', 'c' => 'the hardest word', 'd' => 'the final punctuation', 'e' => 'the central point of the text'],
                'explanation' => 'Main idea adalah gagasan utama atau inti pembahasan teks.',
            ],
            [
                'type' => 'single', 'difficulty' => 'sulit', 'key' => 'b',
                'text' => 'The author’s tone in an analytical exposition is usually ....',
                'options' => ['a' => 'playful and humorous', 'b' => 'logical and persuasive', 'c' => 'random and informal', 'd' => 'silent and unclear', 'e' => 'fictional and dramatic'],
                'explanation' => 'Analytical exposition menyampaikan argumen secara logis dan persuasif.',
            ],
            [
                'type' => 'single', 'difficulty' => 'sulit', 'key' => 'c',
                'text' => '“Although the data were limited, the conclusion was plausible.” The word “plausible” means ....',
                'options' => ['a' => 'impossible', 'b' => 'unrelated', 'c' => 'reasonable', 'd' => 'careless', 'e' => 'temporary'],
                'explanation' => 'Plausible berarti masuk akal atau dapat diterima secara logis.',
            ],
            [
                'type' => 'single', 'difficulty' => 'sulit', 'key' => 'a',
                'text' => 'Which option best states an inference from a text?',
                'options' => ['a' => 'A conclusion based on clues in the text', 'b' => 'A word copied from the title', 'c' => 'A sentence printed in bold', 'd' => 'A direct quote only', 'e' => 'A random guess without evidence'],
                'explanation' => 'Inference adalah kesimpulan berdasarkan petunjuk dari teks, bukan sekadar kutipan langsung.',
            ],
            [
                'type' => 'multiple', 'difficulty' => 'mudah', 'keys' => ['a', 'd'],
                'text' => 'Select all correct examples of auxiliary verbs.',
                'options' => ['a' => 'has', 'b' => 'quickly', 'c' => 'beautiful', 'd' => 'will', 'e' => 'table'],
                'explanation' => 'Has dan will dapat berfungsi sebagai auxiliary verbs.',
            ],
            [
                'type' => 'multiple', 'difficulty' => 'sedang', 'keys' => ['b', 'c'],
                'text' => 'Select all options that can signal cause-and-effect relationship.',
                'options' => ['a' => 'however', 'b' => 'therefore', 'c' => 'because', 'd' => 'meanwhile', 'e' => 'nevertheless'],
                'explanation' => 'Therefore dan because menghubungkan sebab-akibat.',
            ],
            [
                'type' => 'multiple', 'difficulty' => 'sedang', 'keys' => ['a', 'c', 'e'],
                'text' => 'Select all elements commonly checked when identifying a paragraph’s main idea.',
                'options' => ['a' => 'topic sentence', 'b' => 'font color', 'c' => 'repeated key concept', 'd' => 'page number', 'e' => 'supporting details'],
                'explanation' => 'Main idea bisa dilacak dari topic sentence, konsep yang berulang, dan detail pendukung.',
            ],
            [
                'type' => 'multiple', 'difficulty' => 'sulit', 'keys' => ['b', 'd'],
                'text' => 'Select all statements that describe critical reading.',
                'options' => ['a' => 'accepting every sentence without question', 'b' => 'evaluating the writer’s evidence', 'c' => 'ignoring context', 'd' => 'checking assumptions behind an argument', 'e' => 'reading only the first line'],
                'explanation' => 'Critical reading mengevaluasi bukti dan asumsi dalam argumen.',
            ],
        ];

        return collect($items)->map(function (array $item, int $index) use ($tryout, $subjectId) {
            $keys = $item['keys'] ?? [$item['key']];

            return TryoutQuestion::create([
                'tryout_id' => $tryout->id,
                'subject_id' => $subjectId,
                'question_type' => $item['type'],
                'question_text' => $item['text'],
                'option_a' => $item['options']['a'],
                'option_b' => $item['options']['b'],
                'option_c' => $item['options']['c'],
                'option_d' => $item['options']['d'],
                'option_e' => $item['options']['e'] ?? null,
                'correct_answer' => $keys[0],
                'correct_answers' => $item['type'] === 'multiple' ? $keys : null,
                'explanation' => $item['explanation'],
                'difficulty' => $item['difficulty'],
                'order' => $index + 1,
                'correct_rate' => null,
                'answered_count' => 0,
            ]);
        });
    }

    private function seedStudents(?int $gradeId, $now)
    {
        $students = collect();

        for ($i = 1; $i <= 40; $i++) {
            $email = 'irt.student.' . str_pad((string) $i, 2, '0', STR_PAD_LEFT) . '@' . self::STUDENT_EMAIL_DOMAIN;
            $name = 'Siswa IRT ' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);

            $user = User::withTrashed()->where('email', $email)->first();
            if (!$user) {
                $user = new User(['email' => $email]);
            }

            $payload = [
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => 'student',
                'email_verified_at' => $now,
                'school' => 'SMA Simulasi Puwinter',
                'city' => $this->cityFor($i),
                'province' => 'Jawa Timur',
                'birth_date' => now()->subYears(18)->subDays($i)->toDateString(),
                'grade' => '12',
                'is_active' => true,
                'deleted_at' => null,
                'updated_at' => $now,
                'created_at' => $user->exists ? $user->created_at : $now,
            ];

            if (Schema::hasColumn('users', 'grade_id')) {
                $payload['grade_id'] = $gradeId;
            }
            if (Schema::hasColumn('users', 'grade_locked')) {
                $payload['grade_locked'] = true;
            }

            $user->forceFill($payload)->save();
            $students->push($user->fresh());
        }

        return $students;
    }

    private function generateAnswers($questions, int $studentNumber): array
    {
        $answers = [];
        $skillBand = match (true) {
            $studentNumber <= 8 => 0.92,
            $studentNumber <= 18 => 0.72,
            $studentNumber <= 30 => 0.52,
            default => 0.34,
        };

        foreach ($questions as $question) {
            $difficultyPenalty = match ($question->difficulty) {
                'sulit' => 0.28,
                'sedang' => 0.13,
                default => 0.00,
            };
            $probCorrect = max(0.08, min(0.95, $skillBand - $difficultyPenalty));

            $roll = mt_rand(1, 1000) / 1000;

            if ($roll > 0.96) {
                continue; // sengaja kosong sebagian kecil
            }

            if ($question->isMultiple()) {
                $keys = $question->correctKeys();
                $wrongKeys = array_values(array_diff(array_keys($question->options()), $keys));

                if ($roll <= $probCorrect) {
                    $answers[$question->id] = $keys;
                } elseif ($roll <= $probCorrect + 0.18 && count($keys) > 1) {
                    // partial: ambil sebagian kunci benar, kadang tambah distraktor
                    $partial = [$keys[0]];
                    if (($studentNumber + $question->order) % 3 === 0 && isset($wrongKeys[0])) {
                        $partial[] = $wrongKeys[0];
                    }
                    $answers[$question->id] = $partial;
                } else {
                    $answers[$question->id] = [count($wrongKeys) ? $wrongKeys[($studentNumber + $question->order) % count($wrongKeys)] : 'a'];
                }
            } else {
                $keys = $question->correctKeys();
                $options = array_keys($question->options());
                $wrongOptions = array_values(array_diff($options, $keys));

                $answers[$question->id] = $roll <= $probCorrect
                    ? $keys[0]
                    : $wrongOptions[($studentNumber + $question->order) % count($wrongOptions)];
            }
        }

        return $answers;
    }

    private function calculateRegularScore($questions, array $answers): array
    {
        $correct = 0;
        $wrong = 0;
        $empty = 0;
        $rawScore = 0.0;
        $fullPoint = 1.0;
        $penaltyPerWrong = 0.0;

        foreach ($questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;
            $result = $question->grade($userAnswer, $fullPoint, $penaltyPerWrong);
            $status = $result['status'] ?? 'empty';
            $earned = (float) ($result['earned'] ?? 0);

            if ($status === 'correct') {
                $correct++;
                $rawScore += $earned;
            } elseif ($status === 'partial') {
                $rawScore += $earned;
            } elseif ($status === 'wrong') {
                $wrong++;
                $rawScore -= $penaltyPerWrong;
            } else {
                $empty++;
            }
        }

        return [
            'score' => max(0, round($rawScore, 2)),
            'correct' => $correct,
            'wrong' => $wrong,
            'empty' => $empty,
        ];
    }

    private function refreshWeightedScores(Tryout $tryout): void
    {
        $questions = $tryout->questions;
        $weightedMax = max(1e-9, $questions->sum(fn($q) => $q->difficultyWeight()));
        $fullPoint = 1.0;

        foreach ($tryout->attempts()->whereNotNull('submitted_at')->get() as $attempt) {
            $answers = $attempt->answers ?? [];
            $weightedRaw = 0.0;

            foreach ($questions as $question) {
                $userAnswer = $answers[$question->id] ?? null;
                $result = $question->grade($userAnswer);
                $status = $result['status'] ?? 'empty';
                $earned = (float) ($result['earned'] ?? 0);
                $weight = $question->difficultyWeight();

                if ($status === 'correct') {
                    $weightedRaw += $weight;
                } elseif ($status === 'partial') {
                    $weightedRaw += $weight * ($earned / $fullPoint);
                }
            }

            DB::table('user_tryout_attempts')
                ->where('id', $attempt->id)
                ->update([
                    'weighted_score' => round(($weightedRaw / $weightedMax) * 100, 2),
                    'updated_at' => now(),
                ]);
        }
    }

    private function tabSwitchCount(int $studentNumber): int
    {
        return match (true) {
            $studentNumber % 17 === 0 => 4,
            $studentNumber % 11 === 0 => 2,
            $studentNumber % 7 === 0 => 1,
            default => 0,
        };
    }

    private function cityFor(int $studentNumber): string
    {
        return ['Jombang', 'Surabaya', 'Malang', 'Kediri', 'Mojokerto'][$studentNumber % 5];
    }
}
