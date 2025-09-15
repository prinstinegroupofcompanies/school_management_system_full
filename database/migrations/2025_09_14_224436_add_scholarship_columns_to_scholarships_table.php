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
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('type')->default('merit');
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->decimal('percentage', 5, 2)->nullable();
            $table->decimal('max_amount', 10, 2)->nullable();
            $table->decimal('min_amount', 10, 2)->nullable();
            $table->string('academic_year')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->text('eligibility_criteria')->nullable();
            $table->text('requirements')->nullable();
            $table->date('application_deadline')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('max_recipients')->default(1);
            $table->integer('current_recipients')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_merit_based')->default(false);
            $table->boolean('is_need_based')->default(false);
            $table->boolean('is_sports_based')->default(false);
            $table->boolean('is_arts_based')->default(false);
            $table->boolean('is_academic_based')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->text('notes')->nullable();
            
            $table->foreign('class_id')->references('id')->on('class_rooms')->onDelete('set null');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
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
