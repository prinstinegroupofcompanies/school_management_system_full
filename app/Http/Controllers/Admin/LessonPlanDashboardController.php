<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LessonPlan;
use App\Models\Teacher;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LessonPlanDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function dashboard()
    {
        // Get statistics
        $stats = [
            'total_lesson_plans' => LessonPlan::count(),
            'pending_approvals' => LessonPlan::where('status', 'pending')->count(),
            'approved_plans' => LessonPlan::where('status', 'approved')->count(),
            'rejected_plans' => LessonPlan::where('status', 'rejected')->count(),
        ];

        // Get recent lesson plans
        $recentLessonPlans = LessonPlan::with(['teacher.user', 'subject'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.lesson-plans.dashboard', compact('stats', 'recentLessonPlans'));
    }
}
