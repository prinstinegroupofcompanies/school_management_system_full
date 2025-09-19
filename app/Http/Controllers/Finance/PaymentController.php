<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\ClassFeeStructure;
use App\Models\FeePayment;
use App\Models\StudentActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('finance');
    }

    /**
     * Display pending payments and fee management
     */
    public function index(Request $request)
    {
        $query = Student::with(['user', 'classRoom'])
                       ->where('balance_fees', '>', 0);

        // Apply filters
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('admission_number', 'like', "%{$search}%")
                  ->orWhere('student_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $students = $query->orderBy('balance_fees', 'desc')->paginate(15);
        $classes = ClassRoom::all();

        // Calculate summary statistics
        $stats = [
            'total_outstanding' => Student::sum('balance_fees'),
            'total_collected' => Student::sum('paid_fees'),
            'students_with_balance' => Student::where('balance_fees', '>', 0)->count(),
        ];

        return view('finance.payments.index', compact('students', 'classes', 'stats'));
    }

    /**
     * Show payment form for specific student
     */
    public function create(Student $student)
    {
        if ($student->balance_fees <= 0) {
            return redirect()->route('finance.payments.index')
                           ->withErrors(['error' => 'Student has no outstanding fees.']);
        }

        $student->load(['user', 'classRoom']);
        
        return view('finance.payments.create', compact('student'));
    }

    /**
     * Process payment
     */
    public function store(Request $request, Student $student)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $student->balance_fees,
            'payment_method' => 'required|in:cash,bank_transfer,cheque,card,mobile_money',
            'payment_reference' => 'nullable|string|max:255',
            'payment_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Create payment record
            $payment = FeePayment::create([
                'student_id' => $student->id,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'payment_reference' => $validated['payment_reference'],
                'payment_date' => $validated['payment_date'],
                'notes' => $validated['notes'],
                'processed_by' => auth()->id(),
                'status' => 'approved', // Finance officer can approve directly
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'academic_year' => $student->academic_year,
            ]);

            // Update student balance
            $student->recordPayment($payment->amount, 'fee_payment', auth()->user());

            DB::commit();

            return redirect()->route('finance.payments.show', $payment)
                           ->with('success', 'Payment processed and approved successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to process payment: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Show payment details
     */
    public function show(FeePayment $payment)
    {
        $payment->load(['student.user', 'student.classRoom', 'processedBy', 'approvedBy']);
        
        return view('finance.payments.show', compact('payment'));
    }

    /**
     * Payment analytics
     */
    public function analytics(Request $request)
    {
        $academicYear = $request->get('academic_year', date('Y'));

        // Collection statistics
        $stats = [
            'total_collected' => FeePayment::where('status', 'approved')->sum('amount'),
            'total_outstanding' => Student::sum('balance_fees'),
            'students_with_balance' => Student::where('balance_fees', '>', 0)->count(),
            'collection_rate' => 0,
        ];

        $totalFees = Student::where('academic_year', $academicYear)->sum('total_fees');
        if ($totalFees > 0) {
            $stats['collection_rate'] = ($stats['total_collected'] / $totalFees) * 100;
        }

        return view('finance.payments.analytics', compact('stats', 'academicYear'));
    }
}
