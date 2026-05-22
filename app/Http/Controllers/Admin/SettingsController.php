<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index');
    }

    public function uploadLogo(Request $request): RedirectResponse
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        $request->file('logo')->move(public_path('images'), 'logo.png');

        return back()->with('success', 'Logo berhasil diperbarui.');
    }

    public function uploadFavicon(Request $request): RedirectResponse
    {
        $request->validate([
            'favicon' => 'required|image|mimes:png,jpg,jpeg,ico|max:512',
        ]);

        $request->file('favicon')->move(public_path('images'), 'favicon.png');

        return back()->with('success', 'Favicon berhasil diperbarui.');
    }
}
