<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\ClassRoom;
use App\Models\FeeStructure;
use App\Models\FeePayment;
use App\Models\PaymentRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('finance');
    }

    /**
     * Display pending payments and fee management with REAL-TIME data
     */
    public function index(Request $request)
    {
        try {
            // REAL-TIME PAYMENT DATA using StudentFee system
            $query = StudentFee::with(['student.user', 'student.classRoom', 'feeStructure'])
                              ->where('balance', '>', 0);

        // Apply filters
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function($studentQuery) use ($search) {
                $studentQuery->where('student_id', 'like', "%{$search}%")
                           ->orWhere('admission_no', 'like', "%{$search}%")
                           ->orWhereHas('user', function($userQuery) use ($search) {
                               $userQuery->where('name', 'like', "%{$search}%");
                           });
            });
        }

        $studentFees = $query->orderBy('balance', 'desc')->paginate(15);
        $classes = ClassRoom::all();

        // Calculate REAL-TIME summary statistics to match view expectations
        $stats = [
            'total_outstanding' => StudentFee::where('balance', '>', 0)->sum('balance'),
            'total_students_with_balance' => StudentFee::where('balance', '>', 0)->distinct('student_id')->count(),
            'today_collections' => FeePayment::whereDate('payment_date', today())
                                            ->where('status', 'paid')
                                            ->sum('amount_paid') +
                                 PaymentRecord::whereDate('created_at', today())
                                             ->where('status', 'approved')
                                             ->sum('amount'),
            'pending_approval' => PaymentRecord::where('status', 'pending')->count(),
            'approved_payments' => FeePayment::where('status', 'paid')->count() + 
                                 PaymentRecord::where('status', 'approved')->count(),
            'total_revenue' => FeePayment::where('status', 'paid')->sum('amount_paid') +
                             PaymentRecord::where('status', 'approved')->sum('amount'),
        ];
        
        // Get PENDING payments first (most important for finance officers)
        $pendingPayments = PaymentRecord::with(['student.user', 'student.classRoom'])
            ->where('status', 'pending')
            ->latest('created_at')
            ->get();
            
        // Get recent approved/paid payments
        $feePayments = FeePayment::with(['student.user', 'student.classRoom'])
            ->where('status', 'paid')
            ->latest('payment_date')
            ->limit(10)
            ->get();
            
        $approvedPaymentRecords = PaymentRecord::with(['student.user', 'student.classRoom'])
            ->where('status', 'approved')
            ->latest('created_at')
            ->limit(10)
            ->get();
            
        // Combine payments with PENDING payments first, then recent approved/paid
        $combinedPayments = $pendingPayments
            ->concat($feePayments)
            ->concat($approvedPaymentRecords)
            ->sortByDesc(function($payment) {
                // Prioritize pending payments, then sort by date
                if (isset($payment->status) && $payment->status === 'pending') {
                    return now()->addDays(1)->timestamp; // Put pending at top
                }
                return ($payment->payment_date ?? $payment->created_at)->timestamp;
            });
            
        // Create a mock paginator for the combined results
        $payments = new \Illuminate\Pagination\LengthAwarePaginator(
            $combinedPayments->take(15),
            $combinedPayments->count(),
            15,
            request()->get('page', 1),
            ['path' => request()->url(), 'pageName' => 'page']
        );

        // For backward compatibility with the view, also provide students
        $students = $studentFees;

        return view('finance.payments.index', compact('studentFees', 'students', 'classes', 'stats', 'payments'));
        } catch (\Exception $e) {
            \Log::error('PaymentController index error: ' . $e->getMessage());
            $studentFees = collect()->paginate(15);
            $students = collect();
            $classes = collect();
            $stats = [
                'total_outstanding' => 0,
                'total_students_with_balance' => 0,
                'today_collections' => 0,
                'pending_approval' => 0,
                'approved_payments' => 0,
                'total_revenue' => 0,
            ];
            $payments = collect()->paginate(15);
            return view('finance.payments.index', compact('studentFees', 'students', 'classes', 'stats', 'payments'));
        }
    }

    /**
     * Show payment creation form
     */
    public function create(Request $request)
    {
        $students = Student::with(['user', 'classRoom', 'studentFees'])
                          ->whereNotNull('class_id')
                          ->get();
        $classes = ClassRoom::all();
        $feeStructures = FeeStructure::where('is_active', true)->get();

        return view('finance.payments.create', compact('students', 'classes', 'feeStructures'));
    }

    /**
     * Store a new payment record
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'student_fee_id' => 'required|exists:student_fees,id',
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,mobile_money,check',
            'payment_date' => 'required|date',
            'transaction_id' => 'nullable|string',
            'payment_notes' => 'nullable|string',
        ]);

        // Create payment record
        $payment = FeePayment::create([
            'student_id' => $request->student_id,
            'fee_structure_id' => StudentFee::find($request->student_fee_id)->fee_structure_id,
            'amount_paid' => $request->amount_paid,
            'payment_method' => $request->payment_method,
            'payment_date' => $request->payment_date,
            'transaction_id' => $request->transaction_id,
            'payment_notes' => $request->payment_notes,
            'status' => 'paid',
            'collected_by' => auth()->id(),
        ]);

        // Update student fee balance using the service
        $studentFee = StudentFee::find($request->student_fee_id);
        $student = $studentFee->student;
        
        // Update the specific fee balance
        $studentFee->paid_amount += $request->amount_paid;
        $studentFee->balance = max(0, $studentFee->total_amount - $studentFee->paid_amount);
        
        // Note: No status column in student_fees table, using balance to determine payment status
        $studentFee->save();

        return redirect()->route('finance.payments.index')
                        ->with('success', 'Payment recorded successfully.');
    }

    /**
     * Show payment details - handles both FeePayment and PaymentRecord
     */
    public function show($paymentId)
    {
        // Try to find in PaymentRecord first (for pending payments)
        $payment = PaymentRecord::with(['student.user', 'student.classRoom'])
                                ->where('id', $paymentId)
                                ->first();
        
        if (!$payment) {
            // If not found, try FeePayment
            $payment = FeePayment::with(['student.user', 'student.classRoom', 'feeStructure', 'collectedBy'])
                                ->where('id', $paymentId)
                                ->first();
        }
        
        if (!$payment) {
            abort(404, 'Payment not found');
        }
        
        return view('finance.payments.show', compact('payment'));
    }

    /**
     * Show analytics dashboard with real-time data
     */
    public function analytics(Request $request)
    {
        // REAL-TIME analytics data
        $totalRevenue = FeePayment::where('status', 'paid')->sum('amount_paid') +
                       PaymentRecord::where('status', 'approved')->sum('amount');
        
        $monthlyRevenue = FeePayment::where('status', 'paid')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount_paid') +
            PaymentRecord::where('status', 'approved')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
        
        $totalOutstanding = StudentFee::where('balance', '>', 0)->sum('balance');
        
        // Payment method breakdown
        $paymentMethodBreakdown = FeePayment::where('status', 'paid')
            ->selectRaw('payment_method, SUM(amount_paid) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get();
        
        // Class-wise collection
        $classWiseRevenue = StudentFee::with(['student.classRoom'])
            ->selectRaw('class_id, SUM(paid_amount) as collected, SUM(balance) as pending')
            ->groupBy('class_id')
            ->get()
            ->map(function($item) {
                $className = ClassRoom::find($item->class_id)->name ?? 'Unknown Class';
                return (object) [
                    'class_name' => $className,
                    'collected' => $item->collected,
                    'pending' => $item->pending
                ];
            });
        
        // Monthly trend
        $monthlyTrend = FeePayment::where('status', 'paid')
            ->selectRaw("CAST(strftime('%m', payment_date) AS INTEGER) as month, SUM(amount_paid) as total")
            ->whereRaw("strftime('%Y', payment_date) = ?", [date('Y')])
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Recent payments for analytics
        $recentPayments = FeePayment::with(['student.user'])
            ->where('status', 'paid')
            ->latest('payment_date')
            ->limit(10)
            ->get();

        // Prepare stats array for the view
        $stats = [
            'total_collected' => $totalRevenue,
            'monthly_revenue' => $monthlyRevenue,
            'total_outstanding' => $totalOutstanding,
            'students_with_balance' => StudentFee::where('balance', '>', 0)->count(),
            'collection_rate' => $totalRevenue > 0 ? round(($totalRevenue / ($totalRevenue + $totalOutstanding)) * 100, 1) : 0
        ];

        return view('finance.payments.analytics', compact(
            'totalRevenue', 'monthlyRevenue', 'totalOutstanding', 
            'paymentMethodBreakdown', 'classWiseRevenue', 'monthlyTrend', 'recentPayments', 'stats'
        ));
    }
}
