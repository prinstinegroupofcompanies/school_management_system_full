<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Force Migration Runner ===\n\n";

try {
    // First, let's check what's in the database
    echo "1. Checking current database state...\n";
    
    $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
    echo "Existing tables: " . count($tables) . "\n";
    foreach ($tables as $table) {
        echo "   - " . $table->tablename . "\n";
    }
    echo "\n";
    
    // Drop all tables and recreate
    echo "2. Dropping all tables...\n";
    try {
        DB::statement('DROP SCHEMA public CASCADE');
        DB::statement('CREATE SCHEMA public');
        DB::statement('GRANT ALL ON SCHEMA public TO postgres');
        DB::statement('GRANT ALL ON SCHEMA public TO public');
        echo "✅ All tables dropped\n\n";
    } catch (Exception $e) {
        echo "⚠️  Schema drop error (continuing): " . $e->getMessage() . "\n\n";
    }
    
    // Run migrations
    echo "3. Running fresh migrations...\n";
    $exitCode = Artisan::call('migrate', ['--force' => true]);
    
    if ($exitCode === 0) {
        echo "✅ Migrations completed successfully\n\n";
        
        // Check what tables were created
        echo "4. Verifying created tables...\n";
        $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
        echo "Created tables: " . count($tables) . "\n";
        foreach ($tables as $table) {
            echo "   - " . $table->tablename . "\n";
        }
        echo "\n";
        
        // Run seeders with better error handling
        echo "5. Running seeders...\n";
        try {
            $exitCode = Artisan::call('db:seed', ['--force' => true]);
            
            if ($exitCode === 0) {
                echo "✅ Seeders completed successfully\n\n";
            } else {
                echo "❌ Seeders failed, trying individual seeders...\n";
                
                // Try individual seeders
                $seeders = ['ProductionSeeder'];
                foreach ($seeders as $seeder) {
                    try {
                        $exitCode = Artisan::call('db:seed', ['--class' => $seeder, '--force' => true]);
                        if ($exitCode === 0) {
                            echo "✅ $seeder completed successfully\n";
                        } else {
                            echo "❌ $seeder failed\n";
                        }
                    } catch (Exception $e) {
                        echo "❌ $seeder error: " . $e->getMessage() . "\n";
                    }
                }
            }
        } catch (Exception $e) {
            echo "❌ Seeding error: " . $e->getMessage() . "\n";
        }
        
    } else {
        echo "❌ Migrations failed\n";
    }
    
    echo "\n=== Process Complete ===\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
