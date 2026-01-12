<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Member;

echo "Checking for members with incomplete data...\n\n";

$members = Member::all();
$toDelete = [];
$toReview = [];

foreach ($members as $member) {
    $issues = [];
    
    // Check phone number
    if (empty($member->phone)) {
        $issues[] = "No phone number";
    } elseif (strlen($member->phone) < 10) {
        $issues[] = "Phone too short: " . $member->phone;
    } elseif (!preg_match('/^[0-9+\s]+$/', $member->phone)) {
        $issues[] = "Invalid phone format: " . $member->phone;
    }
    
    // Check name
    if (empty($member->first_name)) {
        $issues[] = "No first name";
    }
    
    if (empty($member->last_name)) {
        $issues[] = "No last name";
    }
    
    if (!empty($issues)) {
        echo "Member ID " . $member->id . ": " . $member->first_name . " " . $member->last_name . " - " . $member->phone . "\n";
        echo "  Issues: " . implode(", ", $issues) . "\n\n";
        
        // Mark for deletion if phone is clearly invalid
        if (in_array($member->phone, ['07', 'minor', '+971 54 754 9', '']) || 
            strlen($member->phone) < 7) {
            $toDelete[] = $member;
        } else {
            $toReview[] = $member;
        }
    }
}

if (!empty($toDelete)) {
    echo "Deleting " . count($toDelete) . " members with clearly invalid data:\n";
    foreach ($toDelete as $member) {
        echo "  Deleting: " . $member->first_name . " " . $member->last_name . " (Phone: " . $member->phone . ")\n";
        $member->delete();
    }
}

if (!empty($toReview)) {
    echo "\n" . count($toReview) . " members need manual review:\n";
    foreach ($toReview as $member) {
        echo "  Review: " . $member->first_name . " " . $member->last_name . " (Phone: " . $member->phone . ")\n";
    }
}

echo "\nCleanup complete!\n";
echo "Total members now: " . Member::count() . "\n";
