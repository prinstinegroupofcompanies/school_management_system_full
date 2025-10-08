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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique(); // Stock Keeping Unit
            $table->string('barcode')->nullable(); // Barcode for scanning
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained('inventory_categories')->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained('inventory_suppliers')->nullOnDelete();
            $table->string('unit_of_measure')->default('pcs'); // pcs, kg, liters, etc.
            $table->decimal('unit_cost', 10, 2)->default(0); // Cost per unit
            $table->decimal('selling_price', 10, 2)->nullable(); // Selling price if applicable
            $table->integer('current_stock')->default(0); // Current quantity in stock
            $table->integer('minimum_stock')->default(0); // Minimum stock level for alerts
            $table->integer('maximum_stock')->nullable(); // Maximum stock level
            $table->integer('reorder_level')->default(0); // Level at which to reorder
            $table->integer('reorder_quantity')->default(0); // Quantity to reorder
            $table->string('location')->nullable(); // Storage location
            $table->string('shelf')->nullable(); // Shelf or bin number
            $table->date('expiry_date')->nullable(); // Expiry date for perishable items
            $table->date('last_restocked')->nullable(); // Last restock date
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->boolean('is_trackable')->default(true); // Whether to track stock movements
            $table->boolean('requires_approval')->default(false); // Whether item requires approval for transactions
            $table->json('specifications')->nullable(); // Technical specifications
            $table->json('images')->nullable(); // Item images
            $table->json('metadata')->nullable(); // Additional item data
            $table->timestamps();
            
            $table->index(['category_id', 'status']);
            $table->index(['supplier_id', 'status']);
            $table->index(['sku', 'status']);
            $table->index(['current_stock', 'minimum_stock']);
            $table->index(['expiry_date']);
            $table->index(['location', 'shelf']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
