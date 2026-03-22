<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UploadedForm;
use App\Models\Member;
use App\Models\Document;
use App\Models\User;
use App\Models\LoanGuarantor;
use Illuminate\Support\Facades\DB;

class TestUploadedFormsSeeder extends Seeder
{
    public function run(): void
    {
        echo "Creating test uploaded forms...\n";

        // Clear existing data
        UploadedForm::query()->delete();
        LoanGuarantor::query()->delete();

        // Get or create test data
        $member = Member::first();
        if (!$member) {
            echo "No members found. Please run members seeder first.\n";
            return;
        }

        $adminUser = User::first();
        if (!$adminUser) {
            echo "No admin users found. Creating one...\n";
            $adminUser = User::create([
                'name' => 'Admin User',
                'email' => 'admin@test.com',
                'password' => bcrypt('password'),
            ]);
        }

        $document = Document::where('document_type', 'loan_form')->first();
        if (!$document) {
            echo "No loan documents found. Creating one...\n";
            $document = Document::create([
                'title' => 'Loan Application Form',
                'description' => 'Standard loan application form',
                'filename' => 'loan_form.pdf',
                'original_filename' => 'Loan_Application_Form.pdf',
                'file_path' => 'documents/test/loan_form.pdf',
                'file_size' => 12345,
                'document_type' => 'loan_form',
                'requires_fine' => true,
                'fine_amount' => 5000,
                'is_active' => true,
                'uploaded_by' => 1,
            ]);
        }

        // Create test uploaded forms with different statuses
        $testForms = [
            [
                'member_id' => $member->id,
                'document_id' => $document->id,
                'filename' => 'john_doe_loan_app.pdf',
                'file_path' => 'documents/uploaded-forms/test/john_doe_loan_app.pdf',
                'loan_amount' => 500000,
                'guarantors_required' => 1,
                'status' => 'ready_for_review',
                'created_at' => now()->subDays(2),
            ],
            [
                'member_id' => $member->id,
                'document_id' => $document->id,
                'filename' => 'jane_smith_loan_app.pdf',
                'file_path' => 'documents/uploaded-forms/test/jane_smith_loan_app.pdf',
                'loan_amount' => 750000,
                'guarantors_required' => 2,
                'status' => 'pending_guarantors',
                'created_at' => now()->subDays(1),
            ],
            [
                'member_id' => $member->id,
                'document_id' => $document->id,
                'filename' => 'bob_wilson_loan_app.pdf',
                'file_path' => 'documents/uploaded-forms/test/bob_wilson_loan_app.pdf',
                'loan_amount' => 1200000,
                'guarantors_required' => 3,
                'status' => 'approved',
                'admin_notes' => 'Approved after review. Good repayment history.',
                'reviewed_by' => $adminUser->id,
                'reviewed_at' => now()->subHours(3),
                'created_at' => now()->subDays(3),
            ],
            [
                'member_id' => $member->id,
                'document_id' => $document->id,
                'filename' => 'alice_brown_loan_app.pdf',
                'file_path' => 'documents/uploaded-forms/test/alice_brown_loan_app.pdf',
                'loan_amount' => 300000,
                'guarantors_required' => 1,
                'status' => 'rejected',
                'admin_notes' => 'Rejected: Insufficient savings balance. Current balance too low.',
                'reviewed_by' => $adminUser->id,
                'reviewed_at' => now()->subHours(6),
                'created_at' => now()->subDays(4),
            ],
        ];

        foreach ($testForms as $form) {
            $uploadedForm = UploadedForm::create($form);
            echo "Created uploaded form: {$form['filename']} - {$form['status']}\n";

            // Add some guarantors for testing
            if ($form['status'] === 'ready_for_review' || $form['status'] === 'approved') {
                $this->addTestGuarantors($uploadedForm, $form['guarantors_required']);
            }
        }

        echo "Test uploaded forms created successfully!\n";
        echo "You can now visit: /admin/documents/uploaded-forms\n";
    }

    private function addTestGuarantors(UploadedForm $form, int $count): void
    {
        // Create some test guarantors (using different members for uniqueness)
        $members = Member::take($count + 1)->get(); // Get enough members
        
        for ($i = 0; $i < $count; $i++) {
            // Use a different member for each guarantor to avoid unique constraint
            $guarantorMemberId = $members->get($i % $members->count())->id;
            
            // Calculate percentage to distribute evenly
            $percentage = $i === $count - 1 ? 100 : 50; // Last guarantor covers remaining
            
            LoanGuarantor::create([
                'uploaded_form_id' => $form->id,
                'guarantor_member_id' => $guarantorMemberId,
                'guarantee_percentage' => $percentage,
                'guaranteed_amount' => ($form->loan_amount * $percentage) / 100,
                'guarantee_status' => 'confirmed',
                'guarantee_confirmation' => "I confirm I will guarantee this loan of UGX " . number_format((float)$form->loan_amount, 2) . " as guarantor #" . ($i + 1) . ".",
                'guaranteed_at' => now()->subHours($i + 1),
                'ip_address' => '127.0.0.1',
            ]);
            
            echo "  Added guarantor " . ($i + 1) . ": {$percentage}%\n";
        }
    }
}
