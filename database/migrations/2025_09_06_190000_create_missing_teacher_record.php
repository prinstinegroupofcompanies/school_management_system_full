<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create teacher record for user_id 2 (teacher@school.com) if it doesn't exist
        $existingTeacher = DB::table('teachers')->where('user_id', 2)->first();
        
        if (!$existingTeacher) {
            DB::table('teachers')->insert([
                'user_id' => 2,
                'teacher_id' => 'TCH0002',
                'employee_id' => 'EMP0002',
                'department_id' => 1,
                'designation_id' => 1,
                'qualification' => 'Bachelor of Education',
                'experience' => 5,
                'joining_date' => now()->subYears(5),
                'salary' => 2500.00,
                'basic_salary' => 2000.00,
                'status' => 'active',
                'employment_status' => 'permanent',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('teachers')->where('user_id', 2)->delete();
    }
};
