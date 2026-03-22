<?php

// Simple test to check if the uploaded forms view works without database
echo "=== Testing Uploaded Forms Display ===\n\n";

// Test 1: Check if the uploaded-forms.blade.php view file exists and has content
$viewFile = __DIR__ . '/resources/views/admin/documents/uploaded-forms.blade.php';
if (file_exists($viewFile)) {
    echo "✓ uploaded-forms.blade.php exists\n";
    $content = file_get_contents($viewFile);
    if (strpos($content, '@forelse ($uploadedForms as $form)') !== false) {
        echo "✓ View has the loop to display uploaded forms\n";
    } else {
        echo "✗ View missing the uploaded forms loop\n";
    }
    
    if (strpos($content, 'route(\'admin.documents.review-form\'') !== false) {
        echo "✓ View has the review button\n";
    } else {
        echo "✗ View missing the review button\n";
    }
} else {
    echo "✗ uploaded-forms.blade.php missing\n";
}

// Test 2: Check if the navigation menu was updated correctly
$layoutFile = __DIR__ . '/resources/views/layouts/admin.blade.php';
if (file_exists($layoutFile)) {
    echo "\n✓ admin.blade.php exists\n";
    $content = file_get_contents($layoutFile);
    
    if (strpos($content, 'uploaded-forms') !== false) {
        echo "✓ Navigation includes uploaded-forms link\n";
    } else {
        echo "✗ Navigation missing uploaded-forms link\n";
    }
    
    if (strpos($content, 'documentsDropdown') !== false) {
        echo "✓ Documents dropdown menu exists\n";
    } else {
        echo "✗ Documents dropdown menu missing\n";
    }
} else {
    echo "\n✗ admin.blade.php missing\n";
}

// Test 3: Check if the review-form view exists
$reviewFile = __DIR__ . '/resources/views/admin/documents/review-form.blade.php';
if (file_exists($reviewFile)) {
    echo "\n✓ review-form.blade.php exists\n";
    $content = file_get_contents($reviewFile);
    
    if (strpos($content, 'approve-form') !== false && strpos($content, 'reject-form') !== false) {
        echo "✓ Review form has approve/reject buttons\n";
    } else {
        echo "✗ Review form missing approve/reject buttons\n";
    }
} else {
    echo "\n✗ review-form.blade.php missing\n";
}

// Test 4: Check if the guarantors view exists
$guarantorsFile = __DIR__ . '/resources/views/admin/documents/guarantors.blade.php';
if (file_exists($guarantorsFile)) {
    echo "✓ guarantors.blade.php exists\n";
} else {
    echo "✗ guarantors.blade.php missing\n";
}

// Test 5: Check if the UploadedForm model has the required methods
$modelFile = __DIR__ . '/app/Models/UploadedForm.php';
if (file_exists($modelFile)) {
    echo "\n✓ UploadedForm.php exists\n";
    $content = file_get_contents($modelFile);
    
    $requiredMethods = ['getGuaranteedPercentage', 'getGuaranteeProgressColor', 'getStatusLabel', 'getStatusColor'];
    foreach ($requiredMethods as $method) {
        if (strpos($content, 'function ' . $method) !== false) {
            echo "✓ Model has $method method\n";
        } else {
            echo "✗ Model missing $method method\n";
        }
    }
} else {
    echo "\n✗ UploadedForm.php missing\n";
}

// Test 6: Check if routes exist
$routesFile = __DIR__ . '/routes/web.php';
if (file_exists($routesFile)) {
    echo "\n✓ web.php routes file exists\n";
    $content = file_get_contents($routesFile);
    
    $requiredRoutes = ['uploaded-forms', 'review-form', 'approve-form', 'reject-form', 'view-guarantors'];
    foreach ($requiredRoutes as $route) {
        if (strpos($content, $route) !== false) {
            echo "✓ Route '$route' exists\n";
        } else {
            echo "✗ Route '$route' missing\n";
        }
    }
} else {
    echo "\n✗ web.php routes file missing\n";
}

echo "\n=== Summary ===\n";
echo "If all checks pass, the uploaded forms should display correctly.\n";
echo "The issue might be:\n";
echo "1. No uploaded forms in the database\n";
echo "2. Database connection issue (Docker not running)\n";
echo "3. Missing uploaded forms data\n\n";

echo "To test with data:\n";
echo "1. Start Docker: docker-compose up -d\n";
echo "2. Run migrations: php artisan migrate\n";
echo "3. Create test data: php artisan db:seed --class=TestUploadedFormsSeeder\n";
echo "4. Access: http://localhost:8080/admin/documents/uploaded-forms\n";
