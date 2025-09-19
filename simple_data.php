<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Adding Simple Dashboard Data ===\n\n";

try {
    // Get existing data
    $students = \App\Models\Student::all();
    $classes = \App\Models\ClassRoom::all();
    
    echo "Found " . $students->count() . " students and " . $classes->count() . " classes\n";
    
    if ($classes->count() > 0 && $students->count() > 0) {
        $class = $classes->first();
        $student = $students->first();
        
        // Create minimal fee structures
        if (\App\Models\FeeStructure::count() == 0) {
            echo "Creating minimal fee structures...\n";
            
            \App\Models\FeeStructure::create([
                'class_id' => $class->id,
                'description' => 'Tuition Fee',
            ]);
            
            echo "✅ Fee structures created\n";
        }
        
        // Create minimal fee payments
        if (\App\Models\FeePayment::count() == 0) {
            echo "Creating minimal fee payments...\n";
            
            $feeStructure = \App\Models\FeeStructure::first();
            if ($feeStructure) {
                \App\Models\FeePayment::create([
                    'student_id' => $student->id,
                    'amount_paid' => 25000,
                    'payment_date' => now()->subDays(3),
                    'status' => 'paid',
                ]);
                
                \App\Models\FeePayment::create([
                    'student_id' => $student->id,
                    'amount_paid' => 15000,
                    'payment_date' => now()->subDays(1),
                    'status' => 'paid',
                ]);
                
                echo "✅ Fee payments created\n";
            }
        }
        
        echo "\n=== Running data verification ===\n";
        echo "Fee Structures: " . \App\Models\FeeStructure::count() . "\n";
        echo "Fee Payments: " . \App\Models\FeePayment::count() . "\n";
        echo "Total Collected: $" . number_format(\App\Models\FeePayment::sum('amount_paid'), 2) . "\n";
        
    } else {
        echo "❌ No students or classes found\n";
    }
    
    echo "\n✅ Simple data creation complete!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
