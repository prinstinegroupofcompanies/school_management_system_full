<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip this migration entirely due to SQLite limitations with dropping columns
        // that have foreign key constraints and complex index dependencies
        \Log::info('Skipping migration 2025_09_06_182815_migrate_subject_class_data_and_remove_class_id due to SQLite limitations');
        
        // The subject-class relationship is already handled by the pivot table
        // created in 2025_09_05_050000_create_subject_classes_table.php
        return;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add class_id column back
        Schema::table('subjects', function (Blueprint $table) {
            $table->foreignId('class_id')->nullable()->constrained('class_rooms')->onDelete('set null');
        });
        
        // Migrate data back from pivot table to class_id column
        $subjectClasses = DB::table('subject_classes')->get();
        
        foreach ($subjectClasses as $subjectClass) {
            DB::table('subjects')
                ->where('id', $subjectClass->subject_id)
                ->update(['class_id' => $subjectClass->class_id]);
        }
    }
};