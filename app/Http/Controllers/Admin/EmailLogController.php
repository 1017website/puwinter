<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = EmailLog::with(['user', 'emailVerification'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('to_email', 'like', "%{$search}%")
                    ->orWhere('from_email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('response', 'like', "%{$search}%")
                    ->orWhere('error_message', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        $stats = [
            'total'      => EmailLog::count(),
            'sent'       => EmailLog::where('status', 'sent')->count(),
            'failed'     => EmailLog::where('status', 'failed')->count(),
            'processing' => EmailLog::where('status', 'processing')->count(),
        ];

        return view('admin.email-logs.index', compact('logs', 'stats'));
    }
}
