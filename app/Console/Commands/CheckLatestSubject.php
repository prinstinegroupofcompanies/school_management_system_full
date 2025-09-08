<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckLatestSubject extends Command
{
    protected $signature = 'check:latest-subject';
    protected $description = 'Check the latest created subject';

    public function handle()
    {
        try {
            $this->info('Checking latest subject...');
            
            // Get the latest subject by ID
            $latestSubject = DB::table('subjects')->orderBy('id', 'desc')->first();
            
            if ($latestSubject) {
                $this->info('Latest subject:');
                $this->line('- ID: ' . ($latestSubject->id ?? 'NULL'));
                $this->line('- Name: ' . $latestSubject->name);
                $this->line('- Code: ' . $latestSubject->code);
                $this->line('- Status: ' . $latestSubject->status);
                
                // Check if it has classes
                $classes = DB::table('subject_classes')
                    ->where('subject_id', $latestSubject->id)
                    ->count();
                $this->line('- Classes assigned: ' . $classes);
                
                // Check if it appears in the filtered query
                $visible = DB::table('subjects')
                    ->whereNotNull('id')
                    ->where('id', $latestSubject->id)
                    ->exists();
                $this->line('- Visible in filtered query: ' . ($visible ? 'YES' : 'NO'));
                
            } else {
                $this->error('No subjects found in database');
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
