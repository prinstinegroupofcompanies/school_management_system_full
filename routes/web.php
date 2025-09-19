<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\HostelController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeworkController;
use App\Http\Controllers\StudyMaterialController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\Admin\StaffManagementController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ReportController;
// use App\Http\Controllers\Auth\AuthenticatedSessionController; // Not needed

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/




// Authentication routes (if using Laravel Breeze/Jetstream, these would be handled there)
Route::get('/', function () {
    return redirect()->route('dashboard');
});
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Student Management Routes
    Route::prefix('admin/students')->name('admin.students.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\StudentController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\StudentController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\StudentController::class, 'store'])->name('store');
        Route::get('/{student}', [\App\Http\Controllers\Admin\StudentController::class, 'show'])->name('show');
        Route::get('/{student}/edit', [\App\Http\Controllers\Admin\StudentController::class, 'edit'])->name('edit');
        Route::put('/{student}', [\App\Http\Controllers\Admin\StudentController::class, 'update'])->name('update');
        Route::delete('/{student}', [\App\Http\Controllers\Admin\StudentController::class, 'destroy'])->name('destroy');
    });

    // Teacher Management Routes
    Route::prefix('admin/teachers')->name('admin.teachers.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\TeacherController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\TeacherController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\TeacherController::class, 'store'])->name('store');
        Route::get('/{teacher}', [\App\Http\Controllers\Admin\TeacherController::class, 'show'])->name('show');
        Route::get('/{teacher}/edit', [\App\Http\Controllers\Admin\TeacherController::class, 'edit'])->name('edit');
        Route::put('/{teacher}', [\App\Http\Controllers\Admin\TeacherController::class, 'update'])->name('update');
        Route::delete('/{teacher}', [\App\Http\Controllers\Admin\TeacherController::class, 'destroy'])->name('destroy');
    });

    // Class Management Routes
    Route::prefix('admin/classes')->name('admin.classes.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ClassController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\ClassController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\ClassController::class, 'store'])->name('store');
        Route::get('/{class}', [\App\Http\Controllers\Admin\ClassController::class, 'show'])->name('show');
        Route::get('/{class}/edit', [\App\Http\Controllers\Admin\ClassController::class, 'edit'])->name('edit');
        Route::put('/{class}', [\App\Http\Controllers\Admin\ClassController::class, 'update'])->name('update');
        Route::delete('/{class}', [\App\Http\Controllers\Admin\ClassController::class, 'destroy'])->name('destroy');
    });

    // Subject Management Routes
    Route::prefix('admin/subjects')->name('admin.subjects.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SubjectController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\SubjectController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\SubjectController::class, 'store'])->name('store');
        Route::get('/{subject}', [\App\Http\Controllers\Admin\SubjectController::class, 'show'])->name('show');
        Route::get('/{subject}/edit', [\App\Http\Controllers\Admin\SubjectController::class, 'edit'])->name('edit');
        Route::put('/{subject}', [\App\Http\Controllers\Admin\SubjectController::class, 'update'])->name('update');
        Route::delete('/{subject}', [\App\Http\Controllers\Admin\SubjectController::class, 'destroy'])->name('destroy');
    });

    // Grade Management Routes
    Route::prefix('admin/grades')->name('admin.grades.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\GradeApprovalController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\GradeApprovalController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\GradeApprovalController::class, 'store'])->name('store');
        Route::get('/{grade}', [\App\Http\Controllers\Admin\GradeApprovalController::class, 'show'])->name('show');
        Route::put('/{grade}/approve', [\App\Http\Controllers\Admin\GradeApprovalController::class, 'approve'])->name('approve');
        Route::put('/{grade}/reject', [\App\Http\Controllers\Admin\GradeApprovalController::class, 'reject'])->name('reject');
    });

    // Finance Officer Management Routes
    Route::prefix('admin/finance_officers')->name('admin.finance_officers.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\FinanceOfficerController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\FinanceOfficerController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\FinanceOfficerController::class, 'store'])->name('store');
        Route::get('/{officer}', [\App\Http\Controllers\Admin\FinanceOfficerController::class, 'show'])->name('show');
        Route::get('/{officer}/edit', [\App\Http\Controllers\Admin\FinanceOfficerController::class, 'edit'])->name('edit');
        Route::put('/{officer}', [\App\Http\Controllers\Admin\FinanceOfficerController::class, 'update'])->name('update');
        Route::delete('/{officer}', [\App\Http\Controllers\Admin\FinanceOfficerController::class, 'destroy'])->name('destroy');
    });

    // Class Fee Structure Management Routes
    Route::prefix('admin/fee-structures')->name('admin.fee-structures.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ClassFeeStructureController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\ClassFeeStructureController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\ClassFeeStructureController::class, 'store'])->name('store');
        Route::get('/{feeStructure}', [\App\Http\Controllers\Admin\ClassFeeStructureController::class, 'show'])->name('show');
        Route::get('/{feeStructure}/edit', [\App\Http\Controllers\Admin\ClassFeeStructureController::class, 'edit'])->name('edit');
        Route::put('/{feeStructure}', [\App\Http\Controllers\Admin\ClassFeeStructureController::class, 'update'])->name('update');
        Route::delete('/{feeStructure}', [\App\Http\Controllers\Admin\ClassFeeStructureController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-apply', [\App\Http\Controllers\Admin\ClassFeeStructureController::class, 'bulkApply'])->name('bulk-apply');
    });

    // International Grade Management Routes
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
    
    // Staff Management Routes
    Route::prefix('admin/staff')->name('admin.staff.')->group(function () {
        Route::get('/', [StaffManagementController::class, 'index'])->name('index');
        Route::get('/create', [StaffManagementController::class, 'create'])->name('create');
        Route::post('/', [StaffManagementController::class, 'store'])->name('store');
        Route::get('/{staff}', [StaffManagementController::class, 'show'])->name('show');
        Route::get('/{staff}/edit', [StaffManagementController::class, 'edit'])->name('edit');
        Route::put('/{staff}', [StaffManagementController::class, 'update'])->name('update');
        Route::delete('/{staff}', [StaffManagementController::class, 'destroy'])->name('destroy');
        Route::get('/performance', [StaffManagementController::class, 'performance'])->name('performance');
        Route::get('/performance/create', [StaffManagementController::class, 'createPerformance'])->name('create-performance');
        Route::post('/performance', [StaffManagementController::class, 'storePerformance'])->name('store-performance');
        Route::get('/schedules', [StaffManagementController::class, 'schedules'])->name('schedules');
        Route::get('/schedules/create', [StaffManagementController::class, 'createSchedule'])->name('create-schedule');
        Route::post('/schedules', [StaffManagementController::class, 'storeSchedule'])->name('store-schedule');
        Route::get('/payroll', [StaffManagementController::class, 'payroll'])->name('payroll');
        Route::get('/payroll/create', [StaffManagementController::class, 'createPayroll'])->name('create-payroll');
        Route::post('/payroll', [StaffManagementController::class, 'storePayroll'])->name('store-payroll');
        Route::get('/reports', [StaffManagementController::class, 'reports'])->name('reports');
    });
    
    // Notification Management Routes
    Route::prefix('admin/notifications')->name('admin.notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/create', [NotificationController::class, 'create'])->name('create');
        Route::post('/', [NotificationController::class, 'store'])->name('store');
        Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
        Route::post('/bulk-action', [NotificationController::class, 'bulkAction'])->name('bulk-action');
        Route::get('/templates', [NotificationController::class, 'templates'])->name('templates');
        Route::get('/reports', [NotificationController::class, 'reports'])->name('reports');
    });
    
    // Comprehensive Reports Routes
    Route::prefix('admin/reports')->name('admin.reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/academic', [ReportController::class, 'academic'])->name('academic');
        Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
        Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance');
        Route::get('/staff', [ReportController::class, 'staff'])->name('staff');
        Route::get('/library', [ReportController::class, 'library'])->name('library');
        Route::post('/export', [ReportController::class, 'export'])->name('export');
    });
    
    // Transport Management Routes
    Route::prefix('admin/transport')->name('admin.transport.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\TransportController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\TransportController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\TransportController::class, 'store'])->name('store');
        Route::get('/{transport}', [\App\Http\Controllers\Admin\TransportController::class, 'show'])->name('show');
        Route::get('/{transport}/edit', [\App\Http\Controllers\Admin\TransportController::class, 'edit'])->name('edit');
        Route::put('/{transport}', [\App\Http\Controllers\Admin\TransportController::class, 'update'])->name('update');
        Route::delete('/{transport}', [\App\Http\Controllers\Admin\TransportController::class, 'destroy'])->name('destroy');
        
        // Transport Routes Management
        Route::get('/routes', [\App\Http\Controllers\Admin\TransportController::class, 'routes'])->name('routes');
        Route::get('/routes/create', [\App\Http\Controllers\Admin\TransportController::class, 'createRoute'])->name('create-route');
        Route::post('/routes', [\App\Http\Controllers\Admin\TransportController::class, 'storeRoute'])->name('store-route');
        Route::get('/routes/{route}/edit', [\App\Http\Controllers\Admin\TransportController::class, 'editRoute'])->name('edit-route');
        Route::put('/routes/{route}', [\App\Http\Controllers\Admin\TransportController::class, 'updateRoute'])->name('update-route');
        Route::delete('/routes/{route}', [\App\Http\Controllers\Admin\TransportController::class, 'destroyRoute'])->name('destroy-route');
    });
    
    // Hostel Management Routes
    Route::prefix('admin/hostel')->name('admin.hostel.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\HostelController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\HostelController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\HostelController::class, 'store'])->name('store');
        Route::get('/{hostel}', [\App\Http\Controllers\Admin\HostelController::class, 'show'])->name('show');
        Route::get('/{hostel}/edit', [\App\Http\Controllers\Admin\HostelController::class, 'edit'])->name('edit');
        Route::put('/{hostel}', [\App\Http\Controllers\Admin\HostelController::class, 'update'])->name('update');
        Route::delete('/{hostel}', [\App\Http\Controllers\Admin\HostelController::class, 'destroy'])->name('destroy');
        
        // Hostel Rooms Management
        Route::get('/rooms', [\App\Http\Controllers\Admin\HostelController::class, 'rooms'])->name('rooms');
        Route::get('/rooms/create', [\App\Http\Controllers\Admin\HostelController::class, 'createRoom'])->name('create-room');
        Route::post('/rooms', [\App\Http\Controllers\Admin\HostelController::class, 'storeRoom'])->name('store-room');
        Route::get('/rooms/{room}/edit', [\App\Http\Controllers\Admin\HostelController::class, 'editRoom'])->name('edit-room');
        Route::put('/rooms/{room}', [\App\Http\Controllers\Admin\HostelController::class, 'updateRoom'])->name('update-room');
        Route::delete('/rooms/{room}', [\App\Http\Controllers\Admin\HostelController::class, 'destroyRoom'])->name('destroy-room');
        
        // Hostel Payments Management
        Route::get('/payments', [\App\Http\Controllers\Admin\HostelController::class, 'payments'])->name('payments');
        Route::get('/payments/create', [\App\Http\Controllers\Admin\HostelController::class, 'createPayment'])->name('create-payment');
        Route::post('/payments', [\App\Http\Controllers\Admin\HostelController::class, 'storePayment'])->name('store-payment');
        Route::put('/payments/{payment}/status', [\App\Http\Controllers\Admin\HostelController::class, 'updatePaymentStatus'])->name('update-payment-status');
    });
    
    // other admin routes
});

// Teacher Routes (restricted access)
Route::middleware(['auth', 'teacher'])->group(function () {
    // Teacher Dashboard
    Route::get('/teacher/dashboard', [TeacherController::class, 'dashboard'])->name('teacher.dashboard');
    
    // Teacher Grade Management
    Route::prefix('teacher/grades')->name('teacher.grades.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Teacher\GradeController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Teacher\GradeController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Teacher\GradeController::class, 'store'])->name('store');
        Route::get('/bulk-create', [\App\Http\Controllers\Teacher\GradeController::class, 'bulkCreate'])->name('bulk-create');
        Route::post('/bulk-store', [\App\Http\Controllers\Teacher\GradeController::class, 'bulkStore'])->name('bulk-store');
        Route::get('/analytics', [\App\Http\Controllers\Teacher\GradeController::class, 'analytics'])->name('analytics');
        Route::get('/{grade}', [\App\Http\Controllers\Teacher\GradeController::class, 'show'])->name('show');
        Route::get('/{grade}/edit', [\App\Http\Controllers\Teacher\GradeController::class, 'edit'])->name('edit');
        Route::put('/{grade}', [\App\Http\Controllers\Teacher\GradeController::class, 'update'])->name('update');
        Route::post('/{grade}/submit', [\App\Http\Controllers\Teacher\GradeController::class, 'submit'])->name('submit');
        Route::delete('/{grade}', [\App\Http\Controllers\Teacher\GradeController::class, 'destroy'])->name('destroy');
        Route::get('/ajax/students', [\App\Http\Controllers\Teacher\GradeController::class, 'getStudents'])->name('get-students');
    });
    
    // Teacher Exam Management
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
    
    // Teacher Homework Management
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
    
    // Teacher's assigned students
    Route::get('/teacher/students', [\App\Http\Controllers\Teacher\StudentController::class, 'index'])->name('teacher.students');
    Route::get('/teacher/students/{student}', [\App\Http\Controllers\Teacher\StudentController::class, 'show'])->name('teacher.students.show');
    
    // Teacher's assigned subjects
    Route::get('/teacher/subjects', [\App\Http\Controllers\Teacher\SubjectController::class, 'index'])->name('teacher.subjects');
    Route::get('/teacher/subjects/{subject}', [\App\Http\Controllers\Teacher\SubjectController::class, 'show'])->name('teacher.subjects.show');
    
    // Teacher's assigned classes
    Route::get('/teacher/classes', [\App\Http\Controllers\Teacher\ClassController::class, 'index'])->name('teacher.classes');
    Route::get('/teacher/classes/{class}', [\App\Http\Controllers\Teacher\ClassController::class, 'show'])->name('teacher.classes.show');
    
    // Teacher Grade Management
    Route::prefix('teacher/grades')->name('teacher.grades.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Teacher\GradeManagementController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Teacher\GradeManagementController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Teacher\GradeManagementController::class, 'store'])->name('store');
        Route::get('/bulk-create', [\App\Http\Controllers\Teacher\GradeManagementController::class, 'bulkCreate'])->name('bulk-create');
        Route::post('/bulk-store', [\App\Http\Controllers\Teacher\GradeManagementController::class, 'bulkStore'])->name('bulk-store');
        Route::get('/{grade}/edit', [\App\Http\Controllers\Teacher\GradeManagementController::class, 'edit'])->name('edit');
        Route::put('/{grade}', [\App\Http\Controllers\Teacher\GradeManagementController::class, 'update'])->name('update');
        Route::post('/{grade}/approve', [\App\Http\Controllers\Teacher\GradeManagementController::class, 'approve'])->name('approve');
        Route::post('/{grade}/reject', [\App\Http\Controllers\Teacher\GradeManagementController::class, 'reject'])->name('reject');
        Route::get('/students', [\App\Http\Controllers\Teacher\GradeManagementController::class, 'getStudents'])->name('students');
        Route::post('/export', [\App\Http\Controllers\Teacher\GradeManagementController::class, 'export'])->name('export');
    });
    
    // Teacher Profile Management
    Route::get('/teacher/profile', [\App\Http\Controllers\Teacher\ProfileController::class, 'show'])->name('teacher.profile');
    Route::get('/teacher/profile/edit', [\App\Http\Controllers\Teacher\ProfileController::class, 'edit'])->name('teacher.profile.edit');
    Route::put('/teacher/profile', [\App\Http\Controllers\Teacher\ProfileController::class, 'update'])->name('teacher.profile.update');

    // Teacher Grades
    Route::get('/teacher/grades', [\App\Http\Controllers\Teacher\GradeController::class, 'index'])->name('teacher.grades.index');
    Route::get('/teacher/grades/create', [\App\Http\Controllers\Teacher\GradeController::class, 'create'])->name('teacher.grades.create');
    Route::post('/teacher/grades', [\App\Http\Controllers\Teacher\GradeController::class, 'store'])->name('teacher.grades.store');
    // AJAX: search eligible students by class and subject
    Route::get('/teacher/grades/eligible-students', [\App\Http\Controllers\Teacher\StudentController::class, 'searchEligible'])->name('teacher.grades.eligible');
    Route::get('/teacher/grades/subjects', [\App\Http\Controllers\Teacher\GradeController::class, 'getSubjects'])->name('teacher.grades.subjects');
    Route::get('/teacher/grades/students', [\App\Http\Controllers\Teacher\GradeController::class, 'getEligibleStudents'])->name('teacher.grades.students');
    
    // Exam Questions
    Route::get('/teacher/grades/exam-questions', [\App\Http\Controllers\Teacher\GradeController::class, 'examQuestions'])->name('teacher.grades.exam-questions');
    Route::get('/teacher/grades/exam-questions/create', [\App\Http\Controllers\Teacher\GradeController::class, 'createExamQuestions'])->name('teacher.grades.exam-questions.create');
    Route::post('/teacher/grades/exam-questions', [\App\Http\Controllers\Teacher\GradeController::class, 'storeExamQuestions'])->name('teacher.grades.exam-questions.store');
    Route::post('/teacher/grades/exam-questions/{examPaper}/publish', [\App\Http\Controllers\Teacher\GradeController::class, 'publishExam'])->name('teacher.grades.exam-questions.publish');
    Route::post('/teacher/grades/exam-questions/{examPaper}/send', [\App\Http\Controllers\Teacher\GradeController::class, 'sendExamToClass'])->name('teacher.grades.exam-questions.send');

    // Teacher Exams
    Route::get('/teacher/exams', [ExamController::class, 'teacherExams'])->name('teacher.exams.index');
    Route::get('/teacher/exams/upcoming', [ExamController::class, 'teacherUpcoming'])->name('teacher.exams.upcoming');
    Route::get('/teacher/exams/{examSchedule}/marks', [ExamController::class, 'teacherMarks'])->name('teacher.exams.marks');
    Route::post('/teacher/exams/{examSchedule}/marks', [ExamController::class, 'storeTeacherMarks'])->name('teacher.exams.marks.store');
    Route::get('/teacher/exams/create', [ExamController::class, 'create'])->name('teacher.exams.create');
    Route::post('/teacher/exams', [ExamController::class, 'store'])->name('teacher.exams.store');
    
    // Teacher Exam Management (New)
    Route::get('/teacher/exams/{examSchedule}', [ExamController::class, 'show'])->name('teacher.exams.show');
    Route::post('/teacher/exams/{examSchedule}/questions', [ExamController::class, 'addQuestions'])->name('teacher.exams.questions.store');
    Route::get('/teacher/exams/{examSchedule}/submissions', [ExamController::class, 'showSubmissions'])->name('teacher.exams.submissions');
    Route::get('/teacher/exams/attempts/{attempt}/mark', [ExamController::class, 'showForMarking'])->name('teacher.exams.mark');
    Route::post('/teacher/exams/attempts/{attempt}/mark', [ExamController::class, 'markExam'])->name('teacher.exams.mark.store');
});

// Dashboard routes with authentication middleware
Route::middleware(['auth'])->group(function () {
    Route::get('/student/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
    
    // Student Grade Access
    Route::prefix('student/grades')->name('student.grades.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\GradeController::class, 'index'])->name('index');
        Route::get('/{grade}', [\App\Http\Controllers\Student\GradeController::class, 'show'])->name('show');
        Route::get('/transcript/view', [\App\Http\Controllers\Student\GradeController::class, 'transcript'])->name('transcript');
        Route::get('/transcript/download', [\App\Http\Controllers\Student\GradeController::class, 'downloadTranscript'])->name('download-transcript');
    });
    
    // Student Exam Access
    Route::prefix('student/exams')->name('student.exams.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\ExamController::class, 'index'])->name('index');
        Route::get('/{exam}', [\App\Http\Controllers\Student\ExamController::class, 'show'])->name('show');
        Route::post('/{exam}/start', [\App\Http\Controllers\Student\ExamController::class, 'start'])->name('start');
        Route::get('/{exam}/take/{attempt}', [\App\Http\Controllers\Student\ExamController::class, 'take'])->name('take');
        Route::post('/{exam}/submit/{attempt}', [\App\Http\Controllers\Student\ExamController::class, 'submit'])->name('submit');
        Route::get('/{exam}/result/{attempt}', [\App\Http\Controllers\Student\ExamController::class, 'result'])->name('result');
        Route::post('/{exam}/save-answer/{attempt}', [\App\Http\Controllers\Student\ExamController::class, 'saveAnswer'])->name('save-answer');
    });
    
    // Student Homework Access
    Route::prefix('student/homework')->name('student.homework.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\HomeworkController::class, 'index'])->name('index');
        Route::get('/{assignment}', [\App\Http\Controllers\Student\HomeworkController::class, 'show'])->name('show');
        Route::get('/{assignment}/submit', [\App\Http\Controllers\Student\HomeworkController::class, 'create'])->name('create');
        Route::post('/{assignment}/submit', [\App\Http\Controllers\Student\HomeworkController::class, 'store'])->name('store');
    });
    
    // Student Exams
    Route::get('/student/exams', [ExamController::class, 'index'])->name('student.exams.index');
    Route::get('/student/exams/upcoming', [ExamController::class, 'upcomingExams'])->name('student.exams.upcoming');
    Route::get('/student/exams/{examSchedule}/take', [ExamController::class, 'showForStudent'])->name('student.exams.take');
    Route::post('/student/exams/{examSchedule}/start', [ExamController::class, 'startExam'])->name('student.exams.start');
    Route::post('/student/exams/{examSchedule}/submit', [ExamController::class, 'submitExam'])->name('student.exams.submit');
    Route::get('/student/exams/attempts/{attempt}/results', [ExamController::class, 'showResults'])->name('student.exams.results');
    Route::get('/student/exams/marks', [ExamController::class, 'myMarks'])->name('student.exams.marks');
    
    // Student Profile and Settings
    Route::get('/student/profile', [UserController::class, 'profile'])->name('student.profile');
    Route::get('/student/profile/edit', [UserController::class, 'editProfile'])->name('student.profile.edit');
    Route::put('/student/profile', [UserController::class, 'updateProfile'])->name('student.profile.update');
    Route::get('/student/change-password', [UserController::class, 'changePasswordForm'])->name('student.change-password');
    Route::post('/student/change-password', [UserController::class, 'changePassword'])->name('student.change-password.update');
    
    // Student Subjects (view only)
    Route::get('/student/subjects', [\App\Http\Controllers\Student\SubjectController::class, 'index'])->name('student.subjects.index');
    Route::get('/student/subjects/{subject}', [\App\Http\Controllers\Student\SubjectController::class, 'show'])->name('student.subjects.show');
    
    // Student Teachers (view only)
    Route::get('/student/teachers', [\App\Http\Controllers\Student\TeacherController::class, 'index'])->name('student.teachers.index');
    
    // Student-specific attendance routes
    Route::get('/student/attendance', [\App\Http\Controllers\Student\AttendanceController::class, 'index'])->name('student.attendance.index');
    Route::get('/student/attendance/{id}', [\App\Http\Controllers\Student\AttendanceController::class, 'show'])->name('student.attendance.show');
    
    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    
    Route::get('/finance/dashboard', [FinanceController::class, 'dashboard'])->name('finance.dashboard');
    // Student Finance
    Route::get('/student/dashboard/finance', [\App\Http\Controllers\Student\FinanceController::class, 'index'])->name('student.finance.index');
    Route::get('/student/dashboard/finance/{fee}/pay', [\App\Http\Controllers\Student\FinanceController::class, 'createPayment'])->name('student.finance.create-payment');
    Route::post('/student/dashboard/finance/{fee}/pay', [\App\Http\Controllers\Student\FinanceController::class, 'storePayment'])->name('student.finance.store-payment');
    // Student invoice download
    Route::get('/student/invoices/{studentFee}/download', [\App\Http\Controllers\Finance\InvoiceController::class, 'download'])->name('student.invoices.download');
    // Student Gradesheet
    Route::get('/student/gradesheet', [\App\Http\Controllers\Student\GradesheetController::class, 'show'])->name('student.gradesheet.show');
    Route::get('/student/gradesheet/pdf', [\App\Http\Controllers\Student\GradesheetController::class, 'pdf'])->name('student.gradesheet.pdf');
    
    // Student Library
    Route::prefix('student/library')->name('student.library.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\LibraryController::class, 'index'])->name('index');
        Route::get('/search', [\App\Http\Controllers\Student\LibraryController::class, 'search'])->name('search');
        Route::post('/borrow/{bookId}', [\App\Http\Controllers\Student\LibraryController::class, 'borrow'])->name('borrow');
        Route::post('/return/{bookId}', [\App\Http\Controllers\Student\LibraryController::class, 'return'])->name('return');
    });
    
    // Student Transport
    Route::prefix('student/transport')->name('student.transport.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\TransportController::class, 'index'])->name('index');
        Route::get('/routes', [\App\Http\Controllers\Student\TransportController::class, 'routes'])->name('routes');
        Route::post('/request', [\App\Http\Controllers\Student\TransportController::class, 'request'])->name('request');
    });
    
    // Student Hostel
    Route::prefix('student/hostel')->name('student.hostel.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\HostelController::class, 'index'])->name('index');
        Route::get('/rooms', [\App\Http\Controllers\Student\HostelController::class, 'rooms'])->name('rooms');
        Route::post('/request', [\App\Http\Controllers\Student\HostelController::class, 'request'])->name('request');
        Route::get('/payments', [\App\Http\Controllers\Student\HostelController::class, 'payments'])->name('payments');
    });
    
    // Student Settings
    Route::prefix('student/settings')->name('student.settings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\SettingsController::class, 'index'])->name('index');
        Route::put('/profile', [\App\Http\Controllers\Student\SettingsController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [\App\Http\Controllers\Student\SettingsController::class, 'changePassword'])->name('password.update');
        Route::get('/notifications', [\App\Http\Controllers\Student\SettingsController::class, 'notifications'])->name('notifications');
        Route::put('/notifications', [\App\Http\Controllers\Student\SettingsController::class, 'updateNotifications'])->name('notifications.update');
        Route::get('/privacy', [\App\Http\Controllers\Student\SettingsController::class, 'privacy'])->name('privacy');
        Route::put('/privacy', [\App\Http\Controllers\Student\SettingsController::class, 'updatePrivacy'])->name('privacy.update');
    });
});

// Simple test routes (removed for production)
// Protected routes requiring authentication
Route::middleware([\App\Http\Middleware\Authenticate::class])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        // Students Management
        Route::get('/students', [\App\Http\Controllers\Admin\StudentController::class, 'index'])->name('students.index');
        Route::get('/students/create', [\App\Http\Controllers\Admin\StudentController::class, 'create'])->name('students.create');
        Route::post('/students', [\App\Http\Controllers\Admin\StudentController::class, 'store'])->name('students.store');
        Route::get('/students/{student}', [\App\Http\Controllers\Admin\StudentController::class, 'show'])->name('students.show');
        Route::get('/students/{student}/edit', [\App\Http\Controllers\Admin\StudentController::class, 'edit'])->name('students.edit');
        Route::put('/students/{student}', [\App\Http\Controllers\Admin\StudentController::class, 'update'])->name('students.update');
        Route::delete('/students/{student}', [\App\Http\Controllers\Admin\StudentController::class, 'destroy'])->name('students.destroy');
        
        // Teachers Management
        Route::get('/teachers', [\App\Http\Controllers\Admin\TeacherController::class, 'index'])->name('teachers.index');
        Route::get('/teachers/create', [\App\Http\Controllers\Admin\TeacherController::class, 'create'])->name('teachers.create');
        Route::post('/teachers', [\App\Http\Controllers\Admin\TeacherController::class, 'store'])->name('teachers.store');
        Route::get('/teachers/{teacher}', [\App\Http\Controllers\Admin\TeacherController::class, 'show'])->name('teachers.show');
        Route::get('/teachers/{teacher}/edit', [\App\Http\Controllers\Admin\TeacherController::class, 'edit'])->name('teachers.edit');
        Route::put('/teachers/{teacher}', [\App\Http\Controllers\Admin\TeacherController::class, 'update'])->name('teachers.update');
        Route::delete('/teachers/{teacher}', [\App\Http\Controllers\Admin\TeacherController::class, 'destroy'])->name('teachers.destroy');
        
        // Classes Management
        Route::get('/classes', [\App\Http\Controllers\Admin\ClassController::class, 'index'])->name('classes.index');
        Route::get('/classes/create', [\App\Http\Controllers\Admin\ClassController::class, 'create'])->name('classes.create');
        Route::post('/classes', [\App\Http\Controllers\Admin\ClassController::class, 'store'])->name('classes.store');
        Route::get('/classes/{class}', [\App\Http\Controllers\Admin\ClassController::class, 'show'])->name('classes.show');
        Route::get('/classes/{class}/edit', [\App\Http\Controllers\Admin\ClassController::class, 'edit'])->name('classes.edit');
        Route::put('/classes/{class}', [\App\Http\Controllers\Admin\ClassController::class, 'update'])->name('classes.update');
        Route::delete('/classes/{class}', [\App\Http\Controllers\Admin\ClassController::class, 'destroy'])->name('classes.destroy');
        
        // Subjects Management
        Route::get('/subjects', [\App\Http\Controllers\Admin\SubjectController::class, 'index'])->name('subjects.index');
        Route::get('/subjects/create', [\App\Http\Controllers\Admin\SubjectController::class, 'create'])->name('subjects.create');
        Route::post('/subjects', [\App\Http\Controllers\Admin\SubjectController::class, 'store'])->name('subjects.store');
        Route::get('/subjects/{subject}', [\App\Http\Controllers\Admin\SubjectController::class, 'show'])->name('subjects.show');
        Route::get('/subjects/{subject}/edit', [\App\Http\Controllers\Admin\SubjectController::class, 'edit'])->name('subjects.edit');
        Route::put('/subjects/{subject}', [\App\Http\Controllers\Admin\SubjectController::class, 'update'])->name('subjects.update');
        Route::delete('/subjects/{subject}', [\App\Http\Controllers\Admin\SubjectController::class, 'destroy'])->name('subjects.destroy');
        
        // Exams Management
        Route::get('/exams/types', [ExamController::class, 'types'])->name('exams.types.index');
        Route::get('/exams/schedules', [ExamController::class, 'schedules'])->name('exams.schedules.index');
        Route::get('/exams/marks', [ExamController::class, 'marks'])->name('exams.marks.index');
        
        // Fees Management
        Route::get('/fees/structures', [FeeController::class, 'structures'])->name('fees.structures.index');
        Route::get('/fees/payments', [FeeController::class, 'payments'])->name('fees.payments.index');
        Route::get('/fees/reports', [FeeController::class, 'reports'])->name('fees.reports');
        Route::post('/fees/reports/push', [FeeController::class, 'pushReport'])->name('fees.reports.push');
        Route::get('/fees/reports/submit', [FeeController::class, 'submitReportPage'])->name('fees.reports.submit');
        
        // Library Management
        Route::get('/library', [LibraryController::class, 'index'])->name('library.index');
        
        // Hostel Management
        Route::get('/hostel', [HostelController::class, 'index'])->name('hostel.index');
        
        // Attendance Management
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/students', [AttendanceController::class, 'adminStudentAttendance'])->name('attendance.students');
        Route::get('/attendance/teachers', [AttendanceController::class, 'adminTeacherAttendance'])->name('attendance.teachers');
        Route::get('/attendance/finance', [AttendanceController::class, 'adminFinanceAttendance'])->name('attendance.finance');
        
        // Reports Management
        Route::get('/reports', function() {
            return view('admin.reports.index');
        })->name('reports.index');

        // Grade Approvals
        Route::get('/grades', [\App\Http\Controllers\Admin\GradeApprovalController::class, 'index'])->name('grades.index');
        Route::get('/grades/{grade}', [\App\Http\Controllers\Admin\GradeApprovalController::class, 'show'])->name('grades.show');
        Route::post('/grades/{grade}/approve', [\App\Http\Controllers\Admin\GradeApprovalController::class, 'approve'])->name('grades.approve');
        Route::post('/grades/{grade}/reject', [\App\Http\Controllers\Admin\GradeApprovalController::class, 'reject'])->name('grades.reject');
        
        // Settings Management
        Route::get('/settings', function() {
            $settings = [
                'school_name' => 'Liberia School Management System',
                'school_code' => 'LSMS001',
                'academic_year' => '2024-2025',
                'semester' => '1',
                'grading_system' => 'percentage',
                'passing_grade' => '50',
                'timezone' => 'UTC',
                'date_format' => 'Y-m-d',
                'language' => 'en',
                'pagination_limit' => '20',
                'email_notifications' => true,
                'sms_notifications' => false,
                'push_notifications' => true,
            ];
            return view('admin.settings.index', compact('settings'));
        })->name('settings.index');

        // Finance Officers Management
        Route::get('/finance-officers', [\App\Http\Controllers\Admin\FinanceOfficerController::class, 'index'])->name('finance_officers.index');
        Route::get('/finance-officers/create', [\App\Http\Controllers\Admin\FinanceOfficerController::class, 'create'])->name('finance_officers.create');
        Route::post('/finance-officers', [\App\Http\Controllers\Admin\FinanceOfficerController::class, 'store'])->name('finance_officers.store');
    });
    
    // Admin-only routes for creating and managing entities
    Route::middleware('admin')->group(function () {
        // Quick create routes for dashboard
        Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
        Route::get('/classes/create', [ClassController::class, 'create'])->name('classes.create');
        Route::get('/exams/create', [ExamController::class, 'create'])->name('exams.create');
        
        // Students Management (Admin Only)
        Route::resource('students', StudentController::class);
        Route::get('/students/{student}/attendance', [StudentController::class, 'attendance'])->name('students.attendance');
        Route::get('/students/{student}/exams', [StudentController::class, 'exams'])->name('students.exams');
        Route::get('/students/{student}/fees', [StudentController::class, 'fees'])->name('students.fees');
        Route::get('/students/{student}/homework', [StudentController::class, 'homework'])->name('students.homework');
        Route::get('/students/{student}/timeline', [StudentController::class, 'timeline'])->name('students.timeline');
        Route::post('/students/{student}/documents', [StudentController::class, 'uploadDocuments'])->name('students.documents.upload');
        
        // Teachers Management (Admin Only) - Routes are defined in the first admin middleware group
        
        // Classes Management (Admin Only)
        Route::resource('classes', ClassController::class);
        Route::get('/classes/{class}/students', [ClassController::class, 'students'])->name('classes.students');
        Route::get('/classes/{class}/subjects', [ClassController::class, 'subjects'])->name('classes.subjects');
        Route::get('/classes/{class}/attendance', [ClassController::class, 'attendance'])->name('classes.attendance');
        Route::get('/classes/{class}/schedule', [ClassController::class, 'schedule'])->name('classes.schedule');
        Route::post('/classes/{class}/students', [ClassController::class, 'addStudent'])->name('classes.students.add');
        Route::delete('/classes/{class}/students/{student}', [ClassController::class, 'removeStudent'])->name('classes.students.remove');
        
        // Subjects Management (Admin Only)
        Route::resource('subjects', SubjectController::class);
        Route::get('/subjects/{subject}/students', [SubjectController::class, 'students'])->name('subjects.students');
        Route::get('/subjects/{subject}/teachers', [SubjectController::class, 'teachers'])->name('subjects.teachers');
        Route::get('/subjects/{subject}/materials', [SubjectController::class, 'materials'])->name('subjects.materials');
    });
    
    // Fees (Admin/Finance)
    Route::prefix('finance')->name('finance.')->middleware('finance')->group(function () {
        Route::get('/fee-items', [\App\Http\Controllers\Finance\FeeItemController::class, 'index'])->name('fee-items.index');
        Route::get('/fee-items/create', [\App\Http\Controllers\Finance\FeeItemController::class, 'create'])->name('fee-items.create');
        Route::post('/fee-items', [\App\Http\Controllers\Finance\FeeItemController::class, 'store'])->name('fee-items.store');
        Route::get('/fee-items/{feeItem}', [\App\Http\Controllers\Finance\FeeItemController::class, 'show'])->name('fee-items.show');
        Route::get('/fee-items/{feeItem}/edit', [\App\Http\Controllers\Finance\FeeItemController::class, 'edit'])->name('fee-items.edit');
        Route::put('/fee-items/{feeItem}', [\App\Http\Controllers\Finance\FeeItemController::class, 'update'])->name('fee-items.update');
        Route::delete('/fee-items/{feeItem}', [\App\Http\Controllers\Finance\FeeItemController::class, 'destroy'])->name('fee-items.destroy');

        Route::get('/invoices/create', [\App\Http\Controllers\Finance\InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices/bulk-send', [\App\Http\Controllers\Finance\InvoiceController::class, 'bulkSend'])->name('invoices.bulk-send');
        Route::get('/invoices/{studentFee}/download', [\App\Http\Controllers\Finance\InvoiceController::class, 'download'])->name('invoices.download');

        // Payment approvals
        Route::get('/payments', [\App\Http\Controllers\Finance\PaymentApprovalController::class, 'index'])->name('payments.index');
        Route::post('/payments/{payment}/approve', [\App\Http\Controllers\Finance\PaymentApprovalController::class, 'approve'])->name('payments.approve');
        Route::post('/payments/{payment}/reject', [\App\Http\Controllers\Finance\PaymentApprovalController::class, 'reject'])->name('payments.reject');

        // Finance-accessible fee structures and reports (no admin privilege required)
        Route::get('/fees/structures', [\App\Http\Controllers\FeeController::class, 'structures'])->name('fees.structures.index');
        Route::get('/fees/reports', [\App\Http\Controllers\FeeController::class, 'reports'])->name('fees.reports');
        Route::post('/fees/reports/push', [\App\Http\Controllers\FeeController::class, 'pushReport'])->name('fees.reports.push');
        Route::get('/fees/reports/submit', [\App\Http\Controllers\FeeController::class, 'submitReportPage'])->name('fees.reports.submit');
        
        // Scholarships Management
        Route::get('/scholarships', [\App\Http\Controllers\Finance\ScholarshipController::class, 'index'])->name('scholarships.index');
        Route::get('/scholarships/create', [\App\Http\Controllers\Finance\ScholarshipController::class, 'create'])->name('scholarships.create');
        Route::post('/scholarships', [\App\Http\Controllers\Finance\ScholarshipController::class, 'store'])->name('scholarships.store');
        Route::get('/scholarships/{id}', [\App\Http\Controllers\Finance\ScholarshipController::class, 'show'])->name('scholarships.show');
        Route::get('/scholarships/{id}/edit', [\App\Http\Controllers\Finance\ScholarshipController::class, 'edit'])->name('scholarships.edit');
        Route::put('/scholarships/{id}', [\App\Http\Controllers\Finance\ScholarshipController::class, 'update'])->name('scholarships.update');
        Route::delete('/scholarships/{id}', [\App\Http\Controllers\Finance\ScholarshipController::class, 'destroy'])->name('scholarships.destroy');
        
        // Financial Reports (Finance Officer Access)
        Route::get('/reports/financial', [\App\Http\Controllers\Finance\ReportController::class, 'financial'])->name('reports.financial');
    });
    
    // Exams (Admin Only)
    Route::prefix('exams')->name('exams.')->middleware('admin')->group(function () {
        Route::get('/upcoming', [ExamController::class, 'upcomingExams'])->name('upcoming');
        Route::get('/types', [ExamController::class, 'types'])->name('types.index');
        Route::get('/types/create', [ExamController::class, 'createType'])->name('types.create');
        Route::post('/types', [ExamController::class, 'storeType'])->name('types.store');
        Route::get('/types/{type}', [ExamController::class, 'showType'])->name('types.show');
        Route::get('/types/{type}/edit', [ExamController::class, 'editType'])->name('types.edit');
        Route::put('/types/{type}', [ExamController::class, 'updateType'])->name('types.update');
        Route::delete('/types/{type}', [ExamController::class, 'destroyType'])->name('types.destroy');
        
        Route::get('/schedules', [ExamController::class, 'schedules'])->name('schedules.index');
        Route::get('/schedules/create', [ExamController::class, 'createSchedule'])->name('schedules.create');
        Route::post('/schedules', [ExamController::class, 'storeSchedule'])->name('schedules.store');
        Route::get('/schedules/{schedule}', [ExamController::class, 'showSchedule'])->name('schedules.show');
        Route::get('/schedules/{schedule}/edit', [ExamController::class, 'editSchedule'])->name('schedules.edit');
        Route::put('/schedules/{schedule}', [ExamController::class, 'updateSchedule'])->name('schedules.update');
        Route::delete('/schedules/{schedule}', [ExamController::class, 'destroySchedule'])->name('schedules.destroy');
        
        Route::get('/marks', [ExamController::class, 'marks'])->name('marks.index');
        Route::get('/marks/create', [ExamController::class, 'createMark'])->name('marks.create');
        Route::post('/marks', [ExamController::class, 'storeMark'])->name('marks.store');
        Route::get('/marks/{mark}', [ExamController::class, 'showMark'])->name('marks.show');
        Route::get('/marks/{mark}/edit', [ExamController::class, 'editMark'])->name('marks.edit');
        Route::put('/marks/{mark}', [ExamController::class, 'updateMark'])->name('marks.update');
        Route::delete('/marks/{mark}', [ExamController::class, 'destroyMark'])->name('marks.destroy');
        
        // Student marks route
        Route::get('/student-marks', [ExamController::class, 'studentMarks'])->name('student-marks');
        
        Route::get('/reports', [ExamController::class, 'reports'])->name('reports');
    });
    
    // Library (Admin Only)
    Route::prefix('library')->name('library.')->middleware('admin')->group(function () {
        Route::get('/', [LibraryController::class, 'index'])->name('index');
        Route::resource('books', LibraryController::class)->except(['index']);
        Route::get('/categories', [LibraryController::class, 'categories'])->name('categories');
        Route::get('/issues', [LibraryController::class, 'issues'])->name('issues.index');
        Route::get('/issues/create', [LibraryController::class, 'createIssue'])->name('issues.create');
        Route::post('/issues', [LibraryController::class, 'storeIssue'])->name('issues.store');
        Route::put('/issues/{issue}/return', [LibraryController::class, 'returnBook'])->name('issues.return');
        Route::get('/members', [LibraryController::class, 'members'])->name('members');
    });
    
    // Transport (Admin Only)
    Route::prefix('transport')->name('transport.')->middleware('admin')->group(function () {
        Route::get('/', [TransportController::class, 'index'])->name('index');
        Route::get('/routes', [TransportController::class, 'routes'])->name('routes.index');
        Route::get('/routes/create', [TransportController::class, 'createRoute'])->name('routes.create');
        Route::post('/routes', [TransportController::class, 'storeRoute'])->name('routes.store');
        Route::get('/routes/{route}', [TransportController::class, 'showRoute'])->name('routes.show');
        Route::get('/routes/{route}/edit', [TransportController::class, 'editRoute'])->name('routes.edit');
        Route::put('/routes/{route}', [TransportController::class, 'updateRoute'])->name('routes.update');
        Route::delete('/routes/{route}', [TransportController::class, 'destroyRoute'])->name('routes.destroy');
        
        Route::get('/vehicles', [TransportController::class, 'vehicles'])->name('vehicles.index');
        Route::get('/vehicles/create', [TransportController::class, 'createVehicle'])->name('vehicles.create');
        Route::post('/vehicles', [TransportController::class, 'storeVehicle'])->name('vehicles.store');
        Route::get('/vehicles/{vehicle}', [TransportController::class, 'showVehicle'])->name('vehicles.show');
        Route::get('/vehicles/{vehicle}/edit', [TransportController::class, 'editVehicle'])->name('vehicles.edit');
        Route::put('/vehicles/{vehicle}', [TransportController::class, 'updateVehicle'])->name('vehicles.update');
        Route::delete('/vehicles/{vehicle}', [TransportController::class, 'destroyVehicle'])->name('vehicles.destroy');
    });
    
    // Hostel (Admin Only)
    Route::prefix('hostel')->name('hostel.')->middleware('admin')->group(function () {
        Route::get('/', [HostelController::class, 'index'])->name('index');
        Route::get('/create-hostel', [HostelController::class, 'createHostel'])->name('create-hostel');
        Route::post('/hostels', [HostelController::class, 'storeHostel'])->name('store');
        Route::get('/rooms', [HostelController::class, 'rooms'])->name('rooms.index');
        Route::get('/rooms/create', [HostelController::class, 'createRoom'])->name('rooms.create');
        Route::post('/rooms', [HostelController::class, 'storeRoom'])->name('rooms.store');
        Route::get('/rooms/{room}', [HostelController::class, 'showRoom'])->name('rooms.show');
        Route::get('/rooms/{room}/edit', [HostelController::class, 'editRoom'])->name('rooms.edit');
        Route::put('/rooms/{room}', [HostelController::class, 'updateRoom'])->name('rooms.update');
        Route::delete('/rooms/{room}', [HostelController::class, 'destroyRoom'])->name('rooms.destroy');
        
        Route::get('/room-types', [HostelController::class, 'roomTypes'])->name('room-types');
    });
    
    // Users (Admin Only)
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/users/{user}/profile', [UserController::class, 'profile'])->name('users.profile');
        Route::get('/users/{user}/profile/edit', [UserController::class, 'editProfile'])->name('users.profile.edit');
        Route::put('/users/{user}/profile', [UserController::class, 'updateProfile'])->name('users.profile.update');
        Route::get('/users/{user}/change-password', [UserController::class, 'changePasswordForm'])->name('users.change-password');
        Route::match(['post','put'], '/users/{user}/change-password', [UserController::class, 'changePassword'])->name('users.change-password.update');
        Route::post('/users/reset-all-passwords', [UserController::class, 'resetAllPasswords'])->name('users.reset-all-passwords');
    });

    // Self profile (All authenticated users including finance officers)
    Route::get('/me/profile', [UserController::class, 'profile'])->name('me.profile');
    Route::get('/me/profile/edit', [UserController::class, 'editProfile'])->name('me.profile.edit');
    Route::put('/me/profile', [UserController::class, 'updateProfile'])->name('me.profile.update');
    Route::get('/me/change-password', [UserController::class, 'changePasswordForm'])->name('me.change-password');
    Route::match(['post','put'], '/me/change-password', [UserController::class, 'changePassword'])->name('me.change-password.update');
    
    // Settings (Admin Only)
    Route::middleware('admin')->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::get('/settings/general', [SettingController::class, 'general'])->name('settings.general');
        Route::put('/settings/general', [SettingController::class, 'updateGeneral'])->name('settings.general.update');
        Route::get('/settings/academic', [SettingController::class, 'academic'])->name('settings.academic');
        Route::put('/settings/academic', [SettingController::class, 'updateAcademic'])->name('settings.academic.update');
        Route::get('/settings/financial', [SettingController::class, 'financial'])->name('settings.financial');
        Route::put('/settings/financial', [SettingController::class, 'updateFinancial'])->name('settings.financial.update');
    });
    
    // Attendance
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        // Student attendance
        Route::get('/student', [AttendanceController::class, 'studentAttendance'])->name('student');
        Route::post('/student', [AttendanceController::class, 'storeStudentAttendance'])->name('student.store');
        // Teacher attendance
        Route::get('/teacher', [AttendanceController::class, 'teacherAttendance'])->name('teacher');
        Route::post('/teacher', [AttendanceController::class, 'storeTeacherAttendance'])->name('teacher.store');
        // Teacher-facing history
        Route::get('/history/my-students', [AttendanceController::class, 'myStudentAttendanceHistory'])->name('history.students');
        Route::get('/history/my-teacher', [AttendanceController::class, 'myTeacherAttendanceHistory'])->name('history.teacher');
        // Optional reports endpoints (if implemented later)
        // Route::get('/reports', [AttendanceController::class, 'reports'])->name('reports');
    });
    
    // Homework
    Route::prefix('homework')->name('homework.')->group(function () {
        Route::get('/', [HomeworkController::class, 'index'])->name('index');
        Route::get('/create', [HomeworkController::class, 'create'])->name('create');
        Route::post('/', [HomeworkController::class, 'store'])->name('store');
        Route::get('/{homework}', [HomeworkController::class, 'show'])->name('show');
        Route::get('/{homework}/edit', [HomeworkController::class, 'edit'])->name('edit');
        Route::put('/{homework}', [HomeworkController::class, 'update'])->name('update');
        Route::delete('/{homework}', [HomeworkController::class, 'destroy'])->name('destroy');
    });
    
    // Study Materials
    Route::prefix('study-materials')->name('study-materials.')->group(function () {
        Route::get('/', [StudyMaterialController::class, 'index'])->name('index');
        Route::get('/create', [StudyMaterialController::class, 'create'])->name('create');
        Route::post('/', [StudyMaterialController::class, 'store'])->name('store');
        Route::get('/{material}', [StudyMaterialController::class, 'show'])->name('show');
        Route::get('/{material}/edit', [StudyMaterialController::class, 'edit'])->name('edit');
        Route::put('/{material}', [StudyMaterialController::class, 'update'])->name('update');
        Route::delete('/{material}', [StudyMaterialController::class, 'destroy'])->name('destroy');
        Route::get('/{material}/download', [StudyMaterialController::class, 'download'])->name('download');
    });
    
    // File uploads
    Route::post('/upload', function (Illuminate\Http\Request $request) {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('uploads', 'public');
            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => asset('storage/' . $path),
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'type' => $file->getMimeType(),
            ]);
        }
        return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
    })->name('upload');
    
    // Notifications route removed - using controller instead
    
    // Realtime notifications (JSON for polling)
    Route::get('/notifications.json', function (Illuminate\Http\Request $request) {
        $user = $request->user();
        if (!$user) {
            return response()->json([]);
        }
        try {
            if (method_exists($user, 'notifications')) {
                $items = $user->notifications()
                    ->latest()
                    ->limit(10)
                    ->get(['id', 'data', 'created_at', 'read_at']);
                return response()->json($items);
            }
        } catch (\Throwable $e) {
            return response()->json([]);
        }
        return response()->json([]);
    })->name('notifications.json');
    
    Route::put('/notifications/{id}/read', function (Illuminate\Http\Request $request, $id) {
        $user = $request->user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();
        return redirect()->back()->with('success', 'Notification marked as read');
    })->name('notifications.mark-read');
});

// Fallback route
Route::fallback(function () {
    return view('errors.404');
});
// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login.show');
    
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Debug route to check users (remove in production)
Route::get('/debug-users', function () {
    $users = \App\Models\User::all(['email', 'user_type']);
    return response()->json($users);
});

Route::get('/debug-csrf', function () {
    return response()->json([
        'csrf_token' => csrf_token(),
        'session_id' => session()->getId(),
        'session_data' => session()->all(),
        'meta_token' => request()->session()->token(),
        'session_regenerated' => request()->session()->regenerateToken(),
    ]);
});

Route::get('/debug-auth', function () {
    return response()->json([
        'authenticated' => auth()->check(),
        'user' => auth()->user() ? [
            'id' => auth()->user()->id,
            'email' => auth()->user()->email,
            'user_type' => auth()->user()->user_type,
            'name' => auth()->user()->name
        ] : null
    ]);
});

// One-time install endpoint to run migrations/seed without shell
Route::get('/ops/install', function (Illuminate\Http\Request $request) {
    $token = (string) $request->query('token');
    $expected = (string) config('app.install_token');
    abort_unless($token && $expected && hash_equals($expected, $token), 403);
    $out = [];
    try {
        Artisan::call('migrate', ['--force' => true]);
        $out['migrate'] = trim(Artisan::output());
        if ($request->boolean('seed')) {
            Artisan::call('db:seed', ['--force' => true]);
            $out['seed'] = trim(Artisan::output());
        }
        return response()->json(['ok' => true, 'output' => $out]);
    } catch (\Throwable $e) {
        return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
    }
})->name('ops.install');

Route::get('/test-login', function () {
    $credentials = ['email' => 'teacher@school.com', 'password' => 'password'];
    $attempt = \Illuminate\Support\Facades\Auth::attempt($credentials);
    return response()->json([
        'credentials' => $credentials,
        'attempt_result' => $attempt,
        'authenticated' => auth()->check(),
        'user' => auth()->user() ? [
            'id' => auth()->user()->id,
            'email' => auth()->user()->email,
            'user_type' => auth()->user()->user_type,
            'name' => auth()->user()->name
        ] : null
    ]);
});

// Test admin login directly
Route::get('/test-admin-login', function () {
    $credentials = ['email' => 'admin@school.com', 'password' => 'password'];
    $attempt = \Illuminate\Support\Facades\Auth::attempt($credentials);
    
    if ($attempt) {
        return redirect('/admin/dashboard');
    } else {
        return response()->json([
            'error' => 'Login failed',
            'credentials' => $credentials,
            'attempt_result' => $attempt
        ]);
    }
});

// Simple login form without CSRF
Route::get('/simple-login', function () {
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Simple Login</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; }
            .form-group { margin-bottom: 15px; }
            label { display: block; margin-bottom: 5px; }
            input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
            button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
            button:hover { background: #005a87; }
        </style>
    </head>
    <body>
        <h2>Simple Login Test</h2>
        <form method="POST" action="/simple-login-post">
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="admin@school.com" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" value="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
    </body>
    </html>';
});

Route::post('/simple-login-post', function (Illuminate\Http\Request $request) {
    $credentials = $request->only('email', 'password');
    $attempt = \Illuminate\Support\Facades\Auth::attempt($credentials);
    
    if ($attempt) {
        $user = auth()->user();
        switch ($user->user_type) {
            case 'admin':
                return redirect('/admin/dashboard');
            case 'teacher':
                return redirect('/teacher/dashboard');
            case 'student':
                return redirect('/student/dashboard');
            case 'finance':
                return redirect('/finance/dashboard');
            default:
                return redirect('/dashboard');
        }
    } else {
        return 'Login failed. <a href="/simple-login">Try again</a>';
    }
})->withoutMiddleware(['web']);

// Parent Routes (restricted access)
Route::middleware(['auth', 'parent'])->group(function () {
    // Parent Dashboard
    Route::get('/parent/dashboard', [DashboardController::class, 'index'])->name('parent.dashboard');
    
    // Parent Grade Access
    Route::prefix('parent/grades')->name('parent.grades.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Parent\GradeController::class, 'index'])->name('index');
        Route::get('/{grade}', [\App\Http\Controllers\Parent\GradeController::class, 'show'])->name('show');
        Route::get('/student/{student}', [\App\Http\Controllers\Parent\GradeController::class, 'studentGrades'])->name('student');
        Route::get('/student/{student}/subject/{subject}', [\App\Http\Controllers\Parent\GradeController::class, 'subjectGrades'])->name('subject');
        Route::get('/student/{student}/progress', [\App\Http\Controllers\Parent\GradeController::class, 'academicProgress'])->name('progress');
        Route::post('/student/{student}/download', [\App\Http\Controllers\Parent\GradeController::class, 'downloadReport'])->name('download');
    });
});
