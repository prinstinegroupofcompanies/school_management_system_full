<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\PromotionHistory;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Grade;
use App\Models\StudentFee;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PromoteStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:promote 
                            {--term= : The term (e.g., "Term 3")}
                            {--year= : The academic year (e.g., 2025)}
                            {--class= : Specific class ID to process}
                            {--dry-run : Show what would happen without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Promote or retain students based on final grades';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $term = $this->option('term') ?? $this->ask('Enter term (e.g., Term 3)');
        $year = $this->option('year') ?? $this->ask('Enter academic year', date('Y'));
        $classId = $this->option('class');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
        }

        $query = Student::with(['classRoom'])->active();
        
        if ($classId) {
            $query->where('class_id', $classId);
        }

        $students = $query->get();
        $promoted = 0;
        $retained = 0;

        $this->info("Processing {$students->count()} students for {$term} {$year}...");
        
        $bar = $this->output->createProgressBar($students->count());
        $bar->start();

        DB::beginTransaction();
        try {
            foreach ($students as $student) {
                $averageScore = $this->calculateAverageScore($student, $term, $year);
                $passThreshold = 70.0; // Promote when final average is 70% and above
                
                if ($averageScore >= $passThreshold) {
                    // Promote student
                    $nextClass = $this->getNextClass($student->classRoom);
                    
                    if ($nextClass && !$dryRun) {
                        $this->promoteStudent($student, $nextClass, $term, $year, $averageScore);
                        $promoted++;
                    } else {
                        $this->info("\nNo next class found for student {$student->id}");
                        if (!$dryRun) {
                            $this->retainStudent($student, $term, $year, $averageScore);
                            $retained++;
                        }
                    }
                } else {
                    // Retain student
                    if (!$dryRun) {
                        $this->retainStudent($student, $term, $year, $averageScore);
                        $retained++;
                    }
                }
                
                $bar->advance();
            }

            if (!$dryRun) {
                DB::commit();
                $this->info("\n\nPromotion process completed!");
                $this->info("Promoted: {$promoted}");
                $this->info("Retained: {$retained}");
            } else {
                DB::rollBack();
                $this->info("\n\nDry run completed. No changes made.");
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("\nError: " . $e->getMessage());
            return 1;
        }

        $bar->finish();
        return 0;
    }

    /**
     * Calculate average score for a student.
     */
    private function calculateAverageScore(Student $student, string $term, int $year): float
    {
        // Use year_avg from Grade model if available
        $grades = Grade::where('student_id', $student->id)
            ->where('academic_year', $year)
            ->whereNotNull('year_avg')
            ->get();

        if ($grades->isEmpty()) {
            // Fallback: calculate from semester averages
            $grades = Grade::where('student_id', $student->id)
                ->where('academic_year', $year)
                ->whereNotNull('sem1_avg')
                ->orWhereNotNull('sem2_avg')
                ->get();
            
            if ($grades->isEmpty()) {
                return 0.0;
            }
            
            $totalAvg = $grades->sum('year_avg');
            $count = $grades->whereNotNull('year_avg')->count();
            
            if ($count > 0) {
                return $totalAvg / $count;
            }
            
            // Calculate from semester averages
            $totalSemAvg = 0;
            $semCount = 0;
            foreach ($grades as $grade) {
                if ($grade->sem1_avg !== null) {
                    $totalSemAvg += $grade->sem1_avg;
                    $semCount++;
                }
                if ($grade->sem2_avg !== null) {
                    $totalSemAvg += $grade->sem2_avg;
                    $semCount++;
                }
            }
            
            return $semCount > 0 ? $totalSemAvg / $semCount : 0.0;
        }

        $totalAvg = $grades->sum('year_avg');
        $count = $grades->count();

        return $count > 0 ? $totalAvg / $count : 0.0;
    }

    /**
     * Get next class for promotion.
     */
    private function getNextClass(?ClassRoom $currentClass): ?ClassRoom
    {
        if (!$currentClass) {
            return null;
        }

        // Assuming classes are ordered by level
        // You may need to adjust this logic based on your class structure
        $nextClass = ClassRoom::where('id', '>', $currentClass->id)
            ->orderBy('id')
            ->first();

        return $nextClass;
    }

    /**
     * Promote a student to next class.
     */
    private function promoteStudent(Student $student, ClassRoom $nextClass, string $term, int $year, float $averageScore): void
    {
        $fromClassId = $student->class_id;
        
        // Update student class
        $student->update(['class_id' => $nextClass->id]);
        
        // Reassign subjects from new class
        $newSubjects = $nextClass->subjects ?? collect();
        $student->subjects()->sync($newSubjects->pluck('id'));
        
        // Copy fees structure (create new student_fees record)
        if ($student->classFeeStructure) {
            StudentFee::create([
                'student_id' => $student->id,
                'class_id' => $nextClass->id,
                'total_amount' => $student->classFeeStructure->total_fees ?? 0,
                'paid_amount' => 0,
                'balance' => $student->classFeeStructure->total_fees ?? 0,
                'due_date' => now()->addMonth(),
                'status' => 'unpaid',
                'academic_year' => $year + 1,
            ]);
        }
        
        // Log promotion (student's class_id already updated; dashboard and activities will show new class)
        PromotionHistory::create([
            'student_id' => $student->id,
            'from_class_id' => $fromClassId,
            'to_class_id' => $nextClass->id,
            'term' => $term,
            'year' => $year,
            'status' => 'promoted',
            'average_score' => $averageScore,
            'remarks' => "Promoted from {$fromClassName} to {$nextClass->name} (final average {$averageScore}% >= 70%). Previous class grades remain in grade history.",
            'processed_by' => auth()->id() ?? 1,
        ]);
    }

    /**
     * Retain a student in current class.
     */
    private function retainStudent(Student $student, string $term, int $year, float $averageScore): void
    {
        PromotionHistory::create([
            'student_id' => $student->id,
            'from_class_id' => $student->class_id,
            'to_class_id' => null,
            'term' => $term,
            'year' => $year,
            'status' => 'retained',
            'average_score' => $averageScore,
            'remarks' => "Retained in {$student->classRoom->name} due to average score below passing threshold",
            'processed_by' => auth()->id() ?? 1,
        ]);
    }
}
