@extends('layouts.member')

@section('title', 'My Uploaded Forms')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-file-upload me-2"></i>My Uploaded Forms</h2>
                <a href="{{ route('member.documents.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Documents
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Your Upload History</h5>
                </div>
                <div class="card-body">
                    @if($uploadedForms->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Document</th>
                                        <th>Loan Amount</th>
                                        <th>Status</th>
                                        <th>Guarantors</th>
                                        <th>Uploaded</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($uploadedForms as $uploadedForm)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                                    <div>
                                                        <strong>{{ $uploadedForm->document->title }}</strong><br>
                                                        <small class="text-muted">{{ $uploadedForm->filename }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-success">
                                                    UGX {{ number_format($uploadedForm->loan_amount, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                @switch($uploadedForm->status)
                                                    @case('pending_guarantors')
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-hourglass-half me-1"></i>Pending Guarantors
                                                        </span>
                                                        @break
                                                    @case('ready_for_review')
                                                        <span class="badge bg-info">
                                                            <i class="fas fa-clock me-1"></i>Ready for Review
                                                        </span>
                                                        @break
                                                    @case('approved')
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check-circle me-1"></i>Approved
                                                        </span>
                                                        @break
                                                    @case('rejected')
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-times-circle me-1"></i>Rejected
                                                        </span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ $uploadedForm->status }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-{{ $uploadedForm->guarantors_count >= $uploadedForm->guarantors_required ? 'success' : 'secondary' }} me-2">
                                                        {{ $uploadedForm->guarantors_count }} / {{ $uploadedForm->guarantors_required }}
                                                    </span>
                                                    @if($uploadedForm->guarantors_required > 0)
                                                        <div class="progress" style="width: 60px; height: 8px;">
                                                            <div class="progress-bar bg-{{ $uploadedForm->guarantors_count >= $uploadedForm->guarantors_required ? 'success' : 'warning' }}" 
                                                                 style="width: {{ min(($uploadedForm->guarantors_count / $uploadedForm->guarantors_required) * 100, 100) }}%"></div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $uploadedForm->created_at->format('M d, Y') }}<br>
                                                    {{ $uploadedForm->created_at->format('h:i A') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-cog"></i> Actions
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="#" onclick="viewUploadDetails({{ $uploadedForm->id }})">
                                                                <i class="fas fa-eye me-2"></i>View Details
                                                            </a>
                                                        </li>
                                                        @if($uploadedForm->status === 'pending_guarantors' && $uploadedForm->guarantors_count < $uploadedForm->guarantors_required)
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('member.documents.pending-guarantees') }}">
                                                                    <i class="fas fa-users me-2"></i>Find Guarantors
                                                                </a>
                                                            </li>
                                                        @endif
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item" href="#" onclick="downloadUpload({{ $uploadedForm->id }})">
                                                                <i class="fas fa-download me-2"></i>Download Copy
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Showing {{ $uploadedForms->firstItem() }} to {{ $uploadedForms->lastItem() }} 
                                of {{ $uploadedForms->total() }} uploads
                            </div>
                            {{ $uploadedForms->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-upload fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No Uploaded Forms Yet</h4>
                            <p class="text-muted">You haven't uploaded any loan forms yet.</p>
                            <a href="{{ route('member.documents.index') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Browse Available Documents
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistics Cards -->
            @if($uploadedForms->count() > 0)
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $uploadedForms->count() }}</h4>
                                        <small>Total Uploads</small>
                                    </div>
                                    <i class="fas fa-file-upload fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $uploadedForms->where('status', 'approved')->count() }}</h4>
                                        <small>Approved</small>
                                    </div>
                                    <i class="fas fa-check-circle fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $uploadedForms->where('status', 'pending_guarantors')->count() }}</h4>
                                        <small>Pending Guarantors</small>
                                    </div>
                                    <i class="fas fa-hourglass-half fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">UGX {{ number_format($uploadedForms->sum('loan_amount'), 0) }}</h4>
                                        <small>Total Applied</small>
                                    </div>
                                    <i class="fas fa-money-bill-wave fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Upload Details Modal -->
<div class="modal fade" id="uploadDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="uploadDetailsContent">
                <!-- Content loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewUploadDetails(uploadId) {
    // In a real implementation, you would fetch details via AJAX
    // For now, just show a placeholder
    const content = `
        <div class="text-center py-3">
            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
            <p class="mt-2">Loading upload details...</p>
        </div>
    `;
    
    document.getElementById('uploadDetailsContent').innerHTML = content;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('uploadDetailsModal'));
    modal.show();
    
    // Here you would typically make an AJAX call to get the details
    // fetch('/member/documents/upload-details/' + uploadId)
    //     .then(response => response.json())
    //     .then(data => {
    //         document.getElementById('uploadDetailsContent').innerHTML = data.html;
    //     });
}

function downloadUpload(uploadId) {
    // In a real implementation, you would trigger a download
    window.open('/member/documents/download-upload/' + uploadId, '_blank');
}
</script>
@endsection
