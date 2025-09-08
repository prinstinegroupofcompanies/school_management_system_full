<?php
/**
 * Create Sessions Table Script
 * This will create the missing sessions table for Laravel
 */

echo "=== Creating Sessions Table ===\n\n";

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
    
    // Create sessions table
    $sessionsTable = "
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
    ";
    
    echo "Creating sessions table...\n";
    $pdo->exec($sessionsTable);
    echo "✅ Sessions table created successfully\n";
    
    echo "\n=== Sessions Table Created ===\n";
    echo "Your application should now work properly!\n";
    echo "Access your system at: http://127.0.0.1:8000\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
