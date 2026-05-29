<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Tryout;
use App\Models\TryoutQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        return view('admin.tryouts.create', compact('subjects'));
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
        ]);

        $tryout->update([
            'title'            => $request->title,
            'subject_id'       => $request->subject_id,
            'grade'            => $request->filled('grade') ? $request->grade : null,
            'description'      => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'series'           => $request->series,
            'is_premium'       => $request->boolean('is_premium'),
            'is_published'     => $request->boolean('is_published'),
        ]);

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

    // =========================================================================
    // QUESTIONS
    // =========================================================================

    public function storeQuestion(Request $request, Tryout $tryout): RedirectResponse
    {
        $request->validate([
            'question_text'  => 'required|string',
            'option_a'       => 'required|string',
            'option_b'       => 'required|string',
            'option_c'       => 'required|string',
            'option_d'       => 'required|string',
            'option_e'       => 'nullable|string',
            'correct_answer' => 'required|in:a,b,c,d,e',
            'explanation'    => 'nullable|string',
            'difficulty'     => 'required|in:mudah,sedang,sulit',
            'subject_id'     => 'required|exists:subjects,id',
        ]);

        $tryout->questions()->create([
            'subject_id'     => $request->subject_id,
            'question_text'  => $request->question_text,
            'option_a'       => $request->option_a,
            'option_b'       => $request->option_b,
            'option_c'       => $request->option_c,
            'option_d'       => $request->option_d,
            'option_e'       => $request->option_e,
            'correct_answer' => $request->correct_answer,
            'explanation'    => $request->explanation,
            'difficulty'     => $request->difficulty,
            'order'          => $tryout->questions()->max('order') + 1,
        ]);

        // Update total questions
        $tryout->update(['total_questions' => $tryout->questions()->count()]);

        return back()->with('success', 'Soal berhasil ditambahkan.');
    }

    public function destroyQuestion(TryoutQuestion $question): RedirectResponse
    {
        $tryoutId = $question->tryout_id;
        $question->delete();

        // Update total
        $tryout = Tryout::find($tryoutId);
        $tryout?->update(['total_questions' => $tryout->questions()->count()]);

        return redirect()->route('admin.tryouts.show', $tryoutId)
            ->with('success', 'Soal berhasil dihapus.');
    }
}
