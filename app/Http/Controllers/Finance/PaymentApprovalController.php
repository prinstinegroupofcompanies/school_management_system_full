<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\PaymentRecord;
use App\Models\StudentFee;
use App\Services\StudentFeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'finance']);
    }

    public function index()
    {
        $pending = PaymentRecord::query()->with(['student', 'fee'])->where('status', 'pending')->latest()->paginate(20);
        return view('finance.payments.index', compact('pending'));
    }

    public function approve(PaymentRecord $payment)
    {
        if ($payment->status !== 'pending') {
            return redirect()->back()->with('error', 'Payment has already been processed. Current status: ' . ucfirst($payment->status));
        }

        DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // Use the service to update all fee balances in real-time
            StudentFeeService::updateStudentFeeBalances($payment->student);
        });

        // Notify student about approval using our custom notification system
        \App\Models\Notification::create([
            'user_id' => $payment->student->user->id,
            'title' => 'Payment Approved',
            'message' => 'Your payment of $' . number_format($payment->amount, 2) . ' has been approved and processed successfully.',
            'type' => 'payment_approved',
            'category' => 'finance',
            'subcategory' => 'payment',
            'priority' => 5, // Medium priority
            'status' => 'pending',
            'action_url' => route('student.finance.index'),
            'action_text' => 'View My Finances',
            'related_model' => 'PaymentRecord',
            'related_id' => $payment->id,
            'metadata' => [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'transaction_reference' => $payment->transaction_reference,
                'approved_by' => Auth::id(),
                'approved_by_name' => Auth::user()->name,
            ],
            'delivery_method' => 'in_app',
            'delivery_status' => 'delivered',
            'is_active' => true,
        ]);
        
        // Notify admin about the approved payment
        $adminUsers = \App\Models\User::where('user_type', 'admin')->get();
        foreach ($adminUsers as $adminUser) {
            \App\Models\Notification::create([
                'user_id' => $adminUser->id,
                'title' => 'Payment Approved',
                'message' => 'A payment of $' . number_format($payment->amount, 2) . ' from ' . $payment->student->user->name . ' has been approved by ' . Auth::user()->name . '.',
                'type' => 'payment_approved',
                'category' => 'finance',
                'subcategory' => 'payment',
                'priority' => 5, // Medium priority
                'status' => 'pending',
                'action_url' => route('admin.students.show', $payment->student->id),
                'action_text' => 'View Student',
                'related_model' => 'PaymentRecord',
                'related_id' => $payment->id,
                'metadata' => [
                    'payment_id' => $payment->id,
                    'student_id' => $payment->student_id,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'approved_by' => Auth::id(),
                    'approved_by_name' => Auth::user()->name,
                    'transaction_reference' => $payment->transaction_reference,
                ],
                'delivery_method' => 'in_app',
                'delivery_status' => 'delivered',
                'is_active' => true,
            ]);
        }
        
        return redirect()->back()->with('success', 'Payment approved successfully! Student balances updated and admin notified.');
    }

    public function reject(PaymentRecord $payment, Request $request)
    {
        if ($payment->status !== 'pending') {
            return redirect()->back()->with('error', 'Payment has already been processed. Current status: ' . ucfirst($payment->status));
        }
        $request->validate(['reason' => 'nullable|string|max:500']);
        $payment->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'details' => trim(($payment->details ? $payment->details."\n" : '').'Rejected: '.($request->reason ?? '')),
        ]);
        // Notify student about rejection using our custom notification system
        \App\Models\Notification::create([
            'user_id' => $payment->student->user->id,
            'title' => 'Payment Rejected',
            'message' => 'Your payment of $' . number_format($payment->amount, 2) . ' has been rejected. Reason: ' . ($request->reason ?? 'No reason provided'),
            'type' => 'payment_rejected',
            'category' => 'finance',
            'subcategory' => 'payment',
            'priority' => 6, // Medium-high priority
            'status' => 'pending',
            'action_url' => route('student.finance.index'),
            'action_text' => 'View My Finances',
            'related_model' => 'PaymentRecord',
            'related_id' => $payment->id,
            'metadata' => [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'transaction_reference' => $payment->transaction_reference,
                'rejected_by' => Auth::id(),
                'rejected_by_name' => Auth::user()->name,
                'rejection_reason' => $request->reason ?? 'No reason provided',
            ],
            'delivery_method' => 'in_app',
            'delivery_status' => 'delivered',
            'is_active' => true,
        ]);
        
        // Notify admin about the rejected payment
        $adminUsers = \App\Models\User::where('user_type', 'admin')->get();
        foreach ($adminUsers as $adminUser) {
            \App\Models\Notification::create([
                'user_id' => $adminUser->id,
                'title' => 'Payment Rejected',
                'message' => 'A payment of $' . number_format($payment->amount, 2) . ' from ' . $payment->student->user->name . ' has been rejected by ' . Auth::user()->name . '. Reason: ' . ($request->reason ?? 'No reason provided'),
                'type' => 'payment_rejected',
                'category' => 'finance',
                'subcategory' => 'payment',
                'priority' => 6, // Medium-high priority
                'status' => 'pending',
                'action_url' => route('admin.students.show', $payment->student->id),
                'action_text' => 'View Student',
                'related_model' => 'PaymentRecord',
                'related_id' => $payment->id,
                'metadata' => [
                    'payment_id' => $payment->id,
                    'student_id' => $payment->student_id,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'rejected_by' => Auth::id(),
                    'rejected_by_name' => Auth::user()->name,
                    'rejection_reason' => $request->reason ?? 'No reason provided',
                    'transaction_reference' => $payment->transaction_reference,
                ],
                'delivery_method' => 'in_app',
                'delivery_status' => 'delivered',
                'is_active' => true,
            ]);
        }
        
        return redirect()->back()->with('success', 'Payment rejected successfully! Student and admin notified.');
    }
}


