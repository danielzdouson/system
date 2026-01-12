<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Member;

echo "Deleting all existing members...\n";
$count = Member::count();
Member::query()->delete();
echo "Deleted {$count} existing members.\n\n";

echo "Adding members from Excel data...\n";

$members = [
    ['first_name' => 'NATUKUNDA', 'last_name' => 'ANN', 'phone' => '0755465682'],
    ['first_name' => 'MWESIGYE', 'last_name' => 'JONAH', 'phone' => '0702422010'],
    ['first_name' => 'CHRISTOPHER', 'last_name' => 'MWESIGE', 'phone' => '0703783951'],
    ['first_name' => 'KIMULI', 'last_name' => 'BASIL', 'phone' => '0708586112'],
    ['first_name' => 'SERUNJOJI', 'last_name' => 'JOHN K', 'phone' => '0706505274'],
    ['first_name' => 'NAKIDDE', 'last_name' => 'SUZAN MAVIS', 'phone' => '0772479805'],
    ['first_name' => 'NASAAZI', 'last_name' => 'SHARON', 'phone' => '0702043496'],
    ['first_name' => 'BASIRIKA', 'last_name' => 'AIDAH', 'phone' => '07'], // Incomplete - will skip
    ['first_name' => 'KUKKIRIZA', 'last_name' => 'EMMANUEL', 'phone' => '0706585569'],
    ['first_name' => 'NASASIRA', 'last_name' => 'DAVID', 'phone' => '0708516759'],
    ['first_name' => 'JONAH', 'last_name' => 'MUHUMUZA', 'phone' => '+971 54 754 9'], // Incomplete - will skip
    ['first_name' => 'SSEMWOGERERE', 'last_name' => 'DOUGLAS', 'phone' => '0752520684'],
    ['first_name' => 'MAGALA', 'last_name' => 'MARVIN', 'phone' => '0754129620'],
    ['first_name' => 'BABIRYE', 'last_name' => 'CLAIRE', 'phone' => 'minor'], // Invalid - will skip
    ['first_name' => 'NANCY', 'last_name' => 'KAZIBWE', 'phone' => '0701641908'],
    ['first_name' => 'SSENYUNGULE', 'last_name' => 'SHARIF', 'phone' => '0753633015'],
    ['first_name' => 'MUHIIRWE', 'last_name' => 'MADIINAH', 'phone' => '0752542626'],
    ['first_name' => 'ROSE', 'last_name' => 'NAKALEMA', 'phone' => '0705279439'],
    ['first_name' => 'NAMAKULA', 'last_name' => 'BRIDGET', 'phone' => '0708129611'],
    ['first_name' => 'BUZZI', 'last_name' => 'WC', 'phone' => '0704825796'],
    ['first_name' => 'KAWALYA', 'last_name' => 'BRIAN', 'phone' => '0758556700'],
    ['first_name' => 'GILLIAN', 'last_name' => '.A', 'phone' => '0779574518'],
    ['first_name' => 'MWESIGWA', 'last_name' => 'ELIPHAZI', 'phone' => '0701964272'],
    ['first_name' => 'MUKIIBI', 'last_name' => 'MIIKE', 'phone' => '0751111035'],
    ['first_name' => 'KAMOGA', 'last_name' => 'MAHAD', 'phone' => '0757747242'],
    ['first_name' => 'NKONO', 'last_name' => 'MICHAEL', 'phone' => '0755872400'],
    ['first_name' => 'EVE', 'last_name' => 'KAMPIIRE', 'phone' => '0788266218'],
    ['first_name' => 'LILLIAN', 'last_name' => 'NAMAGANDA', 'phone' => '0703911285'],
    ['first_name' => 'NAMAYEGA', 'last_name' => 'ANNET', 'phone' => '0755255925'],
    ['first_name' => 'NALULE', 'last_name' => 'PROSSY', 'phone' => '0775953678'],
];

$importedCount = 0;
$skippedCount = 0;

foreach ($members as $memberData) {
    // Skip invalid phone numbers
    if (in_array($memberData['phone'], ['07', 'minor', '+971 54 754 9']) || 
        strlen($memberData['phone']) < 10) {
        echo "Skipping: " . $memberData['first_name'] . " " . $memberData['last_name'] . " (Invalid phone: " . $memberData['phone'] . ")\n";
        $skippedCount++;
        continue;
    }

    Member::create($memberData);
    echo "Added: " . $memberData['first_name'] . " " . $memberData['last_name'] . "\n";
    $importedCount++;
}

echo "\nComplete!\n";
echo "✅ Imported: {$importedCount} members\n";
echo "⏭️ Skipped: {$skippedCount} members (invalid phone numbers)\n";
echo "📊 Total members now: " . Member::count() . "\n";
