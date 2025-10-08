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
        Schema::create('inventory_suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // Supplier code for easy reference
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->default('Liberia');
            $table->string('postal_code')->nullable();
            $table->string('website')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->decimal('credit_limit', 12, 2)->nullable(); // Credit limit for purchases
            $table->integer('payment_terms_days')->default(30); // Payment terms in days
            $table->string('tax_id')->nullable(); // Tax identification number
            $table->json('metadata')->nullable(); // Additional supplier data
            $table->timestamps();
            
            $table->index(['status', 'name']);
            $table->index('code');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_suppliers');
    }
};
