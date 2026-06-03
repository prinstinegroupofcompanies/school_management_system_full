<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\ClassRoom;
use App\Models\Guardian;
use App\Services\StudentFeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
// use SimpleSoftwareIO\QrCode\Facades\QrCode; // Install: composer require simplesoftwareio/simple-qrcode

class EnrollmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        // Check for registrar role
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasRole('registrar') && !auth()->user()->hasRole('super_admin') && !auth()->user()->hasRole('vpi')) {
                abort(403, 'Unauthorized access. Registrar role required.');
            }
            return $next($request);
        });
    }

    /**
     * Show enrollment form.
     */
    public function create()
    {
        $classes = ClassRoom::all();
        $academicYear = date('Y');
        return view('registrar.enrollment.create', compact('classes', 'academicYear'));
    }

    /**
     * Enroll a new student.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Student basic info
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date|before:today',
            'address' => 'nullable|string|max:500',
            
            // Academic info
            'class_id' => 'required|exists:class_rooms,id',
            'section_id' => 'nullable|exists:sections,id',
            'academic_year' => 'nullable|integer',
            
            // Guardian info
            'guardian_name' => 'required|string|max:255',
            'guardian_phone' => 'required|string|max:20',
            'guardian_email' => 'nullable|email',
            'guardian_relationship' => 'required|string|max:50',
            'guardian_address' => 'nullable|string|max:500',
            
            // Admission
            'admission_date' => 'nullable|date',
            
            // Optional fields
            'previous_school' => 'nullable|string|max:255',
            'previous_class' => 'nullable|string|max:100',
            'nationality' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            // Generate unique username: school.firstname.lastname.year
            $firstPart = Str::slug(explode(' ', $validated['name'])[0] ?? 'student');
            $lastPart = Str::slug(explode(' ', $validated['name'])[count(explode(' ', $validated['name'])) - 1] ?? 'user');
            $admissionYear = $validated['academic_year'] ?? date('Y');
            $prefix = Str::lower(config('school.short_name', 'school'));
            $username = $prefix . '.' . $firstPart . '.' . $lastPart . '.' . $admissionYear;
            
            // Ensure username is unique
            $counter = 1;
            $originalUsername = $username;
            while (User::where('username', $username)->exists()) {
                $username = $originalUsername . '.' . $counter;
                $counter++;
            }

            // Generate strong random password
            $rawPassword = Str::random(12);
            $password = bcrypt($rawPassword);

            // Create user account
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'] ?? $this->generateEmail($validated['name']),
                'username' => $username,
                'password' => $password,
                'must_change_password' => true, // Force password change on first login
                'user_type' => 'student',
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'is_active' => true,
            ]);

            // Assign student role
            $user->assignRole('student');

            // Create or find guardian
            $guardian = Guardian::firstOrCreate(
                ['guardian_phone' => $validated['guardian_phone']],
                [
                    'guardian_name' => $validated['guardian_name'],
                    'guardian_email' => $validated['guardian_email'],
                    'guardian_relationship' => $validated['guardian_relationship'],
                    'guardian_address' => $validated['guardian_address'],
                ]
            );

            // Create student record (admission_no and student_id auto-generated via model boot)
            $student = Student::create([
                'user_id' => $user->id,
                'class_id' => $validated['class_id'],
                'section_id' => $validated['section_id'] ?? null,
                'academic_year' => $validated['academic_year'] ?? date('Y'),
                'admission_date' => $validated['admission_date'] ?? now(),
                'gender' => $validated['gender'],
                'date_of_birth' => $validated['date_of_birth'],
                'address' => $validated['address'],
                'phone' => $validated['phone'],
                'guardian_id' => $guardian->id,
                'previous_school' => $validated['previous_school'] ?? null,
                'previous_class' => $validated['previous_class'] ?? null,
                'nationality' => $validated['nationality'] ?? 'Liberian',
                'status' => 'active',
                'is_active' => true,
            ]);

            // Assign class fees
            StudentFeeService::assignClassFeesToStudent($student);

            DB::commit();

            // Generate admission letter with credentials
            $admissionLetter = $this->generateAdmissionLetter($student, $username, $rawPassword);

            return redirect()->route('registrar.enrollment.show', $student)
                ->with('success', 'Student enrolled successfully. Admission letter generated.')
                ->with('admission_letter_path', $admissionLetter);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to enroll student: ' . $e->getMessage());
        }
    }

    /**
     * Show enrolled student details.
     */
    public function show(Student $student)
    {
        $student->load(['user', 'classRoom', 'section', 'guardian']);
        return view('registrar.enrollment.show', compact('student'));
    }

    /**
     * Generate admission letter PDF.
     */
    public function generateAdmissionLetter(Student $student, ?string $username = null, ?string $rawPassword = null)
    {
        $student->load(['user', 'classRoom', 'guardian']);
        
        // Use provided credentials or fetch from user
        if (!$username && $student->user && $student->user->username) {
            $username = $student->user->username;
            // Note: We can't retrieve the raw password, so this will only show username
            $rawPassword = null;
        }
        
        // Get school details from config
        $schoolName = config('school.name', 'School');
        $schoolAddress = config('school.address', 'Monrovia, Liberia');
        $schoolLogo = config('school.logo', 'assets/images/school-logo.png');
        $schoolLogoPath = public_path($schoolLogo);
        if (!file_exists($schoolLogoPath)) {
            // Fallback to system settings
            $schoolLogo = \App\Models\SystemSetting::get('school_logo', '');
            $schoolLogoPath = $schoolLogo ? storage_path('app/public/' . $schoolLogo) : null;
        }

        // Generate QR code (if package installed)
        $qrCodeData = json_encode([
            'student_id' => $student->id,
            'admission_no' => $student->admission_no,
            'name' => $student->user->name,
            'class' => $student->classRoom->name ?? 'N/A',
        ]);

        $qrCode = null;
        if (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode')) {
            $qrCode = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                ->size(200)
                ->generate($qrCodeData));
        }

        // Get registrar signature if available
        $registrar = auth()->user();
        $signaturePath = $registrar->signature 
            ? storage_path('app/public/' . $registrar->signature) 
            : null;

        $data = [
            'student' => $student,
            'schoolName' => $schoolName,
            'schoolAddress' => $schoolAddress,
            'schoolLogoPath' => $schoolLogoPath,
            'qrCode' => $qrCode,
            'signaturePath' => $signaturePath,
            'generatedDate' => now()->format('F d, Y'),
            'generatedBy' => auth()->user()->name,
            'username' => $username,
            'rawPassword' => $rawPassword,
            'schoolDescription' => config('school.description', ''),
        ];

        // Generate PDF
        $pdf = Pdf::loadView('registrar.admission-letter', $data);
        
        // Save to storage
        $filename = 'admission-letters/' . $student->admission_no . '_' . now()->format('Y-m-d') . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        return $filename;
    }

    /**
     * Download admission letter.
     */
    public function downloadAdmissionLetter(Student $student)
    {
        $letterPath = $this->generateAdmissionLetter($student);
        $fullPath = storage_path('app/public/' . $letterPath);

        if (file_exists($fullPath)) {
            return response()->download($fullPath, $student->admission_no . '_admission_letter.pdf');
        }

        return redirect()->back()
            ->with('error', 'Admission letter not found.');
    }

    /**
     * View admission letter.
     */
    public function viewAdmissionLetter(Student $student)
    {
        $student->load(['user', 'classRoom', 'guardian']);
        
        $schoolName = config('school.name', 'School');
        $schoolAddress = config('school.address', 'Monrovia, Liberia');
        $schoolLogo = config('school.logo', 'assets/images/school-logo.png');
        $schoolLogoPath = public_path($schoolLogo);
        if (!file_exists($schoolLogoPath)) {
            $schoolLogo = \App\Models\SystemSetting::get('school_logo', '');
            $schoolLogoPath = $schoolLogo ? storage_path('app/public/' . $schoolLogo) : null;
        }
        
        $username = $student->user->username ?? null;
        $rawPassword = null; // Can't retrieve raw password after creation

        $qrCodeData = json_encode([
            'student_id' => $student->id,
            'admission_no' => $student->admission_no,
            'name' => $student->user->name,
            'class' => $student->classRoom->name ?? 'N/A',
        ]);

        $qrCode = null;
        if (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode')) {
            $qrCode = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                ->size(200)
                ->generate($qrCodeData));
        }

        $registrar = auth()->user();
        $signaturePath = $registrar->signature 
            ? storage_path('app/public/' . $registrar->signature) 
            : null;

        $data = [
            'student' => $student,
            'schoolName' => $schoolName,
            'schoolAddress' => $schoolAddress,
            'schoolLogoPath' => $schoolLogoPath,
            'qrCode' => $qrCode,
            'signaturePath' => $signaturePath,
            'generatedDate' => now()->format('F d, Y'),
            'generatedBy' => auth()->user()->name,
            'username' => $username,
            'rawPassword' => $rawPassword,
            'schoolDescription' => config('school.description', ''),
        ];

        $pdf = Pdf::loadView('registrar.admission-letter', $data);
        return $pdf->stream($student->admission_no . '_admission_letter.pdf');
    }

    /**
     * Generate email from name.
     */
    private function generateEmail(string $name): string
    {
        $baseEmail = strtolower(str_replace(' ', '.', $name)) . '@' . config('app.domain', 'school.local');
        $email = $baseEmail;
        $counter = 1;

        while (User::where('email', $email)->exists()) {
            $email = str_replace('@', $counter . '@', $baseEmail);
            $counter++;
        }

        return $email;
    }

    /**
     * List all enrollments.
     */
    public function index(Request $request)
    {
        $query = Student::with(['user', 'classRoom'])->active();

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('admission_no', 'like', "%{$search}%")
              ->orWhere('student_id', 'like', "%{$search}%");
        }

        $students = $query->orderBy('admission_date', 'desc')->paginate(25);
        $classes = ClassRoom::all();

        return view('registrar.enrollment.index', compact('students', 'classes'));
    }
}
