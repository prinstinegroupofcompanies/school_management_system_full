<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassFeeStructure;
use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassFeeStructureController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display a listing of fee structures
     */
    public function index(Request $request)
    {
        $query = ClassFeeStructure::with(['classRoom'])
                                 ->orderBy('academic_year', 'desc')
                                 ->orderBy('created_at', 'desc');

        // Filter by academic year
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $feeStructures = $query->paginate(15);
        $classes = ClassRoom::all();
        $academicYears = ClassFeeStructure::distinct()->pluck('academic_year')->sort()->values();

        return view('admin.fee-structures.index', compact('feeStructures', 'classes', 'academicYears'));
    }

    /**
     * Show the form for creating a new fee structure
     */
    public function create()
    {
        $classes = ClassRoom::all();
        $currentYear = date('Y');
        
        return view('admin.fee-structures.create', compact('classes', 'currentYear'));
    }

    /**
     * Store a newly created fee structure
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:class_rooms,id',
            'academic_year' => 'required|string|max:10',
            'tuition_fee' => 'required|numeric|min:0',
            'registration_fee' => 'nullable|numeric|min:0',
            'library_fee' => 'nullable|numeric|min:0',
            'laboratory_fee' => 'nullable|numeric|min:0',
            'sports_fee' => 'nullable|numeric|min:0',
            'technology_fee' => 'nullable|numeric|min:0',
            'examination_fee' => 'nullable|numeric|min:0',
            'activity_fee' => 'nullable|numeric|min:0',
            'transport_fee' => 'nullable|numeric|min:0',
            'hostel_fee' => 'nullable|numeric|min:0',
            'meal_fee' => 'nullable|numeric|min:0',
            'uniform_fee' => 'nullable|numeric|min:0',
            'book_fee' => 'nullable|numeric|min:0',
            'miscellaneous_fee' => 'nullable|numeric|min:0',
            'payment_frequency' => 'required|in:monthly,quarterly,semester,annual',
            'installments_allowed' => 'required|integer|min:1|max:12',
            'late_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'grace_period_days' => 'nullable|integer|min:0|max:30',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'is_active' => 'boolean',
        ]);

        // Calculate totals and create fee structure
        $feeFields = [
            'registration_fee', 'library_fee', 'laboratory_fee', 'sports_fee',
            'technology_fee', 'examination_fee', 'activity_fee', 'transport_fee',
            'hostel_fee', 'meal_fee', 'uniform_fee', 'book_fee', 'miscellaneous_fee'
        ];

        foreach ($feeFields as $field) {
            $validated[$field] = $validated[$field] ?? 0;
        }

        $mandatoryFees = $validated['tuition_fee'] + $validated['registration_fee'] + 
                       $validated['library_fee'] + $validated['laboratory_fee'] + 
                       $validated['examination_fee'];

        $optionalFees = $validated['sports_fee'] + $validated['technology_fee'] + 
                      $validated['activity_fee'] + $validated['transport_fee'] + 
                      $validated['hostel_fee'] + $validated['meal_fee'] + 
                      $validated['uniform_fee'] + $validated['book_fee'] + 
                      $validated['miscellaneous_fee'];

        $validated['total_mandatory_fees'] = $mandatoryFees;
        $validated['total_optional_fees'] = $optionalFees;
        $validated['total_fees'] = $mandatoryFees + $optionalFees;
        $validated['is_active'] = $request->boolean('is_active', true);

        ClassFeeStructure::create($validated);

        return redirect()->route('admin.fee-structures.index')
                       ->with('success', 'Fee structure created successfully.');
    }

    /**
     * Display the specified fee structure
     */
    public function show(ClassFeeStructure $feeStructure)
    {
        $feeStructure->load(['classRoom']);
        return view('admin.fee-structures.show', compact('feeStructure'));
    }

    /**
     * Show the form for editing the specified resource
     */
    public function edit(ClassFeeStructure $feeStructure)
    {
        $classes = ClassRoom::all();
        return view('admin.fee-structures.edit', compact('feeStructure', 'classes'));
    }

    /**
     * Update the specified resource in storage
     */
    public function update(Request $request, ClassFeeStructure $feeStructure)
    {
        $validated = $request->validate([
            'tuition_fee' => 'required|numeric|min:0',
            'registration_fee' => 'nullable|numeric|min:0',
            'library_fee' => 'nullable|numeric|min:0',
            'laboratory_fee' => 'nullable|numeric|min:0',
            'sports_fee' => 'nullable|numeric|min:0',
            'technology_fee' => 'nullable|numeric|min:0',
            'examination_fee' => 'nullable|numeric|min:0',
            'activity_fee' => 'nullable|numeric|min:0',
            'transport_fee' => 'nullable|numeric|min:0',
            'hostel_fee' => 'nullable|numeric|min:0',
            'meal_fee' => 'nullable|numeric|min:0',
            'uniform_fee' => 'nullable|numeric|min:0',
            'book_fee' => 'nullable|numeric|min:0',
            'miscellaneous_fee' => 'nullable|numeric|min:0',
            'payment_frequency' => 'required|in:monthly,quarterly,semester,annual',
            'installments_allowed' => 'required|integer|min:1|max:12',
            'late_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'grace_period_days' => 'nullable|integer|min:0|max:30',
            'is_active' => 'boolean',
        ]);

        $feeStructure->update($validated);

        return redirect()->route('admin.fee-structures.show', $feeStructure)
                       ->with('success', 'Fee structure updated successfully.');
    }

    /**
     * Remove the specified resource from storage
     */
    public function destroy(ClassFeeStructure $feeStructure)
    {
        $feeStructure->delete();
        
        return redirect()->route('admin.fee-structures.index')
                       ->with('success', 'Fee structure deleted successfully.');
    }
}
