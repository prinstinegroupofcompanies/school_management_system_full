<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transcript;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TranscriptController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = Transcript::with(['student.user', 'class', 'generatedBy', 'approvedBy', 'issuedBy']);

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

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student.user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('transcript_number', 'like', "%{$search}%");
        }

        $transcripts = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $academicYears = Transcript::distinct()->pluck('academic_year')->sort()->values();
        $classes = ClassRoom::orderBy('name')->get();
        $statuses = ['draft', 'generated', 'approved', 'issued', 'archived'];
        $types = ['official', 'unofficial', 'interim'];

        return view('admin.transcripts.index', compact(
            'transcripts', 'academicYears', 'classes', 'statuses', 'types'
        ));
    }

    public function create()
    {
        $students = Student::with(['user', 'classRoom'])->get();
        $classes = ClassRoom::orderBy('name')->get();
        $academicYears = $this->getAcademicYears();
        $semesters = ['First Semester', 'Second Semester', 'Annual'];
        $types = ['official', 'unofficial', 'interim'];

        return view('admin.transcripts.create', compact(
            'students', 'classes', 'academicYears', 'semesters', 'types'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year' => 'required|string',
            'semester' => 'nullable|string',
            'type' => 'required|in:official,unofficial,interim',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $transcript = Transcript::generateForStudent(
                $request->student_id,
                $request->academic_year,
                $request->semester,
                $request->type
            );

            if ($request->filled('notes')) {
                $transcript->update(['notes' => $request->notes]);
            }

            DB::commit();

            return redirect()->route('admin.transcripts.show', $transcript)
                ->with('success', 'Transcript generated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transcript generation failed: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Failed to generate transcript: ' . $e->getMessage());
        }
    }

    public function show(Transcript $transcript)
    {
        $transcript->load([
            'student.user', 'class', 'transcriptGrades.subject', 'transcriptGrades.teacher.user',
            'generatedBy', 'approvedBy', 'issuedBy'
        ]);

        return view('admin.transcripts.show', compact('transcript'));
    }

    public function generatePdf(Transcript $transcript)
    {
        $transcript->load([
            'student.user', 'class', 'transcriptGrades.subject', 'transcriptGrades.teacher.user',
            'generatedBy', 'approvedBy', 'issuedBy'
        ]);

        $pdf = Pdf::loadView('admin.transcripts.pdf', compact('transcript'))
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial'
            ]);

        $filename = "transcript_{$transcript->transcript_number}.pdf";
        $path = "transcripts/{$filename}";

        // Store the PDF
        Storage::put($path, $pdf->output());
        $transcript->update(['pdf_path' => $path]);

        return $pdf->download($filename);
    }

    public function approve(Request $request, Transcript $transcript)
    {
        if (!$transcript->canBeApproved()) {
            return redirect()->route('admin.transcripts.show', $transcript)
                ->with('error', 'This transcript cannot be approved.');
        }

        $request->validate([
            'approver_signature' => 'nullable|string',
            'notes' => 'nullable|string|max:1000'
        ]);

        $transcript->approve();

        if ($request->filled('approver_signature')) {
            $transcript->update(['approver_signature' => $request->approver_signature]);
        }

        if ($request->filled('notes')) {
            $transcript->update(['notes' => $request->notes]);
        }

        return redirect()->route('admin.transcripts.show', $transcript)
            ->with('success', 'Transcript approved successfully.');
    }

    public function issue(Request $request, Transcript $transcript)
    {
        if (!$transcript->canBeIssued()) {
            return redirect()->route('admin.transcripts.show', $transcript)
                ->with('error', 'This transcript cannot be issued.');
        }

        $request->validate([
            'registrar_signature' => 'nullable|string',
            'watermark' => 'nullable|string',
            'valid_until' => 'nullable|date|after:today'
        ]);

        $transcript->issue();

        if ($request->filled('registrar_signature')) {
            $transcript->update(['registrar_signature' => $request->registrar_signature]);
        }

        if ($request->filled('watermark')) {
            $transcript->update(['watermark' => $request->watermark]);
        }

        if ($request->filled('valid_until')) {
            $transcript->update(['valid_until' => $request->valid_until]);
        }

        return redirect()->route('admin.transcripts.show', $transcript)
            ->with('success', 'Transcript issued successfully.');
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
