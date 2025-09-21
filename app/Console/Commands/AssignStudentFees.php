<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Services\StudentFeeService;

class AssignStudentFees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:assign-fees 
                            {--force : Force reassignment even if fees already exist}
                            {--class= : Only assign fees for specific class ID}
                            {--student= : Only assign fees for specific student ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign class-based fee structures to existing students';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting student fee assignment...');

        // Build query based on options
        $query = Student::whereNotNull('class_id');

        if ($this->option('class')) {
            $query->where('class_id', $this->option('class'));
        }

        if ($this->option('student')) {
            $query->where('id', $this->option('student'));
        }

        $students = $query->with(['user', 'classRoom', 'studentFees'])->get();

        if ($students->isEmpty()) {
            $this->warn('No students found matching the criteria.');
            return 0;
        }

        $this->info("Found {$students->count()} students to process.");

        $assignedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        foreach ($students as $student) {
            try {
                $studentName = $student->user->name ?? "Student ID: {$student->id}";
                $className = $student->classRoom->name ?? "Class ID: {$student->class_id}";

                // Check if student already has fees assigned
                if (!$this->option('force') && $student->studentFees->isNotEmpty()) {
                    $this->line("Skipping {$studentName} ({$className}) - already has {$student->studentFees->count()} fees assigned");
                    $skippedCount++;
                    continue;
                }

                // Assign fees using the service
                $beforeCount = $student->studentFees()->count();
                StudentFeeService::assignClassFeesToStudent($student);
                $afterCount = $student->fresh()->studentFees()->count();
                $newFeesCount = $afterCount - $beforeCount;

                if ($newFeesCount > 0) {
                    $this->info("✓ Assigned {$newFeesCount} fees to {$studentName} ({$className})");
                    $assignedCount++;
                } else {
                    $this->line("- No new fees to assign for {$studentName} ({$className})");
                    $skippedCount++;
                }

            } catch (\Exception $e) {
                $this->error("✗ Error processing {$studentName}: " . $e->getMessage());
                $errorCount++;
            }
        }

        // Summary
        $this->newLine();
        $this->info('Assignment Summary:');
        $this->table(['Status', 'Count'], [
            ['Students Processed', $assignedCount],
            ['Students Skipped', $skippedCount],
            ['Errors', $errorCount],
            ['Total', $students->count()]
        ]);

        if ($assignedCount > 0) {
            $this->info('✓ Fee assignment completed successfully!');
        }

        return 0;
    }
}