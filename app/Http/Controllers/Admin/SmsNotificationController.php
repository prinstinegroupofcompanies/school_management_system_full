<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmsNotification;
use App\Models\Student;
use App\Models\Guardian;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SmsNotificationController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->middleware('admin');
        $this->smsService = $smsService;
    }

    /**
     * Display a listing of SMS notifications
     */
    public function index(Request $request)
    {
        $query = SmsNotification::with(['user', 'student', 'parent']);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by phone number or message
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('phone_number', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total' => SmsNotification::count(),
            'pending' => SmsNotification::pending()->count(),
            'sent' => SmsNotification::sent()->count(),
            'delivered' => SmsNotification::delivered()->count(),
            'failed' => SmsNotification::failed()->count(),
            'today' => SmsNotification::whereDate('created_at', today())->count(),
            'this_month' => SmsNotification::whereMonth('created_at', now()->month)->count(),
        ];

        return view('admin.sms-notifications.index', compact('notifications', 'stats'));
    }

    /**
     * Show the form for creating a new SMS notification
     */
    public function create()
    {
        $students = Student::with(['user', 'class', 'guardian'])->get();
        $guardians = Guardian::all();
        
        return view('admin.sms-notifications.create', compact('students', 'guardians'));
    }

    /**
     * Store a newly created SMS notification
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'required|string',
            'message' => 'required|string|max:160',
            'type' => 'required|in:attendance,grades,urgent,general,payment,exam,event',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        try {
            DB::beginTransaction();

            $notifications = [];
            $phoneNumbers = [];

            // Process recipients
            foreach ($request->recipients as $recipient) {
                if (str_starts_with($recipient, 'student_')) {
                    $studentId = str_replace('student_', '', $recipient);
                    $student = Student::with(['user', 'guardian'])->find($studentId);
                    
                    if ($student) {
                        // Add student phone if available
                        if ($student->user && $student->user->phone) {
                            $phoneNumbers[] = $student->user->phone;
                        }
                        
                        // Add parent phone if available
                        if ($student->guardian && $student->guardian->phone) {
                            $phoneNumbers[] = $student->guardian->phone;
                        }
                    }
                } elseif (str_starts_with($recipient, 'parent_')) {
                    $parentId = str_replace('parent_', '', $recipient);
                    $parent = Guardian::find($parentId);
                    
                    if ($parent && $parent->phone) {
                        $phoneNumbers[] = $parent->phone;
                    }
                } elseif (str_starts_with($recipient, 'phone_')) {
                    $phoneNumber = str_replace('phone_', '', $recipient);
                    $phoneNumbers[] = $phoneNumber;
                }
            }

            // Remove duplicates
            $phoneNumbers = array_unique($phoneNumbers);

            // Create notifications
            foreach ($phoneNumbers as $phoneNumber) {
                $notification = SmsNotification::create([
                    'phone_number' => $phoneNumber,
                    'message' => $request->message,
                    'type' => $request->type,
                    'scheduled_at' => $request->scheduled_at,
                    'metadata' => [
                        'created_by' => auth()->id(),
                        'recipients' => $request->recipients,
                        'original_message' => $request->message
                    ]
                ]);

                $notifications[] = $notification;
            }

            // Send immediately if not scheduled
            if (!$request->scheduled_at) {
                $this->smsService->sendBulkSms($notifications);
            }

            DB::commit();

            return redirect()->route('admin.sms-notifications.index')
                           ->with('success', 'SMS notifications created successfully. ' . count($notifications) . ' messages queued for sending.');

        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withInput()
                        ->with('error', 'Failed to create SMS notifications: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified SMS notification
     */
    public function show(SmsNotification $smsNotification)
    {
        $smsNotification->load(['user', 'student', 'parent']);
        
        return view('admin.sms-notifications.show', compact('smsNotification'));
    }

    /**
     * Resend a failed SMS notification
     */
    public function resend(SmsNotification $smsNotification)
    {
        if (!$smsNotification->isFailed()) {
            return back()->with('error', 'Only failed notifications can be resent.');
        }

        try {
            $smsNotification->update(['status' => 'pending']);
            $this->smsService->sendSms($smsNotification);

            return back()->with('success', 'SMS notification resent successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to resend SMS: ' . $e->getMessage());
        }
    }

    /**
     * Send scheduled SMS notifications
     */
    public function sendScheduled()
    {
        try {
            $scheduledNotifications = SmsNotification::readyToSend()->get();
            
            if ($scheduledNotifications->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No scheduled notifications ready to send.',
                    'count' => 0
                ]);
            }

            $results = $this->smsService->sendBulkSms($scheduledNotifications->toArray());
            
            $successCount = count(array_filter($results));
            $failureCount = count($results) - $successCount;

            return response()->json([
                'success' => true,
                'message' => "Processed {$successCount} successful and {$failureCount} failed notifications.",
                'count' => count($results)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send scheduled notifications: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get SMS statistics for dashboard
     */
    public function statistics()
    {
        $stats = [
            'total' => SmsNotification::count(),
            'pending' => SmsNotification::pending()->count(),
            'sent' => SmsNotification::sent()->count(),
            'delivered' => SmsNotification::delivered()->count(),
            'failed' => SmsNotification::failed()->count(),
            'today' => SmsNotification::whereDate('created_at', today())->count(),
            'this_month' => SmsNotification::whereMonth('created_at', now()->month)->count(),
            'by_type' => SmsNotification::selectRaw('type, COUNT(*) as count')
                                      ->groupBy('type')
                                      ->get()
                                      ->pluck('count', 'type'),
            'by_status' => SmsNotification::selectRaw('status, COUNT(*) as count')
                                        ->groupBy('status')
                                        ->get()
                                        ->pluck('count', 'status'),
        ];

        return response()->json($stats);
    }

    /**
     * Send bulk SMS to students
     */
    public function sendBulkToStudents(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'message' => 'required|string|max:160',
            'type' => 'required|in:attendance,grades,urgent,general,payment,exam,event',
        ]);

        try {
            DB::beginTransaction();

            $students = Student::with(['user', 'guardian'])->whereIn('id', $request->student_ids)->get();
            $notifications = [];

            foreach ($students as $student) {
                $studentNotifications = SmsNotification::createForStudent(
                    $student,
                    $request->message,
                    $request->type,
                    ['bulk_send' => true, 'created_by' => auth()->id()]
                );
                
                $notifications = array_merge($notifications, $studentNotifications);
            }

            $this->smsService->sendBulkSms($notifications);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bulk SMS sent successfully to ' . count($students) . ' students.',
                'count' => count($notifications)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send bulk SMS: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send attendance notification
     */
    public function sendAttendanceNotification(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'attendance_data' => 'required|array',
        ]);

        try {
            $student = Student::with(['user', 'guardian', 'class'])->find($request->student_id);
            $notifications = $this->smsService->sendAttendanceNotification($student, $request->attendance_data);

            return response()->json([
                'success' => true,
                'message' => 'Attendance notification sent successfully.',
                'count' => count($notifications)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send attendance notification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send grades notification
     */
    public function sendGradesNotification(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'grades_data' => 'required|array',
        ]);

        try {
            $student = Student::with(['user', 'guardian', 'class'])->find($request->student_id);
            $notifications = $this->smsService->sendGradesNotification($student, $request->grades_data);

            return response()->json([
                'success' => true,
                'message' => 'Grades notification sent successfully.',
                'count' => count($notifications)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send grades notification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send urgent notification
     */
    public function sendUrgentNotification(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'message' => 'required|string|max:160',
        ]);

        try {
            $student = Student::with(['user', 'guardian', 'class'])->find($request->student_id);
            $notifications = $this->smsService->sendUrgentNotification($student, $request->message);

            return response()->json([
                'success' => true,
                'message' => 'Urgent notification sent successfully.',
                'count' => count($notifications)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send urgent notification: ' . $e->getMessage()
            ], 500);
        }
    }
}
