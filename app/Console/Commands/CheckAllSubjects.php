<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckAllSubjects extends Command
{
    protected $signature = 'check:all-subjects';
    protected $description = 'Check all subjects ordered by creation';

    public function handle()
    {
        try {
            $this->info('Checking all subjects...');
            
            $subjects = DB::table('subjects')
                ->orderBy('created_at', 'desc')
                ->get();
            
            $this->info('Total subjects: ' . $subjects->count());
            $this->line('');
            
            foreach ($subjects as $subject) {
                $this->line('ID: ' . ($subject->id ?? 'NULL') . 
                           ' | Name: ' . $subject->name . 
                           ' | Code: ' . $subject->code . 
                           ' | Created: ' . $subject->created_at);
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
