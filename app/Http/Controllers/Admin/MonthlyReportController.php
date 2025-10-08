<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonthlyReport;
use App\Models\Staff;
use App\Models\MonthlyReportMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MonthlyReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = MonthlyReport::with(['staff.user', 'reviewedBy', 'approvedBy']);

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

        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('staff.user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('report_number', 'like', "%{$search}%");
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $months = MonthlyReport::distinct()->pluck('report_month')->sort()->values();
        $years = MonthlyReport::distinct()->pluck('report_year')->sort()->values();
        $staff = Staff::with('user')->orderBy('id')->get();
        $statuses = ['draft', 'submitted', 'reviewed', 'approved', 'rejected'];
        $types = ['teacher', 'staff', 'department', 'school'];

        return view('admin.monthly-reports.index', compact(
            'reports', 'months', 'years', 'staff', 'statuses', 'types'
        ));
    }

    public function create()
    {
        $staff = Staff::with(['user'])->get();
        $months = $this->getMonths();
        $years = $this->getYears();
        $types = ['teacher', 'staff', 'department', 'school'];

        return view('admin.monthly-reports.create', compact(
            'staff', 'months', 'years', 'types'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'report_month' => 'required|string',
            'report_year' => 'required|string',
            'report_type' => 'required|in:teacher,staff,department,school',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $report = MonthlyReport::generateForStaff(
                $request->staff_id,
                $request->report_month,
                $request->report_year,
                $request->report_type
            );

            if ($request->filled('notes')) {
                $report->update(['notes' => $request->notes]);
            }

            DB::commit();

            return redirect()->route('admin.monthly-reports.show', $report)
                ->with('success', 'Monthly report generated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Monthly report generation failed: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Failed to generate monthly report: ' . $e->getMessage());
        }
    }

    public function show(MonthlyReport $monthlyReport)
    {
        $monthlyReport->load([
            'staff.user', 'metrics', 'reviewedBy', 'approvedBy'
        ]);

        return view('admin.monthly-reports.show', compact('monthlyReport'));
    }

    public function edit(MonthlyReport $monthlyReport)
    {
        if (!$monthlyReport->canBeEdited()) {
            return redirect()->route('admin.monthly-reports.show', $monthlyReport)
                ->with('error', 'This report cannot be edited.');
        }

        $monthlyReport->load(['staff.user', 'metrics']);
        $staff = Staff::with(['user'])->get();
        $months = $this->getMonths();
        $years = $this->getYears();
        $types = ['teacher', 'staff', 'department', 'school'];

        return view('admin.monthly-reports.edit', compact(
            'monthlyReport', 'staff', 'months', 'years', 'types'
        ));
    }

    public function update(Request $request, MonthlyReport $monthlyReport)
    {
        if (!$monthlyReport->canBeEdited()) {
            return redirect()->route('admin.monthly-reports.show', $monthlyReport)
                ->with('error', 'This report cannot be edited.');
        }

        $request->validate([
            'report_month' => 'required|string',
            'report_year' => 'required|string',
            'report_type' => 'required|in:teacher,staff,department,school',
            'notes' => 'nullable|string|max:1000',
            'executive_summary' => 'nullable|string|max:2000',
            'detailed_analysis' => 'nullable|string|max:5000',
            'recommendations' => 'nullable|string|max:2000',
            'action_items' => 'nullable|string|max:2000'
        ]);

        $monthlyReport->update($request->only([
            'report_month', 'report_year', 'report_type', 'notes',
            'executive_summary', 'detailed_analysis', 'recommendations', 'action_items'
        ]));

        return redirect()->route('admin.monthly-reports.show', $monthlyReport)
            ->with('success', 'Monthly report updated successfully.');
    }

    public function destroy(MonthlyReport $monthlyReport)
    {
        if (!$monthlyReport->canBeEdited()) {
            return redirect()->route('admin.monthly-reports.show', $monthlyReport)
                ->with('error', 'This report cannot be deleted.');
        }

        // Delete associated files
        if ($monthlyReport->pdf_path && Storage::exists($monthlyReport->pdf_path)) {
            Storage::delete($monthlyReport->pdf_path);
        }

        if ($monthlyReport->excel_path && Storage::exists($monthlyReport->excel_path)) {
            Storage::delete($monthlyReport->excel_path);
        }

        $monthlyReport->delete();

        return redirect()->route('admin.monthly-reports.index')
            ->with('success', 'Monthly report deleted successfully.');
    }

    public function approve(Request $request, MonthlyReport $monthlyReport)
    {
        if (!$monthlyReport->canBeApproved()) {
            return redirect()->route('admin.monthly-reports.show', $monthlyReport)
                ->with('error', 'This report cannot be approved.');
        }

        $request->validate([
            'review_comments' => 'nullable|string|max:1000'
        ]);

        $monthlyReport->approve(auth()->id(), $request->review_comments);

        return redirect()->route('admin.monthly-reports.show', $monthlyReport)
            ->with('success', 'Monthly report approved successfully.');
    }

    public function reject(Request $request, MonthlyReport $monthlyReport)
    {
        if (!$monthlyReport->canBeRejected()) {
            return redirect()->route('admin.monthly-reports.show', $monthlyReport)
                ->with('error', 'This report cannot be rejected.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        $monthlyReport->reject(auth()->id(), $request->rejection_reason);

        return redirect()->route('admin.monthly-reports.show', $monthlyReport)
            ->with('success', 'Monthly report rejected successfully.');
    }

    public function generatePdf(MonthlyReport $monthlyReport)
    {
        $monthlyReport->load([
            'staff.user', 'metrics', 'reviewedBy', 'approvedBy'
        ]);

        $pdf = Pdf::loadView('admin.monthly-reports.pdf', compact('monthlyReport'))
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial'
            ]);

        $filename = "monthly_report_{$monthlyReport->report_number}.pdf";
        $path = "monthly-reports/{$filename}";

        // Store the PDF
        Storage::put($path, $pdf->output());
        $monthlyReport->update(['pdf_path' => $path]);

        return $pdf->download($filename);
    }

    public function bulkGenerate(Request $request)
    {
        $request->validate([
            'staff_ids' => 'required|array|min:1',
            'staff_ids.*' => 'exists:staff,id',
            'report_month' => 'required|string',
            'report_year' => 'required|string',
            'report_type' => 'required|in:teacher,staff,department,school'
        ]);

        $results = [];
        $successCount = 0;
        $errorCount = 0;

        foreach ($request->staff_ids as $staffId) {
            try {
                $report = MonthlyReport::generateForStaff(
                    $staffId,
                    $request->report_month,
                    $request->report_year,
                    $request->report_type
                );
                $results[] = ['staff_id' => $staffId, 'status' => 'success', 'report' => $report];
                $successCount++;
            } catch (\Exception $e) {
                $results[] = ['staff_id' => $staffId, 'status' => 'error', 'message' => $e->getMessage()];
                $errorCount++;
            }
        }

        return redirect()->route('admin.monthly-reports.index')
            ->with('success', "Bulk generation completed. Success: {$successCount}, Errors: {$errorCount}")
            ->with('bulk_results', $results);
    }

    public function statistics()
    {
        $stats = [
            'total_reports' => MonthlyReport::count(),
            'teacher_reports' => MonthlyReport::where('report_type', 'teacher')->count(),
            'staff_reports' => MonthlyReport::where('report_type', 'staff')->count(),
            'submitted_reports' => MonthlyReport::where('status', 'submitted')->count(),
            'approved_reports' => MonthlyReport::where('status', 'approved')->count(),
            'rejected_reports' => MonthlyReport::where('status', 'rejected')->count(),
            'average_performance_score' => MonthlyReport::whereNotNull('overall_performance_score')->avg('overall_performance_score'),
            'reports_by_month' => MonthlyReport::selectRaw('report_month, COUNT(*) as count')
                ->groupBy('report_month')
                ->orderBy('report_month')
                ->get(),
            'reports_by_status' => MonthlyReport::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get(),
            'reports_by_type' => MonthlyReport::selectRaw('report_type, COUNT(*) as count')
                ->groupBy('report_type')
                ->get(),
            'performance_distribution' => MonthlyReport::selectRaw('
                CASE 
                    WHEN overall_performance_score >= 90 THEN "excellent"
                    WHEN overall_performance_score >= 80 THEN "good"
                    WHEN overall_performance_score >= 70 THEN "satisfactory"
                    WHEN overall_performance_score >= 60 THEN "needs_improvement"
                    ELSE "poor"
                END as performance_level,
                COUNT(*) as count
            ')
            ->whereNotNull('overall_performance_score')
            ->groupBy('performance_level')
            ->get()
        ];

        return view('admin.monthly-reports.statistics', compact('stats'));
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
