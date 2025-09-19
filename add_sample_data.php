<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Adding Sample Dashboard Data ===\n\n";

try {
    // Get existing data
    $students = \App\Models\Student::all();
    $classes = \App\Models\ClassRoom::all();
    
    echo "Found " . $students->count() . " students and " . $classes->count() . " classes\n";
    
    if ($classes->count() > 0 && $students->count() > 0) {
        $class = $classes->first();
        $student = $students->first();
        
        // Create fee structures if they don't exist
        if (\App\Models\FeeStructure::count() == 0) {
            echo "Creating fee structures...\n";
            
            \App\Models\FeeStructure::create([
                'class_id' => $class->id,
                'academic_year' => date('Y'),
                'description' => 'Tuition Fee for ' . $class->name,
            ]);
            
            \App\Models\FeeStructure::create([
                'class_id' => $class->id,
                'academic_year' => date('Y'),
                'description' => 'Library Fee for ' . $class->name,
            ]);
            
            echo "✅ Fee structures created\n";
        }
        
        // Create fee payments if they don't exist
        if (\App\Models\FeePayment::count() == 0) {
            echo "Creating fee payments...\n";
            
            $feeStructure = \App\Models\FeeStructure::first();
            if ($feeStructure) {
                \App\Models\FeePayment::create([
                    'student_id' => $student->id,
                    'fee_structure_id' => $feeStructure->id,
                    'amount_paid' => 35000, // 35,000 LRD paid
                    'payment_method' => 'cash',
                    'payment_date' => now()->subDays(3),
                    'status' => 'paid',
                ]);
                
                \App\Models\FeePayment::create([
                    'student_id' => $student->id,
                    'fee_structure_id' => $feeStructure->id,
                    'amount_paid' => 10000, // 10,000 LRD paid
                    'payment_method' => 'mobile_money',
                    'payment_date' => now()->subDays(1),
                    'status' => 'paid',
                ]);
                
                echo "✅ Fee payments created\n";
            }
        }
        
        // Create more attendance records if needed
        if (\App\Models\StudentAttendance::count() < 10) {
            echo "Creating attendance records...\n";
            
            for ($i = 1; $i <= 15; $i++) {
                \App\Models\StudentAttendance::firstOrCreate([
                    'student_id' => $student->id,
                    'class_id' => $class->id,
                    'date' => now()->subDays($i),
                ], [
                    'status' => $i <= 12 ? 'present' : 'absent', // 80% attendance
                    'marked_by' => 1, // Admin user
                ]);
            }
            
            echo "✅ Attendance records created\n";
        }
        
        echo "\n=== Running data verification ===\n";
        
        // Verify data counts
        echo "Fee Structures: " . \App\Models\FeeStructure::count() . "\n";
        echo "Fee Payments: " . \App\Models\FeePayment::count() . "\n";
        echo "Student Attendance: " . \App\Models\StudentAttendance::count() . "\n";
        echo "Total Fee Amount: $" . number_format(\App\Models\FeeStructure::sum('amount'), 2) . "\n";
        echo "Total Collected: $" . number_format(\App\Models\FeePayment::sum('amount_paid'), 2) . "\n";
        
    } else {
        echo "❌ No students or classes found - cannot create sample data\n";
    }
    
    echo "\n✅ Sample data creation complete!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
