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
        $teacherUser = DB::table('users')->where('email', 'teacher@school.com')->first();

        if (! $teacherUser) {
            return;
        }

        $existingTeacher = DB::table('teachers')->where('user_id', $teacherUser->id)->first();

        if ($existingTeacher) {
            return;
        }

        $departmentId = DB::table('departments')->value('id');
        $designationId = DB::table('designations')->value('id');

        DB::table('teachers')->insert([
            'user_id' => $teacherUser->id,
            'teacher_id' => 'TCH' . str_pad((string) $teacherUser->id, 4, '0', STR_PAD_LEFT),
            'employee_id' => 'EMP' . str_pad((string) $teacherUser->id, 4, '0', STR_PAD_LEFT),
            'department_id' => $departmentId,
            'designation_id' => $designationId,
            'qualification' => 'Bachelor of Education',
            'experience' => 5,
            'joining_date' => now()->subYears(5),
            'salary' => 2500.00,
            'basic_salary' => 2000.00,
            'status' => 'active',
            'employment_status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('teachers')->where('user_id', 2)->delete();
    }
};
