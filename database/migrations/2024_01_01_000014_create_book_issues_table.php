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
        Schema::create('book_issues', function (Blueprint $table) {
            $table->id();
            $table->string('issue_no')->unique();
            $table->unsignedBigInteger->constrained();
            $table->unsignedBigInteger;
            $table->unsignedBigInteger('class_id')->nullable();
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->time('issue_time')->nullable();
            $table->time('return_time')->nullable();
            $table->enum('status', ['issued', 'returned', 'overdue', 'lost', 'damaged'])->default('issued');
            $table->text('issue_notes')->nullable();
            $table->text('return_notes')->nullable();
            $table->decimal('fine_amount', 10, 2)->default(0.00);
            $table->text('fine_reason')->nullable();
            $table->boolean('fine_paid')->default(false);
            $table->date('fine_paid_date')->nullable();
            $table->unsignedBigInteger->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->index(['issue_no', 'member_id']);
            $table->index(['book_id', 'status']);
            $table->index(['issue_date', 'due_date']);
            $table->index(['status', 'due_date']);
            $table->index(['issued_by', 'issue_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_issues');
    }
}; 