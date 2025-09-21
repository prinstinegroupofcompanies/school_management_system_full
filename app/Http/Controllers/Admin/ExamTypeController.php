<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamType;
use Illuminate\Http\Request;

class ExamTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = ExamType::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $examTypes = $query->paginate(15);

        return view('admin.exams.types.index', compact('examTypes'));
    }

    public function create()
    {
        return view('admin.exams.types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:exam_types,code',
            'description' => 'nullable|string',
            'type' => 'required|in:written,oral,practical,online,mixed',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|numeric|min:0',
            'duration_minutes' => 'nullable|integer|min:1',
            'is_compulsory' => 'boolean',
            'counts_for_final' => 'boolean',
            'weightage_percentage' => 'required|integer|min:1|max:100',
            'status' => 'required|in:active,inactive',
            'is_active' => 'boolean',
        ]);

        ExamType::create($request->all());

        return redirect()->route('admin.exams.types.index')
                        ->with('success', 'Exam type created successfully.');
    }

    public function show(ExamType $examType)
    {
        $examType->load('examSchedules');
        return view('admin.exams.types.show', compact('examType'));
    }

    public function edit(ExamType $examType)
    {
        return view('admin.exams.types.edit', compact('examType'));
    }

    public function update(Request $request, ExamType $examType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:exam_types,code,' . $examType->id,
            'description' => 'nullable|string',
            'type' => 'required|in:written,oral,practical,online,mixed',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|numeric|min:0',
            'duration_minutes' => 'nullable|integer|min:1',
            'is_compulsory' => 'boolean',
            'counts_for_final' => 'boolean',
            'weightage_percentage' => 'required|integer|min:1|max:100',
            'status' => 'required|in:active,inactive',
            'is_active' => 'boolean',
        ]);

        $examType->update($request->all());

        return redirect()->route('admin.exams.types.index')
                        ->with('success', 'Exam type updated successfully.');
    }

    public function destroy(ExamType $examType)
    {
        // Check if exam type is being used
        if ($examType->examSchedules()->count() > 0) {
            return redirect()->route('admin.exams.types.index')
                            ->with('error', 'Cannot delete exam type that is being used in exam schedules.');
        }

        $examType->delete();

        return redirect()->route('admin.exams.types.index')
                        ->with('success', 'Exam type deleted successfully.');
    }
}
