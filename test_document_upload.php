<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Document Upload Test ===\n\n";

// Test 1: Check if user is authenticated
echo "1. Authentication Status:\n";
$user = auth()->user();
if ($user) {
    echo "   ✓ User logged in: " . $user->name . "\n";
    echo "   ✓ User ID: " . $user->id . "\n";
    echo "   ✓ User Role: " . $user->role . "\n";
} else {
    echo "   ✗ No user logged in\n";
    echo "   → Please login first at: http://localhost:8080/login\n";
}

// Test 2: Check storage directory
echo "\n2. Storage Directory:\n";
$storagePath = storage_path('app/documents');
if (is_dir($storagePath)) {
    echo "   ✓ Documents directory exists: " . $storagePath . "\n";
    if (is_writable($storagePath)) {
        echo "   ✓ Directory is writable\n";
    } else {
        echo "   ✗ Directory is not writable\n";
    }
} else {
    echo "   ✗ Documents directory does not exist\n";
    echo "   → Creating directory...\n";
    if (mkdir($storagePath, 0755, true)) {
        echo "   ✓ Directory created successfully\n";
    } else {
        echo "   ✗ Failed to create directory\n";
    }
}

// Test 3: Check Document model
echo "\n3. Document Model:\n";
try {
    $documentCount = \App\Models\Document::count();
    echo "   ✓ Document model accessible\n";
    echo "   ✓ Total documents: " . $documentCount . "\n";
} catch (\Exception $e) {
    echo "   ✗ Document model error: " . $e->getMessage() . "\n";
}

// Test 4: Check routes
echo "\n4. Routes Check:\n";
try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $documentRoutes = [];
    foreach ($routes as $route) {
        if (str_contains($route->getName(), 'admin.documents')) {
            $documentRoutes[] = $route->getName() . ' - ' . $route->uri();
        }
    }
    echo "   ✓ Found " . count($documentRoutes) . " document routes:\n";
    foreach ($documentRoutes as $route) {
        echo "     - " . $route . "\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Route check error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
echo "\nNext steps:\n";
echo "1. Make sure you're logged in as admin\n";
echo "2. Visit: http://localhost:8080/admin/documents\n";
echo "3. Click 'Upload Document' button\n";
echo "4. Fill in the form and try uploading\n";
echo "5. If still not working, check Laravel logs at: storage/logs/laravel.log\n";
