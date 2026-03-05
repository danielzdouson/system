@extends('layouts.member')

@section('title', 'Upload Loan Form')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('member.dashboard') }}" class="text-decoration-none">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('member.documents.index') }}" class="text-decoration-none">
                            <i class="fas fa-file-alt me-1"></i>Documents
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('member.documents.show', $document) }}" class="text-decoration-none">
                            {{ $document->title }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Upload Completed Form</li>
                </ol>
            </nav>

            <!-- Form Header -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-upload me-2"></i>Upload Completed Loan Application Form
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>Upload Instructions
                        </h6>
                        <ol class="mb-0">
                            <li>Make sure you have downloaded and completely filled the loan application form</li>
                            <li>Ensure all required fields are filled accurately</li>
                            <li>Scan or take a clear photo of the completed form</li>
                            <li>Upload the file below (PDF format preferred)</li>
                            <li>Wait for admin review and guarantor approvals</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Upload Form -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-file-upload me-2"></i>Upload Your Completed Form
                    </h6>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading">
                                <i class="fas fa-exclamation-triangle me-2"></i>Upload Error
                            </h5>
                            <p>Please fix the following errors:</p>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('member.documents.store-upload', $document) }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf
                        
                        <!-- Document Info -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fas fa-file-alt me-1"></i>Document Type
                                </label>
                                <input type="text" class="form-control" value="{{ $document->title }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fas fa-user me-1"></i>Applicant
                                </label>
                                <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                            </div>
                        </div>

                        <!-- Loan Amount -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="loan_amount" class="form-label">
                                    <i class="fas fa-money-bill-wave me-1"></i>Loan Amount Requested (UGX) *
                                </label>
                                <input type="number" class="form-control" id="loan_amount" name="loan_amount" 
                                       value="{{ old('loan_amount') }}" required min="10000" step="1000"
                                       placeholder="Enter the loan amount you are requesting">
                                <div class="form-text">
                                    Enter the amount you wish to borrow (minimum 10,000 UGX)
                                </div>
                            </div>
                        </div>

                        <!-- File Upload -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="file" class="form-label">
                                    <i class="fas fa-file-pdf me-1"></i>Completed Form File *
                                </label>
                                <input type="file" class="form-control" id="file" name="file" 
                                       accept=".pdf,.jpg,.jpeg,.png" required onchange="updateFileInfo()">
                                <div class="form-text">
                                    <i class="fas fa-info-circle text-info"></i>
                                    Accepted formats: PDF, JPG, JPEG, PNG. Maximum file size: 10MB
                                </div>
                                <div id="fileInfo" class="mt-2"></div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="purpose" class="form-label">
                                    <i class="fas fa-comment-alt me-1"></i>Loan Purpose (Optional)
                                </label>
                                <textarea class="form-control" id="purpose" name="purpose" rows="3" 
                                          placeholder="Briefly describe what you need the loan for...">{{ old('purpose') }}</textarea>
                                <div class="form-text">
                                    This helps us understand your loan requirements better
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card border-warning">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-exclamation-triangle me-2"></i>Important Information
                                        </h6>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                            <label class="form-check-label" for="terms">
                                                I confirm that all information provided is accurate and truthful
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="guarantors" name="guarantors" required>
                                            <label class="form-check-label" for="guarantors">
                                                I understand that this loan will require guarantors for approval
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="repayment" name="repayment" required>
                                            <label class="form-check-label" for="repayment">
                                                I commit to repaying this loan according to the SACCO terms and conditions
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('member.documents.show', $document) }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-success" id="submitBtn">
                                        <i class="fas fa-upload me-2"></i>Upload Form
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-question-circle me-2"></i>Need Help?
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Next Steps After Upload:</h6>
                            <ol>
                                <li>Admin review of your application</li>
                                <li>Guarantor requirements (typically 2-3 members)</li>
                                <li>Loan committee approval</li>
                                <li>Disbursement to your account</li>
                            </ol>
                        </div>
                        <div class="col-md-6">
                            <h6>Contact Information:</h6>
                            <p class="mb-2">
                                <i class="fas fa-phone me-2"></i>
                                Call SACCO office for assistance
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-envelope me-2"></i>
                                Email: support@sacco.com
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateFileInfo() {
    const fileInput = document.getElementById('file');
    const fileInfo = document.getElementById('fileInfo');
    
    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        
        if (file.size > 10 * 1024 * 1024) {
            fileInfo.innerHTML = '<div class="alert alert-danger py-2"><small><i class="fas fa-exclamation-triangle me-1"></i>File too large: ' + fileSize + ' MB (max 10MB)</small></div>';
            fileInput.value = '';
        } else {
            fileInfo.innerHTML = '<div class="alert alert-success py-2"><small><i class="fas fa-check-circle me-1"></i>' + file.name + ' (' + fileSize + ' MB) - Ready to upload</small></div>';
        }
    } else {
        fileInfo.innerHTML = '';
    }
}

// Form submission loading state
document.getElementById('uploadForm').addEventListener('submit', function() {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Uploading...';
});

// Validate loan amount
document.getElementById('loan_amount').addEventListener('input', function() {
    const value = parseFloat(this.value);
    if (value < 10000) {
        this.setCustomValidity('Minimum loan amount is 10,000 UGX');
    } else if (value > 10000000) {
        this.setCustomValidity('Maximum loan amount is 10,000,000 UGX');
    } else {
        this.setCustomValidity('');
    }
});
</script>
@endsection
