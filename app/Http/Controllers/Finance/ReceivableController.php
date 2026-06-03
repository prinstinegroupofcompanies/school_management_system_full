<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Receivable;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReceivableController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'finance']);
    }

    /**
     * Display a listing of receivables.
     */
    public function index(Request $request)
    {
        $query = Receivable::with(['student.user', 'createdBy']);

        // Filters
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
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

        // Auto-mark overdue
        Receivable::overdue()->update(['status' => 'overdue']);

        $receivables = $query->orderBy('due_date', 'asc')->paginate(25);
        $students = Student::with('user')->active()->get();
        
        // Statistics
        $stats = [
            'total_pending' => Receivable::status('pending')->sum('amount'),
            'total_paid' => Receivable::status('paid')->sum('amount'),
            'total_overdue' => Receivable::status('overdue')->sum('amount'),
            'count_pending' => Receivable::status('pending')->count(),
            'count_overdue' => Receivable::status('overdue')->count(),
        ];

        return view('finance.receivables.index', compact('receivables', 'students', 'stats'));
    }

    /**
     * Show the form for creating a new receivable.
     */
    public function create()
    {
        $students = Student::with('user')->active()->get();
        return view('finance.receivables.create', compact('students'));
    }

    /**
     * Store a newly created receivable.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date|after_or_equal:today',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['status'] = 'pending';
        $validated['created_by'] = auth()->id();

        $receivable = Receivable::create($validated);

        return redirect()->route('finance.receivables.show', $receivable)
            ->with('success', 'Receivable created successfully.');
    }

    /**
     * Display the specified receivable.
     */
    public function show(Receivable $receivable)
    {
        $receivable->load(['student.user', 'createdBy']);
        return view('finance.receivables.show', compact('receivable'));
    }

    /**
     * Show the form for editing the specified receivable.
     */
    public function edit(Receivable $receivable)
    {
        if ($receivable->status === 'paid') {
            return redirect()->back()
                ->with('error', 'Cannot edit paid receivables.');
        }

        $students = Student::with('user')->active()->get();
        return view('finance.receivables.edit', compact('receivable', 'students'));
    }

    /**
     * Update the specified receivable.
     */
    public function update(Request $request, Receivable $receivable)
    {
        if ($receivable->status === 'paid') {
            return redirect()->back()
                ->with('error', 'Cannot update paid receivables.');
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'description' => 'nullable|string|max:500',
        ]);

        // Auto-update status if overdue
        if ($validated['due_date'] < now()->toDateString() && $receivable->status === 'pending') {
            $validated['status'] = 'overdue';
        }

        $receivable->update($validated);

        return redirect()->route('finance.receivables.show', $receivable)
            ->with('success', 'Receivable updated successfully.');
    }

    /**
     * Remove the specified receivable.
     */
    public function destroy(Receivable $receivable)
    {
        if ($receivable->status === 'paid') {
            return redirect()->back()
                ->with('error', 'Cannot delete paid receivables.');
        }

        $receivable->delete();

        return redirect()->route('finance.receivables.index')
            ->with('success', 'Receivable deleted successfully.');
    }

    /**
     * Mark receivable as paid.
     */
    public function markAsPaid(Request $request, Receivable $receivable)
    {
        $request->validate([
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string',
            'reference_number' => 'nullable|string',
        ]);

        $receivable->markAsPaid();
        
        // Log payment details (could create a payment record here)
        
        return redirect()->back()
            ->with('success', 'Receivable marked as paid.');
    }

    /**
     * Bulk mark as paid.
     */
    public function bulkMarkAsPaid(Request $request)
    {
        $request->validate([
            'receivable_ids' => 'required|array',
            'receivable_ids.*' => 'exists:receivables,id',
        ]);

        $count = Receivable::whereIn('id', $request->receivable_ids)
            ->where('status', '!=', 'paid')
            ->update(['status' => 'paid']);

        return redirect()->back()
            ->with('success', "{$count} receivables marked as paid.");
    }
}
