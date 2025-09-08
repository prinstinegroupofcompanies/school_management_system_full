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
        Schema::create('admission_queries', function (Blueprint $table) {
            $table->id();
            $table->string('query_number')->unique();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('alternative_phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->default('Liberia');
            $table->string('postal_code')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality')->default('Liberian');
            $table->string('previous_school')->nullable();
            $table->string('previous_class')->nullable();
            $table->string('intended_class')->nullable();
            $table->string('intended_academic_year')->nullable();
            $table->enum('query_type', ['general', 'admission', 'fee', 'curriculum', 'facilities', 'other'])->default('general');
            $table->text('query_details');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['new', 'in_progress', 'resolved', 'closed', 'spam'])->default('new');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->string('source')->nullable();
            $table->string('campaign')->nullable();
            $table->json('additional_info')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('assigned_to')->references('id')->on('users');

            // Indexes
            $table->index(['query_number', 'status']);
            $table->index(['name', 'status']);
            $table->index(['email', 'status']);
            $table->index(['phone', 'status']);
            $table->index(['query_type', 'status']);
            $table->index(['priority', 'status']);
            $table->index(['intended_class', 'status']);
            $table->index(['intended_academic_year', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index(['assigned_at', 'status']);
            $table->index(['resolved_at', 'status']);
            $table->index(['source', 'status']);
            $table->index(['created_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_queries');
    }
};
