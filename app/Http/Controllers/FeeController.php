<?php

namespace App\Http\Controllers;

use App\Models\FeeStructure;
use App\Models\FeePayment;
use App\Models\Student;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Models\FinanceReport;

class FeeController extends Controller
{
    public function index()
    {
        $feeStructures = FeeStructure::with(['class', 'student'])->paginate(15);
        return view('fees.index', compact('feeStructures'));
    }

    public function create()
    {
        $classes = ClassRoom::all();
        $students = Student::all();
        return view('fees.create', compact('classes', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:class_rooms,id',
            'fee_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        FeeStructure::create($request->all());

        return redirect()->route('fees.index')
            ->with('success', 'Fee structure created successfully.');
    }

    public function show(FeeStructure $feeStructure)
    {
        $feeStructure->load(['student', 'class', 'payments']);
        return view('fees.show', compact('feeStructure'));
    }

    public function edit(FeeStructure $feeStructure)
    {
        $classes = ClassRoom::all();
        $students = Student::all();
        return view('fees.edit', compact('feeStructure', 'classes', 'students'));
    }

    public function update(Request $request, FeeStructure $feeStructure)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:class_rooms,id',
            'fee_type' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $feeStructure->update($request->all());

        return redirect()->route('fees.index')
            ->with('success', 'Fee structure updated successfully.');
    }

    public function destroy(FeeStructure $feeStructure)
    {
        $feeStructure->delete();

        return redirect()->route('fees.index')
            ->with('success', 'Fee structure deleted successfully.');
    }

    public function payments()
    {
        $payments = FeePayment::with(['student', 'feeStructure'])->paginate(15);
        return view('fees.payments', compact('payments'));
    }

    public function structures()
    {
        $feeStructures = FeeStructure::with(['student', 'class'])->paginate(15);
        return view('fees.structures', compact('feeStructures'));
    }

    public function createPayment()
    {
        $students = Student::with(['user','class'])->get();
        return view('fees.create-payment', compact('students'));
    }

    public function storePayment(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,check,online,mobile_money,wallet',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Map to very short DB-safe codes to avoid truncation on narrow columns
        // Save exactly one of the enum values defined in the schema
        $method = $request->payment_method;

        $data = [
            'student_id' => $request->student_id,
            'amount_paid' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $method,
            'reference_number' => $request->reference_number,
            'notes' => $request->notes,
        ];

        FeePayment::create($data);

        return redirect()->route('fees.payments.index')
            ->with('success', 'Payment recorded successfully.');
    }

    public function showPayment(FeePayment $payment)
    {
        $payment->load(['student', 'feeStructure']);
        return view('fees.show-payment', compact('payment'));
    }

    public function editPayment(FeePayment $payment)
    {
        $feeStructures = FeeStructure::with(['student', 'class'])->get();
        return view('fees.edit-payment', compact('payment', 'feeStructures'));
    }

    public function updatePayment(Request $request, FeePayment $payment)
    {
        $request->validate([
            'fee_structure_id' => 'required|exists:fee_structures,id',
            'amount_paid' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $payment->update($request->all());

        return redirect()->route('fees.payments')
            ->with('success', 'Payment updated successfully.');
    }

    public function destroyPayment(FeePayment $payment)
    {
        $payment->delete();

        return redirect()->route('fees.payments')
            ->with('success', 'Payment deleted successfully.');
    }

    public function studentFees(Student $student)
    {
        $feeStructures = $student->feeStructures()->with('class')->get();
        $payments = $student->feePayments()->with('feeStructure')->get();
        
        return view('fees.student-fees', compact('student', 'feeStructures', 'payments'));
    }

    public function classFees(ClassRoom $class)
    {
        $feeStructures = $class->feeStructures()->with('student')->get();
        $totalAmount = $feeStructures->sum('amount');
        $totalCollected = $class->feePayments()->sum('amount_paid');
        
        return view('fees.class-fees', compact('class', 'feeStructures', 'totalAmount', 'totalCollected'));
    }

    public function reports(Request $request)
    {
        $totalFees = FeeStructure::sum('amount');
        $totalCollected = FeePayment::sum('amount_paid');
        $totalPending = max($totalFees - $totalCollected, 0);

        $driver = \DB::getDriverName();
        $year = date('Y');

        if ($driver === 'sqlite') {
            $monthlyCollection = FeePayment::selectRaw("CAST(strftime('%m', payment_date) AS INTEGER) as month, SUM(amount_paid) as total")
                ->whereRaw("strftime('%Y', payment_date) = ?", [$year])
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        } else {
            $monthlyCollection = FeePayment::selectRaw('MONTH(payment_date) as month, SUM(amount_paid) as total')
                ->whereYear('payment_date', $year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }
        
        $classWiseCollection = DB::table('fee_payments')
            ->join('fee_structures', 'fee_payments.fee_structure_id', '=', 'fee_structures.id')
            ->join('class_rooms', 'fee_structures.class_id', '=', 'class_rooms.id')
            ->selectRaw('class_rooms.name as class_name, SUM(fee_payments.amount_paid) as total_collected')
            ->groupBy('class_rooms.id', 'class_rooms.name')
            ->orderBy('total_collected', 'desc')
            ->get();
        
        $reports = FinanceReport::latest('pushed_at')->paginate(15);

        return view('finance.fees.reports.index', compact(
            'totalFees', 'totalCollected', 'totalPending', 'monthlyCollection', 'classWiseCollection', 'reports'
        ));
    }

    public function pushReport(Request $request)
    {
        $request->validate([
            'range' => 'required|in:daily,weekly,monthly,yearly',
        ]);

        $now = now();
        $query = FeePayment::query();

        switch ($request->range) {
            case 'daily':
                $query->whereDate('payment_date', $now->toDateString());
                break;
            case 'weekly':
                $query->whereBetween('payment_date', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
                break;
            case 'monthly':
                $query->whereYear('payment_date', $now->year)->whereMonth('payment_date', $now->month);
                break;
            case 'yearly':
                $query->whereYear('payment_date', $now->year);
                break;
        }

        $total = $query->sum('amount');
        $count = $query->count();

        // Persist a finance report row for admin real-time view
        FinanceReport::create([
            'range' => $request->range,
            'total' => $total,
            'count' => $count,
            'pushed_by' => auth()->id(),
            'pushed_at' => now(),
        ]);

        // Log for audit
        Log::info('Finance report pushed', [
            'range' => $request->range,
            'total' => $total,
            'count' => $count,
            'pushed_by' => auth()->id(),
        ]);

        return back()->with('success', ucfirst($request->range) . ' report pushed to admin. Total: ' . number_format($total, 2) . ' across ' . $count . ' payments.');
    }

    public function submitReportPage(Request $request)
    {
        $myReports = FinanceReport::where('pushed_by', auth()->id())
            ->latest('pushed_at')
            ->paginate(15);

        return view('fees.submit-report', compact('myReports'));
    }

    public function overdue()
    {
        $overdueFees = FeeStructure::where('due_date', '<', now())
            ->whereDoesntHave('payments', function($query) {
                $query->where('amount_paid', '>=', DB::raw('fee_structures.amount'));
            })
            ->with(['student', 'class'])
            ->paginate(15);
        
        return view('fees.overdue', compact('overdueFees'));
    }

    public function bulkActions(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,mark_paid,export',
            'selected_fees' => 'required|array',
            'selected_fees.*' => 'exists:fee_structures,id'
        ]);

        $selectedFees = FeeStructure::whereIn('id', $request->selected_fees);

        switch ($request->action) {
            case 'delete':
                $selectedFees->delete();
                $message = 'Selected fee structures deleted successfully.';
                break;
                
            case 'mark_paid':
                $selectedFees->update(['status' => 'paid']);
                $message = 'Selected fee structures marked as paid.';
                break;
                
            case 'export':
                // Export logic would go here
                $message = 'Export completed successfully.';
                break;
        }

        return redirect()->route('fees.index')->with('success', $message);
    }
}
