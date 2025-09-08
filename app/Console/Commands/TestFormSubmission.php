<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Models\Teacher;

class TestFormSubmission extends Command
{
    protected $signature = 'test:form-submission';
    protected $description = 'Test form submission process';

    public function handle()
    {
        try {
            $this->info('Testing form submission process...');
            
            // Simulate form data
            $formData = [
                'name' => 'Test Form Subject',
                'code' => 'FORM-TEST-' . time(),
                'teacher_id' => 3,
                'description' => 'Test description',
                'level' => 'junior',
                'status' => 'active',
                'class_ids' => [1, 2, 3] // Multiple classes
            ];
            
            $this->info('Form data: ' . json_encode($formData));
            
            // Check if classes exist
            $classes = ClassRoom::whereIn('id', $formData['class_ids'])->get();
            $this->info('Found ' . $classes->count() . ' classes');
            
            // Check if teacher exists
            $teacher = Teacher::find($formData['teacher_id']);
            $this->info('Teacher found: ' . ($teacher ? $teacher->user->name : 'NOT FOUND'));
            
            // Create subject
            $subjectData = [
                'name' => $formData['name'],
                'code' => $formData['code'],
                'teacher_id' => $formData['teacher_id'],
                'description' => $formData['description'],
                'level' => $formData['level'],
                'status' => $formData['status']
            ];
            
            $subject = Subject::create($subjectData);
            $this->info('Created subject with ID: ' . $subject->id);
            
            // Sync classes
            $subject->classes()->sync($formData['class_ids']);
            $this->info('Synced ' . $subject->classes->count() . ' classes');
            
            // Check if subject appears in list
            $visibleSubject = Subject::whereNotNull('id')->where('id', $subject->id)->first();
            $this->info('Subject visible in list: ' . ($visibleSubject ? 'YES' : 'NO'));
            
            if ($visibleSubject) {
                $this->info('Subject details:');
                $this->line('- Name: ' . $visibleSubject->name);
                $this->line('- Code: ' . $visibleSubject->code);
                $this->line('- Classes: ' . $visibleSubject->classes->pluck('name')->join(', '));
            }
            
            $this->info('✅ Form submission test completed successfully!');
            
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}
