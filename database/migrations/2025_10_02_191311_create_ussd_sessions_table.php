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
        Schema::create('ussd_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique(); // USSD session ID
            $table->string('phone_number');
            $table->string('service_code'); // USSD service code
            $table->enum('status', ['active', 'completed', 'timeout', 'cancelled'])->default('active');
            $table->text('current_menu')->nullable(); // Current menu being displayed
            $table->text('user_input')->nullable(); // User's input
            $table->json('session_data')->nullable(); // Session data storage
            $table->integer('step')->default(1); // Current step in the flow
            $table->timestamp('last_activity')->nullable(); // Last activity timestamp
            $table->timestamp('expires_at')->nullable(); // Session expiry
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('guardians')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['phone_number', 'status']);
            $table->index(['session_id', 'status']);
            $table->index(['expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ussd_sessions');
    }
};
