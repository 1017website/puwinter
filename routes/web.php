<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Payment\SubscriptionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\CourseController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\LeaderboardController;
use App\Http\Controllers\Student\LiveClassController;
use App\Http\Controllers\Student\StudyHistoryController;
use App\Http\Controllers\Student\TryoutController;
use Illuminate\Support\Facades\Route;

// ============================================================================
// PUBLIC
// ============================================================================

Route::get('/', fn() => redirect()->route('dashboard'));

// ============================================================================
// AUTH BREEZE (login, logout, password reset — jangan hapus)
// ============================================================================

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

// ============================================================================
// EMAIL VERIFICATION
// ============================================================================

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
        ->name('verification.notice');
    Route::get('/email/verify/{token}', [EmailVerificationController::class, 'verify'])
        ->name('verification.verify');
    Route::post('/email/resend', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

// ============================================================================
// STUDENT ROUTES
// ============================================================================

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('kelas')->name('student.course.')->group(function () {
        Route::get('/', [CourseController::class, 'index'])->name('index');
        Route::get('/{slug}', [CourseController::class, 'show'])->name('show');
        Route::post('/materi/{materialId}/selesai', [CourseController::class, 'markComplete'])
            ->name('material.complete');
    });

    Route::prefix('live-class')->name('student.live.')->group(function () {
        Route::get('/', [LiveClassController::class, 'index'])->name('index');
        Route::get('/{id}', [LiveClassController::class, 'show'])->name('show');
    });

    Route::prefix('tryout')->name('student.tryout.')->group(function () {
        Route::get('/', [TryoutController::class, 'index'])->name('index');
        Route::get('/{id}/mulai', [TryoutController::class, 'start'])->name('start');
        Route::post('/attempt/{attemptId}/submit', [TryoutController::class, 'submit'])->name('submit');
        Route::get('/attempt/{attemptId}/hasil', [TryoutController::class, 'result'])->name('result');
    });

    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('student.leaderboard.index');
    Route::get('/riwayat', [StudyHistoryController::class, 'index'])->name('student.history.index');

    Route::prefix('upgrade')->name('upgrade.')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index'])->name('index');
        Route::post('/checkout/{slug}', [SubscriptionController::class, 'checkout'])->name('checkout');
        Route::get('/snap', [SubscriptionController::class, 'snap'])->name('snap');
        Route::get('/sukses', [SubscriptionController::class, 'success'])->name('success');
    });
});

// ============================================================================
// MIDTRANS WEBHOOK
// ============================================================================

Route::post('/payment/callback', [SubscriptionController::class, 'callback'])
    ->name('payment.callback')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// ============================================================================
// ADMIN & MENTOR
// ============================================================================

Route::middleware(['auth', 'verified', 'role:admin,superadmin'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
    });

Route::middleware(['auth', 'verified', 'role:mentor,admin,superadmin'])
    ->prefix('mentor')->name('mentor.')
    ->group(function () {
        Route::get('/dashboard', fn() => view('mentor.dashboard'))->name('dashboard');
    });