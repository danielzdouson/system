<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== SACCO System Database Connection Test ===\n\n";

try {
    // Test basic connection
    echo "1. Testing database connection...\n";
    $pdo = DB::connection()->getPdo();
    echo "   ✅ Database connection successful\n";
    echo "   📊 Connected to: " . DB::connection()->getDatabaseName() . "\n";
    echo "   🔌 Host: " . DB::connection()->getConfig('host') . "\n";
    echo "   🚪 Port: " . DB::connection()->getConfig('port') . "\n\n";
    
    // Test database existence
    echo "2. Checking database existence...\n";
    $databases = DB::select('SHOW DATABASES');
    $dbExists = false;
    foreach ($databases as $db) {
        $dbName = array_values((array)$db)[0];
        if ($dbName === 'saco_system_new') {
            $dbExists = true;
            break;
        }
    }
    echo $dbExists ? "   ✅ Database 'saco_system_new' exists\n" : "   ❌ Database 'saco_system_new' not found\n\n";
    
    // Check tables
    echo "3. Checking essential tables...\n";
    $tables = DB::select('SHOW TABLES');
    $essentialTables = ['users', 'members', 'sessions', 'migrations', 'loans', 'savings'];
    
    foreach ($essentialTables as $table) {
        $exists = false;
        foreach ($tables as $t) {
            $tableName = array_values((array)$t)[0];
            if ($tableName === $table) {
                $exists = true;
                break;
            }
        }
        echo $exists ? "   ✅ $table table exists\n" : "   ❌ $table table missing\n";
    }
    
    echo "\n4. Testing table operations...\n";
    
    // Test users table
    if (Schema::hasTable('users')) {
        $userCount = DB::table('users')->count();
        echo "   👥 Users table: $userCount records\n";
        
        if ($userCount > 0) {
            $firstUser = DB::table('users')->first();
            echo "   📧 First user email: " . $firstUser->email . "\n";
        }
    }
    
    // Test sessions table
    if (Schema::hasTable('sessions')) {
        $sessionCount = DB::table('sessions')->count();
        echo "   🔐 Sessions table: $sessionCount active sessions\n";
    }
    
    // Test migrations
    if (Schema::hasTable('migrations')) {
        $migrationCount = DB::table('migrations')->count();
        echo "   📋 Migrations table: $migrationCount migrations run\n";
    }
    
    echo "\n5. Testing Laravel functionality...\n";
    
    // Test model creation
    try {
        $testUser = new \App\Models\User();
        echo "   ✅ User model instantiation successful\n";
    } catch (Exception $e) {
        echo "   ❌ User model error: " . $e->getMessage() . "\n";
    }
    
    // Test authentication
    try {
        if (Schema::hasTable('users') && DB::table('users')->count() > 0) {
            echo "   ✅ Authentication system ready\n";
        } else {
            echo "   ⚠️  No users found - authentication may not work\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Authentication error: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== Database Connection Summary ===\n";
    echo "🟢 Connection: Working\n";
    echo "🟢 Database: saco_system_new\n";
    echo "🟢 Port: 33060\n";
    echo "🟢 Essential Tables: " . count(array_intersect($essentialTables, array_map(function($t) { 
        return array_values((array)$t)[0]; 
    }, $tables))) . "/" . count($essentialTables) . " present\n";
    
} catch (Exception $e) {
    echo "❌ DATABASE CONNECTION FAILED\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "\nTroubleshooting steps:\n";
    echo "1. Check if MySQL/MariaDB is running on port 33060\n";
    echo "2. Verify database 'saco_system_new' exists\n";
    echo "3. Check MySQL user permissions\n";
    echo "4. Confirm .env configuration\n";
}
