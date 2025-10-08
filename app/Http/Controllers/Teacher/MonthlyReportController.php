<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\MonthlyReport;
use App\Models\Staff;
use App\Models\MonthlyReportMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class MonthlyReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('teacher');
    }

    public function index(Request $request)
    {
        $teacher = Staff::where('user_id', Auth::id())->first();
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'Teacher profile not found.');
        }

        $query = MonthlyReport::where('staff_id', $teacher->id)
            ->with(['reviewedBy', 'approvedBy']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('report_type')) {
            $query->where('report_type', $request->report_type);
        }

        if ($request->filled('report_month')) {
            $query->where('report_month', $request->report_month);
        }

        if ($request->filled('report_year')) {
            $query->where('report_year', $request->report_year);
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(10);

        // Get filter options
        $months = MonthlyReport::where('staff_id', $teacher->id)
            ->distinct()->pluck('report_month')->sort()->values();
        $years = MonthlyReport::where('staff_id', $teacher->id)
            ->distinct()->pluck('report_year')->sort()->values();
        $statuses = ['draft', 'submitted', 'reviewed', 'approved', 'rejected'];
        $types = ['teacher', 'staff'];

        return view('teacher.monthly-reports.index', compact(
            'reports', 'months', 'years', 'statuses', 'types', 'teacher'
        ));
    }

    public function create()
    {
        $teacher = Staff::where('user_id', Auth::id())->first();
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'Teacher profile not found.');
        }

        $months = $this->getMonths();
        $years = $this->getYears();
        $types = ['teacher', 'staff'];

        return view('teacher.monthly-reports.create', compact(
            'months', 'years', 'types', 'teacher'
        ));
    }

    public function store(Request $request)
    {
        $teacher = Staff::where('user_id', Auth::id())->first();
        
        if (!$teacher) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'Teacher profile not found.');
        }

        $request->validate([
            'report_month' => 'required|string',
            'report_year' => 'required|string',
            'report_type' => 'required|in:teacher,staff',
            'executive_summary' => 'nullable|string|max:2000',
            'key_achievements' => 'nullable|string|max:2000',
            'challenges_faced' => 'nullable|string|max:2000',
            'next_month_goals' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            // Check if report already exists
            $existingReport = MonthlyReport::where('staff_id', $teacher->id)
                ->where('report_month', $request->report_month)
                ->where('report_year', $request->report_year)
                ->where('report_type', $request->report_type)
                ->first();

            if ($existingReport) {
                return redirect()->route('teacher.monthly-reports.show', $existingReport)
                    ->with('info', 'Report already exists for the specified period.');
            }

            // Generate report
            $report = MonthlyReport::generateForStaff(
                $teacher->id,
                $request->report_month,
                $request->report_year,
                $request->report_type
            );

            // Update with teacher input
            $report->update([
                'executive_summary' => $request->executive_summary,
                'key_achievements' => $request->key_achievements,
                'challenges_faced' => $request->challenges_faced,
                'next_month_goals' => $request->next_month_goals,
                'notes' => $request->notes,
                'is_auto_generated' => false
            ]);

            DB::commit();

            return redirect()->route('teacher.monthly-reports.show', $report)
                ->with('success', 'Monthly report created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Failed to create monthly report: ' . $e->getMessage());
        }
    }

    public function show(MonthlyReport $monthlyReport)
    {
        $teacher = Staff::where('user_id', Auth::id())->first();
        
        if (!$teacher || $monthlyReport->staff_id !== $teacher->id) {
            return redirect()->route('teacher.monthly-reports.index')
                ->with('error', 'Report not found or access denied.');
        }

        $monthlyReport->load([
            'staff.user', 'metrics', 'reviewedBy', 'approvedBy'
        ]);

        return view('teacher.monthly-reports.show', compact('monthlyReport'));
    }

    public function submit(MonthlyReport $monthlyReport)
    {
        $teacher = Staff::where('user_id', Auth::id())->first();
        
        if (!$teacher || $monthlyReport->staff_id !== $teacher->id) {
            return redirect()->route('teacher.monthly-reports.index')
                ->with('error', 'Report not found or access denied.');
        }

        if (!$monthlyReport->canBeSubmitted()) {
            return redirect()->route('teacher.monthly-reports.show', $monthlyReport)
                ->with('error', 'This report cannot be submitted.');
        }

        $monthlyReport->submit();

        return redirect()->route('teacher.monthly-reports.show', $monthlyReport)
            ->with('success', 'Monthly report submitted successfully.');
    }

    private function getMonths()
    {
        return [
            '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
            '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
            '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
        ];
    }

    private function getYears()
    {
        $currentYear = now()->year;
        $years = [];
        
        for ($i = -2; $i <= 2; $i++) {
            $years[] = $currentYear + $i;
        }
        
        return $years;
    }
}
