<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_reports', function (Blueprint $table) {
            $table->id();
            $table->enum('range', ['daily','weekly','monthly','yearly']);
            $table->decimal('total', 12, 2)->default(0);
            $table->unsignedInteger('count')->default(0);
            $table->foreignId('pushed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('pushed_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_reports');
    }
};


