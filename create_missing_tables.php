<?php
/**
 * Create Missing Tables Script
 * This will create all the missing tables for the school management system
 */

echo "=== Creating Missing Database Tables ===\n\n";

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
    
    // Create missing tables
    $tables = [
        'departments' => "
            CREATE TABLE IF NOT EXISTS `departments` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `code` varchar(50) NOT NULL,
                `description` text,
                `head_id` bigint unsigned DEFAULT NULL,
                `status` enum('active','inactive') DEFAULT 'active',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `departments_code_unique` (`code`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'designations' => "
            CREATE TABLE IF NOT EXISTS `designations` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `description` text,
                `department_id` bigint unsigned DEFAULT NULL,
                `status` enum('active','inactive') DEFAULT 'active',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'exam_types' => "
            CREATE TABLE IF NOT EXISTS `exam_types` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `description` text,
                `is_active` tinyint(1) NOT NULL DEFAULT '1',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `exam_types_name_unique` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'books' => "
            CREATE TABLE IF NOT EXISTS `books` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `title` varchar(255) NOT NULL,
                `author` varchar(255) NOT NULL,
                `isbn` varchar(50) DEFAULT NULL,
                `publisher` varchar(255) DEFAULT NULL,
                `publication_year` year DEFAULT NULL,
                `category_id` bigint unsigned DEFAULT NULL,
                `total_copies` int NOT NULL DEFAULT '1',
                `available_copies` int NOT NULL DEFAULT '1',
                `price` decimal(10,2) DEFAULT NULL,
                `description` text,
                `status` enum('available','unavailable','damaged','lost') DEFAULT 'available',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'book_categories' => "
            CREATE TABLE IF NOT EXISTS `book_categories` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `description` text,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'book_issues' => "
            CREATE TABLE IF NOT EXISTS `book_issues` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `book_id` bigint unsigned NOT NULL,
                `student_id` bigint unsigned DEFAULT NULL,
                `teacher_id` bigint unsigned DEFAULT NULL,
                `issue_date` date NOT NULL,
                `due_date` date NOT NULL,
                `return_date` date DEFAULT NULL,
                `status` enum('issued','returned','overdue','lost') DEFAULT 'issued',
                `fine_amount` decimal(10,2) DEFAULT '0.00',
                `notes` text,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'library_members' => "
            CREATE TABLE IF NOT EXISTS `library_members` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `user_id` bigint unsigned NOT NULL,
                `member_type` enum('student','teacher','staff') NOT NULL,
                `member_id` varchar(50) NOT NULL,
                `join_date` date NOT NULL,
                `expiry_date` date DEFAULT NULL,
                `status` enum('active','inactive','suspended') DEFAULT 'active',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `library_members_member_id_unique` (`member_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'transport_routes' => "
            CREATE TABLE IF NOT EXISTS `transport_routes` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `description` text,
                `start_location` varchar(255) NOT NULL,
                `end_location` varchar(255) NOT NULL,
                `distance` decimal(8,2) DEFAULT NULL,
                `estimated_time` int DEFAULT NULL,
                `fare` decimal(10,2) DEFAULT NULL,
                `vehicle_id` bigint unsigned DEFAULT NULL,
                `driver_id` bigint unsigned DEFAULT NULL,
                `status` enum('active','inactive') DEFAULT 'active',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'vehicles' => "
            CREATE TABLE IF NOT EXISTS `vehicles` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `vehicle_number` varchar(50) NOT NULL,
                `vehicle_type` enum('bus','van','car','truck') NOT NULL,
                `make` varchar(100) DEFAULT NULL,
                `model` varchar(100) DEFAULT NULL,
                `year` year DEFAULT NULL,
                `capacity` int NOT NULL,
                `driver_id` bigint unsigned DEFAULT NULL,
                `route_id` bigint unsigned DEFAULT NULL,
                `status` enum('active','inactive','maintenance') DEFAULT 'active',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `vehicles_vehicle_number_unique` (`vehicle_number`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'drivers' => "
            CREATE TABLE IF NOT EXISTS `drivers` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `license_number` varchar(100) NOT NULL,
                `phone` varchar(20) DEFAULT NULL,
                `address` text,
                `hire_date` date DEFAULT NULL,
                `salary` decimal(10,2) DEFAULT NULL,
                `status` enum('active','inactive','suspended') DEFAULT 'active',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `drivers_license_number_unique` (`license_number`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'route_stops' => "
            CREATE TABLE IF NOT EXISTS `route_stops` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `route_id` bigint unsigned NOT NULL,
                `stop_name` varchar(255) NOT NULL,
                `stop_order` int NOT NULL,
                `latitude` decimal(10,8) DEFAULT NULL,
                `longitude` decimal(11,8) DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'settings' => "
            CREATE TABLE IF NOT EXISTS `settings` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `key` varchar(255) NOT NULL,
                `value` text,
                `description` text,
                `type` enum('string','integer','boolean','json') DEFAULT 'string',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `settings_key_unique` (`key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'schools' => "
            CREATE TABLE IF NOT EXISTS `schools` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `address` text,
                `phone` varchar(20) DEFAULT NULL,
                `email` varchar(255) DEFAULT NULL,
                `website` varchar(255) DEFAULT NULL,
                `logo` varchar(255) DEFAULT NULL,
                `established_year` year DEFAULT NULL,
                `principal_name` varchar(255) DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        
        'exam_marks' => "
            CREATE TABLE IF NOT EXISTS `exam_marks` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `exam_schedule_id` bigint unsigned NOT NULL,
                `student_id` bigint unsigned NOT NULL,
                `marks_obtained` decimal(5,2) NOT NULL,
                `total_marks` decimal(5,2) NOT NULL,
                `grade` varchar(5) DEFAULT NULL,
                `remarks` text,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        "
    ];
    
    // Create each table
    foreach ($tables as $tableName => $sql) {
        echo "Creating table '$tableName'...\n";
        $pdo->exec($sql);
        echo "✅ Table '$tableName' created successfully\n\n";
    }
    
    // Insert some basic data
    echo "Inserting basic data...\n";
    
    // Insert departments
    $departments = [
        ['name' => 'Mathematics', 'code' => 'MATH', 'description' => 'Mathematics Department'],
        ['name' => 'Science', 'code' => 'SCI', 'description' => 'Science Department'],
        ['name' => 'English', 'code' => 'ENG', 'description' => 'English Department'],
        ['name' => 'Social Studies', 'code' => 'SOC', 'description' => 'Social Studies Department']
    ];
    
    foreach ($departments as $dept) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO departments (name, code, description, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->execute([$dept['name'], $dept['code'], $dept['description']]);
    }
    echo "✅ Departments inserted\n";
    
    // Insert exam types
    $examTypes = [
        ['name' => 'Midterm Exam', 'description' => 'Midterm examination'],
        ['name' => 'Final Exam', 'description' => 'Final examination'],
        ['name' => 'Quiz', 'description' => 'Short quiz'],
        ['name' => 'Assignment', 'description' => 'Assignment evaluation']
    ];
    
    foreach ($examTypes as $type) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO exam_types (name, description, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
        $stmt->execute([$type['name'], $type['description']]);
    }
    echo "✅ Exam types inserted\n";
    
    // Insert book categories
    $categories = [
        ['name' => 'Textbooks', 'description' => 'Educational textbooks'],
        ['name' => 'Reference', 'description' => 'Reference books'],
        ['name' => 'Fiction', 'description' => 'Fiction books'],
        ['name' => 'Non-Fiction', 'description' => 'Non-fiction books']
    ];
    
    foreach ($categories as $cat) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO book_categories (name, description, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
        $stmt->execute([$cat['name'], $cat['description']]);
    }
    echo "✅ Book categories inserted\n";
    
    // Insert basic settings
    $settings = [
        ['key' => 'school_name', 'value' => 'Liberia School Management System', 'description' => 'School name'],
        ['key' => 'school_address', 'value' => 'Monrovia, Liberia', 'description' => 'School address'],
        ['key' => 'school_phone', 'value' => '+231-XXX-XXXX', 'description' => 'School phone number'],
        ['key' => 'school_email', 'value' => 'info@school.edu.lr', 'description' => 'School email'],
        ['key' => 'timezone', 'value' => 'Africa/Monrovia', 'description' => 'School timezone'],
        ['key' => 'date_format', 'value' => 'Y-m-d', 'description' => 'Date format'],
        ['key' => 'currency', 'value' => 'LRD', 'description' => 'Currency code']
    ];
    
    foreach ($settings as $setting) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO settings (`key`, `value`, description, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->execute([$setting['key'], $setting['value'], $setting['description']]);
    }
    echo "✅ Settings inserted\n";
    
    echo "\n=== All Missing Tables Created Successfully ===\n";
    echo "Your school management system now has all required tables!\n";
    echo "Access your system at: http://127.0.0.1:8000\n";
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
