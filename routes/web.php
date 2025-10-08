<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

// Include health check routes
require_once __DIR__ . '/health.php';

/*
|--------------------------------------------------------------------------
| Web Routes - CLEANED VERSION
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login.show');
    
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard redirect
Route::get('/dashboard', function () {
    $user = auth()->user();
    if (!$user) {
        return redirect()->route('login');
    }
    
    switch ($user->user_type) {
        case 'admin':
            return redirect()->route('admin.dashboard');
        case 'teacher':
            return redirect()->route('teacher.dashboard');
        case 'student':
            return redirect()->route('student.dashboard');
        case 'finance':
            return redirect()->route('finance.dashboard');
        default:
            return redirect()->route('admin.dashboard');
    }
})->name('dashboard')->middleware('auth');

// =============================================================================
// ADMIN ROUTES
// =============================================================================
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Student Management
    Route::prefix('admin/students')->name('admin.students.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\StudentController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\StudentController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\StudentController::class, 'store'])->name('store');
        Route::get('/{student}', [\App\Http\Controllers\Admin\StudentController::class, 'show'])->name('show');
        Route::get('/{student}/edit', [\App\Http\Controllers\Admin\StudentController::class, 'edit'])->name('edit');
        Route::put('/{student}', [\App\Http\Controllers\Admin\StudentController::class, 'update'])->name('update');
        Route::delete('/{student}', [\App\Http\Controllers\Admin\StudentController::class, 'destroy'])->name('destroy');
    });

    // Teacher Management
    Route::prefix('admin/teachers')->name('admin.teachers.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\TeacherController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\TeacherController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\TeacherController::class, 'store'])->name('store');
        Route::get('/{teacher}', [\App\Http\Controllers\Admin\TeacherController::class, 'show'])->name('show');
        Route::get('/{teacher}/edit', [\App\Http\Controllers\Admin\TeacherController::class, 'edit'])->name('edit');
        Route::put('/{teacher}', [\App\Http\Controllers\Admin\TeacherController::class, 'update'])->name('update');
        Route::delete('/{teacher}', [\App\Http\Controllers\Admin\TeacherController::class, 'destroy'])->name('destroy');
    });

    // Class Management
    Route::prefix('admin/classes')->name('admin.classes.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ClassController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\ClassController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\ClassController::class, 'store'])->name('store');
        Route::get('/{class}', [\App\Http\Controllers\Admin\ClassController::class, 'show'])->name('show');
        Route::get('/{class}/edit', [\App\Http\Controllers\Admin\ClassController::class, 'edit'])->name('edit');
        Route::put('/{class}', [\App\Http\Controllers\Admin\ClassController::class, 'update'])->name('update');
        Route::delete('/{class}', [\App\Http\Controllers\Admin\ClassController::class, 'destroy'])->name('destroy');
    });

    // Subject Management
    Route::prefix('admin/subjects')->name('admin.subjects.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SubjectController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\SubjectController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\SubjectController::class, 'store'])->name('store');
        Route::get('/{subject}', [\App\Http\Controllers\Admin\SubjectController::class, 'show'])->name('show');
        Route::get('/{subject}/edit', [\App\Http\Controllers\Admin\SubjectController::class, 'edit'])->name('edit');
        Route::put('/{subject}', [\App\Http\Controllers\Admin\SubjectController::class, 'update'])->name('update');
        Route::delete('/{subject}', [\App\Http\Controllers\Admin\SubjectController::class, 'destroy'])->name('destroy');
    });

    // Grade Approval Management
    Route::prefix('admin/grades')->name('admin.grades.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\GradeApprovalController::class, 'index'])->name('index');
        Route::get('/analytics', [\App\Http\Controllers\Admin\GradeApprovalController::class, 'analytics'])->name('analytics');
        Route::get('/analytics/data', [\App\Http\Controllers\Admin\GradeApprovalController::class, 'getAnalyticsData'])->name('analytics.data');
        Route::get('/all', [\App\Http\Controllers\Admin\GradeApprovalController::class, 'allGrades'])->name('all');
        Route::get('/{grade}', [\App\Http\Controllers\Admin\GradeApprovalController::class, 'show'])->name('show');
        Route::post('/{grade}/approve', [\App\Http\Controllers\Admin\GradeApprovalController::class, 'approve'])->name('approve');
        Route::post('/{grade}/reject', [\App\Http\Controllers\Admin\GradeApprovalController::class, 'reject'])->name('reject');
        Route::post('/bulk-approve', [\App\Http\Controllers\Admin\GradeApprovalController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [\App\Http\Controllers\Admin\GradeApprovalController::class, 'bulkReject'])->name('bulk-reject');
    });

    // Staff Management Routes
    Route::prefix('admin/staff')->name('admin.staff.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\StaffManagementController::class, 'index'])->name('index');
        Route::get('/performance', [\App\Http\Controllers\Admin\StaffManagementController::class, 'performance'])->name('performance');
        Route::get('/schedules', [\App\Http\Controllers\Admin\StaffManagementController::class, 'schedules'])->name('schedules');
        Route::get('/payroll', [\App\Http\Controllers\Admin\StaffManagementController::class, 'payroll'])->name('payroll');
    });

    // Student Routes
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\StudentController::class, 'dashboard'])->name('dashboard');
        Route::get('/grades', [\App\Http\Controllers\StudentController::class, 'grades'])->name('grades.grade-sheet');
        Route::get('/finance', [\App\Http\Controllers\StudentController::class, 'finance'])->name('finance.index');
        Route::get('/exams', [\App\Http\Controllers\StudentController::class, 'exams'])->name('exams.upcoming');
        Route::get('/library', [\App\Http\Controllers\StudentController::class, 'library'])->name('library.index');
        Route::get('/transport', [\App\Http\Controllers\StudentController::class, 'transport'])->name('transport.index');
        Route::get('/hostel', [\App\Http\Controllers\StudentController::class, 'hostel'])->name('hostel.index');
        Route::get('/attendance', [\App\Http\Controllers\StudentController::class, 'attendance'])->name('attendance.index');
        Route::get('/settings', [\App\Http\Controllers\StudentController::class, 'settings'])->name('student.settings');
    });

    // Teacher Routes
    Route::prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\TeacherController::class, 'dashboard'])->name('dashboard');
        Route::get('/grades', [\App\Http\Controllers\TeacherController::class, 'grades'])->name('grades.index');
        Route::get('/grades/create', [\App\Http\Controllers\TeacherController::class, 'createGrade'])->name('grades.create');
        Route::get('/grades/bulk-create', [\App\Http\Controllers\TeacherController::class, 'bulkCreateGrade'])->name('grades.bulk-create');
    });

    // Finance Routes
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\FinanceController::class, 'dashboard'])->name('dashboard');
        Route::get('/payments', [\App\Http\Controllers\FinanceController::class, 'payments'])->name('payments.index');
    });

    // Parent Routes
    Route::prefix('parent')->name('parent.')->group(function () {
        Route::get('/grades', [\App\Http\Controllers\ParentController::class, 'grades'])->name('grades.index');
        Route::get('/grades/progress', [\App\Http\Controllers\ParentController::class, 'progress'])->name('grades.progress');
        Route::get('/grades/download', [\App\Http\Controllers\ParentController::class, 'download'])->name('grades.download');
    });

    // General Routes
    Route::get('/library', [\App\Http\Controllers\LibraryController::class, 'index'])->name('library.index');
    Route::get('/transport', [\App\Http\Controllers\TransportController::class, 'index'])->name('transport.index');
    Route::get('/hostel', [\App\Http\Controllers\HostelController::class, 'index'])->name('hostel.index');
    Route::get('/attendance', [\App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/exams/upcoming', [\App\Http\Controllers\ExamController::class, 'upcoming'])->name('exams.upcoming');
    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');

    // Schedule Management
    Route::prefix('schedules')->name('schedules.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ScheduleController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\ScheduleController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\ScheduleController::class, 'store'])->name('store');
        Route::get('/calendar', [\App\Http\Controllers\ScheduleController::class, 'calendar'])->name('calendar');
        Route::get('/{schedule}', [\App\Http\Controllers\ScheduleController::class, 'show'])->name('show');
        Route::get('/{schedule}/edit', [\App\Http\Controllers\ScheduleController::class, 'edit'])->name('edit');
        Route::put('/{schedule}', [\App\Http\Controllers\ScheduleController::class, 'update'])->name('update');
        Route::delete('/{schedule}', [\App\Http\Controllers\ScheduleController::class, 'destroy'])->name('destroy');
        Route::post('/{schedule}/submit', [\App\Http\Controllers\ScheduleController::class, 'submit'])->name('submit');
        Route::post('/{schedule}/cancel', [\App\Http\Controllers\ScheduleController::class, 'cancel'])->name('cancel');
    });

    // Exam Types Management
    Route::prefix('admin/exams/types')->name('admin.exams.types.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ExamTypeController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\ExamTypeController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\ExamTypeController::class, 'store'])->name('store');
        Route::get('/{examType}', [\App\Http\Controllers\Admin\ExamTypeController::class, 'show'])->name('show');
        Route::get('/{examType}/edit', [\App\Http\Controllers\Admin\ExamTypeController::class, 'edit'])->name('edit');
        Route::put('/{examType}', [\App\Http\Controllers\Admin\ExamTypeController::class, 'update'])->name('update');
        Route::delete('/{examType}', [\App\Http\Controllers\Admin\ExamTypeController::class, 'destroy'])->name('destroy');
    });


    // Fee Structure Management
    Route::prefix('admin/fee-structures')->name('admin.fee-structures.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ClassFeeStructureController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\ClassFeeStructureController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\ClassFeeStructureController::class, 'store'])->name('store');
        Route::get('/{feeStructure}', [\App\Http\Controllers\Admin\ClassFeeStructureController::class, 'show'])->name('show');
        Route::get('/{feeStructure}/edit', [\App\Http\Controllers\Admin\ClassFeeStructureController::class, 'edit'])->name('edit');
        Route::put('/{feeStructure}', [\App\Http\Controllers\Admin\ClassFeeStructureController::class, 'update'])->name('update');
        Route::delete('/{feeStructure}', [\App\Http\Controllers\Admin\ClassFeeStructureController::class, 'destroy'])->name('destroy');
    });

    // Finance Officers Management
    Route::prefix('admin/finance-officers')->name('admin.finance_officers.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\FinanceOfficerController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\FinanceOfficerController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\FinanceOfficerController::class, 'store'])->name('store');
        Route::get('/{officer}', [\App\Http\Controllers\Admin\FinanceOfficerController::class, 'show'])->name('show');
        Route::get('/{officer}/edit', [\App\Http\Controllers\Admin\FinanceOfficerController::class, 'edit'])->name('edit');
        Route::put('/{officer}', [\App\Http\Controllers\Admin\FinanceOfficerController::class, 'update'])->name('update');
        Route::delete('/{officer}', [\App\Http\Controllers\Admin\FinanceOfficerController::class, 'destroy'])->name('destroy');
    });

    // User Management
    Route::prefix('admin/users')->name('admin.users.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('store');
        Route::get('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/reset-password', [\App\Http\Controllers\Admin\UserController::class, 'resetPassword'])->name('reset-password');
    });

    // Staff Management
    Route::prefix('admin/staff')->name('admin.staff.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\StaffManagementController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\StaffManagementController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\StaffManagementController::class, 'store'])->name('store');
        Route::get('/performance', [\App\Http\Controllers\Admin\StaffManagementController::class, 'performance'])->name('performance');
        Route::get('/create-performance', [\App\Http\Controllers\Admin\StaffManagementController::class, 'createPerformance'])->name('create-performance');
        Route::post('/store-performance', [\App\Http\Controllers\Admin\StaffManagementController::class, 'storePerformance'])->name('store-performance');
        Route::get('/performance/{performance}', [\App\Http\Controllers\Admin\StaffManagementController::class, 'showPerformance'])->name('performance.show');
        Route::get('/performance/{performance}/edit', [\App\Http\Controllers\Admin\StaffManagementController::class, 'editPerformance'])->name('performance.edit');
        Route::put('/performance/{performance}', [\App\Http\Controllers\Admin\StaffManagementController::class, 'updatePerformance'])->name('performance.update');
        Route::delete('/performance/{performance}', [\App\Http\Controllers\Admin\StaffManagementController::class, 'destroyPerformance'])->name('performance.destroy');
        Route::get('/payroll', [\App\Http\Controllers\Admin\StaffManagementController::class, 'payroll'])->name('payroll');
        Route::get('/create-payroll', [\App\Http\Controllers\Admin\StaffManagementController::class, 'createPayroll'])->name('create-payroll');
        Route::post('/store-payroll', [\App\Http\Controllers\Admin\StaffManagementController::class, 'storePayroll'])->name('store-payroll');
        Route::get('/payroll/{payroll}', [\App\Http\Controllers\Admin\StaffManagementController::class, 'showPayroll'])->name('payroll.show');
        Route::get('/payroll/{payroll}/edit', [\App\Http\Controllers\Admin\StaffManagementController::class, 'editPayroll'])->name('payroll.edit');
        Route::put('/payroll/{payroll}', [\App\Http\Controllers\Admin\StaffManagementController::class, 'updatePayroll'])->name('payroll.update');
        Route::delete('/payroll/{payroll}', [\App\Http\Controllers\Admin\StaffManagementController::class, 'destroyPayroll'])->name('payroll.destroy');
        Route::get('/payroll/{payroll}/print', [\App\Http\Controllers\Admin\StaffManagementController::class, 'printPayroll'])->name('payroll.print');
        Route::get('/schedules', [\App\Http\Controllers\Admin\StaffManagementController::class, 'schedules'])->name('schedules');
        Route::get('/schedules/{schedule}', [\App\Http\Controllers\Admin\StaffManagementController::class, 'showSchedule'])->name('schedules.show');
        Route::get('/create-schedule', [\App\Http\Controllers\Admin\StaffManagementController::class, 'createSchedule'])->name('create-schedule');
        Route::post('/store-schedule', [\App\Http\Controllers\Admin\StaffManagementController::class, 'storeSchedule'])->name('store-schedule');
        Route::get('/schedules/{schedule}/edit', [\App\Http\Controllers\Admin\StaffManagementController::class, 'editSchedule'])->name('schedules.edit');
        Route::put('/schedules/{schedule}', [\App\Http\Controllers\Admin\StaffManagementController::class, 'updateSchedule'])->name('schedules.update');
        Route::delete('/schedules/{schedule}', [\App\Http\Controllers\Admin\StaffManagementController::class, 'destroySchedule'])->name('schedules.destroy');
        Route::get('/reports', [\App\Http\Controllers\Admin\StaffManagementController::class, 'reports'])->name('reports');
        Route::get('/{staff}', [\App\Http\Controllers\Admin\StaffManagementController::class, 'show'])->name('show');
        Route::get('/{staff}/edit', [\App\Http\Controllers\Admin\StaffManagementController::class, 'edit'])->name('edit');
        Route::put('/{staff}', [\App\Http\Controllers\Admin\StaffManagementController::class, 'update'])->name('update');
        Route::delete('/{staff}', [\App\Http\Controllers\Admin\StaffManagementController::class, 'destroy'])->name('destroy');
    });

    // Notifications Management
    Route::prefix('admin/notifications')->name('admin.notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\NotificationController::class, 'store'])->name('store');
        Route::get('/templates', [\App\Http\Controllers\Admin\NotificationController::class, 'templates'])->name('templates');
        Route::get('/templates/create', [\App\Http\Controllers\Admin\NotificationController::class, 'createTemplate'])->name('templates.create');
        Route::post('/templates', [\App\Http\Controllers\Admin\NotificationController::class, 'storeTemplate'])->name('templates.store');
        Route::get('/reports', [\App\Http\Controllers\Admin\NotificationController::class, 'reports'])->name('reports');
        Route::post('/bulk-action', [\App\Http\Controllers\Admin\NotificationController::class, 'bulkAction'])->name('bulk-action');
        Route::get('/{notification}', [\App\Http\Controllers\Admin\NotificationController::class, 'show'])->name('show');
        Route::delete('/{notification}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('destroy');
    });

    // Reports Management
    Route::prefix('admin/reports')->name('admin.reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
        Route::get('/academic', [\App\Http\Controllers\Admin\ReportController::class, 'academic'])->name('academic');
        Route::get('/financial', [\App\Http\Controllers\Admin\ReportController::class, 'financial'])->name('financial');
        Route::get('/attendance', [\App\Http\Controllers\Admin\ReportController::class, 'attendance'])->name('attendance');
        Route::get('/staff', [\App\Http\Controllers\Admin\ReportController::class, 'staff'])->name('staff');
        Route::get('/library', [\App\Http\Controllers\Admin\ReportController::class, 'library'])->name('library');
        Route::post('/export', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('export');
    });

    // Lesson Plan Approval Management
    Route::prefix('admin/lesson-plans')->name('admin.lesson-plans.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\LessonPlanDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/', [\App\Http\Controllers\Admin\LessonPlanApprovalController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\LessonPlanApprovalController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\LessonPlanApprovalController::class, 'store'])->name('store');
        Route::get('/pending', [\App\Http\Controllers\Admin\LessonPlanApprovalController::class, 'pending'])->name('pending');
        Route::get('/approved', [\App\Http\Controllers\Admin\LessonPlanApprovalController::class, 'approved'])->name('approved');
        Route::get('/rejected', [\App\Http\Controllers\Admin\LessonPlanApprovalController::class, 'rejected'])->name('rejected');
        Route::get('/{lessonPlan}', [\App\Http\Controllers\Admin\LessonPlanApprovalController::class, 'show'])->name('show');
        Route::get('/{lessonPlan}/edit', [\App\Http\Controllers\Admin\LessonPlanApprovalController::class, 'edit'])->name('edit');
        Route::put('/{lessonPlan}', [\App\Http\Controllers\Admin\LessonPlanApprovalController::class, 'update'])->name('update');
        Route::delete('/{lessonPlan}', [\App\Http\Controllers\Admin\LessonPlanApprovalController::class, 'destroy'])->name('destroy');
        Route::post('/{lessonPlan}/approve', [\App\Http\Controllers\Admin\LessonPlanApprovalController::class, 'approve'])->name('approve');
        Route::post('/{lessonPlan}/reject', [\App\Http\Controllers\Admin\LessonPlanApprovalController::class, 'reject'])->name('reject');
        Route::get('/{lessonPlan}/download', [\App\Http\Controllers\Admin\LessonPlanApprovalController::class, 'download'])->name('download');
    });
    
    // Admin Financial Analytics (using Finance controllers for real-time data)
    Route::prefix('admin/finance')->name('admin.finance.')->group(function () {
        Route::get('/analytics', [\App\Http\Controllers\Finance\FinancialReportController::class, 'index'])->name('analytics');
        Route::get('/payments', [\App\Http\Controllers\Finance\PaymentController::class, 'index'])->name('payments');
        Route::get('/reports', [\App\Http\Controllers\Finance\FeeReportController::class, 'index'])->name('reports');
        Route::get('/payments/analytics', [\App\Http\Controllers\Finance\PaymentController::class, 'analytics'])->name('payments.analytics');
        Route::get('/income', [\App\Http\Controllers\Finance\FinancialReportController::class, 'income'])->name('income');
        Route::get('/expenses', [\App\Http\Controllers\Finance\FinancialReportController::class, 'expenses'])->name('expenses');
    });

    // Fee Reports (alias for financial reports)
    Route::get('/admin/fees/reports', [\App\Http\Controllers\Admin\ReportController::class, 'financial'])->name('admin.fees.reports');

    // Transport Management
    Route::prefix('admin/transport')->name('admin.transport.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\TransportController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\TransportController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\TransportController::class, 'store'])->name('store');
        Route::get('/{transport}', [\App\Http\Controllers\Admin\TransportController::class, 'show'])->name('show');
        Route::get('/{transport}/edit', [\App\Http\Controllers\Admin\TransportController::class, 'edit'])->name('edit');
        Route::put('/{transport}', [\App\Http\Controllers\Admin\TransportController::class, 'update'])->name('update');
        Route::delete('/{transport}', [\App\Http\Controllers\Admin\TransportController::class, 'destroy'])->name('destroy');
    });

    // Hostel Management
    Route::prefix('admin/hostel')->name('admin.hostel.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\HostelController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\HostelController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\HostelController::class, 'store'])->name('store');
        Route::get('/{hostel}', [\App\Http\Controllers\Admin\HostelController::class, 'show'])->name('show');
        Route::get('/{hostel}/edit', [\App\Http\Controllers\Admin\HostelController::class, 'edit'])->name('edit');
        Route::put('/{hostel}', [\App\Http\Controllers\Admin\HostelController::class, 'update'])->name('update');
        Route::delete('/{hostel}', [\App\Http\Controllers\Admin\HostelController::class, 'destroy'])->name('destroy');
    });

    // Library Management (Admin/Teacher access)
    Route::prefix('library')->name('library.')->group(function () {
        Route::get('/', [\App\Http\Controllers\LibraryController::class, 'index'])->name('index');
        
        // Book Management
        Route::prefix('books')->name('books.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Library\BookController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Library\BookController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Library\BookController::class, 'store'])->name('store');
            Route::get('/{book}', [\App\Http\Controllers\Library\BookController::class, 'show'])->name('show');
            Route::get('/{book}/edit', [\App\Http\Controllers\Library\BookController::class, 'edit'])->name('edit');
            Route::put('/{book}', [\App\Http\Controllers\Library\BookController::class, 'update'])->name('update');
            Route::delete('/{book}', [\App\Http\Controllers\Library\BookController::class, 'destroy'])->name('destroy');
            Route::get('/{book}/download', [\App\Http\Controllers\Library\BookController::class, 'download'])->name('download');
        });
        
        // Member Management
        Route::prefix('members')->name('members.')->group(function () {
            Route::get('/', [\App\Http\Controllers\LibraryController::class, 'members'])->name('index');
            Route::get('/create', [\App\Http\Controllers\LibraryController::class, 'createMember'])->name('create');
            Route::post('/', [\App\Http\Controllers\LibraryController::class, 'storeMember'])->name('store');
            Route::get('/{member}', [\App\Http\Controllers\LibraryController::class, 'showMember'])->name('show');
            Route::patch('/{member}/suspend', [\App\Http\Controllers\LibraryController::class, 'suspendMember'])->name('suspend');
            Route::patch('/{member}/unsuspend', [\App\Http\Controllers\LibraryController::class, 'unsuspendMember'])->name('unsuspend');
        });
        
        // Book Issues Management
        Route::prefix('issues')->name('issues.')->group(function () {
            Route::get('/', [\App\Http\Controllers\LibraryController::class, 'issues'])->name('index');
            Route::get('/create', [\App\Http\Controllers\LibraryController::class, 'createIssue'])->name('create');
            Route::post('/', [\App\Http\Controllers\LibraryController::class, 'storeIssue'])->name('store');
            Route::get('/{issue}', [\App\Http\Controllers\LibraryController::class, 'showIssue'])->name('show');
            Route::patch('/{issue}/return', [\App\Http\Controllers\LibraryController::class, 'returnIssue'])->name('return');
            Route::patch('/{issue}/pay-fine', [\App\Http\Controllers\LibraryController::class, 'payFine'])->name('pay-fine');
        });
        
        // Legacy routes for backward compatibility
        Route::get('/books', [\App\Http\Controllers\LibraryController::class, 'books'])->name('books');
        Route::get('/members', [\App\Http\Controllers\LibraryController::class, 'members'])->name('members');
        Route::get('/issued', [\App\Http\Controllers\LibraryController::class, 'issued'])->name('issued');
    });

    // Transport Management (Admin/Teacher access)
    Route::prefix('transport')->name('transport.')->group(function () {
        Route::get('/', [\App\Http\Controllers\TransportController::class, 'index'])->name('index');
        
        // Vehicle Management
        Route::prefix('vehicles')->name('vehicles.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Transport\VehicleController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Transport\VehicleController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Transport\VehicleController::class, 'store'])->name('store');
            Route::get('/{vehicle}', [\App\Http\Controllers\Transport\VehicleController::class, 'show'])->name('show');
            Route::get('/{vehicle}/edit', [\App\Http\Controllers\Transport\VehicleController::class, 'edit'])->name('edit');
            Route::put('/{vehicle}', [\App\Http\Controllers\Transport\VehicleController::class, 'update'])->name('update');
            Route::delete('/{vehicle}', [\App\Http\Controllers\Transport\VehicleController::class, 'destroy'])->name('destroy');
        });
        
        // Route Management
        Route::prefix('routes')->name('routes.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Transport\RouteController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Transport\RouteController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Transport\RouteController::class, 'store'])->name('store');
            Route::get('/{route}', [\App\Http\Controllers\Transport\RouteController::class, 'show'])->name('show');
            Route::get('/{route}/edit', [\App\Http\Controllers\Transport\RouteController::class, 'edit'])->name('edit');
            Route::put('/{route}', [\App\Http\Controllers\Transport\RouteController::class, 'update'])->name('update');
            Route::delete('/{route}', [\App\Http\Controllers\Transport\RouteController::class, 'destroy'])->name('destroy');
        });
        
        // Student Assignment Management
        Route::prefix('students')->name('students.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Transport\StudentController::class, 'index'])->name('index');
            Route::get('/assign', [\App\Http\Controllers\Transport\StudentController::class, 'assign'])->name('assign');
            Route::post('/assign', [\App\Http\Controllers\Transport\StudentController::class, 'storeAssignment'])->name('assign.store');
            Route::get('/{student}', [\App\Http\Controllers\Transport\StudentController::class, 'show'])->name('show');
            Route::get('/{student}/edit', [\App\Http\Controllers\Transport\StudentController::class, 'edit'])->name('edit');
            Route::put('/{student}', [\App\Http\Controllers\Transport\StudentController::class, 'update'])->name('update');
            Route::delete('/{student}/remove', [\App\Http\Controllers\Transport\StudentController::class, 'removeAssignment'])->name('remove');
        });
        
        // Legacy routes for backward compatibility
        Route::get('/routes', [\App\Http\Controllers\TransportController::class, 'routes'])->name('routes');
        Route::get('/vehicles', [\App\Http\Controllers\TransportController::class, 'vehicles'])->name('vehicles');
        Route::get('/schedule', [\App\Http\Controllers\TransportController::class, 'schedule'])->name('schedule');
    });

    // Hostel Management (Admin/Teacher access)
    Route::prefix('hostel')->name('hostel.')->group(function () {
        Route::get('/', [\App\Http\Controllers\HostelController::class, 'index'])->name('index');
        
        // Room Management
        Route::prefix('rooms')->name('rooms.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Hostel\RoomController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Hostel\RoomController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Hostel\RoomController::class, 'store'])->name('store');
            Route::get('/{room}', [\App\Http\Controllers\Hostel\RoomController::class, 'show'])->name('show');
            Route::get('/{room}/edit', [\App\Http\Controllers\Hostel\RoomController::class, 'edit'])->name('edit');
            Route::put('/{room}', [\App\Http\Controllers\Hostel\RoomController::class, 'update'])->name('update');
            Route::delete('/{room}', [\App\Http\Controllers\Hostel\RoomController::class, 'destroy'])->name('destroy');
        });
        
        // Student Assignment Management
        Route::prefix('students')->name('students.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Hostel\StudentController::class, 'index'])->name('index');
            Route::get('/assign', [\App\Http\Controllers\Hostel\StudentController::class, 'assign'])->name('assign');
            Route::post('/assign', [\App\Http\Controllers\Hostel\StudentController::class, 'storeAssignment'])->name('assign.store');
            Route::get('/{student}', [\App\Http\Controllers\Hostel\StudentController::class, 'show'])->name('show');
            Route::get('/{student}/edit', [\App\Http\Controllers\Hostel\StudentController::class, 'edit'])->name('edit');
            Route::put('/{student}', [\App\Http\Controllers\Hostel\StudentController::class, 'update'])->name('update');
            Route::delete('/{student}/remove', [\App\Http\Controllers\Hostel\StudentController::class, 'removeAssignment'])->name('remove');
        });
        
        // Legacy routes for backward compatibility
        Route::get('/create-hostel', [\App\Http\Controllers\HostelController::class, 'createHostel'])->name('create-hostel');
        Route::post('/create-hostel', [\App\Http\Controllers\HostelController::class, 'storeHostel'])->name('store-hostel');
        Route::get('/rooms', [\App\Http\Controllers\HostelController::class, 'rooms'])->name('rooms');
        Route::get('/students', [\App\Http\Controllers\HostelController::class, 'students'])->name('students');
        Route::get('/facilities', [\App\Http\Controllers\HostelController::class, 'facilities'])->name('facilities');
    });


    // User Management (Admin/Teacher access)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [\App\Http\Controllers\UserController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\UserController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\UserController::class, 'store'])->name('store');
        Route::post('/reset-all-passwords', [\App\Http\Controllers\UserController::class, 'resetAllPasswords'])->name('reset-all-passwords');
        Route::get('/{user}', [\App\Http\Controllers\UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [\App\Http\Controllers\UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/reset-password', [\App\Http\Controllers\UserController::class, 'resetPassword'])->name('reset-password');
    });

    // Profile routes
    Route::get('/me/profile', [\App\Http\Controllers\UserController::class, 'myProfile'])->name('me.profile');

    // System Settings (Admin/Teacher access)
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SettingController::class, 'index'])->name('index');
        Route::get('/general', [\App\Http\Controllers\SettingController::class, 'general'])->name('general');
        Route::post('/general', [\App\Http\Controllers\SettingController::class, 'updateGeneral'])->name('general.update');
        Route::get('/academic', [\App\Http\Controllers\SettingController::class, 'academic'])->name('academic');
        Route::post('/academic', [\App\Http\Controllers\SettingController::class, 'updateAcademic'])->name('academic.update');
        Route::get('/attendance', [\App\Http\Controllers\SettingController::class, 'attendance'])->name('attendance');
        Route::post('/attendance', [\App\Http\Controllers\SettingController::class, 'updateAttendance'])->name('attendance.update');
        Route::get('/notifications', [\App\Http\Controllers\SettingController::class, 'notifications'])->name('notifications');
        Route::post('/notifications', [\App\Http\Controllers\SettingController::class, 'updateNotifications'])->name('notifications.update');
    });

    // Signature Settings
    Route::prefix('admin/settings')->name('admin.settings.')->group(function () {
        Route::get('/signature', [\App\Http\Controllers\Admin\SettingsController::class, 'signature'])->name('signature');
        Route::post('/signature', [\App\Http\Controllers\Admin\SettingsController::class, 'updateSignature'])->name('signature.update');
        Route::delete('/signature', [\App\Http\Controllers\Admin\SettingsController::class, 'removeSignature'])->name('signature.remove');
    });
});

// =============================================================================
// TEACHER ROUTES
// =============================================================================
Route::middleware(['auth', 'teacher'])->group(function () {
    Route::get('/teacher/dashboard', [TeacherController::class, 'dashboard'])->name('teacher.dashboard');
    
    // Teacher Class Management
    Route::prefix('teacher/classes')->name('teacher.classes.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Teacher\ClassController::class, 'index'])->name('index');
        Route::get('/{class}', [\App\Http\Controllers\Teacher\ClassController::class, 'show'])->name('show');
    });
    
    // Teacher Student Management
    Route::prefix('teacher/students')->name('teacher.students.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Teacher\StudentController::class, 'index'])->name('index');
        Route::get('/{student}', [\App\Http\Controllers\Teacher\StudentController::class, 'show'])->name('show');
    });
    
    // Teacher Subject Management
    Route::prefix('teacher/subjects')->name('teacher.subjects.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Teacher\SubjectController::class, 'index'])->name('index');
        Route::get('/{subject}', [\App\Http\Controllers\Teacher\SubjectController::class, 'show'])->name('show');
    });
    
    // Alias routes for teacher navigation
    Route::get('/teacher/students', [\App\Http\Controllers\Teacher\StudentController::class, 'index'])->name('teacher.students');
    Route::get('/teacher/subjects', [\App\Http\Controllers\Teacher\SubjectController::class, 'index'])->name('teacher.subjects');
    Route::get('/teacher/classes', [\App\Http\Controllers\Teacher\ClassController::class, 'index'])->name('teacher.classes');
    
    // Grade Management
    Route::prefix('teacher/grades')->name('teacher.grades.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Teacher\GradeController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Teacher\GradeController::class, 'create'])->name('create');
        Route::get('/bulk-create', [\App\Http\Controllers\Teacher\GradeController::class, 'bulkCreate'])->name('bulk-create');
        Route::get('/subjects', [\App\Http\Controllers\Teacher\GradeController::class, 'getSubjects'])->name('subjects');
        Route::get('/students', [\App\Http\Controllers\Teacher\GradeController::class, 'getStudents'])->name('students');
        Route::post('/', [\App\Http\Controllers\Teacher\GradeController::class, 'store'])->name('store');
        Route::get('/{grade}', [\App\Http\Controllers\Teacher\GradeController::class, 'show'])->name('show');
        Route::get('/{grade}/edit', [\App\Http\Controllers\Teacher\GradeController::class, 'edit'])->name('edit');
        Route::put('/{grade}', [\App\Http\Controllers\Teacher\GradeController::class, 'update'])->name('update');
        Route::delete('/{grade}', [\App\Http\Controllers\Teacher\GradeController::class, 'destroy'])->name('destroy');
    });
    
    // Exam Management
    Route::prefix('teacher/exams')->name('teacher.exams.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Teacher\ExamController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Teacher\ExamController::class, 'create'])->name('create');
        Route::get('/upcoming', [\App\Http\Controllers\Teacher\ExamController::class, 'upcoming'])->name('upcoming');
        Route::post('/', [\App\Http\Controllers\Teacher\ExamController::class, 'store'])->name('store');
        Route::get('/{exam}', [\App\Http\Controllers\Teacher\ExamController::class, 'show'])->name('show');
        Route::get('/{exam}/edit', [\App\Http\Controllers\Teacher\ExamController::class, 'edit'])->name('edit');
        Route::put('/{exam}', [\App\Http\Controllers\Teacher\ExamController::class, 'update'])->name('update');
        Route::post('/{exam}/publish', [\App\Http\Controllers\Teacher\ExamController::class, 'publish'])->name('publish');
        Route::post('/{exam}/unpublish', [\App\Http\Controllers\Teacher\ExamController::class, 'unpublish'])->name('unpublish');
        Route::delete('/{exam}', [\App\Http\Controllers\Teacher\ExamController::class, 'destroy'])->name('destroy');
    });
    
    // Teacher Profile Management
    Route::prefix('teacher/profile')->name('teacher.profile.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Teacher\ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [\App\Http\Controllers\Teacher\ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [\App\Http\Controllers\Teacher\ProfileController::class, 'update'])->name('update');
    });
    
    // Teacher Profile alias route
    Route::get('/teacher/profile', [\App\Http\Controllers\Teacher\ProfileController::class, 'show'])->name('teacher.profile');
    
    
    // Homework Management
    Route::prefix('teacher/homework')->name('teacher.homework.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Teacher\HomeworkController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Teacher\HomeworkController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Teacher\HomeworkController::class, 'store'])->name('store');
        Route::get('/{assignment}', [\App\Http\Controllers\Teacher\HomeworkController::class, 'show'])->name('show');
        Route::get('/{assignment}/edit', [\App\Http\Controllers\Teacher\HomeworkController::class, 'edit'])->name('edit');
        Route::put('/{assignment}', [\App\Http\Controllers\Teacher\HomeworkController::class, 'update'])->name('update');
        Route::post('/{assignment}/publish', [\App\Http\Controllers\Teacher\HomeworkController::class, 'publish'])->name('publish');
        Route::delete('/{assignment}', [\App\Http\Controllers\Teacher\HomeworkController::class, 'destroy'])->name('destroy');
        Route::post('/submissions/{submission}/grade', [\App\Http\Controllers\Teacher\HomeworkController::class, 'gradeSubmission'])->name('grade-submission');
    });
    
    // Study Materials Management
    Route::prefix('teacher/study-materials')->name('teacher.study-materials.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Teacher\StudyMaterialController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Teacher\StudyMaterialController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Teacher\StudyMaterialController::class, 'store'])->name('store');
        Route::get('/{material}', [\App\Http\Controllers\Teacher\StudyMaterialController::class, 'show'])->name('show');
        Route::get('/{material}/edit', [\App\Http\Controllers\Teacher\StudyMaterialController::class, 'edit'])->name('edit');
        Route::put('/{material}', [\App\Http\Controllers\Teacher\StudyMaterialController::class, 'update'])->name('update');
        Route::delete('/{material}', [\App\Http\Controllers\Teacher\StudyMaterialController::class, 'destroy'])->name('destroy');
    });
    
    // Teacher Settings
    Route::prefix('teacher/settings')->name('teacher.settings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Teacher\SettingsController::class, 'index'])->name('index');
        Route::post('/password', [\App\Http\Controllers\Teacher\SettingsController::class, 'updatePassword'])->name('password');
        Route::post('/notifications', [\App\Http\Controllers\Teacher\SettingsController::class, 'updateNotifications'])->name('notifications');
        Route::post('/privacy', [\App\Http\Controllers\Teacher\SettingsController::class, 'updatePrivacy'])->name('privacy');
        Route::post('/delete-account', [\App\Http\Controllers\Teacher\SettingsController::class, 'deleteAccount'])->name('delete-account');
    });
    
    // Teacher Attendance
    Route::prefix('teacher/attendance')->name('teacher.attendance.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Teacher\AttendanceController::class, 'index'])->name('index');
        Route::get('/take/{classId}', [\App\Http\Controllers\Teacher\AttendanceController::class, 'takeAttendance'])->name('take');
        Route::post('/store', [\App\Http\Controllers\Teacher\AttendanceController::class, 'storeAttendance'])->name('store');
        Route::get('/history/{classId}', [\App\Http\Controllers\Teacher\AttendanceController::class, 'viewHistory'])->name('history');
        Route::get('/student/{studentId}', [\App\Http\Controllers\Teacher\AttendanceController::class, 'studentSummary'])->name('student-summary');
    });

    // Teacher Lesson Plans
    Route::prefix('teacher/lesson-plans')->name('teacher.lesson-plans.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Teacher\LessonPlanController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Teacher\LessonPlanController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Teacher\LessonPlanController::class, 'store'])->name('store');
        Route::get('/{lessonPlan}', [\App\Http\Controllers\Teacher\LessonPlanController::class, 'show'])->name('show');
        Route::get('/{lessonPlan}/edit', [\App\Http\Controllers\Teacher\LessonPlanController::class, 'edit'])->name('edit');
        Route::put('/{lessonPlan}', [\App\Http\Controllers\Teacher\LessonPlanController::class, 'update'])->name('update');
        Route::delete('/{lessonPlan}', [\App\Http\Controllers\Teacher\LessonPlanController::class, 'destroy'])->name('destroy');
        Route::post('/{lessonPlan}/submit', [\App\Http\Controllers\Teacher\LessonPlanController::class, 'submit'])->name('submit');
    });
});

// =============================================================================
// STUDENT ROUTES
// =============================================================================
Route::middleware(['auth', 'student'])->group(function () {
    Route::get('/student/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
    
    // Grade Access
    Route::prefix('student/grades')->name('student.grades.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\GradeController::class, 'index'])->name('index');
        Route::get('/transcript', [\App\Http\Controllers\Student\GradeController::class, 'transcript'])->name('transcript');
        Route::get('/transcript/download', [\App\Http\Controllers\Student\GradeController::class, 'downloadTranscript'])->name('download-transcript');
        Route::get('/grade-sheet/{year}/{semester}', [\App\Http\Controllers\Student\GradeController::class, 'gradeSheet'])->name('grade-sheet');
        Route::get('/download/{year}/{semester}', [\App\Http\Controllers\Student\GradeController::class, 'downloadGradeSheet'])->name('download');
        Route::get('/{grade}', [\App\Http\Controllers\Student\GradeController::class, 'show'])->name('show');
    });
    
    // Student Library Services
    Route::prefix('student/library')->name('student.library.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\LibraryController::class, 'index'])->name('index');
        Route::get('/books', [\App\Http\Controllers\Student\LibraryController::class, 'books'])->name('books');
        Route::get('/books/{book}', [\App\Http\Controllers\Student\LibraryController::class, 'show'])->name('show');
        Route::get('/my-books', [\App\Http\Controllers\Student\LibraryController::class, 'myBooks'])->name('my-books');
    });
    
    // Student Transport Services
    Route::prefix('student/transport')->name('student.transport.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\TransportController::class, 'index'])->name('index');
        Route::get('/routes', [\App\Http\Controllers\Student\TransportController::class, 'routes'])->name('routes');
        Route::get('/vehicles', [\App\Http\Controllers\Student\TransportController::class, 'vehicles'])->name('vehicles');
        Route::get('/schedule', [\App\Http\Controllers\Student\TransportController::class, 'schedule'])->name('schedule');
    });
    
    // Student Hostel Services
    Route::prefix('student/hostel')->name('student.hostel.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\HostelController::class, 'index'])->name('index');
        Route::get('/rooms', [\App\Http\Controllers\Student\HostelController::class, 'rooms'])->name('rooms');
        Route::get('/rooms/{room}', [\App\Http\Controllers\Student\HostelController::class, 'show'])->name('show');
        Route::get('/my-room', [\App\Http\Controllers\Student\HostelController::class, 'myRoom'])->name('my-room');
    });
    
    // Exam Taking
    Route::prefix('student/exams')->name('student.exams.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\ExamController::class, 'index'])->name('index');
        Route::get('/marks', [\App\Http\Controllers\Student\ExamController::class, 'marks'])->name('marks');
        Route::get('/upcoming', [\App\Http\Controllers\Student\ExamController::class, 'upcoming'])->name('upcoming');
        Route::get('/{exam}', [\App\Http\Controllers\Student\ExamController::class, 'show'])->name('show');
        Route::post('/{exam}/start', [\App\Http\Controllers\Student\ExamController::class, 'start'])->name('start');
        Route::get('/{attempt}/take', [\App\Http\Controllers\Student\ExamController::class, 'take'])->name('take');
        Route::post('/{attempt}/submit', [\App\Http\Controllers\Student\ExamController::class, 'submit'])->name('submit');
        Route::get('/{attempt}/result', [\App\Http\Controllers\Student\ExamController::class, 'result'])->name('result');
        Route::post('/save-answer', [\App\Http\Controllers\Student\ExamController::class, 'saveAnswer'])->name('save-answer');
        Route::get('/{attempt}/answers', [\App\Http\Controllers\Student\ExamController::class, 'getAnswers'])->name('get-answers');
    });
    
    // Homework Submission
    Route::prefix('student/homework')->name('student.homework.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\HomeworkController::class, 'index'])->name('index');
        Route::get('/{assignment}', [\App\Http\Controllers\Student\HomeworkController::class, 'show'])->name('show');
        Route::get('/{assignment}/submit', [\App\Http\Controllers\Student\HomeworkController::class, 'create'])->name('create');
        Route::post('/{assignment}/submit', [\App\Http\Controllers\Student\HomeworkController::class, 'store'])->name('store');
    });
    
    // Student Subject Access
    Route::prefix('student/subjects')->name('student.subjects.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\SubjectController::class, 'index'])->name('index');
        Route::get('/{subject}', [\App\Http\Controllers\Student\SubjectController::class, 'show'])->name('show');
    });
    
    // Student Study Materials Access
    Route::prefix('student/study-materials')->name('student.study-materials.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\StudyMaterialController::class, 'index'])->name('index');
        Route::get('/{material}', [\App\Http\Controllers\Student\StudyMaterialController::class, 'show'])->name('show');
        Route::get('/{material}/download', [\App\Http\Controllers\Student\StudyMaterialController::class, 'download'])->name('download');
    });
    
    // Student Gradesheet Access - Removed conflicting routes
    
    // Student Finance Access
    Route::prefix('student/finance')->name('student.finance.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\FinanceController::class, 'index'])->name('index');
        Route::get('/{fee}/pay', [\App\Http\Controllers\Student\FinanceController::class, 'createPayment'])->name('pay');
        Route::post('/{fee}/pay', [\App\Http\Controllers\Student\FinanceController::class, 'storePayment'])->name('store-payment');
    });
    
    // Student Invoice Downloads
    Route::prefix('student/invoices')->name('student.invoices.')->group(function () {
        Route::get('/{fee}/download', [\App\Http\Controllers\Student\FinanceController::class, 'downloadInvoice'])->name('download');
    });
    
    // Student Profile Management
    Route::get('/student/profile', [\App\Http\Controllers\Student\SettingsController::class, 'profile'])->name('student.profile');
    Route::get('/student/profile/edit', [\App\Http\Controllers\Student\SettingsController::class, 'editProfile'])->name('student.profile.edit');
    Route::put('/student/profile', [\App\Http\Controllers\Student\SettingsController::class, 'updateProfile'])->name('student.profile.update');
    Route::get('/student/change-password', [\App\Http\Controllers\Student\SettingsController::class, 'changePasswordForm'])->name('student.change-password');
    Route::post('/student/change-password', [\App\Http\Controllers\Student\SettingsController::class, 'changePassword'])->name('student.change-password.update');

    // Student Library Access
    Route::prefix('student/library')->name('student.library.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\LibraryController::class, 'index'])->name('index');
        Route::get('/search', [\App\Http\Controllers\Student\LibraryController::class, 'search'])->name('search');
        Route::get('/books', [\App\Http\Controllers\Student\LibraryController::class, 'books'])->name('books');
        Route::get('/my-books', [\App\Http\Controllers\Student\LibraryController::class, 'myBooks'])->name('my-books');
        Route::post('/books/{book}/request', [\App\Http\Controllers\Student\LibraryController::class, 'requestBook'])->name('request-book');
    });

    // Student Transport Access
    Route::prefix('student/transport')->name('student.transport.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\TransportController::class, 'index'])->name('index');
        Route::get('/schedule', [\App\Http\Controllers\Student\TransportController::class, 'schedule'])->name('schedule');
        Route::get('/routes', [\App\Http\Controllers\Student\TransportController::class, 'routes'])->name('routes');
        Route::post('/request', [\App\Http\Controllers\Student\TransportController::class, 'request'])->name('request');
    });

    // Student Hostel Access
    Route::prefix('student/hostel')->name('student.hostel.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\HostelController::class, 'index'])->name('index');
        Route::get('/rooms', [\App\Http\Controllers\Student\HostelController::class, 'rooms'])->name('rooms');
        Route::get('/payments', [\App\Http\Controllers\Student\HostelController::class, 'payments'])->name('payments');
        Route::get('/room', [\App\Http\Controllers\Student\HostelController::class, 'myRoom'])->name('room');
        Route::get('/facilities', [\App\Http\Controllers\Student\HostelController::class, 'facilities'])->name('facilities');
        Route::post('/request', [\App\Http\Controllers\Student\HostelController::class, 'request'])->name('request');
        Route::post('/requests', [\App\Http\Controllers\Student\HostelController::class, 'request'])->name('requests');
    });

    // Student Attendance Access
    Route::prefix('student/attendance')->name('student.attendance.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\AttendanceController::class, 'index'])->name('index');
        Route::get('/history', [\App\Http\Controllers\Student\AttendanceController::class, 'history'])->name('history');
        Route::get('/summary', [\App\Http\Controllers\Student\AttendanceController::class, 'summary'])->name('summary');
    });

    // Student Notifications Access
    Route::prefix('student/notifications')->name('student.notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\NotificationController::class, 'index'])->name('index');
        Route::get('/{notification}', [\App\Http\Controllers\Student\NotificationController::class, 'show'])->name('show');
        Route::post('/{notification}/mark-read', [\App\Http\Controllers\Student\NotificationController::class, 'markAsRead'])->name('mark-read');
    });

    // Student Settings Access
    Route::prefix('student/settings')->name('student.settings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\SettingsController::class, 'index'])->name('index');
        Route::put('/profile', [\App\Http\Controllers\Student\SettingsController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [\App\Http\Controllers\Student\SettingsController::class, 'updatePassword'])->name('password.update');
        Route::get('/preferences', [\App\Http\Controllers\Student\SettingsController::class, 'preferences'])->name('preferences');
        Route::post('/preferences', [\App\Http\Controllers\Student\SettingsController::class, 'updatePreferences'])->name('preferences.update');
        Route::get('/notifications', [\App\Http\Controllers\Student\SettingsController::class, 'notifications'])->name('notifications');
        Route::post('/notifications', [\App\Http\Controllers\Student\SettingsController::class, 'updateNotifications'])->name('notifications.update');
        Route::get('/privacy', [\App\Http\Controllers\Student\SettingsController::class, 'privacy'])->name('privacy');
        Route::post('/privacy', [\App\Http\Controllers\Student\SettingsController::class, 'updatePrivacy'])->name('privacy.update');
    });
});

// =============================================================================
// FINANCE ROUTES
// =============================================================================
Route::middleware(['auth', 'finance'])->group(function () {
    Route::get('/finance/dashboard', [FinanceController::class, 'dashboard'])->name('finance.dashboard');
    
    // Payment Management
    Route::prefix('finance/payments')->name('finance.payments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Finance\PaymentController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Finance\PaymentController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Finance\PaymentController::class, 'store'])->name('store');
        Route::get('/{payment}', [\App\Http\Controllers\Finance\PaymentController::class, 'show'])->name('show');
        Route::get('/analytics', [\App\Http\Controllers\Finance\PaymentController::class, 'analytics'])->name('analytics');
        
        // Payment Approval Actions
        Route::post('/{payment}/approve', [\App\Http\Controllers\Finance\PaymentApprovalController::class, 'approve'])->name('approve');
        Route::post('/{payment}/reject', [\App\Http\Controllers\Finance\PaymentApprovalController::class, 'reject'])->name('reject');
    });
    
    // Scholarship Management
    Route::prefix('finance/scholarships')->name('finance.scholarships.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Finance\ScholarshipController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Finance\ScholarshipController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Finance\ScholarshipController::class, 'store'])->name('store');
        Route::get('/{scholarship}', [\App\Http\Controllers\Finance\ScholarshipController::class, 'show'])->name('show');
        Route::get('/{scholarship}/edit', [\App\Http\Controllers\Finance\ScholarshipController::class, 'edit'])->name('edit');
        Route::put('/{scholarship}', [\App\Http\Controllers\Finance\ScholarshipController::class, 'update'])->name('update');
        Route::delete('/{scholarship}', [\App\Http\Controllers\Finance\ScholarshipController::class, 'destroy'])->name('destroy');
        Route::get('/applications', [\App\Http\Controllers\Finance\ScholarshipController::class, 'applications'])->name('applications');
        Route::post('/applications/{application}/approve', [\App\Http\Controllers\Finance\ScholarshipController::class, 'approveApplication'])->name('applications.approve');
        Route::post('/applications/{application}/reject', [\App\Http\Controllers\Finance\ScholarshipController::class, 'rejectApplication'])->name('applications.reject');
    });
    
    // Fee Structure Management
    Route::prefix('finance/fees/structures')->name('finance.fees.structures.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Finance\FeeStructureController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Finance\FeeStructureController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Finance\FeeStructureController::class, 'store'])->name('store');
        Route::get('/{feeStructure}', [\App\Http\Controllers\Finance\FeeStructureController::class, 'show'])->name('show');
        Route::get('/{feeStructure}/edit', [\App\Http\Controllers\Finance\FeeStructureController::class, 'edit'])->name('edit');
        Route::put('/{feeStructure}', [\App\Http\Controllers\Finance\FeeStructureController::class, 'update'])->name('update');
        Route::delete('/{feeStructure}', [\App\Http\Controllers\Finance\FeeStructureController::class, 'destroy'])->name('destroy');
    });
    
    // Fee Reports
    Route::prefix('finance/fees')->name('finance.fees.')->group(function () {
        Route::get('/reports', [\App\Http\Controllers\Finance\FeeReportController::class, 'index'])->name('reports');
        Route::get('/reports/export', [\App\Http\Controllers\Finance\FeeReportController::class, 'export'])->name('reports.export');
        Route::get('/reports/class/{class}', [\App\Http\Controllers\Finance\FeeReportController::class, 'classReport'])->name('reports.class');
        Route::get('/reports/student/{student}', [\App\Http\Controllers\Finance\FeeReportController::class, 'studentReport'])->name('reports.student');
    });
    
    // Financial Reports
    Route::prefix('finance/reports')->name('finance.reports.')->group(function () {
        Route::get('/financial', [\App\Http\Controllers\Finance\FinancialReportController::class, 'index'])->name('financial');
        Route::get('/financial/export', [\App\Http\Controllers\Finance\FinancialReportController::class, 'export'])->name('financial.export');
        Route::get('/payments', [\App\Http\Controllers\Finance\FinancialReportController::class, 'payments'])->name('payments');
        Route::get('/income', [\App\Http\Controllers\Finance\FinancialReportController::class, 'income'])->name('income');
        Route::get('/expenses', [\App\Http\Controllers\Finance\FinancialReportController::class, 'expenses'])->name('expenses');
    });
    
    // Staff Payroll Management (Finance Officer Access)
    Route::prefix('finance/staff')->name('finance.staff.')->group(function () {
        Route::get('/payroll', [\App\Http\Controllers\Admin\StaffManagementController::class, 'payroll'])->name('payroll');
        Route::get('/payroll/{payroll}', [\App\Http\Controllers\Admin\StaffManagementController::class, 'showPayroll'])->name('payroll.show');
        Route::get('/create-payroll', [\App\Http\Controllers\Admin\StaffManagementController::class, 'createPayroll'])->name('create-payroll');
        Route::post('/store-payroll', [\App\Http\Controllers\Admin\StaffManagementController::class, 'storePayroll'])->name('store-payroll');
        Route::get('/payroll/{payroll}/edit', [\App\Http\Controllers\Admin\StaffManagementController::class, 'editPayroll'])->name('payroll.edit');
        Route::put('/payroll/{payroll}', [\App\Http\Controllers\Admin\StaffManagementController::class, 'updatePayroll'])->name('payroll.update');
        Route::delete('/payroll/{payroll}', [\App\Http\Controllers\Admin\StaffManagementController::class, 'destroyPayroll'])->name('payroll.destroy');
    });
});

// =============================================================================
// GENERIC ROUTES (redirects to appropriate user-specific routes)
// =============================================================================
Route::middleware(['auth'])->group(function () {
    // Generic Classes route - redirects based on user type
    Route::get('/classes', function () {
        $user = auth()->user();
        switch ($user->user_type) {
            case 'admin':
                return redirect()->route('admin.classes.index');
            case 'teacher':
                return redirect()->route('teacher.classes.index');
            case 'student':
                return redirect()->route('student.dashboard'); // Students don't have a classes index
            default:
                return redirect()->route('admin.classes.index');
        }
    })->name('classes.index');

    // Generic Exam Types route - redirects based on user type
    Route::get('/exams/types', function () {
        $user = auth()->user();
        switch ($user->user_type) {
            case 'admin':
                return redirect()->route('admin.exams.types.index');
            case 'teacher':
                return redirect()->route('teacher.exams.index'); // Teachers see exam schedules, not types
            case 'student':
                return redirect()->route('student.exams.index'); // Students see their exams
            default:
                return redirect()->route('admin.exams.types.index');
        }
    })->name('exams.types.index');

    // Generic Homework Create route - redirects based on user type
    Route::get('/homework/create', function () {
        $user = auth()->user();
        switch ($user->user_type) {
            case 'admin':
            case 'teacher':
                return redirect()->route('teacher.homework.create');
            case 'student':
                return redirect()->route('student.homework.index'); // Students view homework, don't create
            default:
                return redirect()->route('teacher.homework.create');
        }
    })->name('homework.create');

    // Generic Study Materials Create route - redirects based on user type  
    Route::get('/study-materials/create', function () {
        $user = auth()->user();
        switch ($user->user_type) {
            case 'admin':
            case 'teacher':
                return redirect()->route('teacher.study-materials.create');
            case 'student':
                return redirect()->route('student.dashboard');
            default:
                return redirect()->route('teacher.study-materials.create');
        }
    })->name('study-materials.create');

    // Generic Attendance History route - redirects based on user type
    Route::get('/attendance/history/students', function () {
        $user = auth()->user();
        switch ($user->user_type) {
            case 'admin':
            case 'teacher':
                return redirect()->route('attendance.reports');
            case 'student':
                return redirect()->route('student.attendance.history');
            default:
                return redirect()->route('attendance.reports');
        }
    })->name('attendance.history.students');

    // Generic Teacher Attendance History route
    Route::get('/attendance/history/teacher', function () {
        $user = auth()->user();
        switch ($user->user_type) {
            case 'admin':
            case 'teacher':
                return redirect()->route('attendance.teacher');
            case 'student':
                return redirect()->route('student.attendance.index');
            default:
                return redirect()->route('attendance.teacher');
        }
    })->name('attendance.history.teacher');

    // Generic Homework Index route
    Route::get('/homework', function () {
        $user = auth()->user();
        switch ($user->user_type) {
            case 'admin':
            case 'teacher':
                return redirect()->route('teacher.homework.index');
            case 'student':
                return redirect()->route('student.homework.index');
            default:
                return redirect()->route('teacher.homework.index');
        }
    })->name('homework.index');

    // Generic Study Materials Index route
    Route::get('/study-materials', function () {
        $user = auth()->user();
        switch ($user->user_type) {
            case 'admin':
            case 'teacher':
                return redirect()->route('teacher.study-materials.index');
            case 'student':
                return redirect()->route('student.study-materials.index');
            default:
                return redirect()->route('teacher.study-materials.index');
        }
    })->name('study-materials.index');

    // Generic Notifications route - redirects based on user type
    Route::get('/notifications', function () {
        $user = auth()->user();
        switch ($user->user_type) {
            case 'admin':
                return redirect()->route('admin.notifications.index');
            case 'teacher':
                return redirect()->route('student.notifications.index'); // Teachers can see student-style notifications
            case 'student':
                return redirect()->route('student.notifications.index');
            case 'finance':
                return redirect()->route('admin.notifications.index');
            default:
                return redirect()->route('student.notifications.index');
        }
    })->name('notifications.index');

});

// =============================================================================
// SHARED AUTHENTICATED ROUTES (Admin/Teacher Access)
// =============================================================================
Route::middleware(['auth'])->group(function () {
    // Attendance Management (Admin/Teacher access)
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AttendanceController::class, 'index'])->name('index');
        Route::get('/take', [\App\Http\Controllers\AttendanceController::class, 'take'])->name('take');
        Route::post('/store', [\App\Http\Controllers\AttendanceController::class, 'store'])->name('store');
        Route::get('/reports', [\App\Http\Controllers\AttendanceController::class, 'reports'])->name('reports');
        Route::get('/student', [\App\Http\Controllers\AttendanceController::class, 'studentAttendance'])->name('student');
        Route::get('/teacher', [\App\Http\Controllers\AttendanceController::class, 'teacherAttendance'])->name('teacher');
    });
    
    // Profile Management
    Route::get('/profile', [UserController::class, 'myProfile'])->name('profile');
    Route::get('/profile/edit', [UserController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    
    // Notifications for real-time updates
    Route::get('/notifications.json', function (Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) {
            return response()->json([]);
        }
        try {
            // Use the NotificationService to get notifications
            $service = app(\App\Services\NotificationService::class);
            $notifications = $service->getUnreadNotifications($user->id, 10);
            return response()->json($notifications);
        } catch (\Throwable $e) {
            return response()->json([]);
        }
    })->name('notifications.json');
    
    Route::put('/notifications/{id}/read', function (Illuminate\Http\Request $request, $id) {
        $user = $request->user();
        $service = app(\App\Services\NotificationService::class);
        $service->markAsRead($id, $user->id);
        return response()->json(['success' => true]);
    })->name('notifications.mark-read');
    
    // User Profile Routes (for all user types)
    Route::get('/profile', [UserController::class, 'myProfile'])->name('users.profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('users.update-profile');
    Route::get('/users/{user}/change-password', [UserController::class, 'changePasswordForm'])->name('users.change-password');
    Route::post('/users/{user}/change-password', [UserController::class, 'changePassword'])->name('users.change-password.update');
    Route::get('/me/profile', [UserController::class, 'myProfile'])->name('me.profile');
    Route::get('/me/profile/edit', [UserController::class, 'editProfile'])->name('me.profile.edit');
    Route::put('/me/profile', [UserController::class, 'updateProfile'])->name('me.profile.update');
    
    // API Routes for AJAX calls
    Route::get('/api/students/search', function (Illuminate\Http\Request $request) {
        $query = $request->get('q', '');
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        try {
            $students = \App\Models\Student::with(['user', 'classRoom'])
                ->whereHas('user', function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%");
                })
                ->orWhere('admission_number', 'like', "%{$query}%")
                ->orWhere('student_number', 'like', "%{$query}%")
                ->limit(10)
                ->get()
                ->map(function($student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->user->name,
                        'admission_number' => $student->admission_number,
                        'student_number' => $student->student_number,
                        'class_name' => $student->classRoom->name ?? 'N/A',
                        'total_fees' => $student->total_fees ?? 0,
                        'paid_fees' => $student->paid_fees ?? 0,
                        'balance_fees' => $student->balance_fees ?? 0,
                    ];
                });
            
            return response()->json($students);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    })->name('api.students.search');
});

// Payment Management Routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\PaymentController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\PaymentController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\PaymentController::class, 'store'])->name('store');
        Route::get('/{payment}', [\App\Http\Controllers\PaymentController::class, 'show'])->name('show');
        Route::patch('/{payment}/status', [\App\Http\Controllers\PaymentController::class, 'updateStatus'])->name('update-status');
        Route::post('/record', [\App\Http\Controllers\PaymentController::class, 'recordPayment'])->name('record');
    });
});

// SMS Notification Routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::prefix('admin/sms-notifications')->name('admin.sms-notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SmsNotificationController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\SmsNotificationController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\SmsNotificationController::class, 'store'])->name('store');
        Route::get('/{smsNotification}', [\App\Http\Controllers\Admin\SmsNotificationController::class, 'show'])->name('show');
        Route::post('/{smsNotification}/resend', [\App\Http\Controllers\Admin\SmsNotificationController::class, 'resend'])->name('resend');
        Route::post('/send-scheduled', [\App\Http\Controllers\Admin\SmsNotificationController::class, 'sendScheduled'])->name('send-scheduled');
        Route::get('/statistics', [\App\Http\Controllers\Admin\SmsNotificationController::class, 'statistics'])->name('statistics');
        Route::post('/bulk-to-students', [\App\Http\Controllers\Admin\SmsNotificationController::class, 'sendBulkToStudents'])->name('bulk-to-students');
        Route::post('/attendance-notification', [\App\Http\Controllers\Admin\SmsNotificationController::class, 'sendAttendanceNotification'])->name('attendance-notification');
        Route::post('/grades-notification', [\App\Http\Controllers\Admin\SmsNotificationController::class, 'sendGradesNotification'])->name('grades-notification');
        Route::post('/urgent-notification', [\App\Http\Controllers\Admin\SmsNotificationController::class, 'sendUrgentNotification'])->name('urgent-notification');
    });
});

// USSD Routes (public routes for mobile network access)
Route::prefix('ussd')->name('ussd.')->group(function () {
    Route::post('/handle', [\App\Http\Controllers\UssdController::class, 'handle'])->name('handle');
    Route::post('/delivery-status', [\App\Http\Controllers\UssdController::class, 'deliveryStatus'])->name('delivery-status');
    Route::post('/incoming-sms', [\App\Http\Controllers\UssdController::class, 'incomingSms'])->name('incoming-sms');
});

// SMS Webhook Routes (public routes for provider callbacks)
Route::prefix('sms')->name('sms.')->group(function () {
    Route::post('/delivery-status', [\App\Http\Controllers\UssdController::class, 'deliveryStatus'])->name('delivery-status');
    Route::post('/incoming', [\App\Http\Controllers\UssdController::class, 'incomingSms'])->name('incoming');
});

// Public Admission Routes
Route::prefix('admission')->name('admission.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AdmissionController::class, 'showApplicationForm'])->name('form');
    Route::post('/', [\App\Http\Controllers\AdmissionController::class, 'storeApplication'])->name('store');
    Route::get('/success/{applicationNumber}', [\App\Http\Controllers\AdmissionController::class, 'showSuccess'])->name('success');
    Route::get('/check-status', [\App\Http\Controllers\AdmissionController::class, 'showStatusForm'])->name('check-status');
    Route::post('/check-status', [\App\Http\Controllers\AdmissionController::class, 'checkStatus'])->name('status');
    Route::get('/requirements', [\App\Http\Controllers\AdmissionController::class, 'showRequirements'])->name('requirements');
    Route::get('/calendar', [\App\Http\Controllers\AdmissionController::class, 'showCalendar'])->name('calendar');
    Route::get('/download/{applicationNumber}', [\App\Http\Controllers\AdmissionController::class, 'downloadApplication'])->name('download');
});

// Admin Admission Management Routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::prefix('admin/admission')->name('admin.admission.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdmissionApplicationController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\AdmissionApplicationController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\AdmissionApplicationController::class, 'store'])->name('store');
        Route::get('/{application}', [\App\Http\Controllers\Admin\AdmissionApplicationController::class, 'show'])->name('show');
        Route::get('/{application}/edit', [\App\Http\Controllers\Admin\AdmissionApplicationController::class, 'edit'])->name('edit');
        Route::put('/{application}', [\App\Http\Controllers\Admin\AdmissionApplicationController::class, 'update'])->name('update');
        Route::delete('/{application}', [\App\Http\Controllers\Admin\AdmissionApplicationController::class, 'destroy'])->name('destroy');
        Route::post('/{application}/submit', [\App\Http\Controllers\Admin\AdmissionApplicationController::class, 'submit'])->name('submit');
        Route::post('/{application}/approve', [\App\Http\Controllers\Admin\AdmissionApplicationController::class, 'approve'])->name('approve');
        Route::post('/{application}/reject', [\App\Http\Controllers\Admin\AdmissionApplicationController::class, 'reject'])->name('reject');
        Route::post('/{application}/enroll', [\App\Http\Controllers\Admin\AdmissionApplicationController::class, 'enroll'])->name('enroll');
        Route::get('/{application}/download', [\App\Http\Controllers\Admin\AdmissionApplicationController::class, 'download'])->name('download');
        Route::get('/statistics', [\App\Http\Controllers\Admin\AdmissionApplicationController::class, 'statistics'])->name('statistics');
    });
});

// Admin Transcript Management Routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::prefix('admin/transcripts')->name('admin.transcripts.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\TranscriptController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\TranscriptController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\TranscriptController::class, 'store'])->name('store');
        Route::get('/{transcript}', [\App\Http\Controllers\Admin\TranscriptController::class, 'show'])->name('show');
        Route::get('/{transcript}/edit', [\App\Http\Controllers\Admin\TranscriptController::class, 'edit'])->name('edit');
        Route::put('/{transcript}', [\App\Http\Controllers\Admin\TranscriptController::class, 'update'])->name('update');
        Route::delete('/{transcript}', [\App\Http\Controllers\Admin\TranscriptController::class, 'destroy'])->name('destroy');
        Route::post('/{transcript}/approve', [\App\Http\Controllers\Admin\TranscriptController::class, 'approve'])->name('approve');
        Route::post('/{transcript}/issue', [\App\Http\Controllers\Admin\TranscriptController::class, 'issue'])->name('issue');
        Route::post('/{transcript}/archive', [\App\Http\Controllers\Admin\TranscriptController::class, 'archive'])->name('archive');
        Route::get('/{transcript}/pdf', [\App\Http\Controllers\Admin\TranscriptController::class, 'generatePdf'])->name('pdf');
        Route::get('/{transcript}/excel', [\App\Http\Controllers\Admin\TranscriptController::class, 'generateExcel'])->name('excel');
        Route::post('/bulk-generate', [\App\Http\Controllers\Admin\TranscriptController::class, 'bulkGenerate'])->name('bulk-generate');
        Route::get('/statistics', [\App\Http\Controllers\Admin\TranscriptController::class, 'statistics'])->name('statistics');
    });
});

// Student Transcript Routes
Route::middleware(['auth', 'student'])->group(function () {
    Route::prefix('student/transcripts')->name('student.transcripts.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\TranscriptController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Student\TranscriptController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Student\TranscriptController::class, 'requestTranscript'])->name('request');
        Route::get('/{transcript}', [\App\Http\Controllers\Student\TranscriptController::class, 'show'])->name('show');
        Route::get('/{transcript}/download', [\App\Http\Controllers\Student\TranscriptController::class, 'downloadPdf'])->name('download');
        Route::get('/statistics', [\App\Http\Controllers\Student\TranscriptController::class, 'statistics'])->name('statistics');
    });
});

// Admin Monthly Reports Routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::prefix('admin/monthly-reports')->name('admin.monthly-reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\MonthlyReportController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\MonthlyReportController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\MonthlyReportController::class, 'store'])->name('store');
        Route::get('/{monthlyReport}', [\App\Http\Controllers\Admin\MonthlyReportController::class, 'show'])->name('show');
        Route::get('/{monthlyReport}/edit', [\App\Http\Controllers\Admin\MonthlyReportController::class, 'edit'])->name('edit');
        Route::put('/{monthlyReport}', [\App\Http\Controllers\Admin\MonthlyReportController::class, 'update'])->name('update');
        Route::delete('/{monthlyReport}', [\App\Http\Controllers\Admin\MonthlyReportController::class, 'destroy'])->name('destroy');
        Route::post('/{monthlyReport}/approve', [\App\Http\Controllers\Admin\MonthlyReportController::class, 'approve'])->name('approve');
        Route::post('/{monthlyReport}/reject', [\App\Http\Controllers\Admin\MonthlyReportController::class, 'reject'])->name('reject');
        Route::get('/{monthlyReport}/pdf', [\App\Http\Controllers\Admin\MonthlyReportController::class, 'generatePdf'])->name('pdf');
        Route::post('/bulk-generate', [\App\Http\Controllers\Admin\MonthlyReportController::class, 'bulkGenerate'])->name('bulk-generate');
        Route::get('/statistics', [\App\Http\Controllers\Admin\MonthlyReportController::class, 'statistics'])->name('statistics');
    });
});

// Teacher Monthly Reports Routes
Route::middleware(['auth', 'teacher'])->group(function () {
    Route::prefix('teacher/monthly-reports')->name('teacher.monthly-reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Teacher\MonthlyReportController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Teacher\MonthlyReportController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Teacher\MonthlyReportController::class, 'store'])->name('store');
        Route::get('/{monthlyReport}', [\App\Http\Controllers\Teacher\MonthlyReportController::class, 'show'])->name('show');
        Route::get('/{monthlyReport}/edit', [\App\Http\Controllers\Teacher\MonthlyReportController::class, 'edit'])->name('edit');
        Route::put('/{monthlyReport}', [\App\Http\Controllers\Teacher\MonthlyReportController::class, 'update'])->name('update');
        Route::post('/{monthlyReport}/submit', [\App\Http\Controllers\Teacher\MonthlyReportController::class, 'submit'])->name('submit');
        Route::get('/{monthlyReport}/pdf', [\App\Http\Controllers\Teacher\MonthlyReportController::class, 'downloadPdf'])->name('pdf');
        Route::get('/statistics', [\App\Http\Controllers\Teacher\MonthlyReportController::class, 'statistics'])->name('statistics');
    });
});

// Admin Inventory Management Routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::prefix('admin/inventory')->name('admin.inventory.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\InventoryDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/', [\App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\InventoryController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\InventoryController::class, 'store'])->name('store');
        Route::get('/{item}', [\App\Http\Controllers\Admin\InventoryController::class, 'show'])->name('show');
        Route::get('/{item}/edit', [\App\Http\Controllers\Admin\InventoryController::class, 'edit'])->name('edit');
        Route::put('/{item}', [\App\Http\Controllers\Admin\InventoryController::class, 'update'])->name('update');
        Route::delete('/{item}', [\App\Http\Controllers\Admin\InventoryController::class, 'destroy'])->name('destroy');
        Route::post('/{item}/adjust-stock', [\App\Http\Controllers\Admin\InventoryController::class, 'adjustStock'])->name('adjust-stock');
        Route::post('/{item}/transfer-stock', [\App\Http\Controllers\Admin\InventoryController::class, 'transferStock'])->name('transfer-stock');
        Route::get('/statistics', [\App\Http\Controllers\Admin\InventoryController::class, 'statistics'])->name('statistics');
        
        // Items routes (alias for main routes)
        Route::get('/items', [\App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('items.index');
        Route::get('/items/create', [\App\Http\Controllers\Admin\InventoryController::class, 'create'])->name('items.create');
        Route::post('/items', [\App\Http\Controllers\Admin\InventoryController::class, 'store'])->name('items.store');
        Route::get('/items/{item}', [\App\Http\Controllers\Admin\InventoryController::class, 'show'])->name('items.show');
        Route::get('/items/{item}/edit', [\App\Http\Controllers\Admin\InventoryController::class, 'edit'])->name('items.edit');
        Route::put('/items/{item}', [\App\Http\Controllers\Admin\InventoryController::class, 'update'])->name('items.update');
        Route::delete('/items/{item}', [\App\Http\Controllers\Admin\InventoryController::class, 'destroy'])->name('items.destroy');
        
        // Transactions routes
        Route::get('/transactions', [\App\Http\Controllers\Admin\InventoryTransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/create', [\App\Http\Controllers\Admin\InventoryTransactionController::class, 'create'])->name('transactions.create');
        Route::post('/transactions', [\App\Http\Controllers\Admin\InventoryTransactionController::class, 'store'])->name('transactions.store');
    });

    // Inventory Categories Routes
    Route::prefix('admin/inventory-categories')->name('admin.inventory.categories.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\InventoryCategoryController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\InventoryCategoryController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\InventoryCategoryController::class, 'store'])->name('store');
        Route::get('/{category}', [\App\Http\Controllers\Admin\InventoryCategoryController::class, 'show'])->name('show');
        Route::get('/{category}/edit', [\App\Http\Controllers\Admin\InventoryCategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [\App\Http\Controllers\Admin\InventoryCategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [\App\Http\Controllers\Admin\InventoryCategoryController::class, 'destroy'])->name('destroy');
        Route::post('/{category}/toggle-status', [\App\Http\Controllers\Admin\InventoryCategoryController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Inventory Suppliers Routes
    Route::prefix('admin/inventory-suppliers')->name('admin.inventory.suppliers.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\InventorySupplierController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\InventorySupplierController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\InventorySupplierController::class, 'store'])->name('store');
        Route::get('/{supplier}', [\App\Http\Controllers\Admin\InventorySupplierController::class, 'show'])->name('show');
        Route::get('/{supplier}/edit', [\App\Http\Controllers\Admin\InventorySupplierController::class, 'edit'])->name('edit');
        Route::put('/{supplier}', [\App\Http\Controllers\Admin\InventorySupplierController::class, 'update'])->name('update');
        Route::delete('/{supplier}', [\App\Http\Controllers\Admin\InventorySupplierController::class, 'destroy'])->name('destroy');
        Route::post('/{supplier}/toggle-status', [\App\Http\Controllers\Admin\InventorySupplierController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{supplier}/suspend', [\App\Http\Controllers\Admin\InventorySupplierController::class, 'suspend'])->name('suspend');
    });
});

// Health & Safety Management Routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::prefix('admin/health-safety')->name('admin.health-safety.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Admin\HealthSafetyDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'dashboard'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'statistics'])->name('statistics');
        
        // Health Incidents
        Route::get('/incidents', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'incidents'])->name('incidents.index');
        Route::get('/incidents/create', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'createIncident'])->name('incidents.create');
        Route::post('/incidents', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'storeIncident'])->name('incidents.store');
        Route::get('/incidents/{incident}', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'showIncident'])->name('incidents.show');
        Route::get('/incidents/{incident}/edit', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'editIncident'])->name('incidents.edit');
        Route::put('/incidents/{incident}', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'updateIncident'])->name('incidents.update');
        Route::delete('/incidents/{incident}', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'destroyIncident'])->name('incidents.destroy');
        
        // Health Records
        Route::get('/records', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'records'])->name('records.index');
        Route::get('/records/create', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'createRecord'])->name('records.create');
        Route::post('/records', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'storeRecord'])->name('records.store');
        Route::get('/records/{record}', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'showRecord'])->name('records.show');
        
        // Safety Checks
        Route::get('/safety-checks', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'safetyChecks'])->name('safety-checks.index');
        Route::get('/safety-checks/create', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'createSafetyCheck'])->name('safety-checks.create');
        Route::post('/safety-checks', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'storeSafetyCheck'])->name('safety-checks.store');
        Route::get('/safety-checks/{check}', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'showSafetyCheck'])->name('safety-checks.show');
        Route::post('/safety-checks/{check}/approve', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'approveSafetyCheck'])->name('safety-checks.approve');
        
        // Emergency Contacts
        Route::get('/emergency-contacts', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'emergencyContacts'])->name('emergency-contacts');
        Route::get('/emergency-contacts/create', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'createEmergencyContact'])->name('emergency-contacts.create');
        Route::post('/emergency-contacts', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'storeEmergencyContact'])->name('emergency-contacts.store');
        Route::get('/emergency-contacts/{contact}', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'showEmergencyContact'])->name('emergency-contacts.show');
        Route::get('/emergency-contacts/{contact}/edit', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'editEmergencyContact'])->name('emergency-contacts.edit');
        Route::put('/emergency-contacts/{contact}', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'updateEmergencyContact'])->name('emergency-contacts.update');
        Route::delete('/emergency-contacts/{contact}', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'destroyEmergencyContact'])->name('emergency-contacts.destroy');
        Route::post('/emergency-contacts/{contact}/toggle-status', [\App\Http\Controllers\Admin\HealthSafetyController::class, 'toggleEmergencyContactStatus'])->name('emergency-contacts.toggle-status');
    });
});

// Visitor Management Routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::prefix('admin/visitor-management')->name('admin.visitor-management.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Admin\VisitorManagementDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/', [\App\Http\Controllers\Admin\VisitorManagementController::class, 'dashboard'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Admin\VisitorManagementController::class, 'statistics'])->name('statistics');
        
        // Visitors
        Route::get('/visitors', [\App\Http\Controllers\Admin\VisitorManagementController::class, 'visitors'])->name('visitors.index');
        Route::get('/visitors/create', [\App\Http\Controllers\Admin\VisitorManagementController::class, 'createVisitor'])->name('visitors.create');
        Route::post('/visitors', [\App\Http\Controllers\Admin\VisitorManagementController::class, 'storeVisitor'])->name('visitors.store');
        Route::get('/visitors/{visitor}', [\App\Http\Controllers\Admin\VisitorManagementController::class, 'showVisitor'])->name('visitors.show');
        Route::get('/visitors/{visitor}/edit', [\App\Http\Controllers\Admin\VisitorManagementController::class, 'editVisitor'])->name('visitors.edit');
        Route::put('/visitors/{visitor}', [\App\Http\Controllers\Admin\VisitorManagementController::class, 'updateVisitor'])->name('visitors.update');
        Route::delete('/visitors/{visitor}', [\App\Http\Controllers\Admin\VisitorManagementController::class, 'destroyVisitor'])->name('visitors.destroy');
        Route::post('/visitors/{visitor}/blacklist', [\App\Http\Controllers\Admin\VisitorManagementController::class, 'blacklistVisitor'])->name('visitors.blacklist');
        Route::post('/visitors/{visitor}/remove-blacklist', [\App\Http\Controllers\Admin\VisitorManagementController::class, 'removeFromBlacklist'])->name('visitors.remove-blacklist');
        
        // Visitor Logs
        Route::get('/logs', [\App\Http\Controllers\Admin\VisitorManagementController::class, 'logs'])->name('logs.index');
        Route::get('/logs/create', [\App\Http\Controllers\Admin\VisitorManagementController::class, 'createLog'])->name('logs.create');
        Route::post('/logs', [\App\Http\Controllers\Admin\VisitorManagementController::class, 'storeLog'])->name('logs.store');
        Route::get('/logs/{log}', [\App\Http\Controllers\Admin\VisitorManagementController::class, 'showLog'])->name('logs.show');
        Route::post('/logs/{log}/check-out', [\App\Http\Controllers\Admin\VisitorManagementController::class, 'checkOut'])->name('logs.check-out');
        Route::post('/logs/{log}/cancel', [\App\Http\Controllers\Admin\VisitorManagementController::class, 'cancelLog'])->name('logs.cancel');
        
        // Visitor Categories
        Route::get('/categories', [\App\Http\Controllers\Admin\VisitorManagementController::class, 'categories'])->name('categories');
        Route::get('/categories/create', [\App\Http\Controllers\Admin\VisitorManagementController::class, 'createCategory'])->name('categories.create');
        Route::post('/categories', [\App\Http\Controllers\Admin\VisitorManagementController::class, 'storeCategory'])->name('categories.store');
        Route::get('/categories/{category}', [\App\Http\Controllers\Admin\VisitorManagementController::class, 'showCategory'])->name('categories.show');
        Route::get('/categories/{category}/edit', [\App\Http\Controllers\Admin\VisitorManagementController::class, 'editCategory'])->name('categories.edit');
        Route::put('/categories/{category}', [\App\Http\Controllers\Admin\VisitorManagementController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{category}', [\App\Http\Controllers\Admin\VisitorManagementController::class, 'destroyCategory'])->name('categories.destroy');
        Route::post('/categories/{category}/toggle-status', [\App\Http\Controllers\Admin\VisitorManagementController::class, 'toggleCategoryStatus'])->name('categories.toggle-status');
    });
});

// E-Signatures Routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::prefix('admin/e-signatures')->name('admin.e-signatures.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Admin\ESignatureDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/', [\App\Http\Controllers\Admin\ESignatureController::class, 'dashboard'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Admin\ESignatureController::class, 'statistics'])->name('statistics');
        
        // Signatures
        Route::get('/signatures', [\App\Http\Controllers\Admin\ESignatureController::class, 'signatures'])->name('signatures');
        Route::get('/create', [\App\Http\Controllers\Admin\ESignatureController::class, 'create'])->name('create');
        Route::post('/signatures', [\App\Http\Controllers\Admin\ESignatureController::class, 'store'])->name('store');
        Route::get('/signatures/{signature}', [\App\Http\Controllers\Admin\ESignatureController::class, 'showSignature'])->name('signatures.show');
        Route::post('/signatures/{signature}/verify', [\App\Http\Controllers\Admin\ESignatureController::class, 'verifySignature'])->name('signatures.verify');
        Route::post('/signatures/{signature}/revoke', [\App\Http\Controllers\Admin\ESignatureController::class, 'revokeSignature'])->name('signatures.revoke');
        Route::delete('/signatures/{signature}', [\App\Http\Controllers\Admin\ESignatureController::class, 'destroySignature'])->name('signatures.destroy');
        
        // Templates
        Route::get('/templates', [\App\Http\Controllers\Admin\ESignatureController::class, 'templates'])->name('templates');
        Route::get('/templates/create', [\App\Http\Controllers\Admin\ESignatureController::class, 'createTemplate'])->name('templates.create');
        Route::post('/templates', [\App\Http\Controllers\Admin\ESignatureController::class, 'storeTemplate'])->name('templates.store');
        Route::get('/templates/{template}', [\App\Http\Controllers\Admin\ESignatureController::class, 'showTemplate'])->name('templates.show');
        Route::get('/templates/{template}/edit', [\App\Http\Controllers\Admin\ESignatureController::class, 'editTemplate'])->name('templates.edit');
        Route::put('/templates/{template}', [\App\Http\Controllers\Admin\ESignatureController::class, 'updateTemplate'])->name('templates.update');
        Route::delete('/templates/{template}', [\App\Http\Controllers\Admin\ESignatureController::class, 'destroyTemplate'])->name('templates.destroy');
        Route::post('/templates/{template}/toggle-status', [\App\Http\Controllers\Admin\ESignatureController::class, 'toggleTemplateStatus'])->name('templates.toggle-status');
        
        // Approvals
        Route::get('/approvals', [\App\Http\Controllers\Admin\ESignatureController::class, 'approvals'])->name('approvals');
        Route::get('/approvals/{approval}', [\App\Http\Controllers\Admin\ESignatureController::class, 'showApproval'])->name('approvals.show');
        Route::post('/approvals/{approval}/approve', [\App\Http\Controllers\Admin\ESignatureController::class, 'approveSignature'])->name('approvals.approve');
        Route::post('/approvals/{approval}/reject', [\App\Http\Controllers\Admin\ESignatureController::class, 'rejectSignature'])->name('approvals.reject');
        Route::post('/approvals/{approval}/delegate', [\App\Http\Controllers\Admin\ESignatureController::class, 'delegateApproval'])->name('approvals.delegate');
    });
});

// Comprehensive Reporting Routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::prefix('admin/reports')->name('admin.reports.')->group(function () {
        // Dashboard
        Route::get('/', [\App\Http\Controllers\Admin\ReportManagementController::class, 'dashboard'])->name('dashboard');
        Route::get('/statistics', [\App\Http\Controllers\Admin\ReportManagementController::class, 'statistics'])->name('statistics');
        
        // Templates
        Route::get('/templates', [\App\Http\Controllers\Admin\ReportManagementController::class, 'templates'])->name('templates');
        Route::get('/templates/create', [\App\Http\Controllers\Admin\ReportManagementController::class, 'createTemplate'])->name('templates.create');
        Route::post('/templates', [\App\Http\Controllers\Admin\ReportManagementController::class, 'storeTemplate'])->name('templates.store');
        Route::get('/templates/{template}', [\App\Http\Controllers\Admin\ReportManagementController::class, 'showTemplate'])->name('templates.show');
        Route::get('/templates/{template}/edit', [\App\Http\Controllers\Admin\ReportManagementController::class, 'editTemplate'])->name('templates.edit');
        Route::put('/templates/{template}', [\App\Http\Controllers\Admin\ReportManagementController::class, 'updateTemplate'])->name('templates.update');
        Route::delete('/templates/{template}', [\App\Http\Controllers\Admin\ReportManagementController::class, 'destroyTemplate'])->name('templates.destroy');
        Route::post('/templates/{template}/toggle-status', [\App\Http\Controllers\Admin\ReportManagementController::class, 'toggleTemplateStatus'])->name('templates.toggle-status');
        
        // Schedules
        Route::get('/schedules', [\App\Http\Controllers\Admin\ReportManagementController::class, 'schedules'])->name('schedules');
        Route::get('/schedules/create', [\App\Http\Controllers\Admin\ReportManagementController::class, 'createSchedule'])->name('schedules.create');
        Route::post('/schedules', [\App\Http\Controllers\Admin\ReportManagementController::class, 'storeSchedule'])->name('schedules.store');
        Route::get('/schedules/{schedule}', [\App\Http\Controllers\Admin\ReportManagementController::class, 'showSchedule'])->name('schedules.show');
        Route::get('/schedules/{schedule}/edit', [\App\Http\Controllers\Admin\ReportManagementController::class, 'editSchedule'])->name('schedules.edit');
        Route::put('/schedules/{schedule}', [\App\Http\Controllers\Admin\ReportManagementController::class, 'updateSchedule'])->name('schedules.update');
        Route::delete('/schedules/{schedule}', [\App\Http\Controllers\Admin\ReportManagementController::class, 'destroySchedule'])->name('schedules.destroy');
        Route::post('/schedules/{schedule}/toggle-status', [\App\Http\Controllers\Admin\ReportManagementController::class, 'toggleScheduleStatus'])->name('schedules.toggle-status');
        Route::post('/schedules/{schedule}/execute', [\App\Http\Controllers\Admin\ReportManagementController::class, 'executeSchedule'])->name('schedules.execute');
        
        // Executions
        Route::get('/executions', [\App\Http\Controllers\Admin\ReportManagementController::class, 'executions'])->name('executions');
        Route::get('/executions/{execution}', [\App\Http\Controllers\Admin\ReportManagementController::class, 'showExecution'])->name('executions.show');
        Route::get('/executions/{execution}/download', [\App\Http\Controllers\Admin\ReportManagementController::class, 'downloadExecution'])->name('executions.download');
        Route::post('/executions/{execution}/cancel', [\App\Http\Controllers\Admin\ReportManagementController::class, 'cancelExecution'])->name('executions.cancel');
        Route::post('/executions/{execution}/retry', [\App\Http\Controllers\Admin\ReportManagementController::class, 'retryExecution'])->name('executions.retry');
        Route::delete('/executions/{execution}', [\App\Http\Controllers\Admin\ReportManagementController::class, 'destroyExecution'])->name('executions.destroy');
        
        // Subscriptions
        Route::get('/subscriptions', [\App\Http\Controllers\Admin\ReportManagementController::class, 'subscriptions'])->name('subscriptions');
        Route::get('/subscriptions/create', [\App\Http\Controllers\Admin\ReportManagementController::class, 'createSubscription'])->name('subscriptions.create');
        Route::post('/subscriptions', [\App\Http\Controllers\Admin\ReportManagementController::class, 'storeSubscription'])->name('subscriptions.store');
        Route::get('/subscriptions/{subscription}', [\App\Http\Controllers\Admin\ReportManagementController::class, 'showSubscription'])->name('subscriptions.show');
        Route::get('/subscriptions/{subscription}/edit', [\App\Http\Controllers\Admin\ReportManagementController::class, 'editSubscription'])->name('subscriptions.edit');
        Route::put('/subscriptions/{subscription}', [\App\Http\Controllers\Admin\ReportManagementController::class, 'updateSubscription'])->name('subscriptions.update');
        Route::delete('/subscriptions/{subscription}', [\App\Http\Controllers\Admin\ReportManagementController::class, 'destroySubscription'])->name('subscriptions.destroy');
        Route::post('/subscriptions/{subscription}/toggle-status', [\App\Http\Controllers\Admin\ReportManagementController::class, 'toggleSubscriptionStatus'])->name('subscriptions.toggle-status');
        Route::post('/subscriptions/{subscription}/send', [\App\Http\Controllers\Admin\ReportManagementController::class, 'sendSubscription'])->name('subscriptions.send');
    });

    // Advanced Systems Dashboards - Lesson Plans dashboard already defined above



});

// Fallback route
Route::fallback(function () {
    return view('errors.404');
});
