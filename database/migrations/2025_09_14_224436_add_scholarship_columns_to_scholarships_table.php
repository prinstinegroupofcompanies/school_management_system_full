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
        Schema::table('scholarships', function (Blueprint $table) {
            // Only add columns that don't already exist
            if (!Schema::hasColumn('scholarships', 'name')) {
                $table->string('name');
            }
            if (!Schema::hasColumn('scholarships', 'code')) {
                $table->string('code')->unique();
            }
            if (!Schema::hasColumn('scholarships', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('scholarships', 'type')) {
                $table->string('type')->default('merit');
            }
            if (!Schema::hasColumn('scholarships', 'amount')) {
                $table->decimal('amount', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('scholarships', 'currency')) {
                $table->string('currency', 3)->default('USD');
            }
            if (!Schema::hasColumn('scholarships', 'percentage')) {
                $table->decimal('percentage', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('scholarships', 'max_amount')) {
                $table->decimal('max_amount', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('scholarships', 'min_amount')) {
                $table->decimal('min_amount', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('scholarships', 'academic_year')) {
                $table->string('academic_year')->nullable();
            }
            if (!Schema::hasColumn('scholarships', 'class_id')) {
                $table->unsignedBigInteger('class_id')->nullable();
                $table->foreign('class_id')->references('id')->on('class_rooms')->onDelete('set null');
            }
            if (!Schema::hasColumn('scholarships', 'subject_id')) {
                $table->unsignedBigInteger('subject_id')->nullable();
                $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('set null');
            }
            if (!Schema::hasColumn('scholarships', 'eligibility_criteria')) {
                $table->text('eligibility_criteria')->nullable();
            }
            if (!Schema::hasColumn('scholarships', 'requirements')) {
                $table->text('requirements')->nullable();
            }
            if (!Schema::hasColumn('scholarships', 'application_deadline')) {
                $table->date('application_deadline')->nullable();
            }
            if (!Schema::hasColumn('scholarships', 'start_date')) {
                $table->date('start_date')->nullable();
            }
            if (!Schema::hasColumn('scholarships', 'end_date')) {
                $table->date('end_date')->nullable();
            }
            if (!Schema::hasColumn('scholarships', 'max_recipients')) {
                $table->integer('max_recipients')->default(1);
            }
            if (!Schema::hasColumn('scholarships', 'current_recipients')) {
                $table->integer('current_recipients')->default(0);
            }
            if (!Schema::hasColumn('scholarships', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (!Schema::hasColumn('scholarships', 'is_merit_based')) {
                $table->boolean('is_merit_based')->default(false);
            }
            if (!Schema::hasColumn('scholarships', 'is_need_based')) {
                $table->boolean('is_need_based')->default(false);
            }
            if (!Schema::hasColumn('scholarships', 'is_sports_based')) {
                $table->boolean('is_sports_based')->default(false);
            }
            if (!Schema::hasColumn('scholarships', 'is_arts_based')) {
                $table->boolean('is_arts_based')->default(false);
            }
            if (!Schema::hasColumn('scholarships', 'is_academic_based')) {
                $table->boolean('is_academic_based')->default(false);
            }
            if (!Schema::hasColumn('scholarships', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable();
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('scholarships', 'notes')) {
                $table->text('notes')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scholarships', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['created_by']);
            
            $table->dropColumn([
                'name', 'code', 'description', 'type', 'amount', 'currency',
                'percentage', 'max_amount', 'min_amount', 'academic_year',
                'class_id', 'subject_id', 'eligibility_criteria', 'requirements',
                'application_deadline', 'start_date', 'end_date', 'max_recipients',
                'current_recipients', 'is_active', 'is_merit_based', 'is_need_based',
                'is_sports_based', 'is_arts_based', 'is_academic_based', 'created_by', 'notes'
            ]);
        });
    }
};
