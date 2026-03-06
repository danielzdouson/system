<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\UploadedForm;
use App\Models\Document;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Uploaded Forms ===\n\n";

// Check if uploaded forms table exists and has data
try {
    $count = UploadedForm::count();
    echo "Total Uploaded Forms: " . $count . "\n\n";
    
    if ($count > 0) {
        $forms = UploadedForm::with(['member', 'document'])->take(5)->get();
        
        echo "Recent Uploaded Forms:\n";
        echo "ID\tMember\t\tLoan Amount\tStatus\tDocument\n";
        echo "--------------------------------------------------------\n";
        
        foreach ($forms as $form) {
            $memberName = $form->member ? ($form->member->first_name . ' ' . $form->member->last_name) : 'N/A';
            $docTitle = $form->document ? $form->document->title : 'N/A';
            
            printf("%d\t%-15s\t%s\t%s\t%s\n", 
                $form->id, 
                substr($memberName, 0, 15), 
                number_format($form->loan_amount, 0), 
                $form->status, 
                substr($docTitle, 0, 20)
            );
        }
    } else {
        echo "No uploaded forms found in database.\n";
        
        // Check if documents exist for loan forms
        $loanDocs = Document::where('document_type', 'loan_form')->count();
        echo "Available Loan Documents: " . $loanDocs . "\n";
        
        if ($loanDocs > 0) {
            echo "Loan documents exist but no forms uploaded yet.\n";
        } else {
            echo "No loan documents available for upload.\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Testing Controller Method ===\n";

// Test the controller method logic
try {
    $uploadedForms = UploadedForm::with(['member', 'document', 'guarantors.guarantor'])
        ->orderBy('created_at', 'desc')
        ->paginate(15);
    
    echo "Controller query executed successfully.\n";
    echo "Forms found: " . $uploadedForms->count() . "\n";
    
} catch (Exception $e) {
    echo "Controller query error: " . $e->getMessage() . "\n";
}
