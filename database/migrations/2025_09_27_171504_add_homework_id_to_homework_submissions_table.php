<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('homework_submissions', function (Blueprint $table) {
            // Check if homework_id column doesn't exist before adding it
            if (!Schema::hasColumn('homework_submissions', 'homework_id')) {
                $table->unsignedBigInteger('homework_id')->after('id');
            }
            
            // Check if student_id column doesn't exist before adding it
            if (!Schema::hasColumn('homework_submissions', 'student_id')) {
                $table->unsignedBigInteger('student_id')->after('homework_id');
            }
            
            // Add foreign key constraints if they don't exist
            if (!Schema::hasColumn('homework_submissions', 'homework_id')) {
                $table->foreign('homework_id')->references('id')->on('homework_assignments')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('homework_submissions', 'student_id')) {
                $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('homework_submissions', function (Blueprint $table) {
            $table->dropForeign(['homework_id']);
            $table->dropForeign(['student_id']);
            $table->dropColumn(['homework_id', 'student_id']);
        });
    }
};