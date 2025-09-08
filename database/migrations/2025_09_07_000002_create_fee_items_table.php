<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('price_per_unit', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->unsignedBigInteger('class_id')->nullable()->index();
            $table->string('semester', 32)->nullable()->index();
            $table->year('year')->nullable()->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_items');
    }
};


