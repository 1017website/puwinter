<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Payment\SubscriptionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SystemMaintenanceController;
use App\Http\Controllers\Student\AchievementController;
use App\Http\Controllers\Student\CourseController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\LeaderboardController;
use App\Http\Controllers\Student\LiveClassController;
use App\Http\Controllers\Student\StudyHistoryController;
use App\Http\Controllers\Student\TryoutController;
use App\Http\Controllers\Student\TryoutHistoryController;
use App\Http\Controllers\Student\ProgramController;
use App\Http\Controllers\Student\BankSoalController;
use App\Http\Controllers\Student\MateriPdfController;
use App\Http\Controllers\Student\ExtraClassController;
use App\Http\Controllers\Student\PembahasanController;
use App\Http\Controllers\Student\SettingsController as StudentSettings;
use App\Http\Controllers\Student\GradeChangeController;
use App\Http\Controllers\Admin\LiveClassController as AdminLiveClass;
use App\Http\Controllers\Admin\SettingsController as AdminSettings;
use App\Http\Controllers\Admin\SubjectController as AdminSubject;
use App\Http\Controllers\Admin\GradeController as AdminGrade;
use App\Http\Controllers\Admin\GradeChangeRequestController as AdminGradeRequest;
use App\Http\Controllers\Admin\PlanController as AdminPlan;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\Admin\CourseController as AdminCourse;
use App\Http\Controllers\Admin\TryoutController as AdminTryout;
use App\Http\Controllers\Admin\TryoutResultController as AdminTryoutResult;
use App\Http\Controllers\Admin\SubscriptionController as AdminSubscription;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WelcomeController;

// ============================================================================
// PUBLIC
// ============================================================================

Route::get('/', [WelcomeController::class, 'index'])->name('home');
Route::get('/index2', [WelcomeController::class, 'index2'])->name('home2');
Route::get('/index3', [WelcomeController::class, 'index3'])->name('home3');

// ============================================================================
// AUTH BREEZE (login, logout, password reset — jangan hapus)
// ============================================================================

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Jalur perawatan sistem khusus superadmin (untuk migrate via browser di
    // shared hosting tanpa terminal). Tidak memakai 'verified' agar tetap bisa
    // diakses; pengecekan superadmin dilakukan di dalam controller.
    Route::get('/system/maintenance', [SystemMaintenanceController::class, 'index'])->name('system.maintenance.index');
    Route::post('/system/maintenance/run', [SystemMaintenanceController::class, 'run'])->name('system.maintenance.run');
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

    // Program (akses per-program)
    Route::prefix('program')->name('student.program.')->group(function () {
        Route::get('/', [ProgramController::class, 'index'])->name('index');
        Route::get('/{planId}', [ProgramController::class, 'show'])->name('show');
        Route::post('/{planId}/daftar', [ProgramController::class, 'enroll'])->name('enroll');
    });

    // Kelas Saya
    Route::prefix('kelas')->name('student.course.')->group(function () {
        Route::get('/', [CourseController::class, 'index'])->name('index');
        Route::get('/jelajahi', [CourseController::class, 'explore'])->name('explore');
        Route::get('/{slug}', [CourseController::class, 'show'])->name('show');
        Route::get('/{slug}/materi/{materialId}', [CourseController::class, 'showMaterial'])->name('material.show');
        Route::post('/materi/{materialId}/selesai', [CourseController::class, 'markComplete'])
            ->name('material.complete');
    });

    // Extra Class (mis. TOEFL) — menu tersendiri, bebas tanpa premium
    Route::prefix('extra-class')->name('student.extra.')->group(function () {
        Route::get('/', [ExtraClassController::class, 'index'])->name('index');
    });

    // Live Class
    Route::prefix('live-class')->name('student.live.')->group(function () {
        Route::get('/', [LiveClassController::class, 'index'])->name('index');
        Route::get('/{id}', [LiveClassController::class, 'show'])->name('show');
        // Redirect aman ke Zoom (link asli tidak dirender di HTML)
        Route::get('/{id}/join', [LiveClassController::class, 'join'])->name('join');
    });

    // Tryout
    Route::prefix('tryout')->name('student.tryout.')->group(function () {
        Route::get('/', [TryoutController::class, 'index'])->name('index');
        Route::get('/riwayat', [TryoutHistoryController::class, 'index'])->name('history');
        Route::get('/{id}/mulai', [TryoutController::class, 'start'])->name('start');
        Route::post('/attempt/{attemptId}/submit', [TryoutController::class, 'submit'])->name('submit');
        Route::get('/attempt/{attemptId}/hasil', [TryoutController::class, 'result'])->name('result');
    });

    // Leaderboard & Riwayat
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('student.leaderboard.index');
    Route::get('/riwayat', [StudyHistoryController::class, 'index'])->name('student.history.index');

    // Pencapaian / Achievement
    Route::get('/pencapaian', [AchievementController::class, 'index'])->name('student.achievement.index');

    // Notifikasi (dipakai student & admin)
    Route::prefix('notifikasi')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/baca-semua', [NotificationController::class, 'readAll'])->name('read-all');
        Route::get('/{id}', [NotificationController::class, 'read'])->name('read');
    });

    // Bank Soal
    // Bank Soal dinonaktifkan sementara (uncomment untuk mengaktifkan kembali).
    // Disembunyikan agar siswa tidak bisa melihat semua soal + kunci di luar tryout.
    /*
    Route::prefix('bank-soal')->name('student.bank.')->group(function () {
        Route::get('/', [BankSoalController::class, 'index'])->name('index');
        Route::post('/simpan/{questionId}', [BankSoalController::class, 'toggleSave'])->name('toggle-save');
    });
    */

    // Materi PDF
    Route::prefix('materi-pdf')->name('student.pdf.')->group(function () {
        Route::get('/', [MateriPdfController::class, 'index'])->name('index');
        Route::post('/simpan/{materialId}', [MateriPdfController::class, 'toggleSave'])->name('toggle-save');
    });

    // Pembahasan
    Route::get('/pembahasan', [PembahasanController::class, 'index'])->name('student.pembahasan.index');

    // Pindah Kelas (request ke admin)
    Route::prefix('pindah-kelas')->name('student.grade-change.')->group(function () {
        Route::get('/', [GradeChangeController::class, 'index'])->name('index');
        Route::post('/', [GradeChangeController::class, 'store'])->name('store');
    });

    // Pengaturan Siswa
    Route::prefix('pengaturan')->name('student.settings.')->group(function () {
        Route::get('/', [StudentSettings::class, 'index'])->name('index');
        Route::post('/profil', [StudentSettings::class, 'updateProfile'])->name('profile');
        Route::post('/password', [StudentSettings::class, 'updatePassword'])->name('password');
    });

    // Upgrade Premium (transfer manual)
    Route::prefix('upgrade')->name('upgrade.')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index'])->name('index');
        Route::post('/checkout/{slug}', [SubscriptionController::class, 'checkout'])->name('checkout');
        Route::get('/instruksi/{id}', [SubscriptionController::class, 'instruction'])->name('instruction');
        Route::post('/instruksi/{id}/bukti', [SubscriptionController::class, 'uploadProof'])->name('upload-proof');
    });
});

// ============================================================================
// ADMIN ROUTES
// ============================================================================

Route::middleware(['auth', 'verified', 'role:admin,superadmin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // Users
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminUser::class, 'index'])->name('index');
            Route::get('/create', [AdminUser::class, 'create'])->name('create');
            Route::post('/', [AdminUser::class, 'store'])->name('store');
            Route::get('/{user}', [AdminUser::class, 'show'])->name('show');
            Route::get('/{user}/edit', [AdminUser::class, 'edit'])->name('edit');
            Route::put('/{user}', [AdminUser::class, 'update'])->name('update');
            Route::delete('/{user}', [AdminUser::class, 'destroy'])->name('destroy');
            Route::patch('/{user}/toggle-active', [AdminUser::class, 'toggleActive'])->name('toggle-active');
            Route::post('/{user}/grant-premium', [AdminUser::class, 'grantPremium'])->name('grant-premium');
        });

        // Courses
        Route::prefix('courses')->name('courses.')->group(function () {
            Route::get('/', [AdminCourse::class, 'index'])->name('index');
            Route::get('/create', [AdminCourse::class, 'create'])->name('create');
            Route::post('/', [AdminCourse::class, 'store'])->name('store');
            Route::get('/{course}', [AdminCourse::class, 'show'])->name('show');
            Route::get('/{course}/edit', [AdminCourse::class, 'edit'])->name('edit');
            Route::put('/{course}', [AdminCourse::class, 'update'])->name('update');
            Route::delete('/{course}', [AdminCourse::class, 'destroy'])->name('destroy');
            Route::patch('/{course}/toggle-publish', [AdminCourse::class, 'togglePublish'])->name('toggle-publish');

            // Modules
            Route::post('/{course}/modules', [AdminCourse::class, 'storeModule'])->name('modules.store');
            Route::delete('/modules/{module}', [AdminCourse::class, 'destroyModule'])->name('modules.destroy');

            // Materials
            Route::post('/modules/{module}/materials', [AdminCourse::class, 'storeMaterial'])->name('materials.store');
            Route::delete('/materials/{material}', [AdminCourse::class, 'destroyMaterial'])->name('materials.destroy');
        });

        // Hasil Tryout Siswa
        Route::prefix('tryout-results')->name('tryout-results.')->group(function () {
            Route::get('/', [AdminTryoutResult::class, 'index'])->name('index');
            Route::get('/{attempt}', [AdminTryoutResult::class, 'show'])->name('show');
        });

        // Tryouts
        Route::prefix('tryouts')->name('tryouts.')->group(function () {
            Route::get('/', [AdminTryout::class, 'index'])->name('index');
            Route::get('/create', [AdminTryout::class, 'create'])->name('create');
            Route::post('/', [AdminTryout::class, 'store'])->name('store');
            Route::get('/{tryout}', [AdminTryout::class, 'show'])->name('show');
            Route::get('/{tryout}/edit', [AdminTryout::class, 'edit'])->name('edit');
            Route::put('/{tryout}', [AdminTryout::class, 'update'])->name('update');
            Route::delete('/{tryout}', [AdminTryout::class, 'destroy'])->name('destroy');
            Route::patch('/{tryout}/toggle-publish', [AdminTryout::class, 'togglePublish'])->name('toggle-publish');
            Route::patch('/{tryout}/calibrate-irt', [AdminTryout::class, 'calibrateIrt'])->name('calibrate-irt');

            // Questions
            Route::post('/{tryout}/questions', [AdminTryout::class, 'storeQuestion'])->name('questions.store');
            Route::delete('/questions/{question}', [AdminTryout::class, 'destroyQuestion'])->name('questions.destroy');
        });

        // Subscriptions
        Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
            Route::get('/', [AdminSubscription::class, 'index'])->name('index');
            Route::patch('/{subscription}/activate', [AdminSubscription::class, 'activate'])->name('activate');
            Route::patch('/{subscription}/cancel', [AdminSubscription::class, 'cancel'])->name('cancel');
            Route::patch('/{subscription}/extend', [AdminSubscription::class, 'extend'])->name('extend');
        });

        // Live Class
        Route::prefix('live-classes')->name('live-classes.')->group(function () {
            Route::get('/', [AdminLiveClass::class, 'index'])->name('index');
            Route::get('/create', [AdminLiveClass::class, 'create'])->name('create');
            Route::post('/', [AdminLiveClass::class, 'store'])->name('store');
            Route::get('/{liveClass}/edit', [AdminLiveClass::class, 'edit'])->name('edit');
            Route::put('/{liveClass}', [AdminLiveClass::class, 'update'])->name('update');
            Route::delete('/{liveClass}', [AdminLiveClass::class, 'destroy'])->name('destroy');
            Route::patch('/{liveClass}/status', [AdminLiveClass::class, 'setStatus'])->name('set-status');
        });

        // Settings
        Route::get('/settings', [AdminSettings::class, 'index'])->name('settings.index');
        Route::post('/settings/logo', [AdminSettings::class, 'uploadLogo'])->name('settings.logo');
        Route::post('/settings/favicon', [AdminSettings::class, 'uploadFavicon'])->name('settings.favicon');
        Route::post('/settings/artisan', [AdminSettings::class, 'runArtisan'])->name('settings.artisan');
        Route::post('/settings/bank', [AdminSettings::class, 'updateBank'])->name('settings.bank');
        Route::post('/settings/affiliate', [AdminSettings::class, 'updateAffiliate'])->name('settings.affiliate');

        // Mata Pelajaran
        Route::prefix('subjects')->name('subjects.')->group(function () {
            Route::get('/', [AdminSubject::class, 'index'])->name('index');
            Route::post('/', [AdminSubject::class, 'store'])->name('store');
            Route::put('/{subject}', [AdminSubject::class, 'update'])->name('update');
            Route::delete('/{subject}', [AdminSubject::class, 'destroy'])->name('destroy');
            Route::patch('/{subject}/toggle-active', [AdminSubject::class, 'toggleActive'])->name('toggle-active');
        });

        // Master Kelas (grade)
        Route::prefix('grades')->name('grades.')->group(function () {
            Route::get('/', [AdminGrade::class, 'index'])->name('index');
            Route::post('/', [AdminGrade::class, 'store'])->name('store');
            Route::put('/{grade}', [AdminGrade::class, 'update'])->name('update');
            Route::delete('/{grade}', [AdminGrade::class, 'destroy'])->name('destroy');
            Route::patch('/{grade}/toggle-active', [AdminGrade::class, 'toggleActive'])->name('toggle-active');
        });

        // Permintaan Pindah Kelas
        Route::prefix('grade-requests')->name('grade-requests.')->group(function () {
            Route::get('/', [AdminGradeRequest::class, 'index'])->name('index');
            Route::patch('/{gradeChangeRequest}/approve', [AdminGradeRequest::class, 'approve'])->name('approve');
            Route::patch('/{gradeChangeRequest}/reject', [AdminGradeRequest::class, 'reject'])->name('reject');
        });

        // Paket Harga
        Route::prefix('plans')->name('plans.')->group(function () {
            Route::get('/', [AdminPlan::class, 'index'])->name('index');
            Route::post('/', [AdminPlan::class, 'store'])->name('store');
            Route::put('/{plan}', [AdminPlan::class, 'update'])->name('update');
            Route::delete('/{plan}', [AdminPlan::class, 'destroy'])->name('destroy');
        });

    });

// ============================================================================
// MENTOR ROUTES
// ============================================================================

Route::middleware(['auth', 'verified', 'role:mentor,admin,superadmin'])
    ->prefix('mentor')
    ->name('mentor.')
    ->group(function () {
        Route::get('/dashboard', fn() => view('mentor.dashboard'))->name('dashboard');
    });