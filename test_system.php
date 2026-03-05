<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Document;
use App\Models\Member;

echo "=== SYSTEM DEMO ===" . PHP_EOL;
echo "Documents in system: " . Document::count() . PHP_EOL;
echo "Members in system: " . Member::count() . PHP_EOL;

// Create a test document if none exists
if (Document::count() == 0) {
    $doc = Document::create([
        'title' => 'Test Loan Form',
        'description' => 'Test loan application form',
        'filename' => 'test.pdf',
        'original_filename' => 'test.pdf',
        'file_path' => 'documents/test/test.pdf',
        'file_size' => 1024,
        'document_type' => 'loan_form',
        'requires_fine' => true,
        'fine_amount' => 5000,
        'uploaded_by' => 1,
        'is_active' => true
    ]);
    echo "Created test document: " . $doc->title . PHP_EOL;
} else {
    $doc = Document::first();
    echo "Found existing document: " . $doc->title . PHP_EOL;
}

echo "Document ID: " . $doc->id . PHP_EOL;
echo "Document Type: " . $doc->document_type . PHP_EOL;
echo "Fine Required: " . ($doc->requires_fine ? 'YES (' . $doc->fine_amount . ' UGX)' : 'NO') . PHP_EOL;
echo "=== DEMO COMPLETE ===" . PHP_EOL;
