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
            'tier'             => 'nullable|in:regular,exclusive',
            'duration_months'  => 'required|integer|min:1',
            'start_date'       => 'nullable|date',
            'end_date'         => 'nullable|date|after_or_equal:start_date',
            'quota'            => 'nullable|integer|min:1',
            'price'            => 'required|integer|min:0',
            'original_price'   => 'required|integer|min:0',
            'features'         => 'nullable|string',
            'bonus'            => 'nullable|string|max:255',
            'is_popular'       => 'boolean',
            'order'            => 'nullable|integer',
            'flyer_image'      => 'nullable|image|max:4096',
        ]);

        // Parse fitur dari textarea (satu baris = satu fitur)
        $features = array_filter(
            array_map('trim', explode("\n", $request->input('features', ''))),
            fn($f) => $f !== ''
        );

        $flyerPath = null;
        if ($request->hasFile('flyer_image')) {
            $flyerPath = $request->file('flyer_image')->store('flyers', 'public');
        }

        SubscriptionPlan::create([
            'name'            => $request->name,
            'slug'            => Str::slug($request->name) . '-' . $request->duration_months . 'bln',
            'tier'            => $request->input('tier', 'regular'),
            'duration_months' => $request->duration_months,
            'start_date'      => $request->start_date,
            'end_date'        => $request->end_date,
            'quota'           => $request->filled('quota') ? (int) $request->quota : null,
            'flyer_image'     => $flyerPath,
            'price'           => $request->price,
            'original_price'  => $request->original_price,
            'features'        => array_values($features),
            'bonus'           => $request->bonus,
            'is_popular'      => $request->boolean('is_popular'),
            'is_active'       => true,
            'order'           => $request->input('order', SubscriptionPlan::max('order') + 1),
        ]);

        return back()->with('success', 'Program berhasil ditambahkan.');
    }

    public function update(Request $request, SubscriptionPlan $plan): RedirectResponse
    {
        $request->validate([
            'name'            => 'required|string|max:100',
            'tier'            => 'nullable|in:regular,exclusive',
            'duration_months' => 'required|integer|min:1',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'quota'           => 'nullable|integer|min:1',
            'price'           => 'required|integer|min:0',
            'original_price'  => 'required|integer|min:0',
            'features'        => 'nullable|string',
            'bonus'           => 'nullable|string|max:255',
            'is_popular'      => 'boolean',
            'is_active'       => 'boolean',
            'order'           => 'nullable|integer',
            'flyer_image'     => 'nullable|image|max:4096',
        ]);

        $features = array_filter(
            array_map('trim', explode("\n", $request->input('features', ''))),
            fn($f) => $f !== ''
        );

        $data = [
            'name'            => $request->name,
            'tier'            => $request->input('tier', $plan->tier ?? 'regular'),
            'duration_months' => $request->duration_months,
            'start_date'      => $request->start_date,
            'end_date'        => $request->end_date,
            'quota'           => $request->filled('quota') ? (int) $request->quota : null,
            'price'           => $request->price,
            'original_price'  => $request->original_price,
            'features'        => array_values($features),
            'bonus'           => $request->bonus,
            'is_popular'      => $request->boolean('is_popular'),
            'is_active'       => $request->boolean('is_active'),
            'order'           => $request->input('order', $plan->order),
        ];

        if ($request->hasFile('flyer_image')) {
            // hapus flyer lama bila ada
            if ($plan->flyer_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($plan->flyer_image);
            }
            $data['flyer_image'] = $request->file('flyer_image')->store('flyers', 'public');
        }

        $plan->update($data);

        return back()->with('success', 'Program berhasil diperbarui.');
    }

    public function destroy(SubscriptionPlan $plan): RedirectResponse
    {
        if ($plan->subscriptions()->whereIn('status', ['active', 'pending'])->exists()) {
            return back()->with('error', 'Tidak bisa menghapus program yang masih memiliki subscriber aktif.');
        }

        $plan->delete();
        return back()->with('success', 'Program berhasil dihapus.');
    }
}
