<?php

namespace App\Services;

use App\Models\Student;
use App\Models\FeeStructure;
use App\Models\StudentFee;
use App\Models\PaymentRecord;

class StudentFeeService
{
    /**
     * Assign class fees to a student upon enrollment
     */
    public static function assignClassFeesToStudent(Student $student)
    {
        // Get all active fee structures for the student's class and academic year
        $classFeeStructures = FeeStructure::where('class_id', $student->class_id)
            ->where('academic_year', $student->academic_year)
            ->where('is_active', true)
            ->where('status', 'active')
            ->get();

        foreach ($classFeeStructures as $feeStructure) {
            // Create student fee record if it doesn't exist
            StudentFee::firstOrCreate([
                'student_id' => $student->id,
                'fee_structure_id' => $feeStructure->id,
                'academic_year' => $student->academic_year,
            ], [
                'class_id' => $student->class_id,
                'total_amount' => $feeStructure->final_amount ?? $feeStructure->total_amount,
                'paid_amount' => 0,
                'balance' => $feeStructure->final_amount ?? $feeStructure->total_amount,
                'due_date' => $feeStructure->due_date ?? now()->addMonth(),
                'status' => 'unpaid',
                'year' => $student->academic_year,
                'semester' => 'Academic Year ' . $student->academic_year,
                'fee_type' => $feeStructure->fee_type ?? 'tuition',
                'description' => $feeStructure->description ?? $feeStructure->name ?? 'Class Fee',
            ]);
        }
    }

    /**
     * Update student fee balances based on payments
     */
    public static function updateStudentFeeBalances(Student $student)
    {
        $studentFees = StudentFee::where('student_id', $student->id)->get();

        foreach ($studentFees as $studentFee) {
            // Calculate total approved payments for this fee
            $totalPaid = PaymentRecord::where('student_id', $student->id)
                ->where('fee_id', $studentFee->id)
                ->where('status', 'approved')
                ->sum('amount');

            // Update fee record
            $studentFee->update([
                'paid_amount' => $totalPaid,
                'balance' => $studentFee->total_amount - $totalPaid,
                'status' => $totalPaid >= $studentFee->total_amount ? 'paid' : ($totalPaid > 0 ? 'partial' : 'unpaid'),
            ]);
        }
    }

    /**
     * Get real-time financial summary for student
     */
    public static function getStudentFinancialSummary(Student $student)
    {
        // Ensure fees are up to date
        self::assignClassFeesToStudent($student);
        self::updateStudentFeeBalances($student);

        $fees = StudentFee::where('student_id', $student->id)->get();
        
        return [
            'total_fees' => $fees->sum('total_amount'),
            'paid_amount' => $fees->sum('paid_amount'),
            'balance_amount' => $fees->sum('balance'),
            'overdue_amount' => $fees->where('due_date', '<', now())->where('balance', '>', 0)->sum('balance'),
            'fees_count' => $fees->count(),
            'paid_fees_count' => $fees->where('status', 'paid')->count(),
            'unpaid_fees_count' => $fees->where('balance', '>', 0)->count(),
        ];
    }
}
