<?php

namespace App\Services\Fees;

use App\Models\FeeItem;
use App\Models\Student;
use App\Models\StudentFee;
use Illuminate\Support\Facades\DB;
use App\Models\SystemSetting;

class StudentFeeService
{
    /**
     * Create or update a StudentFee for a student and term from active FeeItems.
     */
    public function createStudentFeeFor(Student $student, ?string $semester, int $year): StudentFee
    {
        $classId = $student->class_id;

        $feeItemsQuery = FeeItem::query()
            ->where('is_active', true)
            ->where(function ($q) use ($classId) {
                $q->whereNull('class_id')->orWhere('class_id', $classId);
            });

        if (!empty($semester)) {
            $feeItemsQuery->where(function ($q) use ($semester) {
                $q->whereNull('semester')->orWhere('semester', $semester);
            });
        }

        $feeItemsQuery->where(function ($q) use ($year) {
            $q->whereNull('year')->orWhere('year', $year);
        });

        $feeItems = $feeItemsQuery->get();

        $breakdown = [];
        $total = 0.0;
        foreach ($feeItems as $item) {
            $lineTotal = (float) $item->total;
            if ($lineTotal <= 0) {
                $lineTotal = (float) $item->quantity * (float) $item->price_per_unit;
            }
            $breakdown[] = [
                'item_name' => $item->item_name,
                'quantity' => $item->quantity,
                'price_per_unit' => (float) $item->price_per_unit,
                'total' => $lineTotal,
            ];
            $total += $lineTotal;
        }

        $defaultDueDays = (int) (SystemSetting::get('fee_due_days', 30));
        $dueDate = now()->addDays(max(0, $defaultDueDays))->toDateString();

        return DB::transaction(function () use ($student, $classId, $semester, $year, $breakdown, $total, $dueDate) {
            $studentFee = StudentFee::query()->updateOrCreate(
                [
                    'student_id' => $student->id,
                    'class_id' => $classId,
                    'semester' => $semester,
                    'year' => $year,
                ],
                [
                    'total_amount' => $total,
                    'paid_amount' => $student->wallet_balance ? (float) $student->wallet_balance : 0,
                    'balance' => max(0, $total - (float) ($student->wallet_balance ?? 0)),
                    'fee_breakdown' => $breakdown,
                    'due_date' => $dueDate,
                ]
            );

            return $studentFee;
        });
    }
}


