<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudyMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudyMaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware('student');
    }

    /**
     * Display study materials for student
     */
    public function index(Request $request)
    {
        $student = $request->user()->student;
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                           ->withErrors(['error' => 'Student profile not found.']);
        }

        // Get study materials for student's subjects (via class subjects)
        $materials = StudyMaterial::whereIn('subject_id', $student->subjects->pluck('id'))
            ->with(['subject', 'teacher.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('student.study-materials.index', compact('materials'));
    }

    /**
     * Display specific study material
     */
    public function show(StudyMaterial $material)
    {
        $student = auth()->user()->student;
        
        if (!$student) {
            abort(403, 'Student record not found');
        }

        // Check if student has access to this material (via subject assignment)
        $hasAccess = $student->subjects->contains($material->subject_id);
        if (!$hasAccess) {
            abort(403, 'You do not have access to this study material');
        }

        $material->load(['subject', 'teacher.user']);

        return view('student.study-materials.show', compact('material'));
    }

    /**
     * Download study material file
     */
    public function download(StudyMaterial $material)
    {
        $student = auth()->user()->student;
        
        if (!$student) {
            abort(403, 'Student record not found');
        }

        // Check if student has access to this material
        $hasAccess = $student->subjects->contains($material->subject_id);
        if (!$hasAccess) {
            abort(403, 'You do not have access to this study material');
        }

        if ($material->type === 'file' && $material->file_path) {
            if (Storage::disk('public')->exists($material->file_path)) {
                return Storage::disk('public')->download($material->file_path, $material->title . '.' . pathinfo($material->file_path, PATHINFO_EXTENSION));
            }
        }

        return redirect()->back()->withErrors(['error' => 'File not found or not downloadable.']);
    }
}
