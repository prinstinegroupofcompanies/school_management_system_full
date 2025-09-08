<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class MigrateSubjectClassData extends Command
{
    protected $signature = 'migrate:subject-class-data';
    protected $description = 'Migrate subject class data to pivot table';

    public function handle()
    {
        $subjects = Subject::whereNotNull('class_id')->get();
        
        $this->info('Found ' . $subjects->count() . ' subjects with class_id');
        
        foreach ($subjects as $subject) {
            DB::table('subject_classes')->insertOrIgnore([
                'subject_id' => $subject->id,
                'class_id' => $subject->class_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        $this->info('Successfully migrated ' . $subjects->count() . ' subjects to pivot table');
        
        return 0;
    }
}