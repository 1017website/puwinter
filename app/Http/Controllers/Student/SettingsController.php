<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        if ($user->isStudent()) {
            $user->ensureAffiliateCode();
        }
        $activeSubscription = $user->activeSubscription();

        return view('student.settings.index', compact('user', 'activeSubscription'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'name'       => 'required|string|max:100',
            'phone'      => 'nullable|string|max:20',
            'school'     => 'nullable|string|max:200',
            'city'       => 'nullable|string|max:100',
            'province'   => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'grade'      => 'nullable|in:10,11,12',
        ]);

        $data = $request->only([
            'name', 'phone', 'school', 'city', 'province', 'birth_date', 'grade',
        ]);
        $data['grade'] = $request->filled('grade') ? $request->grade : null;

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $request->user()->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}
