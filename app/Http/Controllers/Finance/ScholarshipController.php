<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScholarshipController extends Controller
{
    public function index()
    {
        // Get real scholarship data from database
        $scholarships = Scholarship::with(['applications' => function($query) {
            $query->select('scholarship_id', DB::raw('count(*) as total_applicants'), 
                          DB::raw('sum(case when status = "approved" then 1 else 0 end) as awarded'));
        }])
        ->select('id', 'name', 'description', 'amount', 'type', 'is_active', 'application_deadline', 'max_recipients', 'current_recipients')
        ->get()
        ->map(function($scholarship) {
            $applications = $scholarship->applications->first();
            return [
                'id' => $scholarship->id,
                'name' => $scholarship->name,
                'description' => $scholarship->description,
                'amount' => $scholarship->amount,
                'type' => $scholarship->type,
                'status' => $scholarship->is_active ? 'active' : 'inactive',
                'applicants' => $applications ? $applications->total_applicants : 0,
                'awarded' => $applications ? $applications->awarded : 0,
                'deadline' => $scholarship->application_deadline ? $scholarship->application_deadline->format('Y-m-d') : null,
                'max_recipients' => $scholarship->max_recipients,
                'current_recipients' => $scholarship->current_recipients,
                'available_slots' => $scholarship->max_recipients - $scholarship->current_recipients
            ];
        });

        // Calculate statistics
        $totalScholarships = $scholarships->count();
        $totalAmount = $scholarships->sum('amount');
        $totalApplicants = $scholarships->sum('applicants');
        $totalAwarded = $scholarships->sum('awarded');

        $stats = [
            'total_scholarships' => $totalScholarships,
            'total_amount' => $totalAmount,
            'total_applicants' => $totalApplicants,
            'total_awarded' => $totalAwarded,
            'pending_applications' => $totalApplicants - $totalAwarded
        ];

        return view('finance.scholarships.index', compact('scholarships', 'stats'));
    }

    public function create()
    {
        // Get all active students for selection
        $students = Student::where('status', 'active')
            ->with(['classRoom', 'user'])
            ->select('id', 'name', 'email', 'class_id', 'admission_number')
            ->get();
        
        // Get all classes for filtering
        $classes = ClassRoom::where('is_active', true)
            ->select('id', 'name')
            ->get();
        
        // Get all subjects for filtering
        $subjects = Subject::where('is_active', true)
            ->select('id', 'name', 'class_id')
            ->get();

        return view('finance.scholarships.create', compact('students', 'classes', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|string|in:merit,need,sports,community,other',
            'application_deadline' => 'required|date|after:today',
            'max_recipients' => 'required|integer|min:1',
            'academic_year' => 'required|string',
            'class_id' => 'nullable|exists:class_rooms,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
        ]);

        try {
            DB::beginTransaction();

            // Create scholarship
            $scholarship = Scholarship::create([
                'name' => $request->name,
                'code' => 'SCH-' . strtoupper(substr($request->name, 0, 3)) . '-' . date('Y'),
                'description' => $request->description,
                'amount' => $request->amount,
                'type' => $request->type,
                'currency' => 'USD',
                'academic_year' => $request->academic_year,
                'class_id' => $request->class_id,
                'subject_id' => $request->subject_id,
                'application_deadline' => $request->application_deadline,
                'max_recipients' => $request->max_recipients,
                'current_recipients' => 0,
                'is_active' => true,
                'created_by' => auth()->id(),
                'is_merit_based' => $request->type === 'merit',
                'is_need_based' => $request->type === 'need',
                'is_sports_based' => $request->type === 'sports',
                'is_community_based' => $request->type === 'community',
            ]);

            // Award scholarships to selected students and clear their fees
            foreach ($request->student_ids as $studentId) {
                $student = Student::findOrFail($studentId);
                
                // Create scholarship application and mark as approved
                $application = ScholarshipApplication::create([
                    'scholarship_id' => $scholarship->id,
                    'student_id' => $studentId,
                    'application_number' => 'APP-' . $scholarship->id . '-' . $studentId . '-' . time(),
                    'application_date' => now(),
                    'status' => 'approved',
                    'submitted_at' => now(),
                    'approved_at' => now(),
                    'approved_by' => auth()->id(),
                    'final_decision' => 'approved',
                ]);

                // Clear student's outstanding fees for the academic year
                $this->clearStudentFees($student, $request->academic_year, $scholarship->amount);

                // Increment scholarship recipient count
                $scholarship->increment('current_recipients');
            }

            DB::commit();

            return redirect()->route('finance.scholarships.index')
                ->with('success', 'Scholarship created and awarded to ' . count($request->student_ids) . ' students successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create scholarship: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $scholarship = Scholarship::with(['applications.student.user', 'applications.student.classRoom'])
            ->findOrFail($id);

        // Get scholarship applications with student details
        $applications = $scholarship->applications()
            ->with(['student.user', 'student.classRoom'])
            ->get();

        // Calculate statistics
        $totalApplicants = $applications->count();
        $approvedApplicants = $applications->where('status', 'approved')->count();
        $pendingApplicants = $applications->where('status', 'pending')->count();
        $rejectedApplicants = $applications->where('status', 'rejected')->count();

        $stats = [
            'total_applicants' => $totalApplicants,
            'approved_applicants' => $approvedApplicants,
            'pending_applicants' => $pendingApplicants,
            'rejected_applicants' => $rejectedApplicants,
            'total_amount_awarded' => $approvedApplicants * $scholarship->amount,
            'available_slots' => $scholarship->max_recipients - $scholarship->current_recipients
        ];

        return view('finance.scholarships.show', compact('scholarship', 'applications', 'stats'));
    }

    public function edit($id)
    {
        $scholarship = Scholarship::findOrFail($id);
        
        // Get all active students for selection
        $students = Student::where('status', 'active')
            ->with(['classRoom', 'user'])
            ->select('id', 'name', 'email', 'class_id', 'admission_number')
            ->get();
        
        // Get all classes for filtering
        $classes = ClassRoom::where('is_active', true)
            ->select('id', 'name')
            ->get();
        
        // Get all subjects for filtering
        $subjects = Subject::where('is_active', true)
            ->select('id', 'name', 'class_id')
            ->get();

        // Get current scholarship recipients
        $currentRecipients = $scholarship->applications()
            ->where('status', 'approved')
            ->with(['student.user', 'student.classRoom'])
            ->get();

        return view('finance.scholarships.edit', compact('scholarship', 'students', 'classes', 'subjects', 'currentRecipients'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|string|in:merit,need,sports,community,other',
            'application_deadline' => 'required|date|after:today',
            'max_recipients' => 'required|integer|min:1',
            'academic_year' => 'required|string',
            'class_id' => 'nullable|exists:class_rooms,id',
            'subject_id' => 'nullable|exists:subjects,id',
        ]);

        try {
            $scholarship = Scholarship::findOrFail($id);
            
            $scholarship->update([
                'name' => $request->name,
                'description' => $request->description,
                'amount' => $request->amount,
                'type' => $request->type,
                'academic_year' => $request->academic_year,
                'class_id' => $request->class_id,
                'subject_id' => $request->subject_id,
                'application_deadline' => $request->application_deadline,
                'max_recipients' => $request->max_recipients,
                'is_merit_based' => $request->type === 'merit',
                'is_need_based' => $request->type === 'need',
                'is_sports_based' => $request->type === 'sports',
                'is_community_based' => $request->type === 'community',
            ]);

            return redirect()->route('finance.scholarships.index')
                ->with('success', 'Scholarship updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update scholarship: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $scholarship = Scholarship::findOrFail($id);
            
            // Check if scholarship has recipients
            if ($scholarship->current_recipients > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete scholarship with active recipients. Please remove all recipients first.');
            }
            
            $scholarship->delete();
            
            return redirect()->route('finance.scholarships.index')
                ->with('success', 'Scholarship deleted successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete scholarship: ' . $e->getMessage());
        }
    }

    /**
     * Clear student's outstanding fees for the academic year
     */
    private function clearStudentFees(Student $student, string $academicYear, float $scholarshipAmount)
    {
        // Get student's outstanding fees for the academic year
        $outstandingFees = DB::table('fee_payments')
            ->where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->where('status', 'pending')
            ->sum('amount');

        if ($outstandingFees > 0) {
            // Create a scholarship payment record to clear the fees
            DB::table('fee_payments')->insert([
                'student_id' => $student->id,
                'amount' => min($outstandingFees, $scholarshipAmount),
                'payment_date' => now(),
                'payment_method' => 'scholarship',
                'status' => 'paid',
                'academic_year' => $academicYear,
                'notes' => 'Fee cleared by scholarship',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update any remaining outstanding fees
            if ($scholarshipAmount > $outstandingFees) {
                $remainingAmount = $scholarshipAmount - $outstandingFees;
                
                // Create a scholarship stipend record for remaining amount
                DB::table('fee_payments')->insert([
                    'student_id' => $student->id,
                    'amount' => $remainingAmount,
                    'payment_date' => now(),
                    'payment_method' => 'scholarship_stipend',
                    'status' => 'paid',
                    'academic_year' => $academicYear,
                    'notes' => 'Scholarship stipend - remaining amount',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}