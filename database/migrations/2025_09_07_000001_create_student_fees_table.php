<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->index();
            $table->unsignedBigInteger('class_id')->index();
            $table->string('semester', 32)->nullable()->index();
            $table->year('year')->index();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);
            $table->json('fee_breakdown')->nullable();
            $table->timestamps();

            // Intentionally avoiding foreign key constraints here due to unknown table names in the project
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_fees');
    }
};


