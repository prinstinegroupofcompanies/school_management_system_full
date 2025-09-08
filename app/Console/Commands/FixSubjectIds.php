<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixSubjectIds extends Command
{
    protected $signature = 'fix:subject-ids';
    protected $description = 'Fix subjects with NULL IDs';

    public function handle()
    {
        try {
            $this->info('Fixing subjects with NULL IDs...');
            
            // Get subjects with NULL IDs
            $nullIdSubjects = DB::table('subjects')->whereNull('id')->get();
            $this->info('Found ' . $nullIdSubjects->count() . ' subjects with NULL IDs');
            
            // Get the highest existing ID
            $maxId = DB::table('subjects')->whereNotNull('id')->max('id') ?? 0;
            $this->info('Highest existing ID: ' . $maxId);
            
            // Update subjects with NULL IDs
            $currentId = $maxId + 1;
            foreach ($nullIdSubjects as $subject) {
                // Update the subject with a new ID
                DB::table('subjects')
                    ->where('name', $subject->name)
                    ->where('code', $subject->code)
                    ->where('created_at', $subject->created_at)
                    ->update(['id' => $currentId]);
                
                $this->info('Updated subject "' . $subject->name . '" with ID: ' . $currentId);
                $currentId++;
            }
            
            // Reset the auto-increment sequence
            DB::statement('DELETE FROM sqlite_sequence WHERE name = "subjects"');
            DB::statement('INSERT INTO sqlite_sequence (name, seq) VALUES ("subjects", ' . ($currentId - 1) . ')');
            
            $this->info('Fixed auto-increment sequence');
            
            // Verify the fix
            $totalSubjects = DB::table('subjects')->count();
            $validIdSubjects = DB::table('subjects')->whereNotNull('id')->count();
            $this->info('Total subjects: ' . $totalSubjects);
            $this->info('Subjects with valid IDs: ' . $validIdSubjects);
            
            $this->info('✅ Subject IDs fixed successfully!');
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
