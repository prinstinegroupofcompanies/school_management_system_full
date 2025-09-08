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
        Schema::table('exam_schedules', function (Blueprint $table) {
            $table->integer('total_marks')->nullable()->after('end_time');
            $table->integer('passing_marks')->nullable()->after('total_marks');
            $table->string('room_number')->nullable()->after('passing_marks');
            $table->date('exam_date')->nullable()->after('room_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_schedules', function (Blueprint $table) {
            $table->dropColumn(['total_marks', 'passing_marks', 'room_number', 'exam_date']);
        });
    }
};
