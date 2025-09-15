<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Staff;
use App\Models\FeePayment;
use App\Models\Payroll;
use App\Models\StudentAttendance;
use App\Models\Grade;
use App\Models\ExamSchedule;
use App\Models\BookIssue;
use App\Models\Notification;
use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('finance');
    }

    public function index()
    {
        return view('admin.reports.index');
    }

    public function financial(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        
        // Financial Statistics
        $financialStats = [
            'total_revenue' => FeePayment::sum('amount_paid'),
            'total_fees' => DB::table('fee_structures')->sum('amount'),
            'collected_today' => FeePayment::whereDate('payment_date', today())->sum('amount_paid'),
            'collected_this_month' => FeePayment::whereYear('payment_date', date('Y'))
                ->whereMonth('payment_date', date('m'))
                ->sum('amount_paid'),
            'pending_payments' => max(DB::table('fee_structures')->sum('amount') - FeePayment::sum('amount_paid'), 0),
            'overdue_payments' => 0, // Calculate based on due dates
        ];

        // Scholarship Statistics
        $scholarshipStats = [
            'total_scholarships' => Scholarship::count(),
            'active_scholarships' => Scholarship::where('is_active', true)->count(),
            'total_awarded' => ScholarshipApplication::where('status', 'approved')->count(),
            'total_amount_awarded' => ScholarshipApplication::where('status', 'approved')
                ->join('scholarships', 'scholarship_applications.scholarship_id', '=', 'scholarships.id')
                ->sum('scholarships.amount'),
        ];

        // Monthly Collection Data
        $monthlyCollection = FeePayment::selectRaw('strftime("%m", payment_date) as month, SUM(amount_paid) as total')
            ->whereRaw('strftime("%Y", payment_date) = ?', [date('Y')])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Class-wise Collection
        $classWiseCollection = DB::table('fee_payments')
            ->join('fee_structures', 'fee_payments.fee_structure_id', '=', 'fee_structures.id')
            ->join('class_rooms', 'fee_structures.class_id', '=', 'class_rooms.id')
            ->selectRaw('class_rooms.name as class_name, SUM(fee_payments.amount_paid) as total_collected')
            ->groupBy('class_rooms.id', 'class_rooms.name')
            ->orderBy('total_collected', 'desc')
            ->get();

        // Recent Payments
        $recentPayments = FeePayment::with(['student.user'])
            ->latest('payment_date')
            ->take(10)
            ->get();

        // Pending Scholarship Applications
        $pendingScholarships = ScholarshipApplication::where('status', 'pending')
            ->with(['scholarship', 'student.user'])
            ->latest()
            ->take(5)
            ->get();

        // Payment Methods Distribution
        $paymentMethods = FeePayment::selectRaw('payment_method, SUM(amount_paid) as total')
            ->groupBy('payment_method')
            ->get();

        // Fee Structure Analysis
        $feeStructureAnalysis = DB::table('fee_structures')
            ->join('class_rooms', 'fee_structures.class_id', '=', 'class_rooms.id')
            ->selectRaw('class_rooms.name as class_name, fee_structures.amount')
            ->get();

        $data = [
            'financialStats' => $financialStats,
            'scholarshipStats' => $scholarshipStats,
            'monthlyCollection' => $monthlyCollection,
            'classWiseCollection' => $classWiseCollection,
            'recentPayments' => $recentPayments,
            'pendingScholarships' => $pendingScholarships,
            'paymentMethods' => $paymentMethods,
            'feeStructureAnalysis' => $feeStructureAnalysis,
            'dateRange' => $dateRange,
        ];

        return view('finance.reports.financial', $data);
    }

    private function getDateRange($request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        
        return [
            'start' => Carbon::parse($startDate),
            'end' => Carbon::parse($endDate),
        ];
    }
}
