<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        $plans = SubscriptionPlan::withCount('subscriptions')
            ->orderBy('order')
            ->get();

        return view('admin.plans.index', compact('plans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'             => 'required|string|max:100',
            'duration_months'  => 'required|integer|min:1',
            'price'            => 'required|integer|min:0',
            'original_price'   => 'required|integer|min:0',
            'features'         => 'nullable|string',
            'bonus'            => 'nullable|string|max:255',
            'is_popular'       => 'boolean',
            'order'            => 'nullable|integer',
        ]);

        // Parse fitur dari textarea (satu baris = satu fitur)
        $features = array_filter(
            array_map('trim', explode("\n", $request->input('features', ''))),
            fn($f) => $f !== ''
        );

        SubscriptionPlan::create([
            'name'            => $request->name,
            'slug'            => Str::slug($request->name) . '-' . $request->duration_months . 'bln',
            'duration_months' => $request->duration_months,
            'price'           => $request->price,
            'original_price'  => $request->original_price,
            'features'        => array_values($features),
            'bonus'           => $request->bonus,
            'is_popular'      => $request->boolean('is_popular'),
            'is_active'       => true,
            'order'           => $request->input('order', SubscriptionPlan::max('order') + 1),
        ]);

        return back()->with('success', 'Paket berhasil ditambahkan.');
    }

    public function update(Request $request, SubscriptionPlan $plan): RedirectResponse
    {
        $request->validate([
            'name'            => 'required|string|max:100',
            'duration_months' => 'required|integer|min:1',
            'price'           => 'required|integer|min:0',
            'original_price'  => 'required|integer|min:0',
            'features'        => 'nullable|string',
            'bonus'           => 'nullable|string|max:255',
            'is_popular'      => 'boolean',
            'is_active'       => 'boolean',
            'order'           => 'nullable|integer',
        ]);

        $features = array_filter(
            array_map('trim', explode("\n", $request->input('features', ''))),
            fn($f) => $f !== ''
        );

        $plan->update([
            'name'            => $request->name,
            'duration_months' => $request->duration_months,
            'price'           => $request->price,
            'original_price'  => $request->original_price,
            'features'        => array_values($features),
            'bonus'           => $request->bonus,
            'is_popular'      => $request->boolean('is_popular'),
            'is_active'       => $request->boolean('is_active'),
            'order'           => $request->input('order', $plan->order),
        ]);

        return back()->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(SubscriptionPlan $plan): RedirectResponse
    {
        if ($plan->subscriptions()->whereIn('status', ['active', 'pending'])->exists()) {
            return back()->with('error', 'Tidak bisa menghapus paket yang masih memiliki subscriber aktif.');
        }

        $plan->delete();
        return back()->with('success', 'Paket berhasil dihapus.');
    }
}
