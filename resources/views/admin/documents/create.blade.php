@extends('layouts.admin')

@section('title', 'Upload Document')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-file-upload me-2"></i>Upload Document</h2>
                <a href="{{ route('admin.documents.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Documents
                </a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Upload Error</h5>
                    <p>Please fix the following errors:</p>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-cloud-upload-alt me-2"></i>Document Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.documents.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">
                                    <i class="fas fa-heading me-1"></i>Document Title *
                                </label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="{{ old('title') }}" required 
                                       placeholder="e.g., Loan Application Form 2024">
                                <div class="form-text">Enter a descriptive title for the document</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="document_type" class="form-label">
                                    <i class="fas fa-tag me-1"></i>Document Type *
                                </label>
                                <select class="form-select" id="document_type" name="document_type" required>
                                    <option value="">Select Document Type</option>
                                    <option value="constitution" {{ old('document_type') == 'constitution' ? 'selected' : '' }}>
                                        <i class="fas fa-balance-scale"></i> Constitution
                                    </option>
                                    <option value="legal" {{ old('document_type') == 'legal' ? 'selected' : '' }}>
                                        <i class="fas fa-gavel"></i> Legal Document
                                    </option>
                                    <option value="loan_form" {{ old('document_type') == 'loan_form' ? 'selected' : '' }}>
                                        <i class="fas fa-file-contract"></i> Loan Form
                                    </option>
                                    <option value="other" {{ old('document_type') == 'other' ? 'selected' : '' }}>
                                        <i class="fas fa-file"></i> Other
                                    </option>
                                </select>
                                <div class="form-text">Choose the appropriate document category</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left me-1"></i>Description
                                </label>
                                <textarea class="form-control" id="description" name="description" rows="3" 
                                          placeholder="Provide a brief description of this document...">{{ old('description') }}</textarea>
                                <div class="form-text">Optional: Describe what this document is for</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="file" class="form-label">
                                    <i class="fas fa-file-pdf me-1"></i>Upload File *
                                </label>
                                <input type="file" class="form-control" id="file" name="file" 
                                       accept=".pdf" required onchange="updateFileInfo()">
                                <div class="form-text">
                                    <i class="fas fa-info-circle text-info"></i>
                                    Only PDF files are allowed. Maximum file size: 10MB
                                </div>
                                <div id="fileInfo" class="mt-2"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="requires_fine" class="form-label">
                                    <i class="fas fa-coins me-1"></i>Fine Settings
                                </label>
                                <div class="card">
                                    <div class="card-body p-3">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="requires_fine" 
                                                   name="requires_fine" value="1" {{ old('requires_fine') ? 'checked' : '' }}
                                                   onchange="toggleFineAmount()">
                                            <label class="form-check-label" for="requires_fine">
                                                <strong>Apply fine for download</strong>
                                            </label>
                                        </div>
                                        <div id="fineAmountSection" style="display: {{ old('requires_fine') ? 'block' : 'none' }};">
                                            <label for="fine_amount" class="form-label">Fine Amount (UGX)</label>
                                            <input type="number" class="form-control" id="fine_amount" name="fine_amount" 
                                                   value="{{ old('fine_amount', 5000) }}" min="0" step="100">
                                            <div class="form-text">Default: 5,000 UGX for loan forms</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="is_active" class="form-label">
                                    <i class="fas fa-toggle-on me-1"></i>Document Status
                                </label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" 
                                           name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        <strong>Active</strong> - Available for download
                                    </label>
                                </div>
                                <div class="form-text">Inactive documents won't be visible to members</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-lightbulb me-1"></i>Quick Tips
                                </label>
                                <div class="alert alert-info py-2">
                                    <small>
                                        <i class="fas fa-check-circle me-1"></i> Loan forms typically require a 5,000 UGX fine<br>
                                        <i class="fas fa-check-circle me-1"></i> Constitution and legal documents are usually free<br>
                                        <i class="fas fa-check-circle me-1"></i> Make sure PDF files are clear and readable
                                    </small>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.documents.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-upload me-2"></i>Upload Document
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFineAmount() {
    const requiresFine = document.getElementById('requires_fine').checked;
    const fineAmountSection = document.getElementById('fineAmountSection');
    fineAmountSection.style.display = requiresFine ? 'block' : 'none';
    
    if (requiresFine) {
        const documentType = document.getElementById('document_type').value;
        const fineAmount = document.getElementById('fine_amount');
        if (documentType === 'loan_form' && !fineAmount.value) {
            fineAmount.value = 5000;
        }
    }
}

function updateFileInfo() {
    const fileInput = document.getElementById('file');
    const fileInfo = document.getElementById('fileInfo');
    
    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        
        if (file.size > 10 * 1024 * 1024) {
            fileInfo.innerHTML = '<div class="alert alert-danger py-2"><small><i class="fas fa-exclamation-triangle me-1"></i>File too large: ' + fileSize + ' MB (max 10MB)</small></div>';
            fileInput.value = '';
        } else if (file.type !== 'application/pdf') {
            fileInfo.innerHTML = '<div class="alert alert-danger py-2"><small><i class="fas fa-exclamation-triangle me-1"></i>Invalid file type. Only PDF files are allowed.</small></div>';
            fileInput.value = '';
        } else {
            fileInfo.innerHTML = '<div class="alert alert-success py-2"><small><i class="fas fa-check-circle me-1"></i>' + file.name + ' (' + fileSize + ' MB) - Ready to upload</small></div>';
        }
    } else {
        fileInfo.innerHTML = '';
    }
}

// Auto-set fine amount when document type changes
document.getElementById('document_type').addEventListener('change', function() {
    const requiresFine = document.getElementById('requires_fine');
    const fineAmount = document.getElementById('fine_amount');
    
    if (this.value === 'loan_form') {
        requiresFine.checked = true;
        fineAmount.value = 5000;
        toggleFineAmount();
    } else if (this.value === 'constitution' || this.value === 'legal') {
        requiresFine.checked = false;
        toggleFineAmount();
    }
});

// Form submission loading state
document.getElementById('uploadForm').addEventListener('submit', function() {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Uploading...';
});
</script>
@endsection
