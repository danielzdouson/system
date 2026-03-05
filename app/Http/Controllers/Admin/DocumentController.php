<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\UploadedForm;
use App\Models\LoanGuarantor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function index(): View
    {
        $documents = Document::with('uploadedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.documents.index', compact('documents'));
    }

    public function create(): View
    {
        return view('admin.documents.create');
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'document_type' => 'required|in:constitution,legal,loan_form,other',
                'file' => 'required|file|mimes:pdf|max:10240', // 10MB max
                'requires_fine' => 'boolean',
                'fine_amount' => 'nullable|numeric|min:0',
                'is_active' => 'boolean',
            ]);

            // Check if storage directory exists and is writable
            $storagePath = storage_path('app/documents/' . date('Y/m'));
            if (!is_dir($storagePath)) {
                if (!mkdir($storagePath, 0755, true)) {
                    \Log::error('Failed to create storage directory: ' . $storagePath);
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'Unable to create storage directory. Please check permissions.');
                }
            }

            if (!is_writable($storagePath)) {
                \Log::error('Storage directory is not writable: ' . $storagePath);
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Storage directory is not writable. Please check permissions.');
            }

            $file = $request->file('file');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $originalFilename = $file->getClientOriginalName();
            
            // Store file in organized directory structure
            $filePath = $file->storeAs('documents/' . date('Y/m'), $filename, 'local');

            if (!$filePath) {
                \Log::error('Failed to store file: ' . $originalFilename);
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Failed to store the uploaded file. Please try again.');
            }

            $document = Document::create([
                'title' => $request->title,
                'description' => $request->description,
                'filename' => $filename,
                'original_filename' => $originalFilename,
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'document_type' => $request->document_type,
                'requires_fine' => $request->boolean('requires_fine', false),
                'fine_amount' => $request->fine_amount ?? ($request->document_type === 'loan_form' ? 5000 : 0),
                'is_active' => $request->boolean('is_active', true),
                'uploaded_by' => auth()->id(),
            ]);

            \Log::info('Document uploaded successfully: ' . $document->title . ' (ID: ' . $document->id . ')');

            return redirect()
                ->route('admin.documents.index')
                ->with('success', 'Document "' . $document->title . '" uploaded successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Document upload validation failed: ' . json_encode($e->errors()));
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Document upload error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'An error occurred while uploading the document: ' . $e->getMessage());
        }
    }

    public function edit(Document $document): View
    {
        return view('admin.documents.edit', compact('document'));
    }

    public function update(Request $request, Document $document): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_type' => 'required|in:constitution,legal,loan_form,other',
            'requires_fine' => 'boolean',
            'fine_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $document->update([
            'title' => $request->title,
            'description' => $request->description,
            'document_type' => $request->document_type,
            'requires_fine' => $request->boolean('requires_fine', false),
            'fine_amount' => $request->fine_amount ?? ($request->document_type === 'loan_form' ? 5000 : 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.documents.index')
            ->with('success', 'Document updated successfully!');
    }

    public function destroy(Document $document): RedirectResponse
    {
        // Delete file from storage
        Storage::disk('local')->delete($document->file_path);
        
        // Delete document record
        $document->delete();

        return redirect()
            ->route('admin.documents.index')
            ->with('success', 'Document deleted successfully!');
    }

    public function downloadForms(): View
    {
        $uploadedForms = UploadedForm::with(['member', 'document', 'guarantors.guarantor'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.documents.uploaded-forms', compact('uploadedForms'));
    }

    public function reviewForm(UploadedForm $uploadedForm): View
    {
        $uploadedForm->load(['member', 'document', 'guarantors.guarantor']);
        
        return view('admin.documents.review-form', compact('uploadedForm'));
    }

    public function approveForm(Request $request, UploadedForm $uploadedForm): RedirectResponse
    {
        $request->validate([
            'admin_notes' => 'nullable|string',
        ]);

        $uploadedForm->approve(auth()->user(), $request->admin_notes);

        return redirect()
            ->route('admin.documents.uploaded-forms')
            ->with('success', 'Form approved successfully!');
    }

    public function rejectForm(Request $request, UploadedForm $uploadedForm): RedirectResponse
    {
        $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $uploadedForm->reject(auth()->user(), $request->admin_notes);

        return redirect()
            ->route('admin.documents.uploaded-forms')
            ->with('success', 'Form rejected successfully!');
    }

    public function viewGuarantors(UploadedForm $uploadedForm): View
    {
        $guarantors = $uploadedForm->guarantors()->with('guarantor')->get();
        
        return view('admin.documents.guarantors', compact('uploadedForm', 'guarantors'));
    }

    public function downloadUploadedForm(UploadedForm $uploadedForm)
    {
        if (!Storage::disk('local')->exists($uploadedForm->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('local')->download($uploadedForm->file_path, $uploadedForm->filename);
    }
}
