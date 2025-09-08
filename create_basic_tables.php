<?php
/**
 * Create Basic Tables Script
 * This will create the essential tables for the school management system
 */

echo "=== Creating Basic Database Tables ===\n\n";

$host = '127.0.0.1';
$port = 3306;
$username = 'root';
$password = 'Bryant2025@';
$database = 'school_management';

try {
    // Connect to database
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to database\n";
    
    // Create basic tables
    $tables = [
        'users' => "
            CREATE TABLE IF NOT EXISTS `users` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `email_verified_at` timestamp NULL DEFAULT NULL,
                `password` varchar(255) NOT NULL,
                `user_type` enum('admin','teacher','student','parent','accountant','librarian','staff','finance') NOT NULL,
                `phone` varchar(255) DEFAULT NULL,
                `address` text,
                `city` varchar(255) DEFAULT NULL,
                `state` varchar(255) DEFAULT NULL,
                `country` varchar(255) DEFAULT NULL,
                `postal_code` varchar(255) DEFAULT NULL,
                `profile_photo` varchar(255) DEFAULT NULL,
                `status` enum('active','inactive','suspended') DEFAULT 'active',
                `is_active` tinyint(1) NOT NULL DEFAULT '1',
                `last_login_at` timestamp NULL DEFAULT NULL,
                `last_logout_at` timestamp NULL DEFAULT NULL,
                `remember_token` varchar(100) DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `users_email_unique` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'class_rooms' => "
            CREATE TABLE IF NOT EXISTS `class_rooms` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `code` varchar(255) NOT NULL,
                `description` text,
                `capacity` int NOT NULL DEFAULT '40',
                `class_teacher_id` bigint unsigned DEFAULT NULL,
                `room_number` varchar(255) DEFAULT NULL,
                `building` varchar(255) DEFAULT NULL,
                `floor` varchar(255) DEFAULT NULL,
                `status` enum('active','inactive','maintenance') DEFAULT 'active',
                `is_active` tinyint(1) NOT NULL DEFAULT '1',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `class_rooms_code_unique` (`code`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'students' => "
            CREATE TABLE IF NOT EXISTS `students` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `user_id` bigint unsigned NOT NULL,
                `student_id` varchar(255) NOT NULL,
                `class_id` bigint unsigned DEFAULT NULL,
                `section_id` bigint unsigned DEFAULT NULL,
                `admission_date` date DEFAULT NULL,
                `date_of_birth` date DEFAULT NULL,
                `gender` enum('male','female','other') DEFAULT NULL,
                `blood_group` varchar(10) DEFAULT NULL,
                `religion` varchar(255) DEFAULT NULL,
                `nationality` varchar(255) DEFAULT NULL,
                `father_name` varchar(255) DEFAULT NULL,
                `mother_name` varchar(255) DEFAULT NULL,
                `guardian_name` varchar(255) DEFAULT NULL,
                `guardian_phone` varchar(255) DEFAULT NULL,
                `guardian_email` varchar(255) DEFAULT NULL,
                `guardian_address` text,
                `status` enum('active','inactive','graduated','transferred') DEFAULT 'active',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `students_user_id_unique` (`user_id`),
                UNIQUE KEY `students_student_id_unique` (`student_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'teachers' => "
            CREATE TABLE IF NOT EXISTS `teachers` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `user_id` bigint unsigned NOT NULL,
                `teacher_id` varchar(255) NOT NULL,
                `department_id` bigint unsigned DEFAULT NULL,
                `designation_id` bigint unsigned DEFAULT NULL,
                `qualification` varchar(255) DEFAULT NULL,
                `experience` int DEFAULT NULL,
                `salary` decimal(10,2) DEFAULT NULL,
                `joining_date` date DEFAULT NULL,
                `status` enum('active','inactive','resigned','retired') DEFAULT 'active',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `teachers_user_id_unique` (`user_id`),
                UNIQUE KEY `teachers_teacher_id_unique` (`teacher_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'subjects' => "
            CREATE TABLE IF NOT EXISTS `subjects` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `code` varchar(255) NOT NULL,
                `description` text,
                `class_id` bigint unsigned DEFAULT NULL,
                `teacher_id` bigint unsigned DEFAULT NULL,
                `credits` int DEFAULT NULL,
                `status` enum('active','inactive') DEFAULT 'active',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `subjects_code_unique` (`code`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'fee_structures' => "
            CREATE TABLE IF NOT EXISTS `fee_structures` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `class_id` bigint unsigned DEFAULT NULL,
                `amount` decimal(10,2) NOT NULL,
                `due_date` date DEFAULT NULL,
                `academic_year` varchar(255) DEFAULT NULL,
                `status` enum('active','inactive') DEFAULT 'active',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'fee_payments' => "
            CREATE TABLE IF NOT EXISTS `fee_payments` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `student_id` bigint unsigned NOT NULL,
                `fee_structure_id` bigint unsigned NOT NULL,
                `amount` decimal(10,2) NOT NULL,
                `payment_date` date NOT NULL,
                `payment_method` enum('cash','bank_transfer','cheque','online') DEFAULT 'cash',
                `transaction_id` varchar(255) DEFAULT NULL,
                `status` enum('pending','paid','overdue','cancelled') DEFAULT 'pending',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'student_attendances' => "
            CREATE TABLE IF NOT EXISTS `student_attendances` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `student_id` bigint unsigned NOT NULL,
                `class_id` bigint unsigned NOT NULL,
                `date` date NOT NULL,
                `status` enum('present','absent','late','excused') DEFAULT 'present',
                `remarks` text,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `student_attendances_student_id_date_unique` (`student_id`,`date`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'homeworks' => "
            CREATE TABLE IF NOT EXISTS `homeworks` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `title` varchar(255) NOT NULL,
                `description` text,
                `class_id` bigint unsigned NOT NULL,
                `subject_id` bigint unsigned NOT NULL,
                `teacher_id` bigint unsigned NOT NULL,
                `due_date` date NOT NULL,
                `status` enum('active','completed','cancelled') DEFAULT 'active',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'exam_schedules' => "
            CREATE TABLE IF NOT EXISTS `exam_schedules` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `title` varchar(255) NOT NULL,
                `class_id` bigint unsigned NOT NULL,
                `subject_id` bigint unsigned NOT NULL,
                `exam_date` date NOT NULL,
                `start_time` time NOT NULL,
                `end_time` time NOT NULL,
                `room` varchar(255) DEFAULT NULL,
                `status` enum('scheduled','ongoing','completed','cancelled') DEFAULT 'scheduled',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'migrations' => "
            CREATE TABLE IF NOT EXISTS `migrations` (
                `id` int unsigned NOT NULL AUTO_INCREMENT,
                `migration` varchar(255) NOT NULL,
                `batch` int NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'sessions' => "
            CREATE TABLE IF NOT EXISTS `sessions` (
                `id` varchar(255) NOT NULL,
                `user_id` bigint unsigned DEFAULT NULL,
                `ip_address` varchar(45) DEFAULT NULL,
                `user_agent` text,
                `payload` longtext NOT NULL,
                `last_activity` int NOT NULL,
                PRIMARY KEY (`id`),
                KEY `sessions_user_id_index` (`user_id`),
                KEY `sessions_last_activity_index` (`last_activity`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        "
    ];
    
    foreach ($tables as $tableName => $sql) {
        echo "Creating table: $tableName...\n";
        $pdo->exec($sql);
        echo "✅ Created: $tableName\n";
    }
    
    echo "\n=== Basic Tables Created Successfully ===\n";
    echo "You can now run: php artisan db:seed\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
