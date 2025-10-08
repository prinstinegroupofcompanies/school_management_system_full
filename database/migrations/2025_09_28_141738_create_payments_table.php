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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('payable_type'); // 'hostel', 'transport', 'library', 'tuition'
            $table->unsignedBigInteger('payable_id')->nullable(); // ID of the related item
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('payment_method'); // 'cash', 'bank_transfer', 'mobile_money', 'card'
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->text('description')->nullable();
            $table->timestamp('payment_date');
            $table->string('reference_number')->unique();
            $table->timestamps();

            // Indexes
            $table->index(['payable_type', 'payable_id']);
            $table->index('status');
            $table->index('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};