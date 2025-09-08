<?php

namespace App\Http\Controllers;

use App\Models\StudyMaterial;
use App\Models\Subject;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudyMaterialController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $materials = collect();
        $subject = null;
        
        if ($request->has('subject')) {
            $subject = Subject::findOrFail($request->subject);
        }
        
        if ($user->user_type === 'teacher') {
            $query = StudyMaterial::where('teacher_id', $user->id);
            if ($subject) {
                $query->where('subject_id', $subject->id);
            }
            $materials = $query->with(['subject', 'class'])
                ->latest()
                ->paginate(15);
        } elseif ($user->user_type === 'student') {
            $student = $user->student;
            if ($student && $student->class) {
                $query = StudyMaterial::where('class_id', $student->class->id);
                if ($subject) {
                    $query->where('subject_id', $subject->id);
                }
                $materials = $query->with(['subject', 'class', 'teacher'])
                    ->latest()
                    ->paginate(15);
            }
        } else {
            $query = StudyMaterial::with(['subject', 'class', 'teacher']);
            if ($subject) {
                $query->where('subject_id', $subject->id);
            }
            $materials = $query->latest()->paginate(15);
        }
        
        $subjects = Subject::all();
        
        return view('study-materials.index', compact('materials', 'subjects', 'subject'));
    }

    public function create()
    {
        $subjects = Subject::all();
        $classes = ClassRoom::all();
        
        return view('study-materials.create', compact('subjects', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:class_rooms,id',
            'type' => 'required|in:document,video,link,other',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,txt,jpg,jpeg,png|max:10240',
            'link' => 'nullable|url',
            'tags' => 'nullable|string',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'subject_id' => $request->subject_id,
            'class_id' => $request->class_id,
            'teacher_id' => Auth::id(),
            'type' => $request->type,
            'tags' => $request->tags,
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('study-materials', 'public');
            $data['file_path'] = $path;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
        }

        if ($request->link) {
            $data['link'] = $request->link;
        }

        // Handle tags - convert comma-separated string to array
        if ($request->tags) {
            $tags = array_map('trim', explode(',', $request->tags));
            $tags = array_filter($tags); // Remove empty tags
            $data['tags'] = $tags;
        }

        $material = StudyMaterial::create($data);

        return redirect()->route('study-materials.show', $material)
            ->with('success', 'Study material created successfully.');
    }

    public function show(StudyMaterial $material)
    {
        $material->load(['subject', 'class', 'teacher']);
        
        return view('study-materials.show', compact('material'));
    }

    public function edit(StudyMaterial $material)
    {
        // Check if user is the teacher who created this material
        if (Auth::id() !== $material->teacher_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $subjects = Subject::all();
        $classes = ClassRoom::all();
        
        return view('study-materials.edit', compact('material', 'subjects', 'classes'));
    }

    public function update(Request $request, StudyMaterial $material)
    {
        // Check if user is the teacher who created this material
        if (Auth::id() !== $material->teacher_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:class_rooms,id',
            'type' => 'required|in:document,video,link,other',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,txt,jpg,jpeg,png|max:10240',
            'link' => 'nullable|url',
            'tags' => 'nullable|string',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'subject_id' => $request->subject_id,
            'class_id' => $request->class_id,
            'type' => $request->type,
            'tags' => $request->tags,
        ];

        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }
            
            $file = $request->file('file');
            $path = $file->store('study-materials', 'public');
            $data['file_path'] = $path;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
        }

        if ($request->link) {
            $data['link'] = $request->link;
        }

        // Handle tags - convert comma-separated string to array
        if ($request->tags) {
            $tags = array_map('trim', explode(',', $request->tags));
            $tags = array_filter($tags); // Remove empty tags
            $data['tags'] = $tags;
        }

        $material->update($data);

        return redirect()->route('study-materials.show', $material)
            ->with('success', 'Study material updated successfully.');
    }

    public function destroy(StudyMaterial $material)
    {
        // Check if user is the teacher who created this material
        if (Auth::id() !== $material->teacher_id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Delete file if exists
        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }
        
        $material->delete();

        return redirect()->route('study-materials.index')
            ->with('success', 'Study material deleted successfully.');
    }

    public function download(StudyMaterial $material)
    {
        if (!$material->file_path) {
            abort(404, 'No file available for download.');
        }

        return Storage::disk('public')->download($material->file_path, $material->file_name);
    }
}
