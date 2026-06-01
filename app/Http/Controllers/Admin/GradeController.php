<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GradeController extends Controller
{
    public function index(): View
    {
        $grades = Grade::withCount(['users', 'courses', 'tryouts', 'liveClasses'])
            ->orderBy('order')
            ->get();

        return view('admin.grades.index', compact('grades'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'code'  => 'nullable|string|max:50|unique:grades,code',
            'order' => 'nullable|integer',
        ]);

        Grade::create([
            'name'      => $request->name,
            'code'      => $request->input('code') ?: Str::slug($request->name),
            'order'     => $request->input('order', (int) Grade::max('order') + 1),
            'is_active' => true,
        ]);

        return back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function update(Request $request, Grade $grade): RedirectResponse
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'code'  => 'nullable|string|max:50|unique:grades,code,' . $grade->id,
            'order' => 'nullable|integer',
        ]);

        $grade->update([
            'name'  => $request->name,
            'code'  => $request->input('code') ?: $grade->code,
            'order' => $request->input('order', $grade->order),
        ]);

        return back()->with('success', 'Kelas berhasil diperbarui.');
    }

    public function toggleActive(Grade $grade): RedirectResponse
    {
        $grade->update(['is_active' => !$grade->is_active]);
        $label = $grade->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Kelas berhasil $label.");
    }

    public function destroy(Grade $grade): RedirectResponse
    {
        if ($grade->users()->count() > 0
            || $grade->courses()->count() > 0
            || $grade->tryouts()->count() > 0
            || $grade->liveClasses()->count() > 0) {
            return back()->with('error', 'Tidak bisa menghapus kelas yang masih dipakai siswa atau konten.');
        }

        $grade->delete();
        return back()->with('success', 'Kelas berhasil dihapus.');
    }
}
