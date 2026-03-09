<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== USERS AND MEMBERS CHECK ===\n";

$users = App\Models\User::with('member')->get();

echo "\n--- ALL USERS ---\n";
foreach ($users as $user) {
    echo "ID: {$user->id}\n";
    echo "Name: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Role: {$user->role}\n";
    
    if ($user->member) {
        echo "Member: {$user->member->first_name} {$user->member->last_name}\n";
        echo "Member Email: {$user->member->email}\n";
    } else {
        echo "NO LINKED MEMBER\n";
    }
    echo "---\n";
}

echo "\n=== MEMBERS WITHOUT USERS ===\n";
$membersWithoutUsers = App\Models\Member::whereNull('user_id')->get();
foreach ($membersWithoutUsers as $member) {
    echo "Member ID: {$member->id}\n";
    echo "Name: {$member->first_name} {$member->last_name}\n";
    echo "Email: {$member->email}\n";
    echo "---\n";
}

echo "\n=== SEARCHING FOR SPECIFIC NAMES ===\n";
$targetNames = ['semwogerere douglas', 'tim ton', 'clone test'];

foreach ($targetNames as $targetName) {
    echo "\nSearching for: '$targetName'\n";
    
    // Search in users
    $userFound = App\Models\User::where('name', 'like', '%' . $targetName . '%')->first();
    if ($userFound) {
        echo "Found in Users table:\n";
        echo "  ID: {$userFound->id}, Name: {$userFound->name}, Role: {$userFound->role}\n";
        if ($userFound->member) {
            echo "  Linked Member: {$userFound->member->first_name} {$userFound->member->last_name}\n";
        }
    } else {
        echo "Not found in Users table\n";
    }
    
    // Search in members
    $memberFound = App\Models\Member::where(function($query) use ($targetName) {
        $query->where('first_name', 'like', '%' . $targetName . '%')
              ->orWhere('last_name', 'like', '%' . $targetName . '%')
              ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $targetName . '%']);
    })->first();
    
    if ($memberFound) {
        echo "Found in Members table:\n";
        echo "  ID: {$memberFound->id}, Name: {$memberFound->first_name} {$memberFound->last_name}\n";
        if ($memberFound->user) {
            echo "  Linked User: {$memberFound->user->name} (Role: {$memberFound->user->role})\n";
        } else {
            echo "  NO LINKED USER - This member cannot login!\n";
        }
    } else {
        echo "Not found in Members table\n";
    }
}
