<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\PaymentRecord;
use App\Models\StudentFee;
use App\Models\SystemSetting;
use App\Notifications\PaymentSubmittedNotification;
use App\Services\StudentFeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class FinanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        $user = Auth::user();
        $student = $user->student ?? null;
        abort_if(!$student, 403);

        // Ensure student has fees assigned for their current class
        StudentFeeService::assignClassFeesToStudent($student);
        
        // Use the service to get real-time financial data
        $financialSummary = StudentFeeService::getStudentFinancialSummary($student);
        
        // Get updated student fees with all relationships
        $fees = StudentFee::where('student_id', $student->id)
            ->with(['student.classRoom', 'student.user', 'feeStructure'])
            ->orderByDesc('year')
            ->orderBy('semester')
            ->get();

        $firstUnpaidFee = $fees->where('balance', '>', 0)->sortBy('due_date')->first();

        $bankDetails = [
            'bank_name' => SystemSetting::get('bank_name', ''),
            'bank_account' => SystemSetting::get('bank_account', ''),
        ];
        $mobileMoney = [
            'provider' => SystemSetting::get('mobile_money_provider', ''),
            'number' => SystemSetting::get('mobile_money_number', ''),
        ];

        // Real-time approved payments with fee details
        $payments = PaymentRecord::query()
            ->where('student_id', $student->id)
            ->where('status', 'approved')
            ->with(['studentFee'])
            ->latest()
            ->get();

        // Real-time pending payments with fee details
        $pendingPayments = PaymentRecord::query()
            ->where('student_id', $student->id)
            ->where('status', 'pending')
            ->with(['studentFee'])
            ->latest()
            ->get();

        // Extract summary data
        $totalAmount = $financialSummary['total_fees'];
        $paidAmount = $financialSummary['paid_amount'];
        $balanceAmount = $financialSummary['balance_amount'];

        return view('student.finance.index', compact(
            'fees', 'bankDetails', 'mobileMoney', 'payments', 'pendingPayments', 
            'firstUnpaidFee', 'totalAmount', 'paidAmount', 'balanceAmount'
        ));
    }

    public function createPayment(StudentFee $fee)
    {
        $user = Auth::user();
        abort_if($fee->student_id !== ($user->student->id ?? 0), 403);

        $bankDetails = [
            'bank_name' => SystemSetting::get('bank_name', ''),
            'bank_account' => SystemSetting::get('bank_account', ''),
        ];
        $mobileMoney = [
            'provider' => SystemSetting::get('mobile_money_provider', ''),
            'number' => SystemSetting::get('mobile_money_number', ''),
        ];

        return view('student.finance.create_payment', compact('fee', 'bankDetails', 'mobileMoney'));
    }

    public function storePayment(Request $request, StudentFee $fee)
    {
        $user = Auth::user();
        abort_if($fee->student_id !== ($user->student->id ?? 0), 403);

        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:Bank,Mobile Money',
            'transaction_reference' => 'required|string|max:100',
            'date' => 'required|date',
            'details' => 'nullable|string|max:1000',
            'receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $path = $request->file('receipt')->store('receipts', 'public');

        $payment = PaymentRecord::create([
            'student_id' => $user->student->id,
            'fee_id' => $fee->id,
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'],
            'transaction_reference' => $data['transaction_reference'],
            'details' => $data['details'] ?? null,
            'receipt_path' => $path,
            'status' => 'pending',
        ]);

        // Notify finance officers directly using the custom notification model
        $financeUsers = \App\Models\User::query()->where('user_type', 'finance')->get();
        
        foreach ($financeUsers as $financeUser) {
            \App\Models\Notification::create([
                'user_id' => $financeUser->id,
                'title' => 'New Payment Submitted',
                'message' => 'A student submitted a payment of $' . number_format($payment->amount, 2) . ' for approval.',
                'type' => 'payment_submission',
                'category' => 'finance',
                'subcategory' => 'payment',
                'priority' => 7, // High priority
                'status' => 'pending',
                'action_url' => route('finance.payments.index'),
                'action_text' => 'Review Payment',
                'related_model' => 'PaymentRecord',
                'related_id' => $payment->id,
                'metadata' => [
                    'payment_id' => $payment->id,
                    'student_id' => $payment->student_id,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'transaction_reference' => $payment->transaction_reference,
                ],
                'delivery_method' => 'in_app',
                'delivery_status' => 'delivered',
                'is_active' => true,
            ]);
        }

        // Update student fee balances in real-time
        StudentFeeService::updateStudentFeeBalances($user->student);

        return redirect()->route('student.finance.index')->with('success', 'Payment submitted for approval. Your balance will be updated once payment is verified.');
    }
    
    public function downloadInvoice(StudentFee $fee)
    {
        $user = Auth::user();
        abort_if($fee->student_id !== ($user->student->id ?? 0), 403);

        // Generate PDF invoice
        $pdf = Pdf::loadView('student.finance.invoice', compact('fee'));
        
        return $pdf->download('invoice-' . $fee->id . '.pdf');
    }
}


