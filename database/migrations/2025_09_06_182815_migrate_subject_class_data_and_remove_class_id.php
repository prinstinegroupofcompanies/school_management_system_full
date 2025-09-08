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
        // First, migrate existing data from subjects.class_id to the pivot table
        $subjects = DB::table('subjects')->whereNotNull('class_id')->get();
        
        foreach ($subjects as $subject) {
            DB::table('subject_classes')->insert([
                'subject_id' => $subject->id,
                'class_id' => $subject->class_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Remove the class_id column from subjects table
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
        });
        
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('class_id');
        });
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