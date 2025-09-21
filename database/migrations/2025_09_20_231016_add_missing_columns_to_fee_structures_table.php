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
        Schema::table('fee_structures', function (Blueprint $table) {
            // Add missing columns that are required by the FeeStructure model
            $table->string('name')->after('id');
            $table->text('description')->nullable()->after('name');
            $table->string('academic_year')->after('class_id');
            $table->enum('fee_type', ['tuition', 'library', 'laboratory', 'sports', 'transport', 'hostel', 'examination', 'miscellaneous'])->default('tuition')->after('academic_year');
            
            // Add total_amount column (keep the original amount column for now)
            $table->decimal('total_amount', 10, 2)->after('amount');
            
            // Add remaining financial columns
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('total_amount');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_percentage');
            $table->decimal('final_amount', 10, 2)->after('discount_amount');
            $table->date('due_date')->after('final_amount');
            $table->integer('grace_period_days')->default(0)->after('due_date');
            $table->decimal('late_fee_percentage', 5, 2)->default(0)->after('grace_period_days');
            $table->decimal('late_fee_amount', 10, 2)->default(0)->after('late_fee_percentage');
            $table->boolean('allow_installments')->default(false)->after('late_fee_amount');
            $table->integer('max_installments')->default(1)->after('allow_installments');
            $table->enum('status', ['active', 'inactive', 'draft'])->default('active')->after('max_installments');
            $table->boolean('is_active')->default(true)->after('status');
        });
        
        // Copy data from amount to total_amount and set final_amount
        DB::statement('UPDATE fee_structures SET total_amount = amount, final_amount = amount WHERE total_amount IS NULL');
        
        // Now drop the old amount column
        Schema::table('fee_structures', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the amount column
        Schema::table('fee_structures', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->after('class_id');
        });
        
        // Copy data from total_amount to amount
        DB::statement('UPDATE fee_structures SET amount = total_amount WHERE amount IS NULL');
        
        // Remove added columns
        Schema::table('fee_structures', function (Blueprint $table) {
            $table->dropColumn([
                'is_active',
                'status',
                'max_installments',
                'allow_installments',
                'late_fee_amount',
                'late_fee_percentage',
                'grace_period_days',
                'due_date',
                'final_amount',
                'discount_amount',
                'discount_percentage',
                'total_amount',
                'fee_type',
                'academic_year',
                'description',
                'name'
            ]);
        });
    }
};
