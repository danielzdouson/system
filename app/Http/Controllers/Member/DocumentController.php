<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentDownload;
use App\Models\UploadedForm;
use App\Models\LoanGuarantor;
use App\Models\GuarantorNotification;
use App\Models\Fine;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function index(): View
    {
        $documents = Document::active()
            ->orderBy('document_type')
            ->orderBy('title')
            ->get();

        return view('member.documents.index', compact('documents'));
    }

    public function show(Document $document): View
    {
        try {
            $user = Auth::user();
            
            // TEMPORARILY SKIP DATABASE CALLS FOR TESTING
            // $member = $user->member;
            // $hasDownloaded = $document->downloads()
            //     ->where('member_id', $member->id)
            //     ->exists();

            // For testing, assume not downloaded
            $hasDownloaded = false;

            return view('member.documents.show', compact('document', 'hasDownloaded'));
            
        } catch (\Exception $e) {
            \Log::error('Error in show: ' . $e->getMessage());
            
            return redirect()->route('member.documents.index')
                ->with('error', 'Unable to load document details. Please try again later.');
        }
    }

    public function download(Request $request, Document $document): RedirectResponse|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        try {
            $user = Auth::user();
            
            // TEMPORARILY SKIP MEMBER CHECKS FOR TESTING
            // $member = $user->member;

            // For testing, create a dummy member ID
            $dummyMemberId = 999;

            // Check if already downloaded - but allow multiple downloads with fines
            $existingDownload = DocumentDownload::where('document_id', $document->id)
                ->where('member_id', $dummyMemberId)
                ->first();

            // For loan forms, always require fine and create new download record
            if ($document->document_type === 'loan_form') {
                // Create a proper Member object for testing
                $dummyMember = new \App\Models\Member();
                $dummyMember->id = 999;
                
                // Ensure we have a proper Member object
                if (!($dummyMember instanceof \App\Models\Member)) {
                    throw new \Exception('Failed to create Member object');
                }
                
                return $this->handleLoanFormDownload($request, $document, $dummyMember);
            }

            // For other documents, check if already downloaded
            if ($existingDownload) {
                return $this->performDownload($document, $existingDownload);
            }

            // Handle fine requirement for non-loan forms
            if ($document->requires_fine) {
                if ($request->has('confirm_fine') && $request->input('confirm_fine') == '1') {
                    // Create download record without upload window for non-loan forms
                    $download = DocumentDownload::create([
                        'document_id' => $document->id,
                        'member_id' => $dummyMemberId,
                        'downloaded_at' => now(),
                        'ip_address' => $request->ip(),
                        'used_for_upload' => false,
                        'download_purpose' => 'general',
                    ]);

                    // Apply fine
                    $fine = $download->applyFine();
                    
                    // Update download record with fine amount
                    $download->update(['fine_amount' => $fine->amount]);
                    
                    return redirect()
                        ->back()
                        ->with('success', 'Fine of UGX ' . number_format($fine->amount, 2) . ' has been applied. You can now download the document.')
                        ->with('download_ready', true);
                } else {
                    // Don't create download record yet - ask for fine confirmation first
                    return redirect()
                        ->back()
                        ->with('warning', 'This document requires a fine of UGX ' . number_format($document->fine_amount, 2) . '. Please confirm to proceed.')
                        ->with('show_fine_confirmation', true);
                }
            }

            // For documents without fines, create download record and proceed
            $download = DocumentDownload::create([
                'document_id' => $document->id,
                'member_id' => $dummyMemberId,
                'downloaded_at' => now(),
                'ip_address' => $request->ip(),
                'download_purpose' => 'general',
            ]);

            return $this->performDownload($document, $download);
                
        } catch (\Exception $e) {
            \Log::error('Error in download: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Unable to process download. Please try again later.');
        }
    }

    private function handleLoanFormDownload(Request $request, Document $document, \App\Models\Member $member): RedirectResponse|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        // Validate member parameter
        if (!($member instanceof \App\Models\Member)) {
            \Log::error('Invalid member parameter passed to handleLoanFormDownload', [
                'member_type' => gettype($member),
                'member_data' => is_object($member) ? get_class($member) : $member
            ]);
            throw new \InvalidArgumentException('Member parameter must be a Member model instance');
        }
        
        // Always require fine for loan forms
        if ($request->has('confirm_fine') && $request->input('confirm_fine') == '1') {
            // Create new download record with upload window for each download
            $download = DocumentDownload::create([
                'document_id' => $document->id,
                'member_id' => $member->id,
                'downloaded_at' => now(),
                'ip_address' => $request->ip(),
                'upload_window_expires_at' => now()->addDays(10), // 10-day upload window
                'used_for_upload' => false,
                'download_purpose' => 'loan_application',
            ]);

            // Apply fine
            $fine = $download->applyFine();
            
            // Update the download record with fine amount
            $download->update(['fine_amount' => $fine->amount]);
            
            return redirect()
                ->back()
                ->with('success', 'Fine of UGX ' . number_format($fine->amount, 2) . ' has been applied. You can now download the loan form. You have 10 days to upload the completed form.')
                ->with('download_ready', true);
        } else {
            // Ask for fine confirmation
            return redirect()
                ->back()
                ->with('warning', 'This loan form requires a fine of UGX ' . number_format($document->fine_amount, 2) . '. Please confirm to proceed.')
                ->with('show_fine_confirmation', true);
        }
    }

    private function performDownload(Document $document, DocumentDownload $download): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('local')->download($document->file_path, $document->original_filename);
    }

    public function uploadForm(Document $document): View
    {
        if ($document->document_type !== 'loan_form') {
            abort(404, 'Only loan forms can be uploaded');
        }

        $member = Auth::user()->member;
        
        return view('member.documents.upload-form', compact('document', 'member'));
    }

    public function storeUpload(Request $request, Document $document): RedirectResponse
    {
        if ($document->document_type !== 'loan_form') {
            abort(404, 'Only loan forms can be uploaded');
        }

        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240', // 10MB max
            'loan_amount' => 'required|numeric|min:0',
        ]);

        $member = Auth::user()->member;

        // Check if member has a valid download record for this document
        $validDownload = DocumentDownload::getValidDownloadForUpload($member->id, $document->id);
        
        if (!$validDownload) {
            return redirect()
                ->route('member.documents.show', $document)
                ->with('error', 'You must download a fresh loan form first. Your previous download window has expired or has already been used.');
        }

        $file = $request->file('file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        
        // Store file in organized directory structure
        $filePath = $file->storeAs('documents/uploaded-forms/' . date('Y/m'), $filename, 'local');

        $uploadedForm = UploadedForm::create([
            'member_id' => $member->id,
            'document_id' => $document->id,
            'filename' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'loan_amount' => $request->loan_amount,
            'guarantors_required' => 0, // Will be calculated
            'status' => 'pending_guarantors',
            'document_download_id' => $validDownload->id, // Link to the download record
            'form_download_date' => $validDownload->downloaded_at,
        ]);

        // Mark the download record as used
        $validDownload->markAsUsedForUpload();

        // Calculate required guarantors based on loan amount
        $guarantorsRequired = $uploadedForm->calculateGuarantorsRequired();
        $uploadedForm->update(['guarantors_required' => $guarantorsRequired]);

        // Send notification to all members if guarantors are required
        if ($guarantorsRequired > 0) {
            GuarantorNotification::sendToAllMembers($uploadedForm);
        } else {
            $uploadedForm->update(['status' => 'ready_for_review']);
        }

        return redirect()
            ->route('member.documents.my-uploads')
            ->with('success', 'Loan form uploaded successfully! ' . 
                ($guarantorsRequired > 0 ? "It requires {$guarantorsRequired} guarantor(s)." : 'It is ready for admin review.'));
    }

    public function myUploads(): View
    {
        $member = Auth::user()->member;
        $uploadedForms = UploadedForm::where('member_id', $member->id)
            ->with(['document', 'guarantors.guarantor'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('member.documents.my-uploads', compact('uploadedForms'));
    }

    public function pendingGuarantees(): View
    {
        try {
            // Debug: Check authentication first
            if (!Auth::check()) {
                \Log::error('User not authenticated in pendingGuarantees');
                return redirect()->route('login');
            }
            
            $user = Auth::user();
            \Log::info('Authenticated user: ' . $user->name . ' (ID: ' . $user->id . ')');
            
            // TEMPORARILY SKIP DATABASE CALLS FOR TESTING
            // $member = $user->member;
            // if (!$member) {
            //     \Log::error('No member profile found for user: ' . $user->id);
            //     // return redirect()->route('member.dashboard')
            //     //     ->with('error', 'Member profile not found. Please contact administrator.');
            //     
            //     // Create a dummy member object for testing
            //     $member = (object) ['id' => 999];
            // }
            
            // \Log::info('Member profile found: ' . $member->id);
            
            // // Debug: Check if we can find any pending forms first
            // $allPendingForms = UploadedForm::where('status', 'pending_guarantors')->count();
            // \Log::info("Total pending forms: " . $allPendingForms);
            
            // // Get forms that need guarantors and member hasn't guaranteed yet
            // $availableGuarantees = UploadedForm::with(['member', 'document'])
            //     ->where('status', 'pending_guarantors')
            //     ->whereDoesntHave('guarantors', function($query) use ($member) {
            //         $query->where('guarantor_member_id', $member->id);
            //     })
            //     ->orderBy('created_at', 'desc')
            //     ->paginate(10);

            // \Log::info("Available guarantees for member {$member->id}: " . $availableGuarantees->count());

            // Return empty collection for testing
            $availableGuarantees = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1);

            return view('member.documents.pending-guarantees', compact('availableGuarantees'));
            
        } catch (\Exception $e) {
            \Log::error('Error in pendingGuarantees: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->route('member.dashboard')
                ->with('error', 'Unable to load pending guarantees. Please try again later.');
        }
    }

    public function guaranteeDetails(UploadedForm $uploadedForm): View
    {
        $member = Auth::user()->member;
        
        // Check if member can guarantee this form
        if ($uploadedForm->status !== 'pending_guarantors') {
            abort(404, 'This form is not available for guarantee');
        }

        // Check if member already guaranteed
        $existingGuarantee = LoanGuarantor::where('uploaded_form_id', $uploadedForm->id)
            ->where('guarantor_member_id', $member->id)
            ->first();

        if ($existingGuarantee) {
            return redirect()
                ->route('member.documents.pending-guarantees')
                ->with('info', 'You have already responded to this guarantee request');
        }

        // Get borrower's repayment history
        $borrower = $uploadedForm->member;
        $repaymentHistory = $this->getBorrowerRepaymentHistory($borrower);

        return view('member.documents.guarantee-details', compact('uploadedForm', 'borrower', 'repaymentHistory'));
    }

    public function confirmGuarantee(Request $request, UploadedForm $uploadedForm): RedirectResponse
    {
        $member = Auth::user()->member;

        $request->validate([
            'guarantee_percentage' => 'required|numeric|min:1|max:100',
            'confirmation' => 'required|string|min:10',
        ]);

        // Check if member can guarantee this amount
        $guaranteedAmount = ($uploadedForm->loan_amount * $request->guarantee_percentage) / 100;
        $maxGuaranteeAmount = $member->getMaxGuaranteeAmount();

        if ($guaranteedAmount > $maxGuaranteeAmount) {
            return redirect()
                ->back()
                ->with('error', 'You cannot guarantee more than UGX ' . number_format($maxGuaranteeAmount, 2) . ' (50% of your savings)');
        }

        // Check total guaranteed percentage doesn't exceed 100%
        $currentGuaranteedPercentage = $uploadedForm->confirmedGuarantors()->sum('guarantee_percentage');
        if ($currentGuaranteedPercentage + $request->guarantee_percentage > 100) {
            return redirect()
                ->back()
                ->with('error', 'This would exceed 100% guarantee. Only ' . (100 - $currentGuaranteedPercentage) . '% is still available.');
        }

        // Create guarantee record
        $guarantee = LoanGuarantor::create([
            'uploaded_form_id' => $uploadedForm->id,
            'guarantor_member_id' => $member->id,
            'guarantee_percentage' => $request->guarantee_percentage,
            'guaranteed_amount' => $guaranteedAmount,
            'guarantee_status' => 'confirmed',
            'guarantee_confirmation' => $request->confirmation,
            'guaranteed_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('member.documents.pending-guarantees')
            ->with('success', 'You have successfully guaranteed ' . $request->guarantee_percentage . '% of this loan (UGX ' . number_format($guaranteedAmount, 2) . ')');
    }

    public function withdrawGuarantee(LoanGuarantor $loanGuarantor): RedirectResponse
    {
        $member = Auth::user()->member;

        if ($loanGuarantor->guarantor_member_id !== $member->id) {
            abort(403, 'Unauthorized');
        }

        if ($loanGuarantor->guarantee_status === 'withdrawn') {
            return redirect()
                ->back()
                ->with('info', 'This guarantee has already been withdrawn');
        }

        if ($loanGuarantor->guarantee_status === 'called_upon') {
            return redirect()
                ->back()
                ->with('error', 'Cannot withdraw a guarantee that has been called upon');
        }

        $loanGuarantor->withdrawGuarantee('Withdrawn by guarantor');

        return redirect()
            ->back()
            ->with('success', 'Guarantee withdrawn successfully');
    }

    public function guarantorHistory(): View
    {
        try {
            $user = Auth::user();
            
            // TEMPORARILY SKIP DATABASE CALLS FOR TESTING
            // $member = $user->member;
            
            // Return empty collection for testing
            $guarantees = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15, 1);

            return view('member.documents.guarantor-history', compact('guarantees'));
            
        } catch (\Exception $e) {
            \Log::error('Error in guarantorHistory: ' . $e->getMessage());
            
            return redirect()->route('member.dashboard')
                ->with('error', 'Unable to load guarantor history. Please try again later.');
        }
    }

    private function getBorrowerRepaymentHistory(Member $borrower): array
    {
        $loans = $borrower->loans()
            ->with(['repayments'])
            ->orderBy('created_at', 'desc')
            ->take(5) // Show last 5 loans
            ->get();

        $history = [];
        foreach ($loans as $loan) {
            $history[] = [
                'loan_number' => $loan->loan_number ?? 'N/A',
                'amount' => $loan->principal_amount ?? 0,
                'status' => $loan->status ?? 'unknown',
                'balance' => $loan->balance ?? 0,
                'created_at' => $loan->created_at,
                'repayments_count' => $loan->repayments->count(),
                'total_repaid' => $loan->repayments->sum('amount'),
            ];
        }

        return $history;
    }
}
