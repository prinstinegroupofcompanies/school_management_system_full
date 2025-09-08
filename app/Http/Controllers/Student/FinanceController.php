<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\PaymentRecord;
use App\Models\StudentFee;
use App\Models\SystemSetting;
use App\Notifications\PaymentSubmittedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

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

        $fees = StudentFee::query()
            ->where('student_id', $student->id)
            ->orderByDesc('year')
            ->orderBy('semester')
            ->get();

        $firstUnpaidFee = StudentFee::query()
            ->where('student_id', $student->id)
            ->where('balance', '>', 0)
            ->orderBy('due_date')
            ->orderByDesc('year')
            ->orderBy('semester')
            ->first();

        $bankDetails = [
            'bank_name' => SystemSetting::get('bank_name', ''),
            'bank_account' => SystemSetting::get('bank_account', ''),
        ];
        $mobileMoney = [
            'provider' => SystemSetting::get('mobile_money_provider', ''),
            'number' => SystemSetting::get('mobile_money_number', ''),
        ];

        // Only approved payments should show as history
        $payments = PaymentRecord::query()
            ->where('student_id', $student->id)
            ->where('status', 'approved')
            ->latest()
            ->get();

        // Pending payments to show separately
        $pendingPayments = PaymentRecord::query()
            ->where('student_id', $student->id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('student.finance.index', compact('fees', 'bankDetails', 'mobileMoney', 'payments', 'pendingPayments', 'firstUnpaidFee'));
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

        // Notify finance officers (users with user_type = finance)
        $financeUsers = \App\Models\User::query()->where('user_type', 'finance')->get();
        Notification::send($financeUsers, new PaymentSubmittedNotification($payment));

        return redirect()->route('student.finance.index')->with('success', 'Payment submitted for approval.');
    }
}


