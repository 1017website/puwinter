<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramEnrollment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\UserExcelExportService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = $this->filteredQuery($request)
            ->withCount(['enrollments', 'tryoutAttempts'])
            ->with(['subscriptions' => fn ($query) => $query
                ->where('status', 'active')
                ->where('expired_at', '>', now())]);

        $users = $query->latest()->paginate(20)->withQueryString();

        $totalStats = [
            'all' => User::count(),
            'student' => User::where('role', 'student')->count(),
            'mentor' => User::where('role', 'mentor')->count(),
            'admin' => User::whereIn('role', ['admin', 'superadmin'])->count(),
            'premium' => Subscription::where('status', 'active')->where('expired_at', '>', now())->count(),
        ];

        return view('admin.users.index', compact('users', 'totalStats'));
    }

    public function export(Request $request, UserExcelExportService $exportService): BinaryFileResponse
    {
        $users = $this->filteredQuery($request)
            ->withCount(['enrollments', 'tryoutAttempts'])
            ->with([
                'grade',
                'registrationCode',
                'referredBy:id,name',
                'subscriptions' => fn ($query) => $query
                    ->where('status', 'active')
                    ->where('expired_at', '>', now())
                    ->with('plan:id,name'),
            ])
            ->orderBy('name')
            ->get();

        $path = $exportService->create($users, [
            'role' => $request->string('role')->toString() ?: null,
            'status' => $request->string('status')->toString() ?: null,
            'search' => $request->string('search')->toString() ?: null,
        ]);

        $filename = 'data-user-puwinter-'.now()->format('Y-m-d-His').'.xlsx';

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function filteredQuery(Request $request): Builder
    {
        $query = User::query();

        if ($request->filled('role')) {
            $roles = array_values(array_filter(explode(',', (string) $request->role)));
            $query->whereIn('role', $roles);
        }

        if ($request->filled('status')) {
            if ($request->status === 'premium') {
                $query->whereHas('subscriptions', fn ($query) => $query
                    ->where('status', 'active')
                    ->where('expired_at', '>', now()));
            } elseif ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(fn ($query) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('school', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%"));
        }

        return $query;
    }

    /** Form tambah user baru. */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /** Simpan user baru. */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:superadmin,admin,mentor,student',
            'password' => 'required|string|min:8',
            'phone' => 'required|string|max:20',
            'school' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'grade' => 'nullable|in:10,11,12',
            'is_active' => 'boolean',
        ], [
            'phone.required' => 'Nomor HP wajib diisi.',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['grade'] = $request->filled('grade') ? $request->grade : null;

        $user = User::create($validated);

        // Akun dibuat admin dianggap terverifikasi (email_verified_at di luar $fillable)
        $user->forceFill(['email_verified_at' => now()])->save();

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User berhasil dibuat.');
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|in:superadmin,admin,mentor,student',
            'school' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'grade' => 'nullable|string|max:10',
            'password' => 'nullable|string|min:8',
            'is_active' => 'boolean',
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
        $user->update(['is_active' => ! $user->is_active]);

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

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'tier' => $plan->tier ?? 'regular',
            'status' => 'active',
            'started_at' => now(),
            'expired_at' => now()->addMonths((int) $plan->duration_months),
            'payment_method' => 'manual_admin',
            'amount_paid' => 0,
        ]);

        // Akses per-program: tandai enrollment program ini menjadi BERBAYAR.
        ProgramEnrollment::updateOrCreate(
            ['user_id' => $user->id, 'plan_id' => $plan->id],
            [
                'status' => ProgramEnrollment::STATUS_PAID,
                'subscription_id' => $subscription->id,
                'paid_at' => now(),
                'paid_expires_at' => $subscription->expired_at,
                'enrolled_at' => now(),
            ]
        );

        return back()->with('success', "Premium {$plan->name} berhasil diberikan ke {$user->name}.");
    }
}
