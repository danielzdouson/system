@extends('layouts.admin')

@section('title', 'Loan Requests')

@section('content')
<div class="content-wrapper">
    <!-- Enhanced Page Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="page-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h2 mb-2 text-white">
                                    <i class="fas fa-list me-3"></i>
                                    Loan Management - {{ $activeFiscalYear->name }}
                                </h1>
                                <p class="text-white fs-5 mb-0 opacity-90">
                                    <i class="fas fa-hand-holding-usd me-2"></i>
                                    Create direct loans or manage loan applications
                                </p>
                            </div>
                            <div class="text-end">
                                <div class="month-badge">
                                    <span class="badge bg-white text-success fs-6 px-3 py-2">
                                        <i class="fas fa-calendar me-2"></i>
                                        {{ $activeFiscalYear->name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="action-card">
                        <div class="action-card-body">
                            <h5 class="action-title">
                                <i class="fas fa-rocket me-2"></i>
                                Quick Actions
                            </h5>
                            <div class="action-buttons">
                                <button class="action-btn primary" data-bs-toggle="modal" data-bs-target="#newLoanRequestModal">
                                    <i class="fas fa-plus me-2"></i>
                                    New Loan Request
                                </button>
                                <button class="action-btn success" data-bs-toggle="modal" data-bs-target="#directLoanModal">
                                    <i class="fas fa-hand-holding-usd me-2"></i>
                                    Create Direct Loan
                                </button>
                                <a href="{{ route('admin.group-loans.index') }}" class="action-btn warning">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Back to Dashboard
                                </a>
                                <a href="{{ route('admin.group-loans.all') }}" class="action-btn info">
                                    <i class="fas fa-eye me-2"></i>
                                    All Loans
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loan Requests Table -->
            <div class="row">
                <div class="col-12">
                    <div class="data-card">
                        <div class="data-card-header">
                            <h5 class="data-title">
                                <i class="fas fa-list me-2"></i>
                                Loan Applications
                            </h5>
                            <div class="data-subtitle">
                                Review pending loan requests or use direct loan creation for immediate disbursement
                            </div>
                        </div>
                        <div class="data-card-body">
                            @if($loanRequests->count() > 0)
                                <div class="table-responsive">
                                    <table class="enhanced-table">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>
                                                    <i class="fas fa-user me-2"></i>
                                                    Member
                                                </th>
                                                <th>
                                                    <i class="fas fa-money-bill-wave me-2"></i>
                                                    Amount
                                                </th>
                                                <th>
                                                    <i class="fas fa-clock me-2"></i>
                                                    Duration
                                                </th>
                                                <th>
                                                    <i class="fas fa-tag me-2"></i>
                                                    Type
                                                </th>
                                                <th>
                                                    <i class="fas fa-calendar me-2"></i>
                                                    Applied
                                                </th>
                                                <th>
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    Status
                                                </th>
                                                <th>
                                                    <i class="fas fa-cogs me-2"></i>
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($loanRequests as $request)
                                                <tr class="table-row-hover">
                                                    <td>
                                                        <div class="member-info">
                                                            <a href="{{ route('admin.members.show', $request->member->id) }}" class="member-link">
                                                                <strong>{{ $request->member->first_name }} {{ $request->member->last_name }}</strong>
                                                            </a>
                                                            <br>
                                                            <small class="text-muted">Savings: UGX {{ number_format($request->member_savings_at_request, 0) }}</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="amount-badge primary">
                                                            UGX {{ number_format($request->requested_amount, 0) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="duration-badge">{{ $request->duration_months }} months</span>
                                                    </td>
                                                    <td>
                                                        <span class="type-badge">{{ ucfirst($request->loan_type) }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="date-badge">{{ $request->created_at->format('M d, Y') }}</span>
                                                    </td>
                                                    <td>
                                                        {!! $request->getStatusBadge() !!}
                                                    </td>
                                                    <td>
                                                        <div class="action-buttons-inline">
                                                            @if($request->status === 'pending')
                                                                <button class="btn-action success" onclick="approveRequest({{ $request->id }})" title="Approve">
                                                                    <i class="fas fa-check"></i>
                                                                    <span>Approve</span>
                                                                </button>
                                                                <button class="btn-action warning" onclick="rejectRequest({{ $request->id }})" title="Reject">
                                                                    <i class="fas fa-times"></i>
                                                                    <span>Reject</span>
                                                                </button>
                                                            @endif
                                                            <button class="btn-action primary" onclick="viewDetails({{ $request->id }})" title="View Details">
                                                                <i class="fas fa-eye"></i>
                                                                <span>View</span>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $loanRequests->links() }}
                                </div>
                            @else
                                <div class="empty-state text-center py-5">
                                    <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                                    <h4 class="text-muted">No Loan Requests</h4>
                                    <p class="text-muted">No loan applications have been submitted yet.</p>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newLoanRequestModal">
                                        <i class="fas fa-plus me-2"></i>
                                        Create Loan Request
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Loan Request Modal -->
<div class="modal fade" id="newLoanRequestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>
                    New Loan Request
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.group-loans.requests.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Member</label>
                                <select name="member_id" class="form-select" required>
                                    <option value="">Select Member</option>
                                    @foreach($members as $member)
                                        <option value="{{ $member->id }}">{{ $member->first_name }} {{ $member->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Loan Amount (UGX)</label>
                                <input type="number" name="amount" class="form-control" min="10000" step="1000" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Duration (Months)</label>
                                <input type="number" name="duration_months" class="form-control" min="1" max="36" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Loan Type</label>
                                <select name="loan_type" class="form-select" required>
                                    <option value="">Select Type</option>
                                    <option value="personal">Personal</option>
                                    <option value="business">Business</option>
                                    <option value="emergency">Emergency</option>
                                    <option value="education">Education</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Purpose</label>
                        <textarea name="purpose" class="form-control" rows="3" placeholder="Describe the purpose of this loan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Approve Request Modal -->
<div class="modal fade" id="approveRequestModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-check me-2"></i>
                    Approve Loan Request
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="_method" value="POST">
                    <div class="mb-3">
                        <label class="form-label">Interest Rate (%)</label>
                        <input type="number" name="interest_rate" class="form-control" min="1" max="50" step="0.1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Interest Type</label>
                        <select name="interest_type" class="form-select" required>
                            <option value="flat">Flat Rate</option>
                            <option value="reducing_balance">Reducing Balance</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Admin Notes</label>
                        <textarea name="admin_notes" class="form-control" rows="3" placeholder="Add any notes about this approval..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve Loan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Request Modal -->
<div class="modal fade" id="rejectRequestModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-times me-2"></i>
                    Reject Loan Request
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason</label>
                        <textarea name="rejection_reason" class="form-control" rows="4" placeholder="Please provide a reason for rejection..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Direct Loan Modal -->
<div class="modal fade" id="directLoanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-hand-holding-usd me-2"></i>
                    Create Direct Loan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.group-loans.direct-loan.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Direct Loan:</strong> This will create and immediately disburse the loan without requiring approval.
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Member</label>
                                <select name="member_id" class="form-select" required>
                                    <option value="">Select Member</option>
                                    @foreach($members as $member)
                                        <option value="{{ $member->id }}">{{ $member->first_name }} {{ $member->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Principal Amount (UGX)</label>
                                <input type="number" name="principal_amount" class="form-control" min="10000" step="1000" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Interest Rate (%)</label>
                                <input type="number" name="interest_rate" class="form-control" min="1" max="30" step="0.1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Loan Term (Months)</label>
                                <input type="number" name="loan_term_months" class="form-control" min="1" max="60" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Purpose</label>
                        <textarea name="purpose" class="form-control" rows="3" placeholder="Describe the purpose of this loan..." required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Total Interest (UGX)</label>
                                <input type="text" id="totalInterest" class="form-control" readonly placeholder="Will be calculated">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Total Amount (UGX)</label>
                                <input type="text" id="totalAmount" class="form-control" readonly placeholder="Will be calculated">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>
                        Create & Disburse Loan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Enhanced Page Header */
.container-fluid {
    padding: 2rem;
    width: 130%;
}
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    margin-bottom: 2rem;
}

.month-badge .badge {
    border-radius: 50px;
    font-weight: 600;
}

/* Enhanced Action Cards */
.action-card {
    width: 170%;
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 2rem;
}

.action-card-body {
    padding: 2rem;
}

.action-title {
    color: #333;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.action-btn {
    padding: 1rem 2rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    color: white;
    border: none;
    cursor: pointer;
}

.action-btn.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.action-btn.success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.action-btn.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.action-btn.info {
    background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
}

.action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    color: white;
}

/* Enhanced Data Card */
.data-card {
    width: 170%;
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 2rem;
}

.data-card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
}

.data-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.data-subtitle {
    opacity: 0.9;
    margin: 0;
}

.data-card-body {
    padding: 2rem;
}

/* Enhanced Table */
.enhanced-table {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.enhanced-table thead th {
    border: none;
    padding: 1rem;
    font-weight: 600;
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
}

.enhanced-table tbody tr {
    transition: all 0.3s ease;
}

.table-row-hover:hover {
    background-color: rgba(102, 126, 234, 0.1);
    transform: scale(1.01);
}

.enhanced-table td {
    padding: 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #e9ecef;
}

.member-info strong {
    color: #333;
    font-weight: 600;
}

.amount-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    color: white;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.duration-badge {
    background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 15px;
    font-size: 0.7rem;
    font-weight: 600;
}

.type-badge {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 15px;
    font-size: 0.7rem;
    font-weight: 600;
}

.date-badge {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 15px;
    font-size: 0.7rem;
    font-weight: 600;
}

.action-buttons-inline {
    display: flex;
    gap: 0.5rem;
}

.btn-action {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    color: white;
}

.btn-action.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.btn-action.success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.btn-action.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    color: white;
}

/* Empty State */
.empty-state {
    padding: 3rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header {
        padding: 1.5rem;
    }
    
    .page-header h1 {
        font-size: 1.5rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .action-btn {
        width: 100%;
        justify-content: center;
    }
    
    .data-card-header {
        padding: 1.5rem;
    }
    
    .data-card-body {
        padding: 1rem;
    }
    
    .enhanced-table {
        font-size: 0.8rem;
    }
    
    .enhanced-table th,
    .enhanced-table td {
        padding: 0.75rem;
    }
    
    .action-buttons-inline {
        flex-direction: column;
        gap: 0.25rem;
    }
}

/* Enhanced Modal Styling */
.modal-content {
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    border: none;
    overflow: hidden;
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 1.5rem 2rem;
}

.modal-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
}

.btn-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    opacity: 0.8;
    font-size: 1.5rem;
    transition: all 0.3s ease;
}

.btn-close:hover {
    opacity: 1;
    background: rgba(255, 255, 255, 0.3);
}

.modal-body {
    padding: 2rem;
    background: #f8f9fa;
}

.modal-footer {
    background: white;
    border-top: 1px solid #e9ecef;
    padding: 1.5rem 2rem;
}

/* Enhanced Form Styling */
.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-control, .form-select {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: white;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    outline: none;
}

.form-control:hover, .form-select:hover {
    border-color: #dee2e6;
}

/* Enhanced Button Styling */
.btn {
    border-radius: 10px;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    transition: all 0.3s ease;
    border: none;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.9rem;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6c5ce7 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.btn-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
}

.btn-secondary:hover {
    background: linear-gradient(135deg, #5a6268 0%, #495057 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
}

.btn-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.btn-success:hover {
    background: linear-gradient(135deg, #0ea571 0%, #26d0ce 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(17, 153, 142, 0.3);
}

.btn-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.btn-warning:hover {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(240, 147, 251, 0.3);
}

.btn-danger {
    background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
    color: white;
}

.btn-danger:hover {
    background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(245, 87, 108, 0.3);
}

/* Alert Styling */
.alert {
    border: none;
    border-radius: 10px;
    padding: 1rem 1.5rem;
    margin-bottom: 1rem;
}

.alert-info {
    background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
    color: white;
}

.alert-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.alert-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

/* Form Group Enhancements */
.form-group {
    margin-bottom: 1.5rem;
}

.form-text {
    font-size: 0.85rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.input-group {
    border-radius: 10px;
    overflow: hidden;
}

.input-group-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-right: 2px solid rgba(255, 255, 255, 0.1);
}

/* Responsive Design */
@media (max-width: 768px) {
    .modal-body {
        padding: 1.5rem;
    }
    
    .modal-header {
        padding: 1rem 1.5rem;
    }
    
    .modal-title {
        font-size: 1.1rem;
    }
    
    .modal-footer {
        padding: 1rem 1.5rem;
    }
    
    .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .form-control, .form-select {
        padding: 0.6rem 0.8rem;
        font-size: 0.9rem;
    }
    
    .form-label {
        font-size: 0.85rem;
    }
}
</style>

<script>
function approveRequest(requestId) {
    document.getElementById('approveForm').action = `/admin/group-loans/requests/${requestId}/approve`;
    new bootstrap.Modal(document.getElementById('approveRequestModal')).show();
}

function rejectRequest(requestId) {
    document.getElementById('rejectForm').action = `/admin/group-loans/requests/${requestId}/reject`;
    new bootstrap.Modal(document.getElementById('rejectRequestModal')).show();
}

function viewDetails(requestId) {
    // You can implement a details modal or redirect to a details page
    window.location.href = `/admin/group-loans/requests/${requestId}`;
}

// Real-time loan calculation for direct loan modal
document.addEventListener('DOMContentLoaded', function() {
    const principalInput = document.querySelector('input[name="principal_amount"]');
    const rateInput = document.querySelector('input[name="interest_rate"]');
    const termInput = document.querySelector('input[name="loan_term_months"]');
    const totalInterestInput = document.getElementById('totalInterest');
    const totalAmountInput = document.getElementById('totalAmount');

    function calculateLoan() {
        const principal = parseFloat(principalInput.value) || 0;
        const rate = parseFloat(rateInput.value) || 0;
        const term = parseInt(termInput.value) || 0;

        if (principal > 0 && rate > 0 && term > 0) {
            const totalInterest = (principal * rate * term) / 100;
            const totalAmount = principal + totalInterest;
            
            totalInterestInput.value = totalInterest.toLocaleString('en-UG', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
            totalAmountInput.value = totalAmount.toLocaleString('en-UG', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        } else {
            totalInterestInput.value = '';
            totalAmountInput.value = '';
        }
    }

    if (principalInput && rateInput && termInput) {
        principalInput.addEventListener('input', calculateLoan);
        rateInput.addEventListener('input', calculateLoan);
        termInput.addEventListener('input', calculateLoan);
    }
});
</script>
@endsection
