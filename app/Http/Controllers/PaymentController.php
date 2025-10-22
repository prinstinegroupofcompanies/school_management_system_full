<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Student;
use App\Models\HostelStudent;
use App\Models\TransportStudent;
use App\Models\BookIssue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of payments
     */
    public function index(Request $request)
    {
        try {
            $query = Payment::with(['student.user']);

            // Filter by payment type
            if ($request->filled('type')) {
                $query->where('payable_type', $request->type);
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by date range
            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }

            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }

            $payments = $query->orderBy('created_at', 'desc')->paginate(20);

            // Get statistics
            $stats = [
                'total_payments' => Payment::count(),
                'total_amount' => Payment::where('status', 'completed')->sum('amount'),
                'pending_payments' => Payment::where('status', 'pending')->count(),
                'completed_payments' => Payment::where('status', 'completed')->count(),
                'failed_payments' => Payment::where('status', 'failed')->count(),
            ];

            // Get payment types for filter
            $paymentTypes = [
                'hostel' => 'Hostel Fees',
                'transport' => 'Transport Fees',
                'library' => 'Library Fines',
                'tuition' => 'Tuition Fees',
            ];

            return view('payments.index', compact('payments', 'stats', 'paymentTypes'));

        } catch (\Exception $e) {
            Log::error('PaymentController index error: ' . $e->getMessage());
            $payments = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
            $stats = [
                'total_payments' => 0,
                'total_amount' => 0,
                'pending_payments' => 0,
                'completed_payments' => 0,
                'failed_payments' => 0,
            ];
            $paymentTypes = [];
            return view('payments.index', compact('payments', 'stats', 'paymentTypes'));
        }
    }

    /**
     * Show the form for creating a new payment
     */
    public function create()
    {
        try {
            $students = Student::with('user')->get();
            $paymentTypes = [
                'hostel' => 'Hostel Fees',
                'transport' => 'Transport Fees',
                'library' => 'Library Fines',
                'tuition' => 'Tuition Fees',
            ];

            return view('payments.create', compact('students', 'paymentTypes'));

        } catch (\Exception $e) {
            Log::error('PaymentController create error: ' . $e->getMessage());
            $students = collect();
            $paymentTypes = [];
            return view('payments.create', compact('students', 'paymentTypes'));
        }
    }

    /**
     * Store a newly created payment
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'student_id' => 'required|exists:students,id',
                'payment_type' => 'required|in:hostel,transport,library,tuition',
                'amount' => 'required|numeric|min:0',
                'currency' => 'required|string|max:3',
                'payment_method' => 'required|in:cash,bank_transfer,mobile_money,card',
                'description' => 'nullable|string|max:500',
            ]);

            DB::beginTransaction();

            // Create payment record
            $payment = Payment::create([
                'student_id' => $request->student_id,
                'payable_type' => $request->payment_type,
                'payable_id' => null, // Will be set based on payment type
                'amount' => $request->amount,
                'currency' => $request->currency,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'description' => $request->description,
                'payment_date' => now(),
                'reference_number' => 'PAY-' . time() . '-' . rand(1000, 9999),
            ]);

            // Update related records based on payment type
            $this->updateRelatedRecords($payment, $request);

            DB::commit();

            return redirect()->route('payments.index')
                ->with('success', 'Payment recorded successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('PaymentController store error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to record payment. Please try again.');
        }
    }

    /**
     * Display the specified payment
     */
    public function show(Payment $payment)
    {
        try {
            $payment->load(['student.user']);
            
            // Try to load payable relationship, but handle cases where it might not exist
            try {
                $payment->load('payable');
            } catch (\Exception $e) {
                // If payable relationship fails, continue without it
                Log::warning('PaymentController show: Could not load payable relationship for payment ' . $payment->id . ': ' . $e->getMessage());
            }
            
            return view('payments.show', compact('payment'));

        } catch (\Exception $e) {
            Log::error('PaymentController show error: ' . $e->getMessage());
            return redirect()->route('payments.index')
                ->with('error', 'Payment not found.');
        }
    }

    /**
     * Update payment status
     */
    public function updateStatus(Request $request, Payment $payment)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,completed,failed,refunded',
            ]);

            $payment->update([
                'status' => $request->status,
                'updated_at' => now(),
            ]);

            return redirect()->back()
                ->with('success', 'Payment status updated successfully.');

        } catch (\Exception $e) {
            Log::error('PaymentController updateStatus error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update payment status.');
        }
    }

    /**
     * Record payment for specific payable item
     */
    public function recordPayment(Request $request)
    {
        try {
            $request->validate([
                'payable_type' => 'required|string',
                'payable_id' => 'required|integer',
                'amount' => 'required|numeric|min:0',
                'currency' => 'required|string|max:3',
                'payment_method' => 'required|in:cash,bank_transfer,mobile_money,card',
                'description' => 'nullable|string|max:500',
            ]);

            DB::beginTransaction();

            // Get the payable item
            $payable = $this->getPayableItem($request->payable_type, $request->payable_id);
            
            if (!$payable) {
                throw new \Exception('Payable item not found.');
            }

            // Create payment record
            $payment = Payment::create([
                'student_id' => $payable->student_id,
                'payable_type' => $request->payable_type,
                'payable_id' => $request->payable_id,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'description' => $request->description,
                'payment_date' => now(),
                'reference_number' => 'PAY-' . time() . '-' . rand(1000, 9999),
            ]);

            // Update the payable item status
            $this->updatePayableStatus($payable, $request);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully.',
                'payment' => $payment
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('PaymentController recordPayment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to record payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payable item based on type and ID
     */
    private function getPayableItem($type, $id)
    {
        switch ($type) {
            case 'hostel':
                return HostelStudent::find($id);
            case 'transport':
                return TransportStudent::find($id);
            case 'library':
                return BookIssue::find($id);
            default:
                return null;
        }
    }

    /**
     * Update payable item status after payment
     */
    private function updatePayableStatus($payable, $request)
    {
        switch ($request->payable_type) {
            case 'hostel':
                $payable->update(['payment_status' => 'paid']);
                break;
            case 'transport':
                $payable->update(['payment_status' => 'paid']);
                break;
            case 'library':
                $payable->update(['fine_status' => 'paid']);
                break;
        }
    }

    /**
     * Update related records based on payment type
     */
    private function updateRelatedRecords($payment, $request)
    {
        // This method can be extended to update specific records
        // based on the payment type and student
    }
}
