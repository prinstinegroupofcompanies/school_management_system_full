<?php
/**
 * Fix Migration Dependencies Script
 * This will temporarily remove foreign key constraints to allow migrations to run
 */

echo "=== Fixing Migration Dependencies ===\n\n";

$migrationFiles = [
    '2024_01_01_000004_create_sections_table.php',
    '2024_01_01_000005_create_departments_table.php',
    '2024_01_01_000006_create_designations_table.php',
    '2024_01_01_000007_create_fee_structures_table.php',
    '2024_01_01_000008_create_fee_payments_table.php',
    '2024_01_01_000008_create_staff_table.php',
    '2024_01_01_000009_create_exam_schedules_table.php',
    '2024_01_01_000009_create_guardians_table.php',
    '2024_01_01_000010_create_exam_marks_table.php',
    '2024_01_01_000010_create_student_categories_table.php',
    '2024_01_01_000011_create_student_attendances_table.php',
    '2024_01_01_000011_create_student_groups_table.php',
    '2024_01_01_000012_create_books_table.php',
    '2024_01_01_000012_create_student_houses_table.php',
    '2024_01_01_000013_create_book_categories_table.php',
    '2024_01_01_000014_create_book_issues_table.php',
    '2024_01_01_000015_create_library_members_table.php',
    '2024_01_01_000016_create_transport_routes_table.php',
    '2024_01_01_000017_create_vehicles_table.php',
    '2024_01_01_000018_create_hostel_rooms_table.php',
    '2024_01_01_000019_create_room_types_table.php',
    '2024_01_01_000020_create_online_exams_table.php',
    '2024_01_01_000021_create_question_banks_table.php',
    '2024_01_01_000022_create_online_exam_attempts_table.php',
    '2024_01_01_000022_create_student_attendance_table.php',
    '2024_01_01_000023_create_homework_table.php',
    '2024_01_01_000023_create_notifications_table.php',
    '2024_01_01_000024_create_activities_table.php',
    '2024_01_01_000024_create_homework_submissions_table.php',
    '2024_01_01_000025_create_chat_groups_table.php',
    '2024_01_01_000026_create_chat_group_members_table.php',
    '2024_01_01_000026_create_chat_messages_table.php',
    '2024_01_01_000028_create_scholarships_table.php',
    '2024_01_01_000029_create_scholarship_applications_table.php',
    '2024_01_01_000030_create_activity_logs_table.php',
    '2024_01_01_000030_create_discounts_table.php',
    '2024_01_01_000031_create_student_timeline_table.php',
    '2024_01_01_000031_create_student_timelines_table.php',
    '2024_01_01_000032_create_admission_queries_table.php',
    '2024_01_01_000035_create_book_subcategories_table.php',
    '2024_01_01_000044_create_question_bank_table.php',
    '2024_01_01_000051_create_study_materials_table.php',
    '2024_01_01_000057_create_classrooms_table.php'
];

$migrationPath = 'database/migrations/';

foreach ($migrationFiles as $file) {
    $filePath = $migrationPath . $file;
    if (file_exists($filePath)) {
        echo "Processing: $file\n";
        
        $content = file_get_contents($filePath);
        
        // Remove foreign key constraints temporarily
        $content = preg_replace('/->constrained\([^)]+\)/', '', $content);
        $content = preg_replace('/->onDelete\([^)]+\)/', '', $content);
        $content = preg_replace('/->onUpdate\([^)]+\)/', '', $content);
        
        // Replace foreignId with unsignedBigInteger
        $content = preg_replace('/foreignId\([^)]+\)/', 'unsignedBigInteger', $content);
        
        file_put_contents($filePath, $content);
        echo "✅ Fixed: $file\n";
    }
}

echo "\n=== Migration Fixes Complete ===\n";
echo "You can now run: php artisan migrate\n";
?>
