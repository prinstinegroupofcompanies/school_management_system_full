<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Staff;

class ListUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:list {--type= : Filter by user type (student, teacher, admin, finance)} {--format=table : Output format (table, json, csv)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all users in the system with their details';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $format = $this->option('format');

        $query = User::query();

        if ($type) {
            $query->where('user_type', $type);
        }

        $users = $query->with(['student', 'teacher', 'staff'])->get();

        if ($users->isEmpty()) {
            $this->info('No users found.');
            return;
        }

        $this->info("Found {$users->count()} user(s)");

        switch ($format) {
            case 'json':
                $this->output->write(json_encode($users, JSON_PRETTY_PRINT));
                break;
            case 'csv':
                $this->outputCsv($users);
                break;
            default:
                $this->outputTable($users);
                break;
        }
    }

    private function outputTable($users)
    {
        $headers = ['ID', 'Name', 'Email', 'Type', 'Status', 'Created', 'Details'];
        $rows = [];

        foreach ($users as $user) {
            $details = '';
            if ($user->student) {
                $details = "Student ID: {$user->student->student_id}, Class: " . ($user->student->class ? $user->student->class->name : 'N/A');
            } elseif ($user->teacher) {
                $details = "Teacher ID: {$user->teacher->teacher_id}, Subject: " . ($user->teacher->subjects->first() ? $user->teacher->subjects->first()->name : 'N/A');
            } elseif ($user->staff) {
                $details = "Staff ID: {$user->staff->staff_id}, Position: {$user->staff->position}";
            }

            $rows[] = [
                $user->id,
                $user->name,
                $user->email,
                $user->user_type,
                $user->email_verified_at ? 'Verified' : 'Unverified',
                $user->created_at->format('Y-m-d H:i'),
                $details
            ];
        }

        $this->table($headers, $rows);
    }

    private function outputCsv($users)
    {
        $headers = ['ID', 'Name', 'Email', 'Type', 'Status', 'Created', 'Details'];
        $csv = implode(',', $headers) . "\n";

        foreach ($users as $user) {
            $details = '';
            if ($user->student) {
                $details = "Student ID: {$user->student->student_id}, Class: " . ($user->student->class ? $user->student->class->name : 'N/A');
            } elseif ($user->teacher) {
                $details = "Teacher ID: {$user->teacher->teacher_id}, Subject: " . ($user->teacher->subjects->first() ? $user->teacher->subjects->first()->name : 'N/A');
            } elseif ($user->staff) {
                $details = "Staff ID: {$user->staff->staff_id}, Position: {$user->staff->position}";
            }

            $row = [
                $user->id,
                $user->name,
                $user->email,
                $user->user_type,
                $user->email_verified_at ? 'Verified' : 'Unverified',
                $user->created_at->format('Y-m-d H:i'),
                $details
            ];

            $csv .= implode(',', array_map(function($field) {
                return '"' . str_replace('"', '""', $field) . '"';
            }, $row)) . "\n";
        }

        $this->output->write($csv);
    }
}
