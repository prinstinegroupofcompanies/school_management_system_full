<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentFee;
use App\Models\PaymentRecord;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function dashboard()
    {
        // Real-time aggregates
        $totalFees = (float) StudentFee::sum('total_amount');
        $totalCollected = (float) PaymentRecord::where('payment_records.status', 'approved')->sum('amount');
        $totalPending = max(0, $totalFees - $totalCollected);
        $collectedToday = (float) PaymentRecord::where('payment_records.status', 'approved')
            ->whereDate(DB::raw('date(approved_at)'), today())
            ->sum('amount');

        $stats = [
            'total_revenue' => $totalCollected,
            'total_fees' => $totalFees,
            'total_collected' => $totalCollected,
            'total_pending' => $totalPending,
            'collected_today' => $collectedToday,
        ];

        $recent_payments = PaymentRecord::with(['student.user', 'fee'])
            ->latest()
            ->limit(10)
            ->get();

        // Mock pending scholarships data
        $pending_scholarships = collect([
            [
                'student_name' => 'Sarah Wilson',
                'scholarship_name' => 'Academic Excellence',
                'amount' => 2000,
                'application_date' => now()->subDays(7),
            ],
            [
                'student_name' => 'David Brown',
                'scholarship_name' => 'Sports Achievement',
                'amount' => 1500,
                'application_date' => now()->subDays(10),
            ],
        ]);

        $monthlyCollection = PaymentRecord::where('payment_records.status', 'approved')
            ->selectRaw("CAST(strftime('%m', approved_at) AS INTEGER) as month, SUM(amount) as total")
            ->whereRaw("strftime('%Y', approved_at) = ?", [date('Y')])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $classWiseCollection = PaymentRecord::where('payment_records.status', 'approved')
            ->join('student_fees', 'payment_records.fee_id', '=', 'student_fees.id')
            ->join('class_rooms', 'student_fees.class_id', '=', 'class_rooms.id')
            ->selectRaw('class_rooms.name as class_name, SUM(payment_records.amount) as total_collected')
            ->groupBy('class_rooms.id', 'class_rooms.name')
            ->orderByDesc('total_collected')
            ->get();

        $recentActivities = $recent_payments->map(function ($p) {
            return [
                'description' => 'Payment '.$p->amount.' by '.($p->student->user->name ?? 'Student'),
                'created_at' => $p->created_at,
            ];
        });

        return view('dashboard.finance', [
            'stats' => $stats,
            'recent_payments' => $recent_payments,
            'recentPayments' => $recent_payments,
            'monthlyCollection' => $monthlyCollection,
            'classWiseCollection' => $classWiseCollection,
            'recentActivities' => $recentActivities,
        ]);
    }

    private function getPendingPayments()
    {
        // Calculate pending payments based on fee structures and existing payments
        $total_students = Student::count();
        $total_paid = FeePayment::where('status', 'paid')->sum('amount');
        
        // This is a simplified calculation - in reality, you'd check against fee structures
        return $total_students * 1000 - $total_paid; // Assuming 1000 LRD per student
    }

    private function getMonthlyRevenue()
    {
        return FeePayment::where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
    }

    public function reports()
    {
        $monthly_revenue = $this->getMonthlyRevenueData();
        $fee_collection_rate = $this->getFeeCollectionRate();
        $scholarship_distribution = $this->getScholarshipDistribution();

        return view('finance.reports', compact(
            'monthly_revenue',
            'fee_collection_rate',
            'scholarship_distribution'
        ));
    }

    private function getMonthlyRevenueData()
    {
        $months = [];
        $revenue = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            $revenue[] = FeePayment::where('status', 'paid')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('amount');
        }

        return [
            'months' => $months,
            'revenue' => $revenue,
        ];
    }

    private function getFeeCollectionRate()
    {
        $total_expected = Student::count() * 1000; // Simplified calculation
        $total_collected = FeePayment::where('status', 'paid')->sum('amount');
        
        if ($total_expected == 0) {
            return 0;
        }
        
        return round(($total_collected / $total_expected) * 100, 2);
    }

    private function getScholarshipDistribution()
    {
        return ScholarshipApplication::with('scholarship')
            ->where('status', 'approved')
            ->get()
            ->groupBy('scholarship.name')
            ->map(function ($applications) {
                return $applications->sum('scholarship.amount');
            });
    }
}
