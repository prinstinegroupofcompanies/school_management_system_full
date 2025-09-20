<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

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

    // Grade Management
    Route::prefix('admin/grades')->name('admin.grades.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\InternationalGradeController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\InternationalGradeController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\InternationalGradeController::class, 'store'])->name('store');
        Route::get('/analytics', [\App\Http\Controllers\Admin\InternationalGradeController::class, 'analytics'])->name('analytics');
        Route::get('/{grade}', [\App\Http\Controllers\Admin\InternationalGradeController::class, 'show'])->name('show');
        Route::get('/{grade}/edit', [\App\Http\Controllers\Admin\InternationalGradeController::class, 'edit'])->name('edit');
        Route::put('/{grade}', [\App\Http\Controllers\Admin\InternationalGradeController::class, 'update'])->name('update');
        Route::delete('/{grade}', [\App\Http\Controllers\Admin\InternationalGradeController::class, 'destroy'])->name('destroy');
        Route::post('/{grade}/approve', [\App\Http\Controllers\Admin\InternationalGradeController::class, 'approve'])->name('approve');
        Route::post('/{grade}/reject', [\App\Http\Controllers\Admin\InternationalGradeController::class, 'reject'])->name('reject');
        Route::post('/{grade}/publish', [\App\Http\Controllers\Admin\InternationalGradeController::class, 'publish'])->name('publish');
        Route::post('/bulk-approve', [\App\Http\Controllers\Admin\InternationalGradeController::class, 'bulkApprove'])->name('bulk-approve');
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

    // Staff Management
    Route::prefix('admin/staff')->name('admin.staff.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\StaffManagementController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\StaffManagementController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\StaffManagementController::class, 'store'])->name('store');
        Route::get('/{staff}', [\App\Http\Controllers\Admin\StaffManagementController::class, 'show'])->name('show');
        Route::get('/{staff}/edit', [\App\Http\Controllers\Admin\StaffManagementController::class, 'edit'])->name('edit');
        Route::put('/{staff}', [\App\Http\Controllers\Admin\StaffManagementController::class, 'update'])->name('update');
        Route::delete('/{staff}', [\App\Http\Controllers\Admin\StaffManagementController::class, 'destroy'])->name('destroy');
        Route::get('/performance', [\App\Http\Controllers\Admin\StaffManagementController::class, 'performance'])->name('performance');
        Route::get('/payroll', [\App\Http\Controllers\Admin\StaffManagementController::class, 'payroll'])->name('payroll');
        Route::get('/schedules', [\App\Http\Controllers\Admin\StaffManagementController::class, 'schedules'])->name('schedules');
    });

    // Notifications Management
    Route::prefix('admin/notifications')->name('admin.notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\NotificationController::class, 'store'])->name('store');
        Route::get('/templates', [\App\Http\Controllers\Admin\NotificationController::class, 'templates'])->name('templates');
        Route::get('/templates/create', [\App\Http\Controllers\Admin\NotificationController::class, 'createTemplate'])->name('templates.create');
        Route::post('/templates', [\App\Http\Controllers\Admin\NotificationController::class, 'storeTemplate'])->name('templates.store');
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
        Route::post('/export', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('export');
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
});

// =============================================================================
// TEACHER ROUTES
// =============================================================================
Route::middleware(['auth', 'teacher'])->group(function () {
    Route::get('/teacher/dashboard', [TeacherController::class, 'dashboard'])->name('teacher.dashboard');
    
    // Grade Management
    Route::prefix('teacher/grades')->name('teacher.grades.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Teacher\GradeController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Teacher\GradeController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Teacher\GradeController::class, 'store'])->name('store');
        Route::get('/{grade}', [\App\Http\Controllers\Teacher\GradeController::class, 'show'])->name('show');
        Route::get('/{grade}/edit', [\App\Http\Controllers\Teacher\GradeController::class, 'edit'])->name('edit');
        Route::put('/{grade}', [\App\Http\Controllers\Teacher\GradeController::class, 'update'])->name('update');
    });
    
    // Exam Management
    Route::prefix('teacher/exams')->name('teacher.exams.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Teacher\ExamController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Teacher\ExamController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Teacher\ExamController::class, 'store'])->name('store');
        Route::get('/{exam}', [\App\Http\Controllers\Teacher\ExamController::class, 'show'])->name('show');
        Route::get('/{exam}/edit', [\App\Http\Controllers\Teacher\ExamController::class, 'edit'])->name('edit');
        Route::put('/{exam}', [\App\Http\Controllers\Teacher\ExamController::class, 'update'])->name('update');
        Route::post('/{exam}/publish', [\App\Http\Controllers\Teacher\ExamController::class, 'publish'])->name('publish');
        Route::post('/{exam}/unpublish', [\App\Http\Controllers\Teacher\ExamController::class, 'unpublish'])->name('unpublish');
        Route::delete('/{exam}', [\App\Http\Controllers\Teacher\ExamController::class, 'destroy'])->name('destroy');
    });
    
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
});

// =============================================================================
// STUDENT ROUTES
// =============================================================================
Route::middleware(['auth', 'student'])->group(function () {
    Route::get('/student/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
    
    // Grade Access
    Route::prefix('student/grades')->name('student.grades.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\GradeController::class, 'index'])->name('index');
        Route::get('/{grade}', [\App\Http\Controllers\Student\GradeController::class, 'show'])->name('show');
        Route::get('/transcript', [\App\Http\Controllers\Student\GradeController::class, 'transcript'])->name('transcript');
        Route::get('/transcript/download', [\App\Http\Controllers\Student\GradeController::class, 'downloadTranscript'])->name('download-transcript');
    });
    
    // Exam Taking
    Route::prefix('student/exams')->name('student.exams.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\ExamController::class, 'index'])->name('index');
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
    
    // Student Profile Management
    Route::get('/student/profile', [\App\Http\Controllers\Student\SettingsController::class, 'profile'])->name('student.profile');
    Route::get('/student/profile/edit', [\App\Http\Controllers\Student\SettingsController::class, 'editProfile'])->name('student.profile.edit');
    Route::put('/student/profile', [\App\Http\Controllers\Student\SettingsController::class, 'updateProfile'])->name('student.profile.update');
    Route::get('/student/change-password', [\App\Http\Controllers\Student\SettingsController::class, 'changePasswordForm'])->name('student.change-password');
    Route::post('/student/change-password', [\App\Http\Controllers\Student\SettingsController::class, 'changePassword'])->name('student.change-password.update');
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
    });
});

// =============================================================================
// SHARED AUTHENTICATED ROUTES
// =============================================================================
Route::middleware(['auth'])->group(function () {
    // Profile Management
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
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
    Route::get('/users/{user}/profile', [UserController::class, 'profile'])->name('users.profile');
    Route::get('/users/{user}/change-password', [UserController::class, 'changePasswordForm'])->name('users.change-password');
    Route::post('/users/{user}/change-password', [UserController::class, 'changePassword'])->name('users.change-password.update');
    Route::get('/me/profile', [UserController::class, 'myProfile'])->name('me.profile');
    
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

// Fallback route
Route::fallback(function () {
    return view('errors.404');
});
