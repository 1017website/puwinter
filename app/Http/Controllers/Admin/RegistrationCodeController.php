<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegistrationCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationCodeController extends Controller
{
    public function index(Request $request): View
    {
        $query = RegistrationCode::with('creator')->withCount('students');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(fn ($query) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%"));
        }

        $registrationCodes = $query->latest()->paginate(15)->withQueryString();
        $stats = [
            'codes' => RegistrationCode::count(),
            'active' => RegistrationCode::available()->count(),
            'students' => RegistrationCode::withCount('students')->get()->sum('students_count'),
        ];

        return view('admin.registration-codes.index', compact('registrationCodes', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        RegistrationCode::create([
            ...$data,
            'code' => RegistrationCode::generateUniqueCode(),
            'is_active' => true,
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Kode pendaftar berhasil dibuat.');
    }

    public function show(RegistrationCode $registrationCode): View
    {
        $registrationCode->load('creator')->loadCount('students');
        $students = $registrationCode->students()
            ->with('grade')
            ->latest()
            ->paginate(20);

        return view('admin.registration-codes.show', compact('registrationCode', 'students'));
    }

    public function toggleActive(RegistrationCode $registrationCode): RedirectResponse
    {
        $registrationCode->update(['is_active' => ! $registrationCode->is_active]);

        $status = $registrationCode->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Kode {$registrationCode->code} berhasil {$status}.");
    }
}
