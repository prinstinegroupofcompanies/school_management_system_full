<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guardian;
use App\Models\Grade;
use App\Models\Student;

class ParentController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        
        // Get guardian record
        $guardian = Guardian::where('user_id', $user->id)->first();
        
        // Get children (students) linked to this guardian
        $students = $guardian 
            ? Student::where('guardian_id', $guardian->id)
                     ->orWhere('father_id', $guardian->id)
                     ->orWhere('mother_id', $guardian->id)
                     ->orWhere('local_guardian_id', $guardian->id)
                     ->with(['user', 'classRoom'])
                     ->get()
            : collect();
        
        // Calculate average grade
        $averageGrade = $students->avg(function($student) {
            // Placeholder - replace with actual grade calculation
            return 0;
        }) ?? 0;
        
        // Calculate outstanding fees
        $outstandingFees = $students->sum(function($student) {
            return $student->getFeesBalanceAttribute() ?? 0;
        });
        
        // Get recent notifications (placeholder)
        $recentNotifications = collect(); // TODO: Implement notification system
        
        // Compute average grade per student for display
        $childrenWithAvg = $students->map(function ($student) {
            $student->averageGrade = Grade::where('student_id', $student->id)
                ->where('status', 'approved')
                ->whereNotNull('year_avg')
                ->avg('year_avg');
            return $student;
        });

        return view('dashboard.parent', [
            'user' => $user,
            'currentUser' => $user,
            'guardian' => $guardian,
            'children' => $childrenWithAvg,
            'totalChildren' => $students->count(),
            'averageGrade' => $averageGrade,
            'outstandingFees' => $outstandingFees,
            'recentNotifications' => $recentNotifications,
        ]);
    }

    public function grades()
    {
        return view('parent.grades.index');
    }

    public function progress()
    {
        return view('parent.grades.progress');
    }

    public function download()
    {
        return view('parent.grades.download');
    }
}
