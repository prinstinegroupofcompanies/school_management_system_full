<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FeeStructure;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class FeeStructureController extends Controller
{
    public function index(Request $request)
    {
        $query = FeeStructure::with(['classRoom']);
        
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('fee_type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('class_id') && $request->class_id != '') {
            $query->where('class_id', $request->class_id);
        }
        
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        $feeStructures = $query->orderBy('created_at', 'desc')->paginate(15);
        $classes = ClassRoom::orderBy('name')->get();
        
        return view('finance.fee-structures.index', compact('feeStructures', 'classes'));
    }
    
    public function create()
    {
        $classes = ClassRoom::orderBy('name')->get();
        return view('finance.fee-structures.create', compact('classes'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'class_id' => 'required|exists:class_rooms,id',
            'fee_type' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'academic_year' => 'required|string|max:20',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'status' => 'required|in:active,inactive,draft',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'grace_period_days' => 'nullable|integer|min:0',
            'late_fee_percentage' => 'nullable|numeric|min:0',
            'late_fee_amount' => 'nullable|numeric|min:0',
            'max_installments' => 'nullable|integer|min:1',
            'allow_installments' => 'boolean'
        ]);
        
        $data = $request->only([
            'name', 'description', 'class_id', 'academic_year', 'fee_type',
            'due_date', 'status', 'discount_percentage', 'discount_amount',
            'grace_period_days', 'late_fee_percentage', 'late_fee_amount',
            'max_installments'
        ]);
        
        // Set amounts
        $amount = $request->input('amount');
        $data['amount'] = $amount;
        $data['total_amount'] = $amount;
        
        // Calculate final amount after discount
        $discountAmount = $data['discount_amount'] ?? 0;
        $discountPercentage = $data['discount_percentage'] ?? 0;
        
        if ($discountPercentage > 0) {
            $discountAmount += ($amount * $discountPercentage / 100);
        }
        
        $data['final_amount'] = $amount - $discountAmount;
        $data['discount_amount'] = $discountAmount;
        
        // Set boolean fields
        $data['is_active'] = $request->has('is_active') ? true : false;
        $data['allow_installments'] = $request->has('allow_installments') ? true : false;
        
        // Set defaults for nullable fields
        $data['discount_percentage'] = $data['discount_percentage'] ?? 0;
        $data['grace_period_days'] = $data['grace_period_days'] ?? 0;
        $data['late_fee_percentage'] = $data['late_fee_percentage'] ?? 0;
        $data['late_fee_amount'] = $data['late_fee_amount'] ?? 0;
        $data['max_installments'] = $data['max_installments'] ?? 1;
        
        FeeStructure::create($data);
        
        return redirect()->route('finance.fees.structures.index')
                        ->with('success', 'Fee structure created successfully.');
    }
    
    public function show(FeeStructure $feeStructure)
    {
        $feeStructure->load(['classRoom', 'studentFees.student.user']);
        return view('finance.fee-structures.show', compact('feeStructure'));
    }
    
    public function edit(FeeStructure $feeStructure)
    {
        $classes = ClassRoom::orderBy('name')->get();
        return view('finance.fee-structures.edit', compact('feeStructure', 'classes'));
    }
    
    public function update(Request $request, FeeStructure $feeStructure)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'class_id' => 'required|exists:class_rooms,id',
            'fee_type' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'academic_year' => 'required|string|max:20',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'status' => 'required|in:active,inactive,draft',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'grace_period_days' => 'nullable|integer|min:0',
            'late_fee_percentage' => 'nullable|numeric|min:0',
            'late_fee_amount' => 'nullable|numeric|min:0',
            'max_installments' => 'nullable|integer|min:1',
            'allow_installments' => 'boolean'
        ]);
        
        $data = $request->only([
            'name', 'description', 'class_id', 'academic_year', 'fee_type',
            'due_date', 'status', 'discount_percentage', 'discount_amount',
            'grace_period_days', 'late_fee_percentage', 'late_fee_amount',
            'max_installments'
        ]);
        
        // Set amounts
        $amount = $request->input('amount');
        $data['amount'] = $amount;
        $data['total_amount'] = $amount;
        
        // Calculate final amount after discount
        $discountAmount = $data['discount_amount'] ?? 0;
        $discountPercentage = $data['discount_percentage'] ?? 0;
        
        if ($discountPercentage > 0) {
            $discountAmount += ($amount * $discountPercentage / 100);
        }
        
        $data['final_amount'] = $amount - $discountAmount;
        $data['discount_amount'] = $discountAmount;
        
        // Set boolean fields
        $data['is_active'] = $request->has('is_active') ? true : false;
        $data['allow_installments'] = $request->has('allow_installments') ? true : false;
        
        // Set defaults for nullable fields
        $data['discount_percentage'] = $data['discount_percentage'] ?? 0;
        $data['grace_period_days'] = $data['grace_period_days'] ?? 0;
        $data['late_fee_percentage'] = $data['late_fee_percentage'] ?? 0;
        $data['late_fee_amount'] = $data['late_fee_amount'] ?? 0;
        $data['max_installments'] = $data['max_installments'] ?? 1;
        
        $feeStructure->update($data);
        
        return redirect()->route('finance.fees.structures.show', $feeStructure)
                        ->with('success', 'Fee structure updated successfully.');
    }
    
    public function destroy(FeeStructure $feeStructure)
    {
        // Check if fee structure has associated student fees
        if ($feeStructure->studentFees()->exists()) {
            return redirect()->route('finance.fees.structures.index')
                            ->with('error', 'Cannot delete fee structure that has associated student fees.');
        }
        
        $feeStructure->delete();
        
        return redirect()->route('finance.fees.structures.index')
                        ->with('success', 'Fee structure deleted successfully.');
    }
}
