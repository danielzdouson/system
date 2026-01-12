<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Fine;
use App\Models\Member;

echo "Checking fines with missing member relationships...\n\n";

// Get fines that have member_id but the member doesn't exist
$finesWithMissingMembers = Fine::whereNotIn('member_id', function($query) {
    $query->select('id')->from('members');
})->get();

if ($finesWithMissingMembers->count() > 0) {
    echo "Found {$finesWithMissingMembers->count()} fines with missing members:\n";
    
    foreach ($finesWithMissingMembers as $fine) {
        echo "Fine ID: {$fine->id}, Member ID: {$fine->member_id}, Amount: {$fine->amount}\n";
    }
    
    echo "\nDeleting fines with missing members...\n";
    Fine::whereNotIn('member_id', function($query) {
        $query->select('id')->from('members');
    })->delete();
    
    echo "Deleted {$finesWithMissingMembers->count()} fines with missing members.\n";
} else {
    echo "No fines with missing members found.\n";
}

echo "\nChecking fines with null member_id...\n";

// Get fines with null member_id
$finesWithNullMember = Fine::whereNull('member_id')->get();

if ($finesWithNullMember->count() > 0) {
    echo "Found {$finesWithNullMember->count()} fines with null member_id:\n";
    
    foreach ($finesWithNullMember as $fine) {
        echo "Fine ID: {$fine->id}, Amount: {$fine->amount}\n";
    }
    
    echo "\nDeleting fines with null member_id...\n";
    Fine::whereNull('member_id')->delete();
    
    echo "Deleted {$finesWithNullMember->count()} fines with null member_id.\n";
} else {
    echo "No fines with null member_id found.\n";
}

echo "\nCleanup complete!\n";
echo "Total fines now: " . Fine::count() . "\n";
