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
        Schema::table('grades', function (Blueprint $table) {
            // Add academic period columns
            $table->unsignedBigInteger('academic_period_id')->nullable()->after('semester');
            $table->integer('period_1')->nullable()->after('academic_period_id');
            $table->integer('period_2')->nullable()->after('period_1');
            $table->integer('period_3')->nullable()->after('period_2');
            $table->integer('period_4')->nullable()->after('period_3');
            $table->integer('period_5')->nullable()->after('period_4');
            $table->integer('period_6')->nullable()->after('period_5');
            $table->integer('exam')->nullable()->after('period_6');
            $table->decimal('period_average', 5, 2)->nullable()->after('exam');
            
            // Add foreign key
            $table->foreign('academic_period_id')->references('id')->on('academic_periods');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropForeign(['academic_period_id']);
            $table->dropColumn([
                'academic_period_id',
                'period_1',
                'period_2', 
                'period_3',
                'period_4',
                'period_5',
                'period_6',
                'exam',
                'period_average'
            ]);
        });
    }
};