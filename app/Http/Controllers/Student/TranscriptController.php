<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Transcript;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TranscriptController extends Controller
{
    public function __construct()
    {
        $this->middleware('student');
    }

    public function index(Request $request)
    {
        $student = Student::where('user_id', Auth::id())->first();
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not found.');
        }

        $query = Transcript::where('student_id', $student->id)
            ->with(['class', 'generatedBy', 'approvedBy', 'issuedBy']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        $transcripts = $query->orderBy('created_at', 'desc')->paginate(10);

        // Get filter options
        $academicYears = Transcript::where('student_id', $student->id)
            ->distinct()->pluck('academic_year')->sort()->values();
        $statuses = ['generated', 'approved', 'issued'];
        $types = ['official', 'unofficial', 'interim'];

        return view('student.transcripts.index', compact(
            'transcripts', 'academicYears', 'statuses', 'types', 'student'
        ));
    }

    public function show(Transcript $transcript)
    {
        $student = Student::where('user_id', Auth::id())->first();
        
        if (!$student || $transcript->student_id !== $student->id) {
            return redirect()->route('student.transcripts.index')
                ->with('error', 'Transcript not found or access denied.');
        }

        $transcript->load([
            'student.user', 'class', 'transcriptGrades.subject', 'transcriptGrades.teacher.user',
            'generatedBy', 'approvedBy', 'issuedBy'
        ]);

        return view('student.transcripts.show', compact('transcript'));
    }

    public function downloadPdf(Transcript $transcript)
    {
        $student = Student::where('user_id', Auth::id())->first();
        
        if (!$student || $transcript->student_id !== $student->id) {
            return redirect()->route('student.transcripts.index')
                ->with('error', 'Transcript not found or access denied.');
        }

        if (!$transcript->isIssued() && !$transcript->isApproved()) {
            return redirect()->route('student.transcripts.show', $transcript)
                ->with('error', 'This transcript is not yet available for download.');
        }

        if ($transcript->pdf_path && \Storage::exists($transcript->pdf_path)) {
            return \Storage::download($transcript->pdf_path, "transcript_{$transcript->transcript_number}.pdf");
        }

        // Generate PDF if not exists
        $transcript->load([
            'student.user', 'class', 'transcriptGrades.subject', 'transcriptGrades.teacher.user',
            'generatedBy', 'approvedBy', 'issuedBy'
        ]);

        $pdf = \PDF::loadView('student.transcripts.pdf', compact('transcript'))
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial'
            ]);

        $filename = "transcript_{$transcript->transcript_number}.pdf";
        $path = "transcripts/{$filename}";

        // Store the PDF
        \Storage::put($path, $pdf->output());
        $transcript->update(['pdf_path' => $path]);

        return $pdf->download($filename);
    }

    public function requestTranscript(Request $request)
    {
        $student = Student::where('user_id', Auth::id())->first();
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not found.');
        }

        $request->validate([
            'academic_year' => 'required|string',
            'semester' => 'nullable|string',
            'type' => 'required|in:official,unofficial,interim',
            'purpose' => 'required|string|max:500'
        ]);

        try {
            // Check if transcript already exists
            $existingTranscript = Transcript::where('student_id', $student->id)
                ->where('academic_year', $request->academic_year)
                ->where('semester', $request->semester)
                ->where('type', $request->type)
                ->first();

            if ($existingTranscript) {
                return redirect()->route('student.transcripts.show', $existingTranscript)
                    ->with('info', 'Transcript already exists for the specified period.');
            }

            // Generate transcript
            $transcript = Transcript::generateForStudent(
                $student->id,
                $request->academic_year,
                $request->semester,
                $request->type
            );

            // Add request purpose as notes
            $transcript->update(['notes' => "Request Purpose: {$request->purpose}"]);

            return redirect()->route('student.transcripts.show', $transcript)
                ->with('success', 'Transcript request submitted successfully. It will be reviewed by the administration.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to request transcript: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $student = Student::where('user_id', Auth::id())->first();
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not found.');
        }

        $academicYears = $this->getAcademicYears();
        $semesters = ['First Semester', 'Second Semester', 'Annual'];
        $types = ['official', 'unofficial', 'interim'];

        return view('student.transcripts.create', compact(
            'academicYears', 'semesters', 'types', 'student'
        ));
    }

    public function statistics()
    {
        $student = Student::where('user_id', Auth::id())->first();
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not found.');
        }

        $transcripts = Transcript::where('student_id', $student->id)->get();

        $stats = [
            'total_transcripts' => $transcripts->count(),
            'official_transcripts' => $transcripts->where('type', 'official')->count(),
            'unofficial_transcripts' => $transcripts->where('type', 'unofficial')->count(),
            'issued_transcripts' => $transcripts->where('status', 'issued')->count(),
            'average_gpa' => $transcripts->whereNotNull('gpa')->avg('gpa'),
            'highest_gpa' => $transcripts->whereNotNull('gpa')->max('gpa'),
            'lowest_gpa' => $transcripts->whereNotNull('gpa')->min('gpa'),
            'academic_standing_distribution' => $transcripts->whereNotNull('academic_standing')
                ->groupBy('academic_standing')
                ->map->count(),
            'transcripts_by_year' => $transcripts->groupBy('academic_year')
                ->map->count(),
            'transcripts_by_status' => $transcripts->groupBy('status')
                ->map->count()
        ];

        return view('student.transcripts.statistics', compact('stats', 'student'));
    }

    private function getAcademicYears()
    {
        $currentYear = now()->year;
        $years = [];
        
        for ($i = -2; $i <= 2; $i++) {
            $year = $currentYear + $i;
            $years[] = "{$year}-" . ($year + 1);
        }
        
        return $years;
    }
}
