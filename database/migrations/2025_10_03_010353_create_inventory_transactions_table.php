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
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique(); // Auto-generated transaction number
            $table->foreignId('item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->enum('type', ['in', 'out', 'transfer', 'adjustment', 'return', 'damage', 'loss']); // Transaction type
            $table->integer('quantity'); // Quantity involved in transaction
            $table->decimal('unit_cost', 10, 2)->nullable(); // Cost per unit at time of transaction
            $table->decimal('total_cost', 12, 2)->nullable(); // Total cost of transaction
            $table->integer('stock_before'); // Stock level before transaction
            $table->integer('stock_after'); // Stock level after transaction
            $table->string('reference_number')->nullable(); // Reference to purchase order, invoice, etc.
            $table->text('notes')->nullable(); // Additional notes about the transaction
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('transaction_date')->nullable(); // When the transaction actually occurred
            $table->string('location_from')->nullable(); // Source location for transfers
            $table->string('location_to')->nullable(); // Destination location for transfers
            $table->foreignId('supplier_id')->nullable()->constrained('inventory_suppliers')->nullOnDelete();
            $table->json('metadata')->nullable(); // Additional transaction data
            $table->timestamps();
            
            $table->index(['item_id', 'type']);
            $table->index(['type', 'status']);
            $table->index(['created_by', 'status']);
            $table->index(['transaction_date']);
            $table->index(['reference_number']);
            $table->index(['supplier_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
