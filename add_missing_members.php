<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Member;

echo "Adding the 3 missing members...\n\n";

$missingMembers = [
    [
        'first_name' => 'BABIRYE',
        'last_name' => 'CLAIRE',
        'phone' => null // No phone number
    ],
    [
        'first_name' => 'JONAH',
        'last_name' => 'MUHUMUZA',
        'phone' => '+971 54 754 9' // Dubai number as is
    ],
    [
        'first_name' => 'BASIRIKA',
        'last_name' => 'AIDAH',
        'phone' => '0700000000' // Placeholder number
    ]
];

foreach ($missingMembers as $memberData) {
    $member = Member::create($memberData);
    echo "Added: " . $memberData['first_name'] . " " . $memberData['last_name'] . 
         " (Phone: " . ($memberData['phone'] ?? 'NULL') . ")\n";
}

echo "\n✅ Added 3 missing members!\n";
echo "📊 Total members now: " . Member::count() . "\n";
