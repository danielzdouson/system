<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Member;

echo "Checking for duplicate members...\n\n";

// Get all members ordered by phone
$members = Member::orderBy('phone')->get();

$duplicates = [];
$seen = [];

foreach ($members as $member) {
    $phone = $member->phone;
    
    if (isset($seen[$phone])) {
        $duplicates[] = [
            'original' => $seen[$phone],
            'duplicate' => $member
        ];
    } else {
        $seen[$phone] = $member;
    }
}

if (empty($duplicates)) {
    echo "No duplicates found!\n";
} else {
    echo "Found " . count($duplicates) . " duplicate entries:\n\n";
    
    foreach ($duplicates as $dup) {
        echo "DUPLICATE PHONE: " . $dup['duplicate']->phone . "\n";
        echo "  Original: " . $dup['original']->first_name . " " . $dup['original']->last_name . " (ID: " . $dup['original']->id . ")\n";
        echo "  Duplicate: " . $dup['duplicate']->first_name . " " . $dup['duplicate']->last_name . " (ID: " . $dup['duplicate']->id . ")\n\n";
    }
    
    echo "Removing duplicates...\n";
    
    foreach ($duplicates as $dup) {
        $dup['duplicate']->delete();
        echo "Deleted: " . $dup['duplicate']->first_name . " " . $dup['duplicate']->last_name . " (ID: " . $dup['duplicate']->id . ")\n";
    }
    
    echo "\nDuplicates removed successfully!\n";
}

echo "\nTotal members now: " . Member::count() . "\n";
