<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DebugSubjects extends Command
{
    protected $signature = 'debug:subjects';
    protected $description = 'Debug subjects data';

    public function handle()
    {
        try {
            $this->info('Debugging subjects data...');
            
            // Check all subjects
            $allSubjects = DB::table('subjects')->get();
            $this->info('Total subjects in database: ' . $allSubjects->count());
            
            // Check subjects with null IDs
            $nullIdSubjects = DB::table('subjects')->whereNull('id')->get();
            $this->info('Subjects with null ID: ' . $nullIdSubjects->count());
            
            // Check subjects with empty string IDs
            $emptyIdSubjects = DB::table('subjects')->where('id', '')->get();
            $this->info('Subjects with empty string ID: ' . $emptyIdSubjects->count());
            
            // Check subjects with valid IDs
            $validIdSubjects = DB::table('subjects')->whereNotNull('id')->where('id', '!=', '')->get();
            $this->info('Subjects with valid ID: ' . $validIdSubjects->count());
            
            $this->info('Sample of subjects with issues:');
            foreach ($nullIdSubjects->take(3) as $subject) {
                $this->line('- Name: ' . $subject->name . ', Code: ' . $subject->code . ', ID: ' . ($subject->id ?? 'NULL'));
            }
            
            foreach ($emptyIdSubjects->take(3) as $subject) {
                $this->line('- Name: ' . $subject->name . ', Code: ' . $subject->code . ', ID: "' . $subject->id . '"');
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}