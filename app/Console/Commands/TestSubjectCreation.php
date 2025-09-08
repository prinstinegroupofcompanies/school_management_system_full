<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subject;

class TestSubjectCreation extends Command
{
    protected $signature = 'test:subject-creation';
    protected $description = 'Test subject creation and listing';

    public function handle()
    {
        try {
            $this->info('Testing subject creation...');
            
            // Check current subject count
            $beforeCount = Subject::count();
            $this->info('Subjects before creation: ' . $beforeCount);
            
            // Create a test subject
            $subject = Subject::create([
                'name' => 'Test Subject for Display',
                'code' => 'TEST-DISPLAY',
                'teacher_id' => 3,
                'level' => 'junior',
                'status' => 'active'
            ]);
            
            $this->info('Created subject with ID: ' . $subject->id);
            
            // Check subject count after creation
            $afterCount = Subject::count();
            $this->info('Subjects after creation: ' . $afterCount);
            
            // Check if subject appears in list with whereNotNull('id')
            $visibleSubjects = Subject::whereNotNull('id')->count();
            $this->info('Visible subjects (with valid ID): ' . $visibleSubjects);
            
            // Test the classes relationship
            $subject->classes()->sync([1, 2]);
            $this->info('Assigned ' . $subject->classes->count() . ' classes to the subject');
            
            // Show the subject details
            $this->info('Subject details:');
            $this->line('- ID: ' . $subject->id);
            $this->line('- Name: ' . $subject->name);
            $this->line('- Code: ' . $subject->code);
            $this->line('- Status: ' . $subject->status);
            $this->line('- Classes: ' . $subject->classes->pluck('name')->join(', '));
            
            $this->info('✅ Subject creation test completed successfully!');
            
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}