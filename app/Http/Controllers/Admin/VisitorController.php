<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FrontendVisit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VisitorController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        $from = isset($validated['from']) ? Carbon::parse($validated['from'])->startOfDay() : now()->subDays(29)->startOfDay();
        $to = isset($validated['to']) ? Carbon::parse($validated['to'])->endOfDay() : now()->endOfDay();
        if ($from->diffInDays($to) > 365) {
            $from = $to->copy()->subDays(364)->startOfDay();
        }
        $base = FrontendVisit::query()->whereBetween('created_at', [$from, $to]);

        $pageViews = (clone $base)->count();
        $uniqueVisitors = (clone $base)->distinct('visitor_id')->count('visitor_id');
        $stats = [
            'page_views' => $pageViews,
            'unique_visitors' => $uniqueVisitors,
            'today_views' => FrontendVisit::whereDate('created_at', today())->count(),
            'views_per_visitor' => $uniqueVisitors > 0 ? round($pageViews / $uniqueVisitors, 1) : 0,
        ];

        $dailyRows = (clone $base)
            ->selectRaw('DATE(created_at) as visit_date, COUNT(*) as views, COUNT(DISTINCT visitor_id) as visitors')
            ->groupBy('visit_date')
            ->orderBy('visit_date')
            ->get()
            ->keyBy('visit_date');

        $daily = [];
        for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
            $key = $date->format('Y-m-d');
            $daily[] = [
                'date' => $key,
                'label' => $date->format('d M'),
                'views' => (int) ($dailyRows[$key]->views ?? 0),
                'visitors' => (int) ($dailyRows[$key]->visitors ?? 0),
            ];
        }

        $topPages = (clone $base)->selectRaw('path, COUNT(*) as total')->groupBy('path')->orderByDesc('total')->limit(8)->get();
        $referrers = (clone $base)->selectRaw("COALESCE(referrer_domain, 'Langsung') as source, COUNT(*) as total")->groupBy('source')->orderByDesc('total')->limit(8)->get();
        $devices = (clone $base)->selectRaw('device, COUNT(*) as total')->groupBy('device')->orderByDesc('total')->get();
        $recentVisits = (clone $base)->latest()->paginate(20)->withQueryString();

        return view('admin.visitors.index', compact(
            'stats', 'daily', 'topPages', 'referrers', 'devices', 'recentVisits', 'from', 'to'
        ));
    }
}
