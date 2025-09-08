<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\Request;

class GradeApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $grades = Grade::with(['student.user','subject','teacher.user'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()->paginate(20);
        return view('admin.grades.index', compact('grades','status'));
    }

    public function show(Grade $grade)
    {
        $grade->load(['student.user','class','subject','teacher.user']);
        return view('admin.grades.show', compact('grade'));
    }

    public function approve(Grade $grade)
    {
        $grade->calculateSemesterAverages();
        $grade->determinePromotionAndHonors();
        $grade->status = 'approved';
        $grade->approved_by = auth()->id();
        $grade->approved_at = now();
        $grade->save();
        return redirect()->route('admin.grades.index')->with('success','Grade approved.');
    }

    public function reject(Grade $grade)
    {
        $grade->status = 'rejected';
        $grade->approved_by = auth()->id();
        $grade->approved_at = now();
        $grade->save();
        return redirect()->route('admin.grades.index')->with('success','Grade rejected.');
    }
}


