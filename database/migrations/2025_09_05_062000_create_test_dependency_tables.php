<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'student_categories',
            'student_groups',
            'student_houses',
            'admission_queries',
            'transport_routes',
            'hostel_rooms',
            'guardians',
            'fee_structures',
            'scholarships',
            'discounts',
        ];

        foreach ($tables as $name) {
            if (!Schema::hasTable($name)) {
                Schema::create($name, function (Blueprint $table) {
                    $table->id();
                    $table->timestamps();
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'discounts',
            'scholarships',
            'fee_structures',
            'guardians',
            'hostel_rooms',
            'transport_routes',
            'admission_queries',
            'student_houses',
            'student_groups',
            'student_categories',
        ];

        foreach ($tables as $name) {
            if (Schema::hasTable($name)) {
                Schema::drop($name);
            }
        }
    }
};


