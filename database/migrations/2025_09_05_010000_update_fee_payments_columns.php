<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_payments', function (Blueprint $table) {
            // Relax constraints to match real usage
            if (Schema::hasColumn('fee_payments', 'payment_method')) {
                $table->string('payment_method', 50)->nullable()->change();
            }
            if (Schema::hasColumn('fee_payments', 'fee_structure_id')) {
                $table->foreignId('fee_structure_id')->nullable()->change();
            }
            if (Schema::hasColumn('fee_payments', 'payment_no')) {
                $table->string('payment_no')->nullable()->change();
            }
            if (Schema::hasColumn('fee_payments', 'collected_by')) {
                $table->foreignId('collected_by')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        // No strict down; leaving relaxed constraints
    }
};


