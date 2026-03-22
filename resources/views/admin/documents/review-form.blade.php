@extends('layouts.admin')

@section('title', 'Review Uploaded Form')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Review Loan Application</h2>
                <div>
                    <a href="{{ route('admin.documents.uploaded-forms') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Uploaded Forms
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-file-contract me-2"></i>Application Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Applicant:</strong><br>
                                    {{ $uploadedForm->member->first_name }} {{ $uploadedForm->member->last_name }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Member Number:</strong><br>
                                    {{ $uploadedForm->member->membership_number ?? 'N/A' }}
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Document:</strong><br>
                                    {{ $uploadedForm->document->title }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Loan Amount Requested:</strong><br>
                                    UGX {{ number_format($uploadedForm->loan_amount, 2) }}
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Guarantors Required:</strong><br>
                                    {{ $uploadedForm->guarantors->count() }} / {{ $uploadedForm->guarantors_required }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Guaranteed Percentage:</strong><br>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar {{ $uploadedForm->getGuaranteeProgressColor() }}" role="progressbar" 
                                             style="width: {{ $uploadedForm->getGuaranteedPercentage() }}%">
                                            {{ $uploadedForm->getGuaranteedPercentage() }}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Current Status:</strong><br>
                                    <span class="badge bg-{{ $uploadedForm->getStatusColor() }}">
                                        {{ $uploadedForm->getStatusLabel() }}
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Submitted:</strong><br>
                                    {{ $uploadedForm->created_at->format('M d, Y H:i') }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <strong>Download Application:</strong><br>
                                <a href="{{ route('admin.documents.download-uploaded-form', $uploadedForm) }}" 
                                   class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="fas fa-download"></i> Download PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-users me-2"></i>Guarantors</h5>
                        </div>
                        <div class="card-body">
                            @if($uploadedForm->guarantors->count() > 0)
                                @foreach($uploadedForm->guarantors as $guarantor)
                                    <div class="mb-3 p-2 border rounded">
                                        <strong>{{ $guarantor->guarantor->first_name }} {{ $guarantor->guarantor->last_name }}</strong><br>
                                        <small class="text-muted">
                                            {{ $guarantor->guarantee_percentage }}% (UGX {{ number_format($guarantor->guaranteed_amount, 2) }})
                                        </small><br>
                                        <span class="badge bg-{{ $guarantor->getStatusColor() }}">
                                            {{ $guarantor->guarantee_status }}
                                        </span>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">No guarantors yet</p>
                            @endif
                            
                            <a href="{{ route('admin.documents.view-guarantors', $uploadedForm) }}" 
                               class="btn btn-sm btn-outline-info w-100">
                                <i class="fas fa-eye"></i> View All Guarantors
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if($uploadedForm->status === 'ready_for_review')
            <div class="card mt-3">
                <div class="card-header">
                    <h5><i class="fas fa-gavel me-2"></i>Review Actions</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.documents.approve-form', $uploadedForm) }}" method="POST" class="mb-3">
                        @csrf
                        <div class="mb-3">
                            <label for="admin_notes" class="form-label">Admin Notes (Optional)</label>
                            <textarea name="admin_notes" id="admin_notes" class="form-control" rows="3" 
                                      placeholder="Add any notes about this approval..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success" 
                                onclick="return confirm('Are you sure you want to approve this loan application?')">
                            <i class="fas fa-check"></i> Approve Application
                        </button>
                    </form>

                    <form action="{{ route('admin.documents.reject-form', $uploadedForm) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="rejection_notes" class="form-label">Rejection Reason *</label>
                            <textarea name="admin_notes" id="rejection_notes" class="form-control" rows="3" 
                                      placeholder="Please explain why this application is being rejected..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('Are you sure you want to reject this loan application?')">
                            <i class="fas fa-times"></i> Reject Application
                        </button>
                    </form>
                </div>
            </div>
            @else
            <div class="card mt-3">
                <div class="card-body text-center">
                    <p class="text-muted">
                        This application cannot be reviewed at this time. 
                        Current status: <span class="badge bg-{{ $uploadedForm->getStatusColor() }}">
                            {{ $uploadedForm->getStatusLabel() }}
                        </span>
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
