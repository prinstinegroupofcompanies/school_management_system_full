<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Database Migration Status Check ===\n\n";

try {
    // Check database connection
    echo "1. Testing database connection...\n";
    DB::connection()->getPdo();
    echo "✅ Database connection successful\n\n";
    
    // Check if migrations table exists
    echo "2. Checking migrations table...\n";
    if (Schema::hasTable('migrations')) {
        echo "✅ Migrations table exists\n";
        
        // Get migration count
        $migrationCount = DB::table('migrations')->count();
        echo "📊 Total migrations run: $migrationCount\n\n";
        
        // List recent migrations
        echo "3. Recent migrations:\n";
        $recentMigrations = DB::table('migrations')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();
            
        foreach ($recentMigrations as $migration) {
            echo "   - {$migration->migration}\n";
        }
        echo "\n";
        
    } else {
        echo "❌ Migrations table does not exist\n\n";
    }
    
    // Check key tables
    echo "4. Checking key tables...\n";
    $tables = ['users', 'students', 'teachers', 'class_rooms', 'subjects', 'scholarships'];
    
    foreach ($tables as $table) {
        if (Schema::hasTable($table)) {
            $count = DB::table($table)->count();
            echo "✅ $table exists ($count records)\n";
        } else {
            echo "❌ $table does not exist\n";
        }
    }
    
    echo "\n=== Check Complete ===\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
