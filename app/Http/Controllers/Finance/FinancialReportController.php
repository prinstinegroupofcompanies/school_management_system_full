<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FeePayment;
use App\Models\PaymentRecord;
use App\Models\StudentFee;
use App\Models\FeeStructure;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        // Financial overview statistics - Combined from both payment sources
        $feePaymentRevenue = FeePayment::where('status', 'paid')->sum('amount_paid');
        $paymentRecordRevenue = PaymentRecord::where('status', 'approved')->sum('amount');
        $totalRevenue = $feePaymentRevenue + $paymentRecordRevenue;
        
        $monthlyFeePayments = FeePayment::where('status', 'paid')
            ->whereMonth('payment_date', Carbon::now()->month)
            ->whereYear('payment_date', Carbon::now()->year)
            ->sum('amount_paid');
            
        $monthlyPaymentRecords = PaymentRecord::where('status', 'approved')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');
            
        $monthlyRevenue = $monthlyFeePayments + $monthlyPaymentRecords;
        
        $totalOutstanding = StudentFee::where('status', '!=', 'paid')->sum('balance');
        $totalStudents = Student::count();
        
        // Monthly revenue trend (SQLite compatible) - Combined from both sources
        $feePaymentTrend = FeePayment::where('status', 'paid')
            ->whereRaw("strftime('%Y', payment_date) = ?", [Carbon::now()->year])
            ->selectRaw("CAST(strftime('%m', payment_date) AS INTEGER) as month, SUM(amount_paid) as revenue")
            ->groupBy('month')
            ->get();
            
        $paymentRecordTrend = PaymentRecord::where('status', 'approved')
            ->whereRaw("strftime('%Y', created_at) = ?", [Carbon::now()->year])
            ->selectRaw("CAST(strftime('%m', created_at) AS INTEGER) as month, SUM(amount) as revenue")
            ->groupBy('month')
            ->get();
            
        // Combine trends by month
        $monthlyTrend = collect();
        for ($month = 1; $month <= 12; $month++) {
            $feeRevenue = $feePaymentTrend->where('month', $month)->first()->revenue ?? 0;
            $recordRevenue = $paymentRecordTrend->where('month', $month)->first()->revenue ?? 0;
            $monthlyTrend->push((object)[
                'month' => $month,
                'revenue' => $feeRevenue + $recordRevenue
            ]);
        }
        
        // Payment method breakdown - Combined from both sources
        $feePaymentMethods = FeePayment::where('status', 'paid')
            ->selectRaw('payment_method, SUM(amount_paid) as total')
            ->groupBy('payment_method')
            ->get();
            
        $paymentRecordMethods = PaymentRecord::where('status', 'approved')
            ->selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get();
            
        // Combine payment methods
        $paymentMethods = collect();
        $allMethods = $feePaymentMethods->pluck('payment_method')->merge($paymentRecordMethods->pluck('payment_method'))->unique();
        
        foreach ($allMethods as $method) {
            $feeTotal = $feePaymentMethods->where('payment_method', $method)->first()->total ?? 0;
            $recordTotal = $paymentRecordMethods->where('payment_method', $method)->first()->total ?? 0;
            $paymentMethods->push((object)[
                'payment_method' => $method,
                'total' => $feeTotal + $recordTotal
            ]);
        }
        
        // Outstanding by class
        $outstandingByClass = StudentFee::with(['student.classRoom'])
            ->where('student_fees.status', '!=', 'paid')
            ->join('students', 'student_fees.student_id', '=', 'students.id')
            ->join('class_rooms', 'students.class_id', '=', 'class_rooms.id')
            ->selectRaw('class_rooms.name as class_name, SUM(student_fees.balance) as outstanding')
            ->groupBy('class_rooms.id', 'class_rooms.name')
            ->get();
        
        // Recent large payments - Combined from both sources
        $largeFeePayments = FeePayment::with(['student.user', 'student.classRoom'])
            ->where('status', 'paid')
            ->where('amount_paid', '>=', 1000)
            ->latest()
            ->limit(5)
            ->get();
            
        $largePaymentRecords = PaymentRecord::with(['student.user', 'student.classRoom'])
            ->where('status', 'approved')
            ->where('amount', '>=', 1000)
            ->latest()
            ->limit(5)
            ->get();
            
        // Combine and sort by date
        $largePayments = $largeFeePayments
            ->concat($largePaymentRecords)
            ->sortByDesc(function($payment) {
                return $payment->payment_date ?? $payment->created_at;
            })
            ->take(10)
            ->values();
        
        return view('finance.reports.financial', compact(
            'totalRevenue', 'monthlyRevenue', 'totalOutstanding', 'totalStudents',
            'monthlyTrend', 'paymentMethods', 'outstandingByClass', 'largePayments'
        ));
    }
    
    public function export(Request $request)
    {
        // TODO: Implement financial report export
        return response()->json(['message' => 'Financial report export coming soon']);
    }
    
    public function payments(Request $request)
    {
        // Get payments from both sources
        $feePaymentQuery = FeePayment::with(['student.user', 'student.classRoom', 'studentFee.feeStructure']);
        $paymentRecordQuery = PaymentRecord::with(['student.user', 'student.classRoom']);
        
        // Apply filters to both queries
        if ($request->has('status') && $request->status != '') {
            if ($request->status === 'paid') {
                $feePaymentQuery->where('status', 'paid');
                $paymentRecordQuery->where('status', 'approved');
            } else {
                $feePaymentQuery->where('status', $request->status);
                $paymentRecordQuery->where('status', $request->status);
            }
        }
        
        if ($request->has('payment_method') && $request->payment_method != '') {
            $feePaymentQuery->where('payment_method', $request->payment_method);
            $paymentRecordQuery->where('payment_method', $request->payment_method);
        }
        
        if ($request->has('date_from') && $request->date_from != '') {
            $feePaymentQuery->whereDate('payment_date', '>=', $request->date_from);
            $paymentRecordQuery->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to != '') {
            $feePaymentQuery->whereDate('payment_date', '<=', $request->date_to);
            $paymentRecordQuery->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Get results and combine
        $feePayments = $feePaymentQuery->get();
        $paymentRecords = $paymentRecordQuery->get();
        
        $combinedPayments = $feePayments
            ->concat($paymentRecords)
            ->sortByDesc(function($payment) {
                return $payment->payment_date ?? $payment->created_at;
            });
        
        // Manual pagination
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $payments = new \Illuminate\Pagination\LengthAwarePaginator(
            $combinedPayments->forPage($currentPage, $perPage),
            $combinedPayments->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'pageName' => 'page']
        );
        
        // Calculate totals
        $totalFeeAmount = $feePayments->where('status', 'paid')->sum('amount_paid');
        $totalRecordAmount = $paymentRecords->where('status', 'approved')->sum('amount');
        $totalAmount = $totalFeeAmount + $totalRecordAmount;
        
        $pendingFeeAmount = $feePayments->where('status', 'pending')->sum('amount_paid');
        $pendingRecordAmount = $paymentRecords->where('status', 'pending')->sum('amount');
        $pendingAmount = $pendingFeeAmount + $pendingRecordAmount;
        
        return view('finance.reports.payments', compact('payments', 'totalAmount', 'pendingAmount'));
    }
    
    public function income(Request $request)
    {
        // Monthly income analysis (SQLite compatible) - Combined from both sources
        $monthlyFeeIncome = FeePayment::where('status', 'paid')
            ->whereRaw("strftime('%Y', payment_date) = ?", [Carbon::now()->year])
            ->selectRaw("CAST(strftime('%m', payment_date) AS INTEGER) as month, SUM(amount_paid) as income")
            ->groupBy('month')
            ->get();
            
        $monthlyRecordIncome = PaymentRecord::where('status', 'approved')
            ->whereRaw("strftime('%Y', created_at) = ?", [Carbon::now()->year])
            ->selectRaw("CAST(strftime('%m', created_at) AS INTEGER) as month, SUM(amount) as income")
            ->groupBy('month')
            ->get();
            
        // Combine monthly income
        $monthlyIncome = collect();
        $monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                      'July', 'August', 'September', 'October', 'November', 'December'];
                      
        for ($month = 1; $month <= 12; $month++) {
            $feeIncome = $monthlyFeeIncome->where('month', $month)->first()->income ?? 0;
            $recordIncome = $monthlyRecordIncome->where('month', $month)->first()->income ?? 0;
            $monthlyIncome->push((object)[
                'month' => $month,
                'month_name' => $monthNames[$month - 1],
                'income' => $feeIncome + $recordIncome
            ]);
        }
        
        // Income by fee type
        $incomeByFeeType = FeePayment::with(['studentFee.feeStructure'])
            ->where('status', 'paid')
            ->get()
            ->groupBy(function($payment) {
                return $payment->studentFee->feeStructure->fee_type ?? 'Other';
            })
            ->map(function($payments) {
                return $payments->sum('amount_paid');
            });
        
        // Daily income for current month (SQLite compatible) - Combined from both sources
        $dailyFeeIncome = FeePayment::where('status', 'paid')
            ->whereRaw("strftime('%m', payment_date) = ?", [sprintf('%02d', Carbon::now()->month)])
            ->whereRaw("strftime('%Y', payment_date) = ?", [Carbon::now()->year])
            ->selectRaw("CAST(strftime('%d', payment_date) AS INTEGER) as day, SUM(amount_paid) as income")
            ->groupBy('day')
            ->get();
            
        $dailyRecordIncome = PaymentRecord::where('status', 'approved')
            ->whereRaw("strftime('%m', created_at) = ?", [sprintf('%02d', Carbon::now()->month)])
            ->whereRaw("strftime('%Y', created_at) = ?", [Carbon::now()->year])
            ->selectRaw("CAST(strftime('%d', created_at) AS INTEGER) as day, SUM(amount) as income")
            ->groupBy('day')
            ->get();
            
        // Combine daily income
        $dailyIncome = collect();
        $daysInMonth = Carbon::now()->daysInMonth;
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $feeIncome = $dailyFeeIncome->where('day', $day)->first()->income ?? 0;
            $recordIncome = $dailyRecordIncome->where('day', $day)->first()->income ?? 0;
            if ($feeIncome > 0 || $recordIncome > 0) {
                $dailyIncome->push((object)[
                    'day' => $day,
                    'income' => $feeIncome + $recordIncome
                ]);
            }
        }
        
        return view('finance.reports.income', compact('monthlyIncome', 'incomeByFeeType', 'dailyIncome'));
    }
    
    public function expenses(Request $request)
    {
        // TODO: Implement expenses tracking
        // For now, return a placeholder view
        $totalExpenses = 0;
        $monthlyExpenses = collect();
        $expenseCategories = collect();
        
        return view('finance.reports.expenses', compact('totalExpenses', 'monthlyExpenses', 'expenseCategories'));
    }
}
