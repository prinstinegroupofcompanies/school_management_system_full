<?php

namespace App\Http\Controllers;

use App\Models\Homework;
use App\Models\Subject;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeworkController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $homeworks = collect();
        
        if ($user->user_type === 'teacher') {
            $homeworks = Homework::where('teacher_id', $user->id)
                ->with(['subject', 'class'])
                ->latest()
                ->paginate(15);
        } elseif ($user->user_type === 'student') {
            $student = $user->student;
            if ($student && $student->class) {
                $homeworks = Homework::where('class_id', $student->class->id)
                    ->with(['subject', 'class', 'teacher'])
                    ->latest()
                    ->paginate(15);
            }
        } else {
            $homeworks = Homework::with(['subject', 'class', 'teacher'])
                ->latest()
                ->paginate(15);
        }
        
        return view('homework.index', compact('homeworks'));
    }

    public function create()
    {
        $subjects = Subject::all();
        $classes = ClassRoom::all();
        
        return view('homework.create', compact('subjects', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:class_rooms,id',
            'due_date' => 'required|date|after:today',
        ]);

        $homework = Homework::create([
            'title' => $request->title,
            'description' => $request->description,
            'subject_id' => $request->subject_id,
            'class_id' => $request->class_id,
            'teacher_id' => Auth::id(),
            'due_date' => $request->due_date,
            'status' => 'active',
        ]);

        return redirect()->route('homework.show', $homework)
            ->with('success', 'Homework created successfully.');
    }

    public function show(Homework $homework)
    {
        $homework->load(['subject', 'class', 'teacher']);
        
        return view('homework.show', compact('homework'));
    }

    public function edit(Homework $homework)
    {
        // Check if user is the teacher who created this homework
        if (Auth::id() !== $homework->teacher_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $subjects = Subject::all();
        $classes = ClassRoom::all();
        
        return view('homework.edit', compact('homework', 'subjects', 'classes'));
    }

    public function update(Request $request, Homework $homework)
    {
        // Check if user is the teacher who created this homework
        if (Auth::id() !== $homework->teacher_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:class_rooms,id',
            'due_date' => 'required|date',
            'max_score' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,completed',
        ]);

        $homework->update($request->all());

        return redirect()->route('homework.show', $homework)
            ->with('success', 'Homework updated successfully.');
    }

    public function destroy(Homework $homework)
    {
        // Check if user is the teacher who created this homework
        if (Auth::id() !== $homework->teacher_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $homework->delete();

        return redirect()->route('homework.index')
            ->with('success', 'Homework deleted successfully.');
    }
}
