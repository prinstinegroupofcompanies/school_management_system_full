<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\PaymentRecord;
use App\Models\StudentFee;
use App\Notifications\PaymentDecisionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

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
        abort_if($payment->status !== 'pending', 400, 'Payment already processed');

        DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            /** @var StudentFee $fee */
            $fee = $payment->fee;
            $newPaid = (float) $fee->paid_amount + (float) $payment->amount;
            $newBalance = max(0, (float) $fee->total_amount - $newPaid);
            $fee->update([
                'paid_amount' => $newPaid,
                'balance' => $newBalance,
            ]);
        });

        Notification::send($payment->student->user, new PaymentDecisionNotification($payment));
        return redirect()->back()->with('success', 'Payment approved and balances updated');
    }

    public function reject(PaymentRecord $payment, Request $request)
    {
        abort_if($payment->status !== 'pending', 400, 'Payment already processed');
        $request->validate(['reason' => 'nullable|string|max:500']);
        $payment->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'details' => trim(($payment->details ? $payment->details."\n" : '').'Rejected: '.($request->reason ?? '')),
        ]);
        Notification::send($payment->student->user, new PaymentDecisionNotification($payment));
        return redirect()->back()->with('success', 'Payment rejected');
    }
}


