<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolRegistrationController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ─── Public ──────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ─── Public: Pricing & School Registration ───────────────────────
Route::get('/pricing', [SubscriptionController::class, 'plans'])->name('pricing');
Route::get('/schools/register', [SchoolRegistrationController::class, 'showForm'])->name('schools.register');
Route::post('/schools/register', [SchoolRegistrationController::class, 'register'])->name('schools.register.submit');
Route::get('/schools/payment/success', [SchoolRegistrationController::class, 'paymentSuccess'])->name('schools.payment.success');

// ─── Guest (non-authenticated) ──────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login',   [LoginController::class, 'login']);

    Route::post('/register', [RegisterController::class, 'register']);

    // Registration (PPDB) via Public Link
    Route::get('/daftar/{school}', [App\Http\Controllers\RegistrationController::class, 'showRegistrationForm'])->name('registration.form');
    Route::post('/daftar/{school}', [App\Http\Controllers\RegistrationController::class, 'register'])->name('registration.submit');
    Route::get('/daftar/{school}/success', [App\Http\Controllers\RegistrationController::class, 'success'])->name('registration.success');
});

// ─── Authenticated ──────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (self-service)
    Route::get('/profile',       [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile',       [ProfileController::class, 'update'])->name('profile.update');

    // Report Card / Rapor
    Route::get('/reports',              [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{student}',    [ReportController::class, 'show'])->name('report.show');
    Route::get('/reports/{student}/pdf',[ReportController::class, 'exportPdf'])->name('report.pdf');

    // Announcements
    Route::resource('announcements', AnnouncementController::class);

    // Attendance
    Route::get('/attendances',              [AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('/attendances/create',       [AttendanceController::class, 'create'])->name('attendances.create');
    Route::post('/attendances',             [AttendanceController::class, 'store'])->name('attendances.store');
    Route::get('/attendances/{subject}',    [AttendanceController::class, 'show'])->name('attendances.show');

    // Grades
    Route::resource('grades', GradeController::class);

    // Subjects
    Route::resource('subjects', SubjectController::class);

    // ─── NEW: Academic Years ─────────────────────────────────────
    Route::resource('academic-years', AcademicYearController::class);
    Route::patch('/academic-years/{academic_year}/activate', [AcademicYearController::class, 'activate'])->name('academic-years.activate');

    // ─── NEW: Classrooms ─────────────────────────────────────────
    Route::resource('classrooms', ClassroomController::class);

    // ─── NEW: Enrollments ────────────────────────────────────────
    Route::resource('enrollments', EnrollmentController::class)->only(['index', 'create', 'store', 'destroy']);

    // ─── NEW: Messages ───────────────────────────────────────────
    Route::get('/messages',         [MessageController::class, 'inbox'])->name('messages.inbox');
    Route::get('/messages/sent',    [MessageController::class, 'sent'])->name('messages.sent');
    Route::get('/messages/create',  [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages',        [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{message}',[MessageController::class, 'show'])->name('messages.show');
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');

    // ─── NEW: Events / Calendar ──────────────────────────────────
    Route::resource('events', EventController::class);
    Route::get('/events-calendar-data', [EventController::class, 'calendarData'])->name('events.calendar-data');

    // ─── NEW: Notifications ──────────────────────────────────────
    Route::get('/notifications',              [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read',    [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');

    // ─── NEW: Schools (School Admin Actions) ──────────────────────
    Route::post('/schools/{school}/regenerate-invite', [SchoolController::class, 'regenerateInviteCode'])->name('schools.regenerate-invite');

    // Users — admin only
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');

        // Schools management
        Route::resource('schools', SchoolController::class)->except(['create', 'store']);
        Route::get('/admin/schools/create', [SchoolController::class, 'create'])->name('admin.schools.create');
        Route::post('/admin/schools', [SchoolController::class, 'store'])->name('admin.schools.store');

        // Subscriptions management
        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('/subscriptions/create', [SubscriptionController::class, 'create'])->name('subscriptions.create');
        Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
        Route::get('/subscriptions/{subscription}', [SubscriptionController::class, 'show'])->name('subscriptions.show');
        Route::patch('/subscriptions/{subscription}/status', [SubscriptionController::class, 'updateStatus'])->name('subscriptions.update-status');
    });
});
