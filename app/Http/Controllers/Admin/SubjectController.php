<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(): View
    {
        $subjects = Subject::withCount(['courses', 'tryouts', 'liveClasses'])
            ->orderBy('order')
            ->get();

        return view('admin.subjects.index', compact('subjects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'  => 'required|string|max:100|unique:subjects,name',
            'icon'  => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'order' => 'nullable|integer',
        ]);

        Subject::create([
            'name'      => $request->name,
            'slug'      => Str::slug($request->name),
            'icon'      => $request->input('icon', 'fa-book'),
            'color'     => $request->input('color', '#2563EB'),
            'order'     => $request->input('order', Subject::max('order') + 1),
            'is_active' => true,
        ]);

        return back()->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $request->validate([
            'name'  => 'required|string|max:100|unique:subjects,name,' . $subject->id,
            'icon'  => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'order' => 'nullable|integer',
        ]);

        $subject->update([
            'name'  => $request->name,
            'slug'  => Str::slug($request->name),
            'icon'  => $request->input('icon', $subject->icon),
            'color' => $request->input('color', $subject->color),
            'order' => $request->input('order', $subject->order),
        ]);

        return back()->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function toggleActive(Subject $subject): RedirectResponse
    {
        $subject->update(['is_active' => !$subject->is_active]);
        $label = $subject->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Mata pelajaran berhasil $label.");
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        if ($subject->courses()->count() > 0 || $subject->tryouts()->count() > 0) {
            return back()->with('error', 'Tidak bisa menghapus mata pelajaran yang masih digunakan oleh kelas atau tryout.');
        }

        $subject->delete();
        return back()->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
