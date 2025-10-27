<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\StudyMaterial;
use App\Models\Subject;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudyMaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware('teacher');
    }

    public function index()
    {
        $teacher = auth()->user()->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')
                           ->withErrors(['error' => 'Teacher profile not found.']);
        }

        $materials = StudyMaterial::where('teacher_id', $teacher->user_id)
                                 ->with(['subject', 'class'])
                                 ->latest()
                                 ->paginate(15);

        return view('teacher.study-materials.index', compact('materials'));
    }

    public function create()
    {
        $teacher = auth()->user()->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')
                           ->withErrors(['error' => 'Teacher profile not found.']);
        }

        $subjects = Subject::where('teacher_id', $teacher->id)->get();
        $classes = ClassRoom::whereHas('subjects', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->get();

        return view('teacher.study-materials.create', compact('subjects', 'classes'));
    }

    public function store(Request $request)
    {
        $teacher = auth()->user()->teacher;
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')
                           ->withErrors(['error' => 'Teacher profile not found.']);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:class_rooms,id',
            'material_type' => 'required|in:document,video,link,other',
            'file' => 'nullable|file|max:10240', // 10MB max
            'link_url' => 'nullable|url',
        ]);

        // Verify teacher owns the subject
        $subject = Subject::where('id', $request->subject_id)
                         ->where('teacher_id', $teacher->id)
                         ->first();
        
        if (!$subject) {
            return back()->withErrors(['subject_id' => 'You are not authorized to upload materials for this subject.'])
                        ->withInput();
        }

        $material = new StudyMaterial();
        $material->title = $request->title;
        $material->description = $request->description;
        $material->subject_id = $request->subject_id;
        $material->class_id = $request->class_id;
        $material->teacher_id = $teacher->user_id; // Use user_id instead of teacher model id
        $material->type = $request->material_type;
        $material->link = $request->link_url;

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('study-materials', $filename, 'public');
            $material->file_path = $path;
            $material->file_name = $file->getClientOriginalName();
            $material->file_size = $file->getSize();
        }

        $material->save();

        return redirect()->route('teacher.study-materials.index')
                        ->with('success', 'Study material uploaded successfully.');
    }

    public function show(StudyMaterial $material)
    {
        $teacher = auth()->user()->teacher;
        
        if ($material->teacher_id !== $teacher->user_id) {
            return redirect()->route('teacher.study-materials.index')
                           ->withErrors(['error' => 'You are not authorized to view this material.']);
        }

        $material->load(['subject', 'class']);
        return view('teacher.study-materials.show', compact('material'));
    }

    public function edit(StudyMaterial $material)
    {
        $teacher = auth()->user()->teacher;
        
        if ($material->teacher_id !== $teacher->user_id) {
            return redirect()->route('teacher.study-materials.index')
                           ->withErrors(['error' => 'You are not authorized to edit this material.']);
        }

        $subjects = Subject::where('teacher_id', $teacher->id)->get();
        $classes = ClassRoom::whereHas('subjects', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->get();

        return view('teacher.study-materials.edit', compact('material', 'subjects', 'classes'));
    }

    public function update(Request $request, StudyMaterial $material)
    {
        $teacher = auth()->user()->teacher;
        
        if ($material->teacher_id !== $teacher->user_id) {
            return redirect()->route('teacher.study-materials.index')
                           ->withErrors(['error' => 'You are not authorized to update this material.']);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:class_rooms,id',
            'material_type' => 'required|in:document,video,link,other',
            'file' => 'nullable|file|max:10240',
            'link_url' => 'nullable|url',
        ]);

        // Verify teacher owns the subject
        $subject = Subject::where('id', $request->subject_id)
                         ->where('teacher_id', $teacher->id)
                         ->first();
        
        if (!$subject) {
            return back()->withErrors(['subject_id' => 'You are not authorized to upload materials for this subject.'])
                        ->withInput();
        }

        $material->title = $request->title;
        $material->description = $request->description;
        $material->subject_id = $request->subject_id;
        $material->class_id = $request->class_id;
        $material->type = $request->material_type;
        $material->link = $request->link_url;

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file
            if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
                Storage::disk('public')->delete($material->file_path);
            }

            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('study-materials', $filename, 'public');
            $material->file_path = $path;
            $material->file_name = $file->getClientOriginalName();
            $material->file_size = $file->getSize();
        }

        $material->save();

        return redirect()->route('teacher.study-materials.index')
                        ->with('success', 'Study material updated successfully.');
    }

    public function destroy(StudyMaterial $material)
    {
        $teacher = auth()->user()->teacher;
        
        if ($material->teacher_id !== $teacher->user_id) {
            return redirect()->route('teacher.study-materials.index')
                           ->withErrors(['error' => 'You are not authorized to delete this material.']);
        }

        // Delete file if exists
        if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
            Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();

        return redirect()->route('teacher.study-materials.index')
                        ->with('success', 'Study material deleted successfully.');
    }
}
