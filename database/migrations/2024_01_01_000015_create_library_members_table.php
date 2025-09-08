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
        Schema::create('library_members', function (Blueprint $table) {
            $table->id();
            $table->string('member_id')->unique();
            $table->unsignedBigInteger->constrained();
            $table->enum('member_type', ['student', 'teacher', 'staff', 'parent', 'external'])->default('student');
            $table->string('card_number')->unique();
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->integer('max_books_allowed')->default(3);
            $table->integer('current_books_borrowed')->default(0);
            $table->decimal('fine_balance', 10, 2)->default(0.00);
            $table->enum('status', ['active', 'inactive', 'suspended', 'expired'])->default('active');
            $table->text('suspension_reason')->nullable();
            $table->date('suspension_start_date')->nullable();
            $table->date('suspension_end_date')->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['member_id', 'member_type']);
            $table->index(['card_number', 'status']);
            $table->index(['issue_date', 'expiry_date']);
            $table->index(['status', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_members');
    }
}; 