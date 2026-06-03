<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('feature_key'); // e.g. transport, hostel, finance, exams
            $table->boolean('enabled')->default(true);
            $table->timestamps();
            $table->unique(['school_id', 'feature_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_addons');
    }
};
