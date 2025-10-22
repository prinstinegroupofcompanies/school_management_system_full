<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking database tables...\n";

try {
    $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
    echo "Available tables:\n";
    foreach ($tables as $table) {
        echo "- " . $table->name . "\n";
    }
    
    // Check if exam_questions table exists and its structure
    if (in_array('exam_questions', array_column($tables, 'name'))) {
        echo "\nExam_questions table structure:\n";
        $columns = DB::select("PRAGMA table_info(exam_questions)");
        foreach ($columns as $column) {
            echo "- " . $column->name . " (" . $column->type . ")\n";
        }
    } else {
        echo "\nExam_questions table does not exist.\n";
    }
    
    // Check if exam_papers table exists and its structure
    if (in_array('exam_papers', array_column($tables, 'name'))) {
        echo "\nExam_papers table structure:\n";
        $columns = DB::select("PRAGMA table_info(exam_papers)");
        foreach ($columns as $column) {
            echo "- " . $column->name . " (" . $column->type . ")\n";
        }
    } else {
        echo "\nExam_papers table does not exist.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}