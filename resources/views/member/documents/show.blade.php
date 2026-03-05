@extends('layouts.member')

@section('title', $document->title)

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
                    <li class="breadcrumb-item active">{{ $document->title }}</li>
                </ol>
            </nav>

            <!-- Document Header -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-file-alt me-2"></i>{{ $document->title }}
                        </h5>
                        <span class="badge bg-light text-dark">{{ $document->getFormattedDocumentType() }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="text-muted mb-3">Document Information</h6>
                            <p class="mb-2">
                                <strong>Type:</strong> 
                                <span class="badge bg-{{ $document->getTypeColor() }}">
                                    {{ $document->getFormattedDocumentType() }}
                                </span>
                            </p>
                            @if($document->description)
                            <p class="mb-2">
                                <strong>Description:</strong> {{ $document->description }}
                            </p>
                            @endif
                            <p class="mb-2">
                                <strong>File Size:</strong> {{ $document->getFormattedFileSize() }}
                            </p>
                            <p class="mb-2">
                                <strong>Uploaded:</strong> {{ $document->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted mb-3">Download Status</h6>
                            @if($hasDownloaded)
                                <div class="alert alert-success py-2">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Already Downloaded</strong>
                                    <br><small>You can download this document again.</small>
                                </div>
                            @else
                                <div class="alert alert-info py-2">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Not Downloaded Yet</strong>
                                    <br><small>Click below to download this document.</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Download Section -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-download me-2"></i>Download Document
                    </h6>
                </div>
                <div class="card-body">
                    @if($document->requires_fine)
                        <div class="alert alert-warning">
                            <h6 class="alert-heading">
                                <i class="fas fa-coins me-2"></i>Fine Required
                            </h6>
                            <p class="mb-2">
                                This document requires a fine of <strong>{{ number_format($document->fine_amount, 2) }} UGX</strong> to download.
                            </p>
                            <p class="mb-0">
                                The fine will be automatically charged to your account upon download.
                            </p>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">{{ $document->original_filename }}</h6>
                            <small class="text-muted">{{ $document->getFormattedFileSize() }}</small>
                        </div>
                        <form action="{{ route('member.documents.download', $document) }}" method="POST">
                            @csrf
                            @if($document->requires_fine && !$hasDownloaded)
                                <button type="submit" name="confirm_fine" value="1" 
                                        class="btn btn-warning"
                                        onclick="return confirm('Are you sure you want to download this document? A fine of {{ number_format($document->fine_amount, 2) }} UGX will be charged to your account.')">
                                    <i class="fas fa-download me-2"></i>Download & Pay Fine
                                </button>
                            @else
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-download me-2"></i>Download Document
                                </button>
                            @endif
                        </form>
                    </div>

                    @if($document->document_type === 'loan_form')
                    <div class="mt-3">
                        <div class="alert alert-info py-2">
                            <small>
                                <i class="fas fa-info-circle me-1"></i>
                                <strong>Loan Form Instructions:</strong><br>
                                1. Download this loan application form<br>
                                2. Fill in all required information<br>
                                3. Upload the completed form for processing<br>
                                4. Wait for guarantor approvals
                            </small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Additional Information -->
            @if($document->document_type === 'constitution' || $document->document_type === 'legal')
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-balance-scale me-2"></i>Important Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-light">
                        <h6 class="alert-heading">Document Guidelines</h6>
                        <p class="mb-2">
                            This {{ strtolower($document->getFormattedDocumentType()) }} contains important information about the SACCO operations, 
                            rules, and regulations that all members should be familiar with.
                        </p>
                        <hr>
                        <p class="mb-0">
                            <strong>Please read this document carefully</strong> and keep a copy for your records. 
                            If you have any questions about the contents, please contact the SACCO administration.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="mt-4">
                <a href="{{ route('member.documents.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Documents
                </a>
                @if($document->document_type === 'loan_form')
                    <a href="{{ route('member.documents.upload-form', $document) }}" class="btn btn-success ms-2">
                        <i class="fas fa-upload me-2"></i>Upload Completed Form
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
