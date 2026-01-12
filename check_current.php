<?php

use App\Models\Member;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

echo "Current members in database:\n";
$currentMembers = Member::select('id', 'first_name', 'last_name', 'national_id')->get();
foreach ($currentMembers as $member) {
    echo $member->id . ': ' . $member->first_name . ' ' . $member->last_name . ' (' . $member->national_id . ')' . PHP_EOL;
}

echo "\nTotal: " . $currentMembers->count() . PHP_EOL;
