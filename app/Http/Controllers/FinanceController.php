<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeeStructure;
use App\Models\Student;
use App\Models\Scholarship;
use App\Models\ScholarshipApplication;
use App\Models\FeePayment;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function dashboard()
    {
        // Use safe database queries to prevent crashes
        $totalFees = $this->safeQuery(function() {
            return (float) FeeStructure::sum('amount');
        }) ?: 0;
        
        $totalCollected = $this->safeQuery(function() {
            return (float) FeePayment::where('status', 'paid')->sum('amount_paid');
        }) ?: 0;
        
        $totalPending = max(0, $totalFees - $totalCollected);
        
        $collectedToday = $this->safeQuery(function() {
            return (float) FeePayment::where('status', 'paid')
                ->whereDate('payment_date', today())
                ->sum('amount_paid');
        }) ?: 0;

        $stats = [
            'total_revenue' => $totalCollected,
            'total_fees' => $totalFees,
            'total_collected' => $totalCollected,
            'total_pending' => $totalPending,
            'collected_today' => $collectedToday,
        ];

        $recent_payments = $this->safeQuery(function() {
            return FeePayment::with(['student.user', 'feeStructure'])
                ->latest('payment_date')
                ->limit(10)
                ->get();
        }) ?: collect();

        // Get real scholarship data
        $scholarshipStats = $this->getScholarshipStats();
        $pending_scholarships = $this->getPendingScholarships();

        $monthlyCollection = $this->safeQuery(function() {
            return FeePayment::where('status', 'paid')
                ->selectRaw("CAST(strftime('%m', payment_date) AS INTEGER) as month, SUM(amount_paid) as total")
                ->whereRaw("strftime('%Y', payment_date) = ?", [date('Y')])
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }) ?: collect();

        $classWiseCollection = $this->safeQuery(function() {
            return FeePayment::where('status', 'paid')
                ->join('fee_structures', 'fee_payments.fee_structure_id', '=', 'fee_structures.id')
                ->join('class_rooms', 'fee_structures.class_id', '=', 'class_rooms.id')
                ->selectRaw('class_rooms.name as class_name, SUM(fee_payments.amount_paid) as total_collected')
                ->groupBy('class_rooms.id', 'class_rooms.name')
                ->orderByDesc('total_collected')
                ->get();
        }) ?: collect();

        $recentActivities = $recent_payments->map(function ($p) {
            return [
                'description' => 'Payment $'.number_format($p->amount_paid, 2).' by '.($p->student->user->name ?? 'Student'),
                'created_at' => $p->payment_date ?? $p->created_at,
            ];
        });

        return view('dashboard.finance', [
            'stats' => $stats,
            'recent_payments' => $recent_payments,
            'recentPayments' => $recent_payments,
            'monthlyCollection' => $monthlyCollection,
            'classWiseCollection' => $classWiseCollection,
            'recentActivities' => $recentActivities,
            'scholarshipStats' => $scholarshipStats,
            'pending_scholarships' => $pending_scholarships,
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

    private function getScholarshipStats()
    {
        $totalScholarships = $this->safeQuery(function() {
            return Scholarship::count();
        }) ?: 0;
        
        $activeScholarships = $this->safeQuery(function() {
            return Scholarship::where('is_active', true)->count();
        }) ?: 0;
        
        $totalAwarded = $this->safeQuery(function() {
            return ScholarshipApplication::where('status', 'approved')->count();
        }) ?: 0;
        
        $totalAmountAwarded = $this->safeQuery(function() {
            return ScholarshipApplication::where('status', 'approved')
                ->join('scholarships', 'scholarship_applications.scholarship_id', '=', 'scholarships.id')
                ->sum('scholarships.amount');
        }) ?: 0;

        return [
            'total_scholarships' => $totalScholarships,
            'active_scholarships' => $activeScholarships,
            'total_awarded' => $totalAwarded,
            'total_amount_awarded' => $totalAmountAwarded,
        ];
    }

    private function getPendingScholarships()
    {
        return $this->safeQuery(function() {
            return ScholarshipApplication::with(['scholarship', 'student.user'])
                ->where('status', 'pending')
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($application) {
                    return [
                        'student_name' => $application->student->user->name ?? 'Unknown',
                        'scholarship_name' => $application->scholarship->name ?? 'Unknown Scholarship',
                        'amount' => $application->scholarship->amount ?? 0,
                        'application_date' => $application->application_date ?? $application->created_at,
                    ];
                });
        }) ?: collect();
    }

    private function getScholarshipDistribution()
    {
        return $this->safeQuery(function() {
            return ScholarshipApplication::with('scholarship')
                ->where('status', 'approved')
                ->get()
                ->groupBy('scholarship.name')
                ->map(function ($applications) {
                    return $applications->sum('scholarship.amount');
                });
        }) ?: collect();
    }

    /**
     * Safe database query wrapper to prevent crashes when tables don't exist
     */
    private function safeQuery($callback, $default = null)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            return $default ?? collect();
        }
    }
}
