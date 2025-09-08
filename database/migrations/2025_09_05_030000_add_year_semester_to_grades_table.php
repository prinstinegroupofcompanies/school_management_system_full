<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            if (!Schema::hasColumn('grades', 'academic_year')) {
                $table->integer('academic_year')->after('teacher_id');
            }
            if (!Schema::hasColumn('grades', 'semester')) {
                $table->integer('semester')->after('academic_year');
            }
        });

        // Adjust unique constraint to include academic_year
        Schema::table('grades', function (Blueprint $table) {
            // Drop previous unique if exists
            try {
                $table->dropUnique(['student_id','class_id','subject_id']);
            } catch (\Throwable $e) {
                // ignore if it doesn't exist or name differs
            }
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->unique(['student_id','class_id','subject_id','academic_year'], 'grades_unique_per_year');
            $table->index(['academic_year','semester']);
        });
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            try {
                $table->dropUnique('grades_unique_per_year');
            } catch (\Throwable $e) {
            }
            try {
                $table->dropIndex(['academic_year','semester']);
            } catch (\Throwable $e) {
            }
            if (Schema::hasColumn('grades', 'semester')) {
                $table->dropColumn('semester');
            }
            if (Schema::hasColumn('grades', 'academic_year')) {
                $table->dropColumn('academic_year');
            }
            // Restore original unique (without year)
            try {
                $table->unique(['student_id','class_id','subject_id']);
            } catch (\Throwable $e) {
            }
        });
    }
};


