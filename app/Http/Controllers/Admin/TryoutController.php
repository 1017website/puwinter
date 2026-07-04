<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Tryout;
use App\Models\TryoutQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TryoutController extends Controller
{
    public function index(Request $request): View
    {
        $query = Tryout::with('subject')->withCount(['questions', 'attempts']);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $tryouts  = $query->latest()->paginate(15)->withQueryString();
        $subjects = Subject::active()->get();

        return view('admin.tryouts.index', compact('tryouts', 'subjects'));
    }

    public function create(): View
    {
        $subjects = Subject::active()->get();
        $plans = \App\Models\SubscriptionPlan::active()->orderBy('order')->get();
        return view('admin.tryouts.create', compact('subjects', 'plans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'subject_id'       => 'nullable|exists:subjects,id',
            'grade'            => 'nullable|in:10,11,12',
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
            'grade'            => $request->filled('grade') ? $request->grade : null,
            'description'      => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'series'           => $request->series,
            'is_premium'       => $request->boolean('is_premium'),
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
        $tryout->load(['subject', 'questions.subject']);
        $subjects = Subject::active()->get();

        return view('admin.tryouts.show', compact('tryout', 'subjects'));
    }

    public function edit(Tryout $tryout): View
    {
        $subjects = Subject::active()->get();
        return view('admin.tryouts.edit', compact('tryout', 'subjects'));
    }

    public function update(Request $request, Tryout $tryout): RedirectResponse
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'subject_id'       => 'nullable|exists:subjects,id',
            'grade'            => 'nullable|in:10,11,12',
            'description'      => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'series'           => 'nullable|string|max:100',
            'is_premium'       => 'boolean',
            'is_published'     => 'boolean',
            'scoring_mode'     => 'nullable|in:regular,irt',
        ]);

        $newMode = $request->input('scoring_mode', $tryout->scoring_mode ?? 'regular');

        $payload = [
            'title'            => $request->title,
            'subject_id'       => $request->subject_id,
            'grade'            => $request->filled('grade') ? $request->grade : null,
            'description'      => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'series'           => $request->series,
            'is_premium'       => $request->boolean('is_premium'),
            'is_published'     => $request->boolean('is_published'),
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
        $request->validate([
            'question_text'    => 'required|string',
            'question_type'    => 'required|in:single,multiple',
            'option_a'         => 'required|string',
            'option_b'         => 'required|string',
            'option_c'         => 'required|string',
            'option_d'         => 'required|string',
            'option_e'         => 'nullable|string',
            'correct_answer'   => 'required_if:question_type,single|nullable|in:a,b,c,d,e',
            'correct_answers'  => 'required_if:question_type,multiple|nullable|array|min:2',
            'correct_answers.*'=> 'in:a,b,c,d,e',
            'explanation'      => 'nullable|string',
            'score_weight'     => 'required|numeric|min:0.01|max:9999',
            'subject_id'       => 'required|exists:subjects,id',
            'order'            => 'required|integer|min:1',
        ]);

        $type = $request->question_type;

        // Normalisasi kunci jawaban sesuai tipe.
        if ($type === 'multiple') {
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
            ]));
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

        DB::transaction(function () use ($tryout, $request, $type, $correctAnswer, $correctAnswers, $order) {
            // Jika nomor disisipkan di tengah, geser soal setelahnya agar urutan tetap rapi.
            $tryout->questions()
                ->where('order', '>=', $order)
                ->increment('order');

            $tryout->questions()->create([
                'subject_id'      => $request->subject_id,
                'question_type'   => $type,
                'question_text'   => $request->question_text,
                'option_a'        => $request->option_a,
                'option_b'        => $request->option_b,
                'option_c'        => $request->option_c,
                'option_d'        => $request->option_d,
                'option_e'        => $request->option_e,
                'correct_answer'  => $correctAnswer,
                'correct_answers' => $correctAnswers,
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

    public function destroyQuestion(TryoutQuestion $question): RedirectResponse
    {
        $tryoutId = $question->tryout_id;
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
}
