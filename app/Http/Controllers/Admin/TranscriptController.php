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
        $query = Transcript::with(['student.user', 'student.classRoom', 'generatedBy']);

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $transcripts = $query->orderBy('created_at', 'desc')->paginate(20);

        $academicYears = Transcript::distinct()->pluck('academic_year')->sort()->values();
        $classes = ClassRoom::orderBy('name')->get();

        return view('admin.transcripts.index', compact(
            'transcripts', 'academicYears', 'classes'
        ));
    }

    public function create()
    {
        $students = Student::with(['user', 'classRoom'])->orderBy('created_at', 'desc')->get();
        $classes = ClassRoom::orderBy('name')->get();
        $academicYears = $this->getAcademicYears();

        return view('admin.transcripts.create', compact(
            'students', 'classes', 'academicYears'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year' => 'required|string',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $student = Student::findOrFail($request->student_id);
            $year = (int) preg_replace('/^(\d+).*/', '$1', $request->academic_year);
            $transcript = Transcript::generateSimpleTranscript($student, $year);

            if ($request->filled('notes')) {
                $transcript->update(['remarks' => $request->notes]);
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
        $transcript->load(['student.user', 'student.classRoom', 'generatedBy']);

        return view('admin.transcripts.show', compact('transcript'));
    }

    public function generatePdf(Transcript $transcript)
    {
        $transcript->load(['student.user', 'student.classRoom', 'generatedBy']);
        $school = \App\Models\School::first();

        $pdf = Pdf::loadView('admin.transcripts.pdf', compact('transcript', 'school'))
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial'
            ]);

        $studentName = \Str::slug($transcript->student->user->name ?? 'student');
        $filename = "transcript_{$studentName}_{$transcript->academic_year}.pdf";
        $path = "transcripts/{$filename}";

        Storage::put($path, $pdf->output());

        return $pdf->download($filename);
    }

    public function approve(Request $request, Transcript $transcript)
    {
        return redirect()->route('admin.transcripts.show', $transcript)
            ->with('info', 'Approval workflow is not configured for this transcript type.');
    }

    public function issue(Request $request, Transcript $transcript)
    {
        return redirect()->route('admin.transcripts.show', $transcript)
            ->with('info', 'Issue workflow is not configured for this transcript type.');
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
