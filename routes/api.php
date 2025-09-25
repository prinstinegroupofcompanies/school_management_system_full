<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\FeeController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\LibraryController;
use App\Http\Controllers\Api\TransportController;
use App\Http\Controllers\Api\HostelController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\RealtimeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/statistics', [DashboardController::class, 'statistics']);
    Route::get('/dashboard/recent-activities', [DashboardController::class, 'recentActivities']);

    // User management
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::get('/profile', [UserController::class, 'profile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::post('/change-password', [UserController::class, 'changePassword']);
        Route::get('/roles', [UserController::class, 'getRoles']);
        Route::get('/statistics', [UserController::class, 'getStatistics']);
    });

    // Student management
    Route::prefix('students')->group(function () {
        Route::get('/', [StudentController::class, 'index']);
        Route::post('/', [StudentController::class, 'store']);
        Route::get('/{id}', [StudentController::class, 'show']);
        Route::put('/{id}', [StudentController::class, 'update']);
        Route::delete('/{id}', [StudentController::class, 'destroy']);
        Route::get('/{id}/attendance', [StudentController::class, 'attendance']);
        Route::get('/{id}/exams', [StudentController::class, 'exams']);
        Route::get('/{id}/fees', [StudentController::class, 'fees']);
        Route::get('/{id}/homework', [StudentController::class, 'homework']);
        Route::get('/{id}/timeline', [StudentController::class, 'timeline']);
        Route::post('/{id}/documents', [StudentController::class, 'uploadDocuments']);
        Route::get('/statistics', [StudentController::class, 'statistics']);
    });

    // Teacher management
    Route::prefix('teachers')->group(function () {
        Route::get('/', [TeacherController::class, 'index']);
        Route::post('/', [TeacherController::class, 'store']);
        Route::get('/{id}', [TeacherController::class, 'show']);
        Route::put('/{id}', [TeacherController::class, 'update']);
        Route::delete('/{id}', [TeacherController::class, 'destroy']);
        Route::get('/{id}/classes', [TeacherController::class, 'classes']);
        Route::get('/{id}/subjects', [TeacherController::class, 'subjects']);
        Route::get('/{id}/attendance', [TeacherController::class, 'attendance']);
        Route::get('/{id}/schedule', [TeacherController::class, 'schedule']);
        Route::get('/statistics', [TeacherController::class, 'statistics']);
    });

    // Class management
    Route::prefix('classes')->group(function () {
        Route::get('/', [ClassController::class, 'index']);
        Route::post('/', [ClassController::class, 'store']);
        Route::get('/{id}', [ClassController::class, 'show']);
        Route::put('/{id}', [ClassController::class, 'update']);
        Route::delete('/{id}', [ClassController::class, 'destroy']);
        Route::get('/{id}/students', [ClassController::class, 'students']);
        Route::get('/{id}/subjects', [ClassController::class, 'subjects']);
        Route::get('/{id}/attendance', [ClassController::class, 'attendance']);
        Route::get('/{id}/schedule', [ClassController::class, 'schedule']);
        Route::post('/{id}/students', [ClassController::class, 'addStudent']);
        Route::delete('/{id}/students/{studentId}', [ClassController::class, 'removeStudent']);
        Route::get('/statistics', [ClassController::class, 'statistics']);
    });

    // Subject management
    Route::prefix('subjects')->group(function () {
        Route::get('/', [SubjectController::class, 'index']);
        Route::post('/', [SubjectController::class, 'store']);
        Route::get('/{id}', [SubjectController::class, 'show']);
        Route::put('/{id}', [SubjectController::class, 'update']);
        Route::delete('/{id}', [SubjectController::class, 'destroy']);
        Route::get('/{id}/students', [SubjectController::class, 'students']);
        Route::get('/{id}/teachers', [SubjectController::class, 'teachers']);
        Route::get('/{id}/materials', [SubjectController::class, 'materials']);
        Route::get('/statistics', [SubjectController::class, 'statistics']);
    });

    // Fee management
    Route::prefix('fees')->group(function () {
        Route::get('/structures', [FeeController::class, 'structures']);
        Route::post('/structures', [FeeController::class, 'storeStructure']);
        Route::get('/structures/{id}', [FeeController::class, 'showStructure']);
        Route::put('/structures/{id}', [FeeController::class, 'updateStructure']);
        Route::delete('/structures/{id}', [FeeController::class, 'destroyStructure']);
        Route::get('/payments', [FeeController::class, 'payments']);
        Route::post('/payments', [FeeController::class, 'storePayment']);
        Route::get('/payments/{id}', [FeeController::class, 'showPayment']);
        Route::put('/payments/{id}', [FeeController::class, 'updatePayment']);
        Route::delete('/payments/{id}', [FeeController::class, 'destroyPayment']);
        Route::get('/statistics', [FeeController::class, 'statistics']);
        Route::get('/reports', [FeeController::class, 'reports']);
    });

    // Exam management
    Route::prefix('exams')->group(function () {
        Route::get('/types', [ExamController::class, 'types']);
        Route::post('/types', [ExamController::class, 'storeType']);
        Route::get('/types/{id}', [ExamController::class, 'showType']);
        Route::put('/types/{id}', [ExamController::class, 'updateType']);
        Route::delete('/types/{id}', [ExamController::class, 'destroyType']);
        Route::get('/schedules', [ExamController::class, 'schedules']);
        Route::post('/schedules', [ExamController::class, 'storeSchedule']);
        Route::get('/schedules/{id}', [ExamController::class, 'showSchedule']);
        Route::put('/schedules/{id}', [ExamController::class, 'updateSchedule']);
        Route::delete('/schedules/{id}', [ExamController::class, 'destroySchedule']);
        Route::get('/marks', [ExamController::class, 'marks']);
        Route::post('/marks', [ExamController::class, 'storeMark']);
        Route::get('/marks/{id}', [ExamController::class, 'showMark']);
        Route::put('/marks/{id}', [ExamController::class, 'updateMark']);
        Route::delete('/marks/{id}', [ExamController::class, 'destroyMark']);
        Route::get('/statistics', [ExamController::class, 'statistics']);
        Route::get('/reports', [ExamController::class, 'reports']);
    });

    // Library management
    Route::prefix('library')->group(function () {
        Route::get('/books', [LibraryController::class, 'books']);
        Route::post('/books', [LibraryController::class, 'storeBook']);
        Route::get('/books/{id}', [LibraryController::class, 'showBook']);
        Route::put('/books/{id}', [LibraryController::class, 'updateBook']);
        Route::delete('/books/{id}', [LibraryController::class, 'destroyBook']);
        Route::get('/categories', [LibraryController::class, 'categories']);
        Route::get('/issues', [LibraryController::class, 'issues']);
        Route::post('/issues', [LibraryController::class, 'storeIssue']);
        Route::put('/issues/{id}/return', [LibraryController::class, 'returnBook']);
        Route::get('/members', [LibraryController::class, 'members']);
        Route::get('/statistics', [LibraryController::class, 'statistics']);
    });

    // Transport management
    Route::prefix('transport')->group(function () {
        Route::get('/routes', [TransportController::class, 'routes']);
        Route::post('/routes', [TransportController::class, 'storeRoute']);
        Route::get('/routes/{id}', [TransportController::class, 'showRoute']);
        Route::put('/routes/{id}', [TransportController::class, 'updateRoute']);
        Route::delete('/routes/{id}', [TransportController::class, 'destroyRoute']);
        Route::get('/vehicles', [TransportController::class, 'vehicles']);
        Route::post('/vehicles', [TransportController::class, 'storeVehicle']);
        Route::get('/vehicles/{id}', [TransportController::class, 'showVehicle']);
        Route::put('/vehicles/{id}', [TransportController::class, 'updateVehicle']);
        Route::delete('/vehicles/{id}', [TransportController::class, 'destroyVehicle']);
        Route::get('/statistics', [TransportController::class, 'statistics']);
    });

    // Hostel management
    Route::prefix('hostel')->group(function () {
        Route::get('/rooms', [HostelController::class, 'rooms']);
        Route::post('/rooms', [HostelController::class, 'storeRoom']);
        Route::get('/rooms/{id}', [HostelController::class, 'showRoom']);
        Route::put('/rooms/{id}', [HostelController::class, 'updateRoom']);
        Route::delete('/rooms/{id}', [HostelController::class, 'destroyRoom']);
        Route::get('/room-types', [HostelController::class, 'roomTypes']);
        Route::get('/statistics', [HostelController::class, 'statistics']);
    });

    // File uploads
    Route::post('/upload', function (Request $request) {
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
    });

    // Notifications
    Route::get('/notifications', function (Request $request) {
        $user = $request->user();
        $notifications = $user->notifications()->paginate(15);
        return response()->json($notifications);
    });

    Route::put('/notifications/{id}/read', function (Request $request, $id) {
        $user = $request->user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();
        return response()->json(['success' => true]);
    });

    // Real-time updates
    Route::prefix('realtime')->group(function () {
        Route::get('/check-updates', [RealtimeController::class, 'checkUpdates']);
        Route::post('/mark-read', [RealtimeController::class, 'markAsRead']);
        Route::get('/unread-count', [RealtimeController::class, 'unreadCount']);
    });

    // Settings
    Route::get('/settings', function () {
        return response()->json([
            'school' => config('school'),
            'app' => [
                'name' => config('app.name'),
                'locale' => config('app.locale'),
                'timezone' => config('app.timezone'),
            ],
        ]);
    });
});

// Fallback route
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found',
    ], 404);
});
