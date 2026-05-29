<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /** Daftar notifikasi milik user yang sedang login. */
    public function index(Request $request): View
    {
        $notifications = Notification::forUser($request->user()->id)
            ->latest()
            ->paginate(20);

        // Pilih layout sesuai role (admin pakai layout admin, lainnya layout student)
        $layout = $request->user()->isAdmin() ? 'admin.layouts.app' : 'layouts.student';

        return view('notifications.index', compact('notifications', 'layout'));
    }

    /** Tandai satu notifikasi sebagai dibaca, lalu arahkan ke url tujuannya. */
    public function read(Request $request, int $id): RedirectResponse
    {
        $notif = Notification::forUser($request->user()->id)->findOrFail($id);

        if ($notif->isUnread()) {
            $notif->update(['read_at' => now()]);
        }

        return redirect($notif->url ?: route('notifications.index'));
    }

    /** Tandai semua notifikasi user sebagai dibaca. */
    public function readAll(Request $request): RedirectResponse
    {
        Notification::forUser($request->user()->id)
            ->unread()
            ->update(['read_at' => now()]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
