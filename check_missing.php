<?php

use App\Models\Member;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

echo "Checking missing members from Excel...\n";

// Expected 30 members from Excel
$expectedMembers = [
    'NATUKUNDA ANN',
    'MWESIGYE JONAH',
    'CHRISTOPHER MWESIGYE',
    'KIMULI BASIL',
    'SERUNJOJI JOHN K',
    'NAKIDDE SUZAN MAVIS',
    'NASAAZI SHARON',
    'BASIRIKA AIDAH',
    'KUKKIRIZA EMMANUEL',
    'NASASIRA DAVID',
    'JONAH MUHUMUZA',
    'SSEMWOGERERE DOUGLAS',
    'MAGALA MARVIN',
    'BABIRYE CLAIRE',
    'NANCY KAZIBWE',
    'SSENYUNGULE SHARIF',
    'MUHIIRWE MADIINAH',
    'ROSE NAKALEMA',
    'BUZZI WC',
    'KAWALYA BRIAN',
    'GILLIAN A',
    'MWESIGWA ELIPHAZI',
    'MUKIIBI MIIKE',
    'KAMOGA MAHAD',
    'NKONO MICHAEL',
    'EVE KAMPIIRE',
    'LILLIAN NAMAGANDA',
    'NAMAYEGA ANNET'
];

$currentMembers = Member::pluck('first_name')->toArray();

echo "Expected: " . count($expectedMembers) . " members\n";
echo "Current: " . count($currentMembers) . " members\n";

$missing = array_diff($expectedMembers, $currentMembers);
$extra = array_diff($currentMembers, $expectedMembers);

if (!empty($missing)) {
    echo "\nMissing members:\n";
    foreach ($missing as $name) {
        echo "- $name\n";
    }
}

if (!empty($extra)) {
    echo "\nExtra members in database:\n";
    foreach ($extra as $name) {
        echo "- $name\n";
    }
}
