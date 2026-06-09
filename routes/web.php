<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceSessionController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamQuestionController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\ParentDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolRegistrationController;
use App\Http\Controllers\StudentAttendanceController;
use App\Http\Controllers\StudentExamController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/pricing', function () {
    return view('pricing');
})->name('pricing');

// School Registration (public)
Route::get('/schools/register', [SchoolRegistrationController::class, 'showForm'])->name('schools.register');
Route::post('/schools/register', [SchoolRegistrationController::class, 'register'])->name('schools.register.submit');
Route::get('/schools/payment/success', [SchoolRegistrationController::class, 'paymentSuccess'])->name('schools.payment.success');

// Student Registration (public)
Route::get('/daftar/{school}', [RegistrationController::class, 'showRegistrationForm'])->name('registration.form');
Route::post('/daftar/{school}', [RegistrationController::class, 'register'])->name('registration.submit');
Route::get('/daftar/{school}/success', [RegistrationController::class, 'success'])->name('registration.success');

// Authenticated routes
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Academic Years (start-new-semester MUST be before resource to avoid route collision)
    Route::post('academic-years/start-new-semester', [AcademicYearController::class, 'startNewSemester'])->name('academic-years.start-new-semester');
    Route::resource('academic-years', AcademicYearController::class);
    Route::patch('academic-years/{academic_year}/activate', [AcademicYearController::class, 'activate'])->name('academic-years.activate');

    // Schools (admin)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/schools/create', [SchoolController::class, 'create'])->name('schools.create');
        Route::post('/schools', [SchoolController::class, 'store'])->name('schools.store');
    });

    // Schools
    Route::resource('schools', SchoolController::class)->except(['create', 'store']);
    Route::post('schools/{school}/regenerate-invite', [SchoolController::class, 'regenerateInvite'])->name('schools.regenerate-invite');

    // Exams
    Route::resource('exams', ExamController::class);
    Route::post('exams/{exam}/questions', [ExamQuestionController::class, 'store'])->name('exams.questions.store');
    Route::delete('exams/{exam}/questions/{question}', [ExamQuestionController::class, 'destroy'])->name('exams.questions.destroy');

    // Classrooms
    Route::resource('classrooms', ClassroomController::class);

    // Subjects
    Route::resource('subjects', SubjectController::class);

    // Enrollments
    Route::resource('enrollments', EnrollmentController::class)->except(['show', 'edit', 'update']);

    // Grades
    Route::resource('grades', GradeController::class);

    // Attendance Sessions
    Route::resource('attendance-sessions', AttendanceSessionController::class)->except(['edit', 'update', 'destroy']);
    Route::post('attendance-sessions/{attendance_session}/close', [AttendanceSessionController::class, 'close'])->name('attendance-sessions.close');
    Route::post('attendance-sessions/{attendance_session}/refresh-qr', [AttendanceSessionController::class, 'refreshQr'])->name('attendance-sessions.refresh-qr');

    // Attendances
    Route::resource('attendances', AttendanceController::class)->only(['index', 'create', 'store']);
    Route::get('attendances/{subject}', [AttendanceController::class, 'show'])->name('attendances.show');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{student}', [ReportController::class, 'show'])->name('report.show');
    Route::get('/reports/{student}/pdf', [ReportController::class, 'exportPdf'])->name('report.pdf');

    // Users
    Route::resource('users', UserController::class);
    Route::post('users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');

    // Announcements
    Route::resource('announcements', AnnouncementController::class);

    // Events
    Route::resource('events', EventController::class);
    Route::get('events-calendar-data', [EventController::class, 'calendarData'])->name('events.calendar-data');

    // Messages
    Route::get('messages', [MessageController::class, 'inbox'])->name('messages.inbox');
    Route::get('messages/sent', [MessageController::class, 'sent'])->name('messages.sent');
    Route::get('messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('messages/{message}', [MessageController::class, 'show'])->name('messages.show');
    Route::delete('messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');

    // Subscriptions
    Route::resource('subscriptions', SubscriptionController::class)->only(['index', 'create', 'store', 'show']);
    Route::patch('subscriptions/{subscription}/status', [SubscriptionController::class, 'updateStatus'])->name('subscriptions.update-status');

    // Parents
    Route::resource('parents', ParentController::class)->only(['index', 'create', 'store', 'destroy']);

    // Parent Dashboard
    Route::get('/parent-dashboard', [ParentDashboardController::class, 'index'])->name('parent.dashboard');
    Route::get('/parent-dashboard/child/{child}', [ParentDashboardController::class, 'showChild'])->name('parent.dashboard.show');

    // Student Attendances
    Route::get('student/attendances', [StudentAttendanceController::class, 'index'])->name('student.attendances.index');
    Route::get('student/attendances/scan', [StudentAttendanceController::class, 'scan'])->name('student.attendance.scan');
    Route::post('student/attendances/scan', [StudentAttendanceController::class, 'processScan'])->name('student.attendance.process');

    // Student Exams
    Route::get('student/exams', [StudentExamController::class, 'index'])->name('student.exams.index');
    Route::post('student/exams/{exam}/start', [StudentExamController::class, 'start'])->name('student.exams.start');
    Route::get('student/exams/{exam}/take/{attempt}', [StudentExamController::class, 'take'])->name('student.exams.take');
    Route::post('student/exams/{exam}/submit/{attempt}', [StudentExamController::class, 'submit'])->name('student.exams.submit');
});

require __DIR__.'/auth.php';
