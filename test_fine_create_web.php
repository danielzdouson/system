<?php

// Simulate a web request to test the fines create page
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create a mock request
$request = \Illuminate\Http\Request::create('/admin/fines/create', 'GET');

// Bootstrap the application for web
$app->instance('request', $request);

// Start session to simulate web environment
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // Get the controller
    $controller = new \App\Http\Controllers\Admin\FineController();
    
    // Call the create method
    $response = $controller->create();
    
    echo "✅ Controller executed successfully\n";
    echo "Response type: " . get_class($response) . "\n";
    
    if ($response instanceof \Illuminate\View\View) {
        echo "View name: " . $response->name() . "\n";
        echo "Data keys: " . implode(', ', array_keys($response->getData())) . "\n";
        
        // Try to render the view
        $rendered = $response->render();
        echo "✅ View rendered successfully, length: " . strlen($rendered) . "\n";
        
        // Check for form elements
        if (strpos($rendered, '<form method="POST"') !== false) {
            echo "✅ Form found in rendered view\n";
        } else {
            echo "❌ Form NOT found in rendered view\n";
        }
        
        // Check for member select
        if (strpos($rendered, 'name="member_id"') !== false) {
            echo "✅ Member select found\n";
        } else {
            echo "❌ Member select NOT found\n";
        }
        
        // Check for fiscal year select
        if (strpos($rendered, 'name="fiscal_year_id"') !== false) {
            echo "✅ Fiscal year select found\n";
        } else {
            echo "❌ Fiscal year select NOT found\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
