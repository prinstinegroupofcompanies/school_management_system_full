<?php

namespace App\Observers;

use App\Models\Student;
use App\Services\StudentFeeService;

class StudentObserver
{
    /**
     * Handle the Student "created" event.
     */
    public function created(Student $student): void
    {
        // Automatically assign class fees when a student is created/enrolled
        StudentFeeService::assignClassFeesToStudent($student);
    }

    /**
     * Handle the Student "updated" event.
     */
    public function updated(Student $student): void
    {
        // If class_id or academic_year changed, reassign fees
        if ($student->wasChanged(['class_id', 'academic_year'])) {
            StudentFeeService::assignClassFeesToStudent($student);
        }
    }
}