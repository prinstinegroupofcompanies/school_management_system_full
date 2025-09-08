<?php
/**
 * Reset Database Script
 * This will drop and recreate the school_management database
 */

echo "=== Resetting Database ===\n\n";

$host = '127.0.0.1';
$port = 3306;
$username = 'root';
$password = 'Bryant2025@';
$database = 'school_management';

try {
    // Connect without specifying database
    $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to MySQL server\n";
    
    // Drop database if exists
    echo "Dropping database '$database'...\n";
    $pdo->exec("DROP DATABASE IF EXISTS `$database`");
    echo "✅ Database dropped\n";
    
    // Create database
    echo "Creating database '$database'...\n";
    $pdo->exec("CREATE DATABASE `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Database created\n";
    
    echo "\n=== Database Reset Complete ===\n";
    echo "You can now run: php artisan migrate\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
