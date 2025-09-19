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
        Schema::table('students', function (Blueprint $table) {
            // Auto-generated unique identifiers
            $table->string('admission_number')->unique()->nullable()->after('student_id');
            $table->string('student_number')->unique()->nullable()->after('admission_number');
            
            // Enhanced academic tracking
            $table->json('assigned_subjects')->nullable()->after('class_id');
            $table->json('assigned_teachers')->nullable()->after('assigned_subjects');
            
            // Fee structure tracking
            $table->decimal('total_fees', 10, 2)->default(0)->after('wallet_balance');
            $table->decimal('paid_fees', 10, 2)->default(0)->after('total_fees');
            $table->decimal('balance_fees', 10, 2)->default(0)->after('paid_fees');
            
            // Academic performance tracking
            $table->decimal('current_gpa', 3, 2)->nullable()->after('balance_fees');
            $table->string('current_grade')->nullable()->after('current_gpa');
            $table->integer('attendance_percentage')->default(0)->after('current_grade');
            
            // Activity records
            $table->json('activity_log')->nullable()->after('attendance_percentage');
            $table->timestamp('last_activity_at')->nullable()->after('activity_log');
            
            // International standards compliance
            $table->string('international_student_id')->unique()->nullable()->after('student_number');
            $table->string('curriculum_type')->default('international')->after('international_student_id');
            $table->json('learning_objectives')->nullable()->after('curriculum_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'admission_number',
                'student_number', 
                'assigned_subjects',
                'assigned_teachers',
                'total_fees',
                'paid_fees',
                'balance_fees',
                'current_gpa',
                'current_grade',
                'attendance_percentage',
                'activity_log',
                'last_activity_at',
                'international_student_id',
                'curriculum_type',
                'learning_objectives'
            ]);
        });
    }
};