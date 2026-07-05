<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\SubscriptionPlan;
use App\Models\Tryout;
use App\Models\TryoutQuestion;
use App\Models\TryoutPassage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TryoutController extends Controller
{
    public function index(Request $request): View
    {
        $query = Tryout::with(['subject', 'gradeLevel', 'plan'])->withCount(['questions', 'attempts']);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('grade_id')) {
            $query->where('grade_id', (int) $request->grade_id);
        }

        $tryouts  = $query->latest()->paginate(15)->withQueryString();
        $subjects = Subject::active()->get();
        $grades   = Grade::active()->get();

        return view('admin.tryouts.index', compact('tryouts', 'subjects', 'grades'));
    }

    public function create(): View
    {
        $subjects = Subject::active()->get();
        $grades   = Grade::active()->get();
        $plans    = SubscriptionPlan::active()->with('grades')->orderBy('order')->get();

        return view('admin.tryouts.create', compact('subjects', 'grades', 'plans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'subject_id'       => 'nullable|exists:subjects,id',
            'grade_id'         => 'nullable|exists:grades,id',
            'description'      => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'series'           => 'nullable|string|max:100',
            'is_premium'       => 'boolean',
            'plan_id'          => 'nullable|exists:subscription_plans,id',
            'access_tier'      => 'required|in:free,paid,both',
            'scoring_mode'     => 'nullable|in:regular,irt',
        ]);

        $tryout = Tryout::create([
            'title'            => $request->title,
            'slug'             => Str::slug($request->title) . '-' . time(),
            'subject_id'       => $request->subject_id,
            'grade_id'         => $request->filled('grade_id') ? (int) $request->grade_id : null,
            'grade'            => $this->legacyGradeCode($request->input('grade_id')),
            'description'      => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'series'           => $request->series,
            'is_premium'       => $request->input('access_tier') === 'paid' || $request->boolean('is_premium'),
            'plan_id'          => $request->filled('plan_id') ? (int) $request->plan_id : null,
            'access_tier'      => $request->input('access_tier', 'both'),
            'scoring_mode'     => $request->input('scoring_mode', 'regular'),
            'is_published'     => false,
        ]);

        return redirect()->route('admin.tryouts.show', $tryout)
            ->with('success', 'Tryout berhasil dibuat. Tambahkan soal sekarang.');
    }

    public function show(Tryout $tryout): View
    {
        $tryout->load(['subject', 'gradeLevel', 'plan', 'questions.subject', 'questions.passage', 'passages.questions']);
        $subjects = Subject::active()->get();

        return view('admin.tryouts.show', compact('tryout', 'subjects'));
    }

    public function edit(Tryout $tryout): View
    {
        $subjects = Subject::active()->get();
        $grades   = Grade::active()->get();
        $plans    = SubscriptionPlan::active()->with('grades')->orderBy('order')->get();

        return view('admin.tryouts.edit', compact('tryout', 'subjects', 'grades', 'plans'));
    }

    public function update(Request $request, Tryout $tryout): RedirectResponse
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'subject_id'       => 'nullable|exists:subjects,id',
            'grade_id'         => 'nullable|exists:grades,id',
            'description'      => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'series'           => 'nullable|string|max:100',
            'is_premium'       => 'boolean',
            'is_published'     => 'boolean',
            'plan_id'          => 'nullable|exists:subscription_plans,id',
            'access_tier'      => 'required|in:free,paid,both',
            'scoring_mode'     => 'nullable|in:regular,irt',
        ]);

        $newMode = $request->input('scoring_mode', $tryout->scoring_mode ?? 'regular');

        $payload = [
            'title'            => $request->title,
            'subject_id'       => $request->subject_id,
            'grade_id'         => $request->filled('grade_id') ? (int) $request->grade_id : null,
            'grade'            => $this->legacyGradeCode($request->input('grade_id')),
            'description'      => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'series'           => $request->series,
            'is_premium'       => $request->input('access_tier') === 'paid' || $request->boolean('is_premium'),
            'is_published'     => $request->boolean('is_published'),
            'plan_id'          => $request->filled('plan_id') ? (int) $request->plan_id : null,
            'access_tier'      => $request->input('access_tier', 'both'),
            'scoring_mode'     => $newMode,
        ];

        // Jika mode penilaian berubah, reset status kalibrasi.
        if ($newMode !== $tryout->scoring_mode) {
            $payload['irt_calibrated'] = false;
        }

        $tryout->update($payload);

        return back()->with('success', 'Tryout berhasil diperbarui.');
    }

    public function destroy(Tryout $tryout): RedirectResponse
    {
        $tryout->delete();
        return redirect()->route('admin.tryouts.index')
            ->with('success', 'Tryout berhasil dihapus.');
    }

    public function togglePublish(Tryout $tryout): RedirectResponse
    {
        $tryout->update([
            'is_published'    => !$tryout->is_published,
            'total_questions' => $tryout->questions()->count(),
        ]);

        $status = $tryout->is_published ? 'dipublikasikan' : 'disembunyikan';
        return back()->with('success', "Tryout berhasil $status.");
    }

    /**
     * Kalibrasi IRT: hitung bobot kesulitan tiap soal dari seluruh attempt
     * selesai, lalu re-score semua peserta. Jalankan setelah tryout ditutup.
     */
    public function calibrateIrt(Tryout $tryout, \App\Services\IrtScoringService $irt): RedirectResponse
    {
        if (!$tryout->isIrt()) {
            return back()->with('error', 'Tryout ini bukan mode IRT.');
        }

        $peserta = $tryout->attempts()->whereNotNull('submitted_at')->count();
        if ($peserta < 1) {
            return back()->with('error', 'Belum ada peserta yang menyelesaikan tryout ini.');
        }

        $irt->calibrate($tryout);
        $updated = $irt->rescoreAll($tryout);

        return back()->with('success',
            "Kalibrasi IRT selesai. Bobot {$tryout->questions()->count()} soal dihitung ulang & {$updated} peringkat peserta diperbarui.");
    }

    // =========================================================================
    // QUESTIONS
    // =========================================================================

    public function storeQuestion(Request $request, Tryout $tryout): RedirectResponse
    {
        $type = (string) $request->input('question_type', 'single');

        $rules = [
            'passage_id'       => 'nullable|exists:tryout_passages,id',
            'question_text'    => 'required|string',
            'question_image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'question_type'    => 'required|in:single,multiple,matrix',
            'explanation'      => 'nullable|string',
            'score_weight'     => 'required|numeric|min:0.01|max:9999',
            'subject_id'       => 'required|exists:subjects,id',
            'order'            => 'required|integer|min:1',
        ];

        if ($type === 'matrix') {
            $rules += [
                'option_a'                   => 'required|string',
                'option_b'                   => 'required|string',
                'option_c'                   => 'nullable|string',
                'option_d'                   => 'nullable|string',
                'option_e'                   => 'nullable|string',
                'matrix_columns'             => 'required|array',
                'matrix_columns.col_1'       => 'required|string|max:100',
                'matrix_columns.col_2'       => 'required|string|max:100',
                'correct_matrix_answers'     => 'required|array',
                'correct_matrix_answers.*'   => 'nullable|in:col_1,col_2',
            ];
        } else {
            $rules += [
                'option_a'          => 'required|string',
                'option_b'          => 'required|string',
                'option_c'          => 'required|string',
                'option_d'          => 'required|string',
                'option_e'          => 'nullable|string',
                'correct_answer'    => 'required_if:question_type,single|nullable|in:a,b,c,d,e',
                'correct_answers'   => 'required_if:question_type,multiple|nullable|array|min:2',
                'correct_answers.*' => 'in:a,b,c,d,e',
            ];
        }

        $request->validate($rules, [
            'matrix_columns.col_1.required' => 'Nama kategori kolom pertama wajib diisi.',
            'matrix_columns.col_2.required' => 'Nama kategori kolom kedua wajib diisi.',
            'correct_matrix_answers.required' => 'Kunci kategori wajib dipilih untuk setiap baris yang terisi.',
        ]);

        $passageId = $request->filled('passage_id') ? (int) $request->passage_id : null;
        if ($passageId && !$tryout->passages()->whereKey($passageId)->exists()) {
            return back()->withInput()->with('error', 'Soal cerita yang dipilih tidak valid untuk tryout ini.');
        }

        // Normalisasi kunci jawaban sesuai tipe.
        $matrixColumns = null;
        if ($type === 'matrix') {
            $availableRows = array_keys(array_filter([
                'a' => $request->option_a,
                'b' => $request->option_b,
                'c' => $request->option_c,
                'd' => $request->option_d,
                'e' => $request->option_e,
            ], fn($value) => filled($value)));

            $matrixKeys = [];
            foreach ($availableRows as $rowKey) {
                $selectedColumn = $request->input("correct_matrix_answers.$rowKey");
                if (!in_array($selectedColumn, ['col_1', 'col_2'], true)) {
                    return back()->withInput()
                        ->with('error', 'Setiap baris kategori yang terisi wajib dipilih kuncinya.');
                }
                $matrixKeys[$rowKey] = $selectedColumn;
            }

            if (count($availableRows) < 2) {
                return back()->withInput()
                    ->with('error', 'Soal kategori minimal punya 2 baris/pernyataan.');
            }

            $matrixColumns = [
                'col_1' => trim((string) $request->input('matrix_columns.col_1')),
                'col_2' => trim((string) $request->input('matrix_columns.col_2')),
            ];
            $correctAnswer  = 'a'; // fallback untuk kolom legacy correct_answer
            $correctAnswers = $matrixKeys;
        } elseif ($type === 'multiple') {
            $keys = collect($request->input('correct_answers', []))
                ->map(fn($k) => strtolower((string) $k))
                ->unique()
                ->values()
                ->all();
            // Pastikan kunci yang dipilih punya opsi terisi (mis. tidak memilih 'e' jika opsi e kosong).
            $available = array_keys(array_filter([
                'a' => $request->option_a, 'b' => $request->option_b,
                'c' => $request->option_c, 'd' => $request->option_d,
                'e' => $request->option_e,
            ], fn($value) => filled($value)));
            $keys = array_values(array_intersect($keys, $available));

            if (count($keys) < 2) {
                return back()->withInput()
                    ->with('error', 'Soal multiple jawaban minimal punya 2 kunci yang valid.');
            }

            $correctAnswer  = $keys[0];   // simpan 1 nilai di kolom lama agar kompatibel
            $correctAnswers = $keys;
        } else {
            $correctAnswer  = $request->correct_answer;
            $correctAnswers = null;
        }

        $maxOrder = (int) $tryout->questions()->max('order');
        $order = min((int) $request->input('order', $maxOrder + 1), $maxOrder + 1);

        $questionImage = $this->uploadTryoutImage($request, 'question_image');

        DB::transaction(function () use ($tryout, $request, $passageId, $questionImage, $type, $correctAnswer, $correctAnswers, $matrixColumns, $order) {
            // Jika nomor disisipkan di tengah, geser soal setelahnya agar urutan tetap rapi.
            $tryout->questions()
                ->where('order', '>=', $order)
                ->increment('order');

            $tryout->questions()->create([
                'passage_id'      => $passageId,
                'subject_id'      => $request->subject_id,
                'question_type'   => $type,
                'question_text'   => $request->question_text,
                'question_image'  => $questionImage,
                'option_a'        => $request->option_a,
                'option_b'        => $request->option_b,
                'option_c'        => $request->option_c,
                'option_d'        => $request->option_d,
                'option_e'        => $request->option_e,
                'correct_answer'  => $correctAnswer,
                'correct_answers' => $correctAnswers,
                'matrix_columns'  => $matrixColumns,
                'explanation'     => $request->explanation,
                'difficulty'      => 'sedang',
                'score_weight'    => (float) $request->score_weight,
                'order'           => $order,
            ]);

            // Update total questions
            $tryout->update(['total_questions' => $tryout->questions()->count()]);
        });

        return back()->with('success', 'Soal berhasil ditambahkan.');
    }

    public function storePassage(Request $request, Tryout $tryout): RedirectResponse
    {
        $request->validate([
            'title'         => 'nullable|string|max:255',
            'passage_text'  => 'required_without:passage_image|nullable|string',
            'passage_image' => 'required_without:passage_text|nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'source'        => 'nullable|string|max:255',
            'order'         => 'required|integer|min:1',
        ], [
            'passage_text.required_without'  => 'Isi teks soal cerita atau upload gambar stimulus.',
            'passage_image.required_without' => 'Isi teks soal cerita atau upload gambar stimulus.',
        ]);

        $maxOrder = (int) $tryout->passages()->max('order');
        $order = min((int) $request->input('order', $maxOrder + 1), $maxOrder + 1);
        $imagePath = $this->uploadTryoutImage($request, 'passage_image');

        DB::transaction(function () use ($tryout, $request, $order, $imagePath) {
            $tryout->passages()
                ->where('order', '>=', $order)
                ->increment('order');

            $tryout->passages()->create([
                'title'         => $request->title,
                'passage_text'  => $request->passage_text,
                'passage_image' => $imagePath,
                'source'        => $request->source,
                'order'         => $order,
            ]);
        });

        return back()->with('success', 'Soal cerita/stimulus berhasil ditambahkan. Sekarang soal bisa dikaitkan ke cerita tersebut.');
    }

    public function updatePassage(Request $request, TryoutPassage $passage): RedirectResponse
    {
        $request->validate([
            'title'         => 'nullable|string|max:255',
            'passage_text'  => 'nullable|string',
            'passage_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'source'        => 'nullable|string|max:255',
            'order'         => 'required|integer|min:1',
            'remove_image'  => 'nullable|boolean',
        ]);

        $tryout = $passage->tryout;
        if (!$tryout) {
            return back()->with('error', 'Data tryout untuk soal cerita ini tidak ditemukan.');
        }

        $hasNewImage = $request->hasFile('passage_image');
        $willRemoveImage = $request->boolean('remove_image');
        $hasFinalImage = $hasNewImage || (!$willRemoveImage && filled($passage->passage_image));

        if (!filled($request->passage_text) && !$hasFinalImage) {
            return back()->withInput()
                ->with('error', 'Isi teks soal cerita, upload gambar stimulus, atau jangan hapus gambar yang sudah ada.');
        }

        $newImagePath = $hasNewImage ? $this->uploadTryoutImage($request, 'passage_image') : null;
        $targetOrder = min((int) $request->input('order', $passage->order), max(1, (int) $tryout->passages()->count()));
        $oldOrder = (int) $passage->order;

        DB::transaction(function () use ($request, $passage, $tryout, $targetOrder, $oldOrder, $newImagePath, $willRemoveImage) {
            if ($targetOrder < $oldOrder) {
                $tryout->passages()
                    ->where('id', '!=', $passage->id)
                    ->whereBetween('order', [$targetOrder, $oldOrder - 1])
                    ->increment('order');
            } elseif ($targetOrder > $oldOrder) {
                $tryout->passages()
                    ->where('id', '!=', $passage->id)
                    ->whereBetween('order', [$oldOrder + 1, $targetOrder])
                    ->decrement('order');
            }

            $imagePath = $passage->passage_image;

            if ($newImagePath) {
                $this->deletePublicUpload($passage->passage_image);
                $imagePath = $newImagePath;
            } elseif ($willRemoveImage) {
                $this->deletePublicUpload($passage->passage_image);
                $imagePath = null;
            }

            $passage->update([
                'title'         => $request->title,
                'passage_text'  => $request->passage_text,
                'passage_image' => $imagePath,
                'source'        => $request->source,
                'order'         => $targetOrder,
            ]);
        });

        return redirect()->route('admin.tryouts.show', $tryout)
            ->with('success', 'Soal cerita/stimulus berhasil diperbarui.');
    }

    public function destroyPassage(TryoutPassage $passage): RedirectResponse
    {
        $tryoutId = $passage->tryout_id;
        $tryout = $passage->tryout;

        DB::transaction(function () use ($passage, $tryout) {
            $passage->questions()->update(['passage_id' => null]);
            $this->deletePublicUpload($passage->passage_image);
            $passage->delete();

            if ($tryout) {
                $tryout->passages()->orderBy('order')->get()->values()->each(function (TryoutPassage $item, int $index) {
                    $item->update(['order' => $index + 1]);
                });
            }
        });

        return redirect()->route('admin.tryouts.show', $tryoutId)
            ->with('success', 'Soal cerita/stimulus berhasil dihapus. Soal yang memakai cerita tersebut tidak ikut terhapus.');
    }

    public function destroyQuestion(TryoutQuestion $question): RedirectResponse
    {
        $tryoutId = $question->tryout_id;
        $this->deletePublicUpload($question->question_image);
        $question->delete();

        // Update total dan rapikan kembali nomor soal agar berurutan.
        $tryout = Tryout::find($tryoutId);
        if ($tryout) {
            $tryout->questions()->orderBy('order')->get()->values()->each(function (TryoutQuestion $item, int $index) {
                $item->update(['order' => $index + 1]);
            });
            $tryout->update(['total_questions' => $tryout->questions()->count()]);
        }

        return redirect()->route('admin.tryouts.show', $tryoutId)
            ->with('success', 'Soal berhasil dihapus.');
    }

    private function uploadTryoutImage(Request $request, string $field): ?string
    {
        if (!$request->hasFile($field)) {
            return null;
        }

        $file = $request->file($field);
        $directory = public_path('uploads/tryouts');

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $filename = now()->format('YmdHis') . '-' . Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $filename);

        return 'uploads/tryouts/' . $filename;
    }


    private function deletePublicUpload(?string $path): void
    {
        if (!$path) {
            return;
        }

        $fullPath = public_path($path);
        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
    private function legacyGradeCode(?string $gradeId): ?string
    {
        if (!$gradeId) {
            return null;
        }

        return Grade::whereKey((int) $gradeId)->value('code');
    }

}
