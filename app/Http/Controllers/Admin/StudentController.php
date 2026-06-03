<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\ClassRoom;
use App\Models\StudentActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Student::with(['user', 'classRoom']);

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('admission_number', 'like', "%{$search}%");
            }

            // Filter by class
            if ($request->filled('class_id')) {
                $query->where('class_id', $request->class_id);
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('status', $request->status);
                });
            }

            $students = $query->paginate(15);
            $classes = ClassRoom::all();

            return view('admin.students.index', compact('students', 'classes'));
        } catch (\Exception $e) {
            \Log::error('StudentController index error: ' . $e->getMessage());
            $classes = collect();
            $students = collect()->paginate(15);
            return view('admin.students.index', compact('students', 'classes'));
        }
    }

    /**
     * Export students.
     */
    public function export(Request $request, $format = 'csv')
    {
        try {
            $query = Student::with(['user', 'classRoom']);

            // Apply same filters as index
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('admission_number', 'like', "%{$search}%");
            }

            if ($request->filled('class_id')) {
                $query->where('class_id', $request->class_id);
            }

            if ($request->filled('status')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('status', $request->status);
                });
            }

            $students = $query->get();

            $exportColumns = [
                'admission_no' => 'Admission Number',
                'student_id' => 'Student ID',
                'user.name' => 'Student Name',
                'user.email' => 'Email',
                'user.phone' => 'Phone',
                'classRoom.name' => 'Class',
                'gender' => 'Gender',
                'date_of_birth' => 'Date of Birth',
                'status' => 'Status',
            ];

            return (new \App\Services\ExportService)->export($students, $format, $exportColumns, 'students');
        } catch (\Exception $e) {
            \Log::error('Student export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $classes = ClassRoom::all();
            return view('admin.students.create', compact('classes'));
        } catch (\Exception $e) {
            return view('admin.students.create', ['classes' => collect()]);
        }
    }

    public function store(Request $request)
    {
        $existingParent = User::where('email', $request->guardian_email)->where('user_type', 'parent')->first();
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'class_id' => 'required|exists:class_rooms,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'nationality' => 'nullable|string|max:100',
            'religion' => 'nullable|string|max:100',
            'blood_group' => 'nullable|string|max:10',
            'guardian_email' => 'required|email',
        ];
        if (!$existingParent) {
            $rules['guardian_name'] = 'required|string|max:255';
            $rules['guardian_phone'] = 'required|string|max:20';
            $rules['guardian_relationship'] = 'required|string|max:100';
            $rules['guardian_password'] = ['required', 'confirmed', 'min:8'];
        }
        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'user_type' => 'student',
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'status' => 'active',
                'school_id' => auth()->user()->school_id,
            ]);

            if ($existingParent) {
                $guardian = \App\Models\Guardian::where('user_id', $existingParent->id)->first();
                if (!$guardian) {
                    $guardian = \App\Models\Guardian::create([
                        'user_id' => $existingParent->id,
                        'guardian_id' => 'G' . str_pad((\App\Models\Guardian::count() + 1), 4, '0', STR_PAD_LEFT),
                        'relationship' => 'guardian',
                        'status' => 'active',
                    ]);
                }
            } else {
                $guardianUser = User::create([
                    'name' => $validated['guardian_name'],
                    'email' => $validated['guardian_email'],
                    'password' => $validated['guardian_password'],
                    'user_type' => 'parent',
                    'phone' => $validated['guardian_phone'],
                    'status' => 'active',
                    'school_id' => auth()->user()->school_id,
                ]);
                $guardian = \App\Models\Guardian::create([
                    'user_id' => $guardianUser->id,
                    'guardian_id' => 'G' . str_pad((\App\Models\Guardian::count() + 1), 4, '0', STR_PAD_LEFT),
                    'relationship' => strtolower($validated['guardian_relationship']),
                    'status' => 'active',
                ]);
            }

            $admissionNo = 'ADM' . date('Y') . str_pad((Student::count() + 1), 4, '0', STR_PAD_LEFT);
            $student = Student::create([
                'user_id' => $user->id,
                'class_id' => $validated['class_id'],
                'admission_no' => $admissionNo,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'academic_year' => date('Y'),
                'admission_date' => now(),
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'nationality' => $validated['nationality'] ?? 'Liberian',
                'religion' => $validated['religion'] ?? null,
                'blood_group' => $validated['blood_group'] ?? null,
                'guardian_id' => $guardian->id,
                'status' => 'active',
                'is_active' => true,
            ]);

            DB::commit();

            $msg = 'Student created successfully! Admission Number: ' . $student->admission_no;
            if (!$existingParent) {
                $msg .= ' Parent can log in at ' . url('/login') . ' with email: ' . $validated['guardian_email'] . ' and the password you set.';
            } else {
                $msg .= ' Student linked to existing parent (' . $validated['guardian_email'] . ').';
            }
            return redirect()->route('admin.students.show', $student)->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Student creation error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create student: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    public function show(Student $student)
    {
        try {
            $student->load(['user', 'classRoom']);
            return view('admin.students.show', compact('student'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Student not found.']);
        }
    }

    public function edit(Student $student)
    {
        try {
            $classes = ClassRoom::all();
            $student->load(['user', 'classRoom']);
            return view('admin.students.edit', compact('student', 'classes'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Student not found.']);
        }
    }

    public function update(Request $request, Student $student)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $student->user_id,
                'class_id' => 'required|exists:class_rooms,id',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'date_of_birth' => 'required|date',
                'gender' => 'required|in:male,female,other',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'nationality' => 'nullable|string|max:100',
                'religion' => 'nullable|string|max:100',
                'blood_group' => 'nullable|string|max:10',
            ]);

            DB::beginTransaction();

            // Update user account
            $student->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            // Update student record
            $student->update([
                'class_id' => $request->class_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'phone' => $request->phone,
                'address' => $request->address,
                'nationality' => $request->nationality ?? 'Liberian',
                'religion' => $request->religion,
                'blood_group' => $request->blood_group,
            ]);

            DB::commit();

            return redirect()->route('admin.students.show', $student)->with('success', 'Student updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Student update error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update student: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    public function destroy(Student $student)
    {
        try {
            DB::beginTransaction();
            
            // Delete associated guardian if exists
            if ($student->guardian) {
                $student->guardian->user->delete();
                $student->guardian->delete();
            }
            
            // Delete student user account
            $student->user->delete();
            
            // Delete student record
            $student->delete();
            
            DB::commit();
            
            return redirect()->route('admin.students.index')->with('success', 'Student deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Student deletion error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete student: ' . $e->getMessage()]);
        }
    }

    /**
     * Admin: list grade periods and years for a student (for viewing/printing grade sheets).
     */
    public function grades(Student $student)
    {
        $student->load(['user', 'classRoom']);
        $periods = \App\Models\Grade::where('student_id', $student->id)
            ->select('academic_year', 'semester')
            ->distinct()
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->get()
            ->map(fn($g) => [
                'year' => $g->academic_year,
                'semester' => $g->semester,
                'period_name' => ($g->semester == 1 ? 'Semester 1 (Term 1)' : ($g->semester == 2 ? 'Semester 2 (Term 2)' : "Period {$g->semester}")) . " - {$g->academic_year}",
            ]);
        $yearsWithGrades = \App\Models\Grade::where('student_id', $student->id)
            ->where('status', 'approved')
            ->distinct()
            ->pluck('academic_year')
            ->sort()
            ->values();
        return view('admin.students.grades', compact('student', 'periods', 'yearsWithGrades'));
    }

    /**
     * Admin: view student grade sheet by year and semester (printable).
     */
    public function gradeSheet(Student $student, $year, $semester)
    {
        $student->load(['user', 'classRoom']);
        $grades = \App\Models\Grade::where('student_id', $student->id)
            ->where('academic_year', $year)
            ->where('semester', $semester)
            ->where('status', 'approved')
            ->with(['subject', 'class', 'teacher.user'])
            ->orderBy('subject_id')
            ->get();
        $stats = [
            'total_subjects' => $grades->count(),
            'average_score' => $grades->count() > 0 ? $grades->avg('year_avg') : 0,
            'highest_score' => $grades->count() > 0 ? $grades->max('year_avg') : 0,
            'lowest_score' => $grades->count() > 0 ? $grades->min('year_avg') : 0,
            'passed_subjects' => $grades->where('year_avg', '>=', 50)->count(),
            'failed_subjects' => $grades->where('year_avg', '<', 50)->count(),
        ];
        $adminSignature = \App\Models\User::where('user_type', 'admin')->first()?->signature ?? null;
        $school = \App\Models\School::first();
        return view('student.grades.grade-sheet', compact('year', 'semester', 'student', 'grades', 'stats', 'adminSignature', 'school'));
    }

    /**
     * Admin: download student grade sheet PDF by year and semester.
     */
    public function downloadGradeSheet(Student $student, $year, $semester)
    {
        $student->load(['user', 'classRoom']);
        $grades = \App\Models\Grade::where('student_id', $student->id)
            ->where('academic_year', $year)
            ->where('semester', $semester)
            ->where('status', 'approved')
            ->with(['subject', 'class', 'teacher.user'])
            ->orderBy('subject_id')
            ->get();
        $stats = [
            'total_subjects' => $grades->count(),
            'average_score' => $grades->count() > 0 ? $grades->avg('year_avg') : 0,
            'highest_score' => $grades->count() > 0 ? $grades->max('year_avg') : 0,
            'lowest_score' => $grades->count() > 0 ? $grades->min('year_avg') : 0,
            'passed_subjects' => $grades->where('year_avg', '>=', 50)->count(),
            'failed_subjects' => $grades->where('year_avg', '<', 50)->count(),
        ];
        $adminSignature = \App\Models\User::where('user_type', 'admin')->first()?->signature ?? null;
        $school = \App\Models\School::first();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('student.grades.grade-sheet-pdf', compact('year', 'semester', 'student', 'grades', 'stats', 'adminSignature', 'school'));
        $filename = "Grade_Sheet_Period_{$semester}_{$year}_" . ($student->admission_no ?? $student->id) . ".pdf";
        return $pdf->download($filename);
    }

    /**
     * Admin: view full-year grade sheet (yearly average, promotion 70%).
     */
    public function fullYearGradeSheet(Student $student, $year)
    {
        $student->load(['user', 'classRoom']);
        $grades = \App\Models\Grade::where('student_id', $student->id)
            ->where('academic_year', $year)
            ->where('status', 'approved')
            ->with(['subject', 'class', 'teacher.user'])
            ->orderBy('semester')->orderBy('subject_id')
            ->get();
        $yearlyAverage = $grades->count() > 0 ? round($grades->avg('year_avg'), 2) : 0;
        $bySemester = $grades->groupBy('semester');
        $stats = [
            'total_subjects' => $grades->unique('subject_id')->count(),
            'yearly_average' => $yearlyAverage,
            'eligible_for_promotion' => $yearlyAverage >= 70.0,
            'passed_subjects' => $grades->where('year_avg', '>=', 50)->count(),
            'failed_subjects' => $grades->where('year_avg', '<', 50)->count(),
        ];
        $adminSignature = \App\Models\User::where('user_type', 'admin')->first()?->signature ?? null;
        $school = \App\Models\School::first();
        return view('student.grades.grade-sheet-full-year', compact('year', 'student', 'grades', 'bySemester', 'stats', 'adminSignature', 'school'));
    }

    /**
     * Admin: download full-year grade sheet PDF.
     */
    public function downloadFullYearGradeSheet(Student $student, $year)
    {
        $student->load(['user', 'classRoom']);
        $grades = \App\Models\Grade::where('student_id', $student->id)
            ->where('academic_year', $year)
            ->where('status', 'approved')
            ->with(['subject', 'class', 'teacher.user'])
            ->orderBy('semester')->orderBy('subject_id')
            ->get();
        $yearlyAverage = $grades->count() > 0 ? round($grades->avg('year_avg'), 2) : 0;
        $bySemester = $grades->groupBy('semester');
        $stats = [
            'total_subjects' => $grades->unique('subject_id')->count(),
            'yearly_average' => $yearlyAverage,
            'eligible_for_promotion' => $yearlyAverage >= 70.0,
            'passed_subjects' => $grades->where('year_avg', '>=', 50)->count(),
            'failed_subjects' => $grades->where('year_avg', '<', 50)->count(),
        ];
        $adminSignature = \App\Models\User::where('user_type', 'admin')->first()?->signature ?? null;
        $school = \App\Models\School::first();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('student.grades.grade-sheet-full-year-pdf', compact('year', 'student', 'grades', 'bySemester', 'stats', 'adminSignature', 'school'));
        $filename = "Grade_Sheet_Full_Year_{$year}_" . ($student->admission_no ?? $student->id) . ".pdf";
        return $pdf->download($filename);
    }
}