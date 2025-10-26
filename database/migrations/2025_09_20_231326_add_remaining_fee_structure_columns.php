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
        // Skip this migration entirely - all columns already exist in the fee_structures table
        \Log::info('Skipping migration 2025_09_20_231326_add_remaining_fee_structure_columns - all columns already exist');
        
        // Note: The amount column is kept for backward compatibility alongside total_amount
        return;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back amount column
        Schema::table('fee_structures', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->after('fee_type');
        });
        
        // Copy data back
        DB::statement('UPDATE fee_structures SET amount = total_amount');
        
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
                'total_amount'
            ]);
        });
    }
};
