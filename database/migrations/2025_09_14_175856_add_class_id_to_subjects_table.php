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
        // Skip this migration - class_id column already exists in subjects table
        // and we're using the pivot table subject_classes for many-to-many relationships
        if (!Schema::hasColumn('subjects', 'class_id')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->unsignedBigInteger('class_id')->nullable();
                $table->foreign('class_id')->references('id')->on('class_rooms')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropColumn('class_id');
        });
    }
};
