<?php

echo "=== Setting up test data for uploaded forms ===\n\n";

// Step 1: Check if Docker is running
echo "1. Checking Docker status...\n";
$dockerCheck = shell_exec('docker ps 2>/dev/null');
if (strpos($dockerCheck, 'CONTAINER ID') === false) {
    echo "❌ Docker is not running. Please start Docker first.\n";
    echo "   Run: docker-compose up -d\n";
    exit(1);
} else {
    echo "✅ Docker is running\n";
}

// Step 2: Check if database container is running
echo "\n2. Checking database container...\n";
$dbCheck = shell_exec('docker ps | grep saco_system_db');
if (empty($dbCheck)) {
    echo "❌ Database container not running. Starting it...\n";
    shell_exec('docker-compose up -d db');
    sleep(5); // Wait for database to start
    echo "✅ Database container started\n";
} else {
    echo "✅ Database container is running\n";
}

// Step 3: Try to run the seeder
echo "\n3. Running test data seeder...\n";
$seederOutput = shell_exec('php artisan db:seed --class=TestUploadedFormsSeeder 2>&1');
echo $seederOutput;

if (strpos($seederOutput, 'Test uploaded forms created successfully') !== false) {
    echo "\n✅ Test data created successfully!\n";
} else {
    echo "\n❌ Failed to create test data. Check the errors above.\n";
}

echo "\n=== Next Steps ===\n";
echo "1. Make sure your Laravel application is running\n";
echo "2. Login as admin\n";
echo "3. Navigate to: Documents ▼ → Uploaded Forms\n";
echo "4. You should see 4 test loan applications with different statuses\n\n";

echo "Test Applications Created:\n";
echo "- Ready for Review (UGX 500,000)\n";
echo "- Pending Guarantors (UGX 750,000)\n";
echo "- Approved (UGX 1,200,000)\n";
echo "- Rejected (UGX 300,000)\n\n";

echo "URL: http://localhost:8080/admin/documents/uploaded-forms\n";
