<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeeStructure;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use App\Models\FeePayment;
use App\Models\PaymentRecord;
use App\Models\ClassRoom;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinanceController extends Controller
{
    public function dashboard()
    {
        // REAL-TIME FINANCIAL STATISTICS - NO MOCK DATA
        
        // Total fees from all active fee structures (real data)
        $totalFees = FeeStructure::where('is_active', true)
            ->where('status', 'active')
            ->sum('total_amount');
        
        // Total collected from student fee payments (real data from both payment systems)
        $totalCollectedFeePayments = FeePayment::where('status', 'paid')->sum('amount_paid');
        $totalCollectedPaymentRecords = PaymentRecord::where('status', 'approved')->sum('amount');
        $totalCollected = $totalCollectedFeePayments + $totalCollectedPaymentRecords;
        
        // Real-time pending amounts from student fees
        $totalPending = StudentFee::where('status', '!=', 'paid')->sum('balance');
        
        // Today's collection (real data)
        $todayCollection = FeePayment::where('status', 'paid')
            ->whereDate('payment_date', today())
            ->sum('amount_paid') + 
            PaymentRecord::where('status', 'approved')
            ->whereDate('created_at', today())
            ->sum('amount');
        
        // This month's collection (real data)
        $monthlyRevenue = FeePayment::where('status', 'paid')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount_paid') + 
            PaymentRecord::where('status', 'approved')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
        
        // Real student and fee structure counts
        $totalStudents = Student::whereNotNull('class_id')->count();
        $totalFeeStructures = FeeStructure::where('is_active', true)->count();
        
        // REAL-TIME RECENT PAYMENTS (no mock data)
        $recentFeePayments = FeePayment::with(['student.user'])
            ->where('status', 'paid')
            ->orderBy('payment_date', 'desc')
            ->limit(5)
            ->get();
            
        $recentPaymentRecords = PaymentRecord::with(['student.user'])
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        // Combine and sort recent payments
        $recent_payments = $recentFeePayments->concat($recentPaymentRecords)
            ->sortByDesc(function($payment) {
                return $payment->payment_date ?? $payment->created_at;
            })
            ->take(10);
        
        // REAL-TIME SCHOLARSHIP DATA
        $totalScholarships = Scholarship::count();
        $activeScholarships = Scholarship::where('is_active', true)->count();
        $approvedApplications = ScholarshipApplication::where('status', 'approved')->count();
        $totalScholarshipAmount = ScholarshipApplication::where('scholarship_applications.status', 'approved')
            ->join('scholarships', 'scholarship_applications.scholarship_id', '=', 'scholarships.id')
            ->sum('scholarships.amount');
        
        // Real pending scholarship applications
        $pending_scholarships = ScholarshipApplication::where('status', 'pending')
            ->with(['scholarship', 'student.user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($application) {
                return [
                    'student_name' => $application->student->user->name ?? 'Unknown',
                    'scholarship_name' => $application->scholarship->name ?? 'Unknown',
                    'amount' => $application->scholarship->amount ?? 0,
                    'application_date' => $application->created_at,
                ];
            });
        
        // REAL-TIME MONTHLY COLLECTION CHART DATA
        $monthlyCollectionFeePayments = FeePayment::where('status', 'paid')
            ->selectRaw("CAST(strftime('%m', payment_date) AS INTEGER) as month, SUM(amount_paid) as total")
            ->whereRaw("strftime('%Y', payment_date) = ?", [date('Y')])
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');
            
        // Add payment records to monthly data
        $monthlyPaymentRecords = PaymentRecord::where('status', 'approved')
            ->selectRaw("CAST(strftime('%m', created_at) AS INTEGER) as month, SUM(amount) as total")
            ->whereRaw("strftime('%Y', created_at) = ?", [date('Y')])
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            
        // Combine monthly data
        foreach ($monthlyPaymentRecords as $record) {
            if (isset($monthlyCollectionFeePayments[$record->month])) {
                $monthlyCollectionFeePayments[$record->month]->total += $record->total;
            } else {
                $monthlyCollectionFeePayments[$record->month] = $record;
            }
        }
        
        $monthlyCollection = $monthlyCollectionFeePayments->values();
        
        // REAL-TIME CLASS-WISE COLLECTION
        $classWiseCollection = StudentFee::with(['student.classRoom'])
            ->selectRaw('class_id, SUM(total_amount) as total_fees, SUM(paid_amount) as total_collected, SUM(balance) as pending')
            ->groupBy('class_id')
            ->get()
            ->map(function($item) {
                $className = ClassRoom::find($item->class_id)->name ?? 'Unknown Class';
                return (object) [
                    'class_name' => $className,
                    'total_collected' => $item->total_collected,
                    'total_fees' => $item->total_fees,
                    'pending' => $item->pending
                ];
            });
        
        // REAL-TIME RECENT ACTIVITIES
        $recentActivities = collect();
        
        // Add recent payments
        foreach ($recent_payments->take(5) as $payment) {
            $studentName = $payment->student->user->name ?? 'Unknown Student';
            $amount = $payment->amount_paid ?? $payment->amount ?? 0;
            $recentActivities->push([
                'description' => "Payment $" . number_format($amount, 2) . " by " . $studentName,
                'created_at' => $payment->payment_date ?? $payment->created_at,
                'type' => 'payment'
            ]);
        }
        
        // Add recent scholarship approvals
        $recentScholarshipApprovals = ScholarshipApplication::where('status', 'approved')
            ->with(['student.user', 'scholarship'])
            ->orderBy('updated_at', 'desc')
            ->limit(3)
            ->get();
            
        foreach ($recentScholarshipApprovals as $approval) {
            $studentName = $approval->student->user->name ?? 'Unknown Student';
            $scholarshipName = $approval->scholarship->name ?? 'Unknown Scholarship';
            $recentActivities->push([
                'description' => "Scholarship '{$scholarshipName}' awarded to {$studentName}",
                'created_at' => $approval->updated_at,
                'type' => 'scholarship'
            ]);
        }
        
        // Add recent fee structure creations
        $recentFeeStructures = FeeStructure::with(['classRoom'])
            ->orderBy('created_at', 'desc')
            ->limit(2)
            ->get();
            
        foreach ($recentFeeStructures as $feeStructure) {
            $className = $feeStructure->classRoom->name ?? 'Unknown Class';
            $recentActivities->push([
                'description' => "Fee structure '{$feeStructure->name}' created for {$className}",
                'created_at' => $feeStructure->created_at,
                'type' => 'fee_structure'
            ]);
        }
        
        // Sort recent activities by date
        $recentActivities = $recentActivities->sortByDesc('created_at')->take(8);

        // Create stats array for view compatibility
        $stats = [
            'total_fees' => $totalFees,
            'total_collected' => $totalCollected,
            'total_pending' => $totalPending,
            'collected_today' => $todayCollection,
            'monthly_revenue' => $monthlyRevenue,
            'total_revenue' => $totalCollected,
            'fee_structures' => $totalFeeStructures,
        ];

        // Fix variable names to match view expectations
        $recentPayments = $recent_payments;
        
        // Create scholarshipStats array for view compatibility
        $scholarshipStats = [
            'total_scholarships' => $totalScholarships,
            'active_scholarships' => $activeScholarships,
            'total_awarded' => $approvedApplications,
            'total_amount_awarded' => $totalScholarshipAmount,
        ];

        return view('dashboard.finance', compact(
            'totalFees', 'totalCollected', 'totalPending', 'todayCollection', 'monthlyRevenue',
            'totalStudents', 'totalFeeStructures', 'recent_payments', 'recentPayments', 'totalScholarships',
            'activeScholarships', 'approvedApplications', 'totalScholarshipAmount',
            'pending_scholarships', 'classWiseCollection', 'recentActivities', 'monthlyCollection',
            'stats', 'scholarshipStats'
        ));
    }

    public function payments()
    {
        return view('finance.payments');
    }
}
