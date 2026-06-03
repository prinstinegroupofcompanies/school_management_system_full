<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Payable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayableController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'finance']);
    }

    /**
     * Display a listing of payables.
     */
    public function index(Request $request)
    {
        $query = Payable::with('createdBy');

        // Filters
        if ($request->filled('vendor')) {
            $query->where('vendor', 'like', '%' . $request->vendor . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('due_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('due_date', '<=', $request->date_to);
        }

        $payables = $query->orderBy('due_date', 'asc')->paginate(25);
        
        // Statistics
        $stats = [
            'total_pending' => Payable::status('pending')->sum('amount'),
            'total_paid' => Payable::status('paid')->sum('amount'),
            'count_pending' => Payable::status('pending')->count(),
            'count_overdue' => Payable::pending()
                ->where('due_date', '<', now())
                ->count(),
        ];

        return view('finance.payables.index', compact('payables', 'stats'));
    }

    /**
     * Show the form for creating a new payable.
     */
    public function create()
    {
        return view('finance.payables.create');
    }

    /**
     * Store a newly created payable.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date|after_or_equal:today',
            'description' => 'nullable|string|max:500',
            'invoice_number' => 'nullable|string|max:100',
        ]);

        $validated['status'] = 'pending';
        $validated['created_by'] = auth()->id();

        $payable = Payable::create($validated);

        return redirect()->route('finance.payables.show', $payable)
            ->with('success', 'Payable created successfully.');
    }

    /**
     * Display the specified payable.
     */
    public function show(Payable $payable)
    {
        $payable->load('createdBy');
        return view('finance.payables.show', compact('payable'));
    }

    /**
     * Show the form for editing the specified payable.
     */
    public function edit(Payable $payable)
    {
        if ($payable->status === 'paid') {
            return redirect()->back()
                ->with('error', 'Cannot edit paid payables.');
        }

        return view('finance.payables.edit', compact('payable'));
    }

    /**
     * Update the specified payable.
     */
    public function update(Request $request, Payable $payable)
    {
        if ($payable->status === 'paid') {
            return redirect()->back()
                ->with('error', 'Cannot update paid payables.');
        }

        $validated = $request->validate([
            'vendor' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'description' => 'nullable|string|max:500',
            'invoice_number' => 'nullable|string|max:100',
        ]);

        $payable->update($validated);

        return redirect()->route('finance.payables.show', $payable)
            ->with('success', 'Payable updated successfully.');
    }

    /**
     * Remove the specified payable.
     */
    public function destroy(Payable $payable)
    {
        if ($payable->status === 'paid') {
            return redirect()->back()
                ->with('error', 'Cannot delete paid payables.');
        }

        $payable->delete();

        return redirect()->route('finance.payables.index')
            ->with('success', 'Payable deleted successfully.');
    }

    /**
     * Mark payable as paid.
     */
    public function markAsPaid(Request $request, Payable $payable)
    {
        $request->validate([
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string',
            'reference_number' => 'nullable|string',
        ]);

        $payable->markAsPaid();
        
        return redirect()->back()
            ->with('success', 'Payable marked as paid.');
    }

    /**
     * Bulk mark as paid.
     */
    public function bulkMarkAsPaid(Request $request)
    {
        $request->validate([
            'payable_ids' => 'required|array',
            'payable_ids.*' => 'exists:payables,id',
        ]);

        $count = Payable::whereIn('id', $request->payable_ids)
            ->where('status', '!=', 'paid')
            ->update(['status' => 'paid']);

        return redirect()->back()
            ->with('success', "{$count} payables marked as paid.");
    }

    /**
     * Income/Expenditure report.
     */
    public function incomeExpenditureReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Income (from receivables paid)
        $income = Payable::status('paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        // Expenditure (from payables paid)
        $expenditure = Payable::status('paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        // Also include fee payments as income
        $feeIncome = \App\Models\FeePayment::where('status', 'paid')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount_paid');

        $totalIncome = $feeIncome; // Receivables are tracked separately
        $totalExpenditure = $expenditure;
        $netProfit = $totalIncome - $totalExpenditure;

        return view('finance.reports.income-expenditure', compact(
            'startDate',
            'endDate',
            'totalIncome',
            'totalExpenditure',
            'netProfit',
            'feeIncome',
            'expenditure'
        ));
    }
}
