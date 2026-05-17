<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::withCount(['enrollments', 'tryoutAttempts'])
            ->with(['subscriptions' => fn($q) => $q->where('status','active')->where('expired_at','>',now())]);

        // Filter role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter status
        if ($request->filled('status')) {
            if ($request->status === 'premium') {
                $query->whereHas('subscriptions', fn($q) =>
                    $q->where('status','active')->where('expired_at','>',now())
                );
            } elseif ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('name', 'like', "%$s%")
                ->orWhere('email', 'like', "%$s%")
                ->orWhere('school', 'like', "%$s%")
                ->orWhere('city', 'like', "%$s%")
            );
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        $totalStats = [
            'all'     => User::count(),
            'student' => User::where('role', 'student')->count(),
            'mentor'  => User::where('role', 'mentor')->count(),
            'admin'   => User::whereIn('role', ['admin','superadmin'])->count(),
            'premium' => Subscription::where('status','active')->where('expired_at','>',now())->count(),
        ];

        return view('admin.users.index', compact('users', 'totalStats'));
    }

    public function show(User $user): View
    {
        $user->load([
            'subscriptions.plan',
            'enrollments.course',
            'tryoutAttempts.tryout',
            'achievements.achievement',
        ]);

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $plans = SubscriptionPlan::active()->get();
        return view('admin.users.edit', compact('user', 'plans'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|in:superadmin,admin,mentor,student',
            'school'   => 'nullable|string|max:255',
            'city'     => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'grade'    => 'nullable|string|max:10',
            'password' => 'nullable|string|min:8',
            'is_active'=> 'boolean',
        ]);

        $data = $request->except('password');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        // Tidak boleh hapus diri sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    // Toggle aktif/nonaktif user
    public function toggleActive(User $user): RedirectResponse
    {
        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "User berhasil $status.");
    }

    // Grant premium manual
    public function grantPremium(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        Subscription::create([
            'user_id'        => $user->id,
            'plan_id'        => $plan->id,
            'status'         => 'active',
            'started_at'     => now(),
            'expired_at'     => now()->addMonths($plan->duration_months),
            'payment_method' => 'manual_admin',
            'amount_paid'    => 0,
        ]);

        return back()->with('success', "Premium {$plan->name} berhasil diberikan ke {$user->name}.");
    }
}
