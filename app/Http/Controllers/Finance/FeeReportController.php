<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FeeStructure;
use App\Models\StudentFee;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\FeePayment;
use App\Models\PaymentRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FeeReportController extends Controller
{
    public function index(Request $request)
    {
        $classes = ClassRoom::orderBy('name')->get();
        
        // Summary statistics
        $totalFeeStructures = FeeStructure::where('is_active', true)->count();
        $totalStudentFees = StudentFee::sum('total_amount');
        $totalPaidAmount = StudentFee::sum('paid_amount');
        $totalPendingAmount = StudentFee::where('balance', '>', 0)->sum('balance');
        
        // Monthly collection data (SQLite compatible) - Combined from both payment sources
        $feePaymentCollections = FeePayment::where('status', 'paid')
            ->whereRaw("strftime('%Y', payment_date) = ?", [Carbon::now()->year])
            ->selectRaw("CAST(strftime('%m', payment_date) AS INTEGER) as month, SUM(amount_paid) as total")
            ->groupBy('month')
            ->get();
            
        $paymentRecordCollections = PaymentRecord::where('status', 'approved')
            ->whereRaw("strftime('%Y', created_at) = ?", [Carbon::now()->year])
            ->selectRaw("CAST(strftime('%m', created_at) AS INTEGER) as month, SUM(amount) as total")
            ->groupBy('month')
            ->get();
            
        // Combine both collections by month
        $monthlyCollections = collect();
        for ($month = 1; $month <= 12; $month++) {
            $feeTotal = $feePaymentCollections->where('month', $month)->first()->total ?? 0;
            $recordTotal = $paymentRecordCollections->where('month', $month)->first()->total ?? 0;
            $monthlyCollections->push((object)[
                'month' => $month,
                'total' => $feeTotal + $recordTotal
            ]);
        }
        
        // Class-wise fee collection
        $classWiseCollection = StudentFee::with(['student.classRoom'])
            ->selectRaw('students.class_id, SUM(student_fees.total_amount) as total_fees, SUM(student_fees.paid_amount) as collected, SUM(student_fees.balance) as pending')
            ->join('students', 'student_fees.student_id', '=', 'students.id')
            ->groupBy('students.class_id')
            ->get();
        
        // Recent payments - Combined from both sources
        $recentFeePayments = FeePayment::with(['student.user', 'student.classRoom'])
            ->where('status', 'paid')
            ->latest()
            ->limit(5)
            ->get();
            
        $recentPaymentRecords = PaymentRecord::with(['student.user', 'student.classRoom'])
            ->where('status', 'approved')
            ->latest()
            ->limit(5)
            ->get();
            
        // Combine and sort by date
        $recentPayments = $recentFeePayments
            ->concat($recentPaymentRecords)
            ->sortByDesc(function($payment) {
                return $payment->payment_date ?? $payment->created_at;
            })
            ->take(10)
            ->values();
        
        return view('finance.fee-reports.index', compact(
            'classes', 'totalFeeStructures', 'totalStudentFees', 'totalPaidAmount', 
            'totalPendingAmount', 'monthlyCollections', 'classWiseCollection', 'recentPayments'
        ));
    }
    
    public function export(Request $request)
    {
        // TODO: Implement CSV/PDF export functionality
        return response()->json(['message' => 'Export functionality coming soon']);
    }
    
    public function classReport(Request $request, ClassRoom $class)
    {
        $students = Student::where('class_id', $class->id)
            ->with(['user', 'studentFees.feeStructure'])
            ->get();
        
        $classFeeStructures = FeeStructure::where('class_id', $class->id)
            ->where('is_active', true)
            ->get();
        
        $totalClassFees = $students->sum(function($student) {
            return $student->studentFees->sum('total_amount');
        });
        
        $totalClassPaid = $students->sum(function($student) {
            return $student->studentFees->sum('paid_amount');
        });
        
        $totalClassPending = $students->sum(function($student) {
            return $student->studentFees->sum('balance');
        });
        
        return view('finance.fee-reports.class', compact(
            'class', 'students', 'classFeeStructures', 
            'totalClassFees', 'totalClassPaid', 'totalClassPending'
        ));
    }
    
    public function studentReport(Request $request, Student $student)
    {
        $student->load(['user', 'classRoom', 'studentFees.feeStructure']);
        
        // Payment history from both sources
        $feePaymentHistory = FeePayment::where('student_id', $student->id)
            ->with(['studentFee.feeStructure'])
            ->orderBy('payment_date', 'desc')
            ->get();
            
        $paymentRecordHistory = PaymentRecord::where('student_id', $student->id)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Combine payment history
        $paymentHistory = $feePaymentHistory
            ->concat($paymentRecordHistory)
            ->sortByDesc(function($payment) {
                return $payment->payment_date ?? $payment->created_at;
            })
            ->values();
        
        $totalFees = $student->studentFees->sum('total_amount');
        $totalPaid = $student->studentFees->sum('paid_amount');
        $totalPending = $student->studentFees->sum('balance');
        
        return view('finance.fee-reports.student', compact(
            'student', 'paymentHistory', 'totalFees', 'totalPaid', 'totalPending'
        ));
    }
}
