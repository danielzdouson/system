@extends('layouts.member')

@section('title', 'Guarantor History')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-handshake me-2"></i>My Guarantor History</h2>
                <div>
                    <a href="{{ route('member.documents.pending-guarantees') }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-users me-2"></i>Pending Guarantees
                    </a>
                    <a href="{{ route('member.documents.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Documents
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Statistics Cards -->
            @if($guarantees->count() > 0)
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $guarantees->count() }}</h4>
                                        <small>Total Guarantees</small>
                                    </div>
                                    <i class="fas fa-handshake fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $guarantees->where('guarantee_status', 'confirmed')->count() }}</h4>
                                        <small>Active Guarantees</small>
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
                                        <h4 class="mb-0">{{ $guarantees->where('guarantee_status', 'called_upon')->count() }}</h4>
                                        <small>Called Upon</small>
                                    </div>
                                    <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">UGX {{ number_format($guarantees->sum('guaranteed_amount'), 0) }}</h4>
                                        <small>Total Guaranteed</small>
                                    </div>
                                    <i class="fas fa-money-bill-wave fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Guarantee History</h5>
                </div>
                <div class="card-body">
                    @if($guarantees->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Borrower</th>
                                        <th>Document</th>
                                        <th>Loan Amount</th>
                                        <th>Your Guarantee</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($guarantees as $guarantee)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        {{ strtoupper(substr($guarantee->uploadedForm->member->user->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <strong>{{ $guarantee->uploadedForm->member->user->name }}</strong><br>
                                                        <small class="text-muted">ID: {{ $guarantee->uploadedForm->member->membership_number }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                                    <div>
                                                        <strong>{{ $guarantee->uploadedForm->document->title }}</strong><br>
                                                        <small class="text-muted">{{ $guarantee->uploadedForm->filename }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-success">
                                                    UGX {{ number_format($guarantee->uploadedForm->loan_amount, 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>
                                                    <span class="badge bg-primary fs-6">{{ $guarantee->guarantee_percentage }}%</span><br>
                                                    <strong class="text-primary">UGX {{ number_format($guarantee->guaranteed_amount, 2) }}</strong>
                                                </div>
                                            </td>
                                            <td>
                                                @switch($guarantee->guarantee_status)
                                                    @case('confirmed')
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check-circle me-1"></i>Confirmed
                                                        </span>
                                                        @break
                                                    @case('withdrawn')
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-times-circle me-1"></i>Withdrawn
                                                        </span>
                                                        @break
                                                    @case('called_upon')
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-exclamation-triangle me-1"></i>Called Upon
                                                        </span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ $guarantee->guarantee_status }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $guarantee->created_at->format('M d, Y') }}<br>
                                                    {{ $guarantee->created_at->format('h:i A') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="#" onclick="viewGuaranteeDetails({{ $guarantee->id }})">
                                                                <i class="fas fa-eye me-2"></i>View Details
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="#" onclick="viewBorrowerHistory({{ $guarantee->uploadedForm->member_id }})">
                                                                <i class="fas fa-user me-2"></i>Borrower Profile
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        @if($guarantee->guarantee_status === 'confirmed')
                                                            <li>
                                                                <a class="dropdown-item text-warning" href="#" 
                                                                   onclick="confirmWithdrawGuarantee({{ $guarantee->id }})">
                                                                    <i class="fas fa-undo me-2"></i>Withdraw Guarantee
                                                                </a>
                                                            </li>
                                                        @endif
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
                                Showing {{ $guarantees->firstItem() }} to {{ $guarantees->lastItem() }} 
                                of {{ $guarantees->total() }} guarantees
                            </div>
                            {{ $guarantees->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-handshake fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No Guarantees Yet</h4>
                            <p class="text-muted">You haven't guaranteed any loans yet.</p>
                            <a href="{{ route('member.documents.pending-guarantees') }}" class="btn btn-primary">
                                <i class="fas fa-users me-2"></i>Browse Pending Guarantees
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Guarantee Details Modal -->
<div class="modal fade" id="guaranteeDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Guarantee Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="guaranteeDetailsContent">
                <!-- Content loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Withdraw Confirmation Modal -->
<div class="modal fade" id="withdrawModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Withdraw Guarantee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to withdraw this guarantee?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone and may affect the borrower's loan application.
                </div>
                <form id="withdrawForm" method="POST" action="">
                    @csrf
                    <div class="mb-3">
                        <label for="withdrawal_reason" class="form-label">Reason for withdrawal (optional)</label>
                        <textarea class="form-control" id="withdrawal_reason" name="withdrawal_reason" rows="3" 
                                  placeholder="Please provide a reason for withdrawing this guarantee..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="submitWithdrawal()">
                    <i class="fas fa-undo me-2"></i>Withdraw Guarantee
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function viewGuaranteeDetails(guaranteeId) {
    const content = `
        <div class="text-center py-3">
            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
            <p class="mt-2">Loading guarantee details...</p>
        </div>
    `;
    
    document.getElementById('guaranteeDetailsContent').innerHTML = content;
    
    const modal = new bootstrap.Modal(document.getElementById('guaranteeDetailsModal'));
    modal.show();
    
    // Here you would typically make an AJAX call to get the details
    // fetch('/member/documents/guarantee-details/' + guaranteeId)
    //     .then(response => response.json())
    //     .then(data => {
    //         document.getElementById('guaranteeDetailsContent').innerHTML = data.html;
    //     });
}

function viewBorrowerHistory(memberId) {
    // In a real implementation, you would navigate to borrower profile
    window.location.href = '/member/profile/' + memberId;
}

function confirmWithdrawGuarantee(guaranteeId) {
    const form = document.getElementById('withdrawForm');
    form.action = '/member/documents/withdraw-guarantee/' + guaranteeId;
    
    const modal = new bootstrap.Modal(document.getElementById('withdrawModal'));
    modal.show();
}

function submitWithdrawal() {
    const form = document.getElementById('withdrawForm');
    form.submit();
}

// Auto-refresh for called upon guarantees
function checkCalledUponGuarantees() {
    fetch('/member/documents/check-called-upon')
        .then(response => response.json())
        .then(data => {
            if (data.hasCalledUpon) {
                // Show notification
                const alert = document.createElement('div');
                alert.className = 'alert alert-danger alert-dismissible fade show position-fixed';
                alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                alert.innerHTML = `
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Action Required:</strong> One of your guarantees has been called upon!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(alert);
                
                // Auto-remove after 10 seconds
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 10000);
            }
        });
}

// Check every 5 minutes
setInterval(checkCalledUponGuarantees, 300000);
</script>
@endsection
