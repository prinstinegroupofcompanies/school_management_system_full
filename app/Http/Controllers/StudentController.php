<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\ClassRoom;
use App\Models\Section;
use App\Models\Subject;
use App\Models\StudentAttendance;
use App\Models\ExamSchedule;
use App\Models\Homework;
use App\Models\FeeStructure;
use App\Models\FeePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        
        try {
            $student = $user->student;
        } catch (\Exception $e) {
            // Handle case where students table doesn't exist
            \Log::warning('Students table not found, creating safe dashboard: ' . $e->getMessage());
            return $this->createSafeDashboard($user);
        }
        
        if (!$student) {
            // Create a basic dashboard with safe defaults if student record is missing
            return $this->createSafeDashboard($user);
        }

        $session = [
            'academic_year' => (int) date('Y'),
            'semester' => (int) (date('n') <= 6 ? 1 : 2),
        ];

        // Get student's class with safe queries
        $class = $this->safeQuery(function() use ($student) {
            return ClassRoom::with('subjects')->find($student->class_id);
        });

        // Get assigned subjects (synced from class on enrollment)
        $subjects = $this->safeQuery(function() use ($student) {
            return $student->subjects()
                ->get(['subjects.id', 'subjects.name', 'subjects.code', 'subjects.description']);
        }) ?: collect();

        // Get comprehensive real-time attendance data
        $attendanceTotal = StudentAttendance::where('student_id', $student->id)->count();
        $attendancePresent = StudentAttendance::where('student_id', $student->id)
            ->where('status', 'present')->count();
        $attendanceAbsent = StudentAttendance::where('student_id', $student->id)
            ->where('status', 'absent')->count();
        $attendanceLate = StudentAttendance::where('student_id', $student->id)
            ->where('status', 'late')->count();
        $attendanceExcused = StudentAttendance::where('student_id', $student->id)
            ->where('status', 'excused')->count();
        
        $attendanceRate = $attendanceTotal > 0 ? round(($attendancePresent / $attendanceTotal) * 100, 2) : 0;

        // Get today's attendance
        $todayAttendance = StudentAttendance::where('student_id', $student->id)
            ->whereDate('attendance_date', today())
            ->latest('attendance_date')
            ->first();
            
        // Get recent attendance records (last 10 days)
        $recentAttendance = StudentAttendance::where('student_id', $student->id)
            ->latest('attendance_date')
            ->limit(10)
            ->get();
            
        // Get this month's attendance
        $thisMonthAttendance = StudentAttendance::where('student_id', $student->id)
            ->whereMonth('attendance_date', now()->month)
            ->whereYear('attendance_date', now()->year)
            ->get();
            
        // Calculate attendance statistics
        $attendanceStats = [
            'total_days' => $attendanceTotal,
            'present_days' => $attendancePresent,
            'absent_days' => $attendanceAbsent,
            'late_days' => $attendanceLate,
            'excused_days' => $attendanceExcused,
            'attendance_rate' => $attendanceRate,
            'this_month_total' => $thisMonthAttendance->count(),
            'this_month_present' => $thisMonthAttendance->where('status', 'present')->count(),
            'today_status' => $todayAttendance ? $todayAttendance->status : 'not_recorded',
        ];

        // Get upcoming exams for student's class
        $upcomingExams = ExamSchedule::with(['examType', 'subject'])
            ->where('class_id', $student->class_id)
            ->where('status', 'published')
            ->where('start_date', '>=', now()->toDateString())
            ->orderBy('start_date')
            ->take(5)
            ->get();

        // Get recent homework for student's class
        $homework = Homework::where('class_id', $student->class_id)
            ->whereDate('due_date', '>=', today())
            ->orderBy('due_date')
            ->take(5)
            ->get(['id', 'title', 'due_date']);

        // Ensure student has fees assigned for their current class
        \App\Services\StudentFeeService::assignClassFeesToStudent($student);
        
        // Get real-time fee status using the service
        $financialSummary = \App\Services\StudentFeeService::getStudentFinancialSummary($student);
        $feeStatus = [
            'total_fees' => $financialSummary['total_fees'],
            'total_paid' => $financialSummary['paid_amount'],
            'pending' => $financialSummary['balance_amount'],
            'percentage_paid' => $financialSummary['total_fees'] > 0 ? round(($financialSummary['paid_amount'] / $financialSummary['total_fees']) * 100, 2) : 0,
        ];

        // Get recent activities from real data
        $recentActivities = collect();
        
        // Recent attendance records
        $recentAttendances = StudentAttendance::where('student_id', $student->id)
            ->latest('date')
            ->take(3)
            ->get(['date', 'status'])
            ->map(function($a) {
                return [
                    'description' => 'Attendance: ' . ucfirst($a->status) . ' on ' . \Carbon\Carbon::parse($a->date)->format('M d, Y'),
                    'created_at' => $a->date ? \Carbon\Carbon::parse($a->date) : now(),
                ];
            });

        // Recent fee payments
        $recentPayments = FeePayment::where('student_id', $student->id)
            ->latest('payment_date')
            ->take(3)
            ->get(['amount_paid', 'payment_date'])
            ->map(function($p) {
                return [
                    'description' => 'Fee payment of $' . number_format($p->amount_paid, 2),
                    'created_at' => $p->payment_date ? \Carbon\Carbon::parse($p->payment_date) : now(),
                ];
            });

        $recentActivities = $recentAttendances->merge($recentPayments)->sortByDesc('created_at')->take(5);

        // Get library statistics
        $libraryStats = [
            'total_books' => \App\Models\Book::count(),
            'available_books' => \App\Models\Book::where('status', 'available')->count(),
            'borrowed_books' => \App\Models\BookIssue::where('status', 'borrowed')->count(),
            'my_borrowed' => \App\Models\BookIssue::where('student_id', $student->id)->where('status', 'borrowed')->count(),
        ];

        // Get transport statistics
        $transportStats = [
            'total_routes' => \App\Models\TransportRoute::count(),
            'active_routes' => \App\Models\TransportRoute::where('status', 'active')->count(),
            'total_vehicles' => \App\Models\Transport::where('status', 'active')->count(),
            'total_students' => \App\Models\Student::whereNotNull('transport_route_id')->count(),
        ];

        // Get student's transport route
        $myRoute = null;
        if ($student->transport_route_id) {
            $myRoute = \App\Models\TransportRoute::with('transport')->find($student->transport_route_id);
        }

        // Get hostel statistics
        $hostelStats = [
            'total_hostels' => \App\Models\Hostel::where('status', 'active')->count(),
            'total_rooms' => \App\Models\HostelRoom::where('is_active', true)->count(),
            'total_capacity' => \App\Models\HostelRoom::where('is_active', true)->sum('capacity'),
            'current_occupancy' => \App\Models\HostelRoom::where('is_active', true)->sum('current_occupancy'),
        ];

        // Get student's hostel room
        $myRoom = null;
        if ($student->hostel_room_id) {
            $myRoom = \App\Models\HostelRoom::with('hostel')->find($student->hostel_room_id);
        }

        // Calculate real statistics
        $stats = [
            'total_subjects' => $subjects->count(),
            'attendance_rate' => $attendanceRate,
            'upcoming_exams' => $upcomingExams->count(),
            'recent_grades' => 0, // Will be updated when grading system is implemented
        ];

        return view('dashboard.student', compact(
            'stats', 
            'homework', 
            'user', 
            'subjects',
            'feeStatus',
            'upcomingExams',
            'recentActivities',
            'session',
            'libraryStats',
            'transportStats',
            'myRoute',
            'hostelStats',
            'myRoom',
            'attendanceStats',
            'recentAttendance',
            'todayAttendance',
            'thisMonthAttendance'
        ));
    }

    private function getAttendanceRate($student)
    {
        $total_attendance = $student->attendances()->count();
        $present_attendance = $student->attendances()->where('status', 'present')->count();
        
        if ($total_attendance == 0) {
            return 0;
        }
        
        return round(($present_attendance / $total_attendance) * 100, 2);
    }

    private function getUpcomingExams($student)
    {
        return \App\Models\ExamSchedule::where('class_id', $student->class_id)
            ->where('exam_date', '>=', now())
            ->orderBy('exam_date')
            ->limit(5)
            ->get();
    }

    private function getRecentGrades($student)
    {
        return \App\Models\ExamMark::where('student_id', $student->id)
            ->with('examSchedule', 'subject')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    private function getRecentActivities($student)
    {
        return collect([
            [
                'description' => 'New homework assigned',
                'created_at' => now()->subHours(2),
            ],
            [
                'description' => 'Exam result published',
                'created_at' => now()->subDays(1),
            ],
            [
                'description' => 'Attendance marked',
                'created_at' => now()->subDays(2),
            ],
        ]);
    }

    private function getRecentHomework($student)
    {
        return \App\Models\Homework::where('class_id', $student->class_id)
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->limit(5)
            ->get();
    }

    public function index(Request $request)
    {
        $query = Student::with(['user', 'class', 'section']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('admission_no', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->paginate(15);
        $classes = ClassRoom::all();

        return view('students.index', compact('students', 'classes'));
    }

    public function create()
    {
        $classes = ClassRoom::all();
        $subjects = \App\Models\Subject::with('class')->get();
        $sections = \App\Models\Section::where('is_active', true)->get();
        return view('students.create', compact('classes','subjects','sections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'student_id' => 'required|string|unique:students,student_id',
            'class_id' => 'required|exists:class_rooms,id',
            'section_id' => 'required|exists:sections,id',
            'gender' => 'required|in:male,female,other',
            'level' => 'required|in:junior,senior',
            'subject_ids' => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
            'academic_year' => 'nullable|string|max:9',
            'admission_no' => 'nullable|string|unique:students,admission_no',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_email' => 'nullable|email',
            'guardian_phone' => 'nullable|string|max:20',
            'guardian_address' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => 'student',
                'phone' => $request->phone,
                'address' => $request->address,
                'status' => 'active',
            ]);

            // User role is set via user_type field

            // Create guardian if guardian information is provided, otherwise use existing guardian
            $guardianId = null;
            if ($request->guardian_name) {
                $guardianEmail = $request->guardian_email ?: 'guardian_' . $user->id . '@school.local';
                
                // Check if user with this email already exists
                $existingUser = User::where('email', $guardianEmail)->first();
                
                if ($existingUser) {
                    // Use existing user
                    $guardianUser = $existingUser;
                    
                    // Update user information if needed
                    $guardianUser->update([
                        'name' => $request->guardian_name,
                        'user_type' => 'parent',
                        'phone' => $request->guardian_phone,
                        'address' => $request->guardian_address,
                        'status' => 'active',
                    ]);
                } else {
                    // Create new user
                    $guardianUser = User::create([
                        'name' => $request->guardian_name,
                        'email' => $guardianEmail,
                        'password' => Hash::make('password123'), // Default password
                        'user_type' => 'parent',
                        'phone' => $request->guardian_phone,
                        'address' => $request->guardian_address,
                        'status' => 'active',
                    ]);
                }

                // Check if guardian record already exists for this user
                $existingGuardian = \App\Models\Guardian::where('user_id', $guardianUser->id)->first();
                
                if ($existingGuardian) {
                    // Use existing guardian
                    $guardianId = $existingGuardian->id;
                } else {
                    // Create new guardian record
                    // Generate guardian ID
                    $guardianIdStr = 'G' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
                    while (\App\Models\Guardian::where('guardian_id', $guardianIdStr)->exists()) {
                        $guardianIdStr = 'G' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
                    }

                    $guardian = \App\Models\Guardian::create([
                        'user_id' => $guardianUser->id,
                        'guardian_id' => $guardianIdStr,
                        'relationship' => 'guardian',
                        'is_primary_guardian' => true,
                        'status' => 'active',
                    ]);

                    $guardianId = $guardian->id;
                }
            } else {
                // Use the first available guardian if no guardian information is provided
                $existingGuardian = \App\Models\Guardian::first();
                if ($existingGuardian) {
                    $guardianId = $existingGuardian->id;
                }
            }

            // Generate a safe admission number if not provided
            $admissionNo = $request->admission_no;
            if (!$admissionNo) {
                $year = (string) (now()->year);
                do {
                    $admissionNo = $year . '-' . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
                } while (\App\Models\Student::where('admission_no', $admissionNo)->exists());
            }

            // Split the full name into first and last name
            $nameParts = explode(' ', trim($request->name), 2);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

            $student = Student::create([
                'user_id' => $user->id,
                'student_id' => $request->student_id,
                'class_id' => $request->class_id,
                'section_id' => $request->section_id,
                'level' => $request->level,
                'academic_year' => $request->academic_year ?? config('app.school.academic_year'),
                'admission_no' => $admissionNo,
                'admission_date' => $request->admission_date ?? now()->toDateString(),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'phone' => $request->phone,
                'address' => $request->address,
                'guardian_id' => $guardianId,
                'guardian_name' => $request->guardian_name,
                'guardian_phone' => $request->guardian_phone,
                'guardian_email' => $request->guardian_email,
                'guardian_address' => $request->guardian_address,
                'status' => 'active',
            ]);

            // Enroll into selected subjects (optional)
            if ($request->filled('subject_ids')) {
                $student->subjects()->sync($request->subject_ids);
            }

            DB::commit();

            // Log the created student credentials for validation
            \Log::info('Student created successfully', [
                'student_id' => $student->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
                'login_credentials' => [
                    'username' => $user->email,
                    'password' => 'The password you entered during registration'
                ]
            ]);

            return redirect()->route('students.index')
                ->with('success', 'Student created successfully. Login credentials: Email: ' . $user->email . ', Password: [The password you entered]');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create student: ' . $e->getMessage());
        }
    }

    public function show(Student $student)
    {
        $student->load(['user', 'class', 'section']);
        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $classes = ClassRoom::all();
        $sections = Section::all();
        $subjects = \App\Models\Subject::with('class')->get();
        $enrolledSubjectIds = method_exists($student, 'subjects') ? $student->subjects()->pluck('subjects.id')->toArray() : [];
        return view('students.edit', compact('student', 'classes', 'sections','subjects','enrolledSubjectIds'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->user_id,
            'class_id' => 'required|exists:class_rooms,id',
            'section_id' => 'nullable|exists:sections,id',
            'level' => 'nullable|in:junior,senior',
            'subject_ids' => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
            'student_id' => 'nullable|string|unique:students,student_id,' . $student->id,
            'admission_no' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        try {
            $student->user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            $student->update([
                'class_id' => $request->class_id,
                'section_id' => $request->section_id,
                'level' => $request->level ?? $student->level,
                'student_id' => $request->student_id ?? $student->student_id,
                'admission_no' => $request->admission_no ?? $student->admission_no,
                'gender' => $request->gender ?? $student->gender,
                'phone' => $request->phone ?? $student->phone,
                'address' => $request->address ?? $student->address,
            ]);

            if ($request->filled('subject_ids')) {
                $student->subjects()->sync($request->subject_ids);
            }

            return redirect()->route('students.index')
                ->with('success', 'Student updated successfully');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update student: ' . $e->getMessage());
        }
    }

    public function destroy(Student $student)
    {
        try {
            $student->user->delete();
            return redirect()->route('students.index')
                ->with('success', 'Student deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete student: ' . $e->getMessage());
        }
    }

    public function attendance(Student $student)
    {
        $attendance = $student->attendance()->latest()->paginate(30);
        return view('students.attendance', compact('student', 'attendance'));
    }

    public function exams(Student $student)
    {
        $exams = $student->examMarks()->with(['examSchedule'])->latest()->paginate(15);
        return view('students.exams', compact('student', 'exams'));
    }

    public function fees(Student $student)
    {
        $fees = $student->feePayments()->with(['feeStructure'])->latest()->paginate(15);
        return view('students.fees', compact('student', 'fees'));
    }

    public function homework(Student $student)
    {
        $homework = $student->homeworkSubmissions()->with(['homework'])->latest()->paginate(15);
        return view('students.homework', compact('student', 'homework'));
    }

    public function timeline(Student $student)
    {
        $timeline = $student->timeline()->latest()->paginate(20);
        return view('students.timeline', compact('student', 'timeline'));
    }

    public function uploadDocuments(Request $request, Student $student)
    {
        $request->validate([
            'documents.*' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        try {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('student_documents', 'public');
                
                $student->documents()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }

            return back()->with('success', 'Documents uploaded successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to upload documents: ' . $e->getMessage());
        }
    }

    /**
     * Test student login credentials
     */
    public function testStudentCredentials($studentId, $password)
    {
        $student = Student::with('user')->find($studentId);
        
        if (!$student) {
            return ['success' => false, 'message' => 'Student not found'];
        }

        $user = $student->user;
        
        // Test authentication
        if (\Illuminate\Support\Facades\Hash::check($password, $user->password)) {
            return [
                'success' => true,
                'message' => 'Credentials are valid',
                'student' => [
                    'id' => $student->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_type' => $user->user_type,
                    'status' => $user->status
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Invalid password',
                'student' => [
                    'id' => $student->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_type' => $user->user_type
                ]
            ];
        }
    }

    /**
     * Safe database query wrapper to prevent crashes when tables don't exist
     */
    private function safeQuery($callback, $default = null)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            return $default ?? collect();
        }
    }

    /**
     * Create a safe dashboard when student record is missing
     */
    private function createSafeDashboard($user)
    {
        $session = [
            'academic_year' => (int) date('Y'),
            'semester' => (int) (date('n') <= 6 ? 1 : 2),
        ];

        $stats = [
            'total_subjects' => 8, // Demo data
            'attendance_rate' => 87.5,
            'upcoming_exams' => 3,
            'recent_grades' => 5,
        ];

        $feeStatus = [
            'total_fees' => 55000,
            'total_paid' => 35000,
            'pending' => 20000,
            'percentage_paid' => 63.6,
        ];

        // Create safe attendance stats
        $attendanceStats = [
            'total_days' => 0,
            'present_days' => 0,
            'absent_days' => 0,
            'late_days' => 0,
            'excused_days' => 0,
            'attendance_rate' => 0,
            'this_month_total' => 0,
            'this_month_present' => 0,
            'today_status' => 'not_recorded',
        ];

        return view('dashboard.student', compact(
            'stats', 
            'user',
            'feeStatus',
            'session',
            'attendanceStats'
        ) + [
            'homework' => collect([
                (object) ['title' => 'Mathematics Assignment 1', 'due_date' => now()->addDays(3)],
                (object) ['title' => 'English Essay', 'due_date' => now()->addDays(5)],
                (object) ['title' => 'Science Lab Report', 'due_date' => now()->addWeek()],
            ]),
            'attendance' => (object) ['status' => 'present', 'date' => today()],
            'subjects' => collect([
                (object) ['id' => 1, 'name' => 'Mathematics', 'code' => 'MATH101', 'description' => 'Advanced Mathematics'],
                (object) ['id' => 2, 'name' => 'English', 'code' => 'ENG101', 'description' => 'English Language and Literature'],
                (object) ['id' => 3, 'name' => 'Science', 'code' => 'SCI101', 'description' => 'General Science'],
                (object) ['id' => 4, 'name' => 'History', 'code' => 'HIST101', 'description' => 'World History'],
            ]),
            'upcomingExams' => collect([
                (object) [
                    'id' => 1,
                    'title' => 'Mid-term Mathematics',
                    'exam_date' => now()->addDays(5)->format('Y-m-d'),
                    'start_time' => '09:00',
                    'examType' => (object) ['name' => 'Mid-term'],
                    'subject' => (object) ['name' => 'Mathematics']
                ],
                (object) [
                    'id' => 2,
                    'title' => 'English Literature',
                    'exam_date' => now()->addDays(8)->format('Y-m-d'),
                    'start_time' => '10:30',
                    'examType' => (object) ['name' => 'Final'],
                    'subject' => (object) ['name' => 'English']
                ],
                (object) [
                    'id' => 3,
                    'title' => 'Science Practical',
                    'exam_date' => now()->addDays(12)->format('Y-m-d'),
                    'start_time' => '14:00',
                    'examType' => (object) ['name' => 'Practical'],
                    'subject' => (object) ['name' => 'Science']
                ],
            ]),
            'recentActivities' => collect([
                ['description' => 'Attendance marked: Present', 'created_at' => now()->subHours(2)],
                ['description' => 'Assignment submitted: Math Homework', 'created_at' => now()->subDays(1)],
                ['description' => 'Grade received: English Essay - A', 'created_at' => now()->subDays(2)],
            ]),
            'libraryStats' => [
                'total_books' => 0,
                'available_books' => 0,
                'borrowed_books' => 0,
                'my_borrowed' => 0,
            ],
            'transportStats' => [
                'total_routes' => 0,
                'active_routes' => 0,
                'total_vehicles' => 0,
                'total_students' => 0,
            ],
            'myRoute' => null,
            'hostelStats' => [
                'total_hostels' => 0,
                'total_rooms' => 0,
                'total_capacity' => 0,
                'current_occupancy' => 0,
            ],
            'myRoom' => null,
            'recentAttendance' => collect(),
            'todayAttendance' => null,
            'thisMonthAttendance' => collect(),
        ]);
    }

    public function grades()
    {
        return view('student.grades');
    }

    public function finance()
    {
        return view('student.finance');
    }

    public function library()
    {
        return view('student.library');
    }

    public function transport()
    {
        return view('student.transport');
    }

    public function hostel()
    {
        return view('student.hostel');
    }

    public function settings()
    {
        return view('student.settings');
    }
}
