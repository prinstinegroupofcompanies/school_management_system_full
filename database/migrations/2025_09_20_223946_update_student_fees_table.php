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
        Schema::table('student_fees', function (Blueprint $table) {
            if (!Schema::hasColumn('student_fees', 'fee_structure_id')) {
                $table->unsignedBigInteger('fee_structure_id')->nullable()->after('student_id');
            }
            if (!Schema::hasColumn('student_fees', 'academic_year')) {
                $table->integer('academic_year')->nullable()->after('year');
            }
            if (!Schema::hasColumn('student_fees', 'due_date')) {
                $table->date('due_date')->nullable()->after('balance');
            }
            if (!Schema::hasColumn('student_fees', 'status')) {
                $table->string('status')->default('unpaid')->after('due_date');
            }
            if (!Schema::hasColumn('student_fees', 'fee_type')) {
                $table->string('fee_type')->nullable()->after('status');
            }
            if (!Schema::hasColumn('student_fees', 'description')) {
                $table->text('description')->nullable()->after('fee_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_fees', function (Blueprint $table) {
            $table->dropColumn(['fee_structure_id', 'academic_year', 'due_date', 'status', 'fee_type', 'description']);
        });
    }
};
