@extends('layouts.admin')

@section('title', 'All Loans')

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
                                    All Loans - {{ $activeFiscalYear->name }}
                                </h1>
                                <p class="text-white fs-5 mb-0 opacity-90">
                                    <i class="fas fa-eye me-2"></i>
                                    Complete overview of all loans in the system
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

    <div class="content" style="">
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
                                <a href="{{ route('admin.group-loans.index') }}" class="action-btn primary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Dashboard
                                </a>
                                <a href="{{ route('admin.group-loans.requests') }}" class="action-btn success">
                                    <i class="fas fa-list me-2"></i>
                                    Loan Requests
                                </a>
                                <a href="{{ route('admin.group-loans.reports') }}" class="action-btn warning">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    Reports
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="filter-card">
                        <div class="filter-card-body">
                            <h5 class="filter-title">
                                <i class="fas fa-filter me-2"></i>
                                Filter Loans
                            </h5>
                            <form method="GET" class="filter-form">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="">All Status</option>
                                                <option value="active">Active</option>
                                                <option value="completed">Completed</option>
                                                <option value="defaulted">Defaulted</option>
                                                <option value="suspended">Suspended</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Member</label>
                                            <input type="text" name="member" class="form-control" placeholder="Search member...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Min Amount</label>
                                            <input type="number" name="min_amount" class="form-control" placeholder="Min amount">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Max Amount</label>
                                            <input type="number" name="max_amount" class="form-control" placeholder="Max amount">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search me-2"></i>
                                            Apply Filters
                                        </button>
                                        <a href="{{ route('admin.group-loans.all') }}" class="btn btn-secondary">
                                            <i class="fas fa-redo me-2"></i>
                                            Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loans Table -->
            <div class="row">
                <div class="col-12">
                    <div class="data-card">
                        <div class="data-card-header">
                            <h5 class="data-title">
                                <i class="fas fa-list me-2"></i>
                                All Loans
                            </h5>
                            <div class="data-subtitle">
                                Complete list of all loans with their current status
                            </div>
                        </div>
                        <div class="data-card-body">
                            @if($loans->count() > 0)
                                <div class="table-responsive">
                                    <table class="enhanced-table">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>
                                                    <i class="fas fa-hashtag me-2"></i>
                                                    Loan #
                                                </th>
                                                <th>
                                                    <i class="fas fa-user me-2"></i>
                                                    Member
                                                </th>
                                                <th>
                                                    <i class="fas fa-money-bill-wave me-2"></i>
                                                    Principal
                                                </th>
                                                <th>
                                                    <i class="fas fa-percentage me-2"></i>
                                                    Rate
                                                </th>
                                                <th>
                                                    <i class="fas fa-calendar me-2"></i>
                                                    Disbursed
                                                </th>
                                                <th>
                                                    <i class="fas fa-balance-scale me-2"></i>
                                                    Balance
                                                </th>
                                                <th>
                                                    <i class="fas fa-chart-line me-2"></i>
                                                    Progress
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
                                            @foreach($loans as $loan)
                                                <tr class="table-row-hover">
                                                    <td>
                                                        <span class="loan-number">{{ $loan->loan_number }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="member-info">
                                                            @if($loan->member)
                                                                <a href="{{ route('admin.members.show', $loan->member->id) }}" class="member-link">
                                                                    <strong>{{ $loan->member->first_name }} {{ $loan->member->last_name }}</strong>
                                                                </a>
                                                            @else
                                                                <span class="text-muted">
                                                                    <strong>Unknown Member (ID: {{ $loan->member_id }})</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="amount-badge primary">
                                                            UGX {{ number_format($loan->principal_amount, 0) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="rate-badge">{{ $loan->interest_rate }}%</span>
                                                    </td>
                                                    <td>
                                                        <span class="date-badge">{{ $loan->disbursement_date->format('M d, Y') }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="balance-badge">
                                                            UGX {{ number_format($loan->balance, 0) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="progress-container">
                                                            <div class="progress">
                                                                <div class="progress-bar" role="progressbar" 
                                                                     style="width: {{ $loan->getProgressPercentage() }}%"
                                                                     aria-valuenow="{{ $loan->getProgressPercentage() }}" 
                                                                     aria-valuemin="0" aria-valuemax="100">
                                                                    {{ number_format($loan->getProgressPercentage(), 1) }}%
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        {!! $loan->getStatusBadge() !!}
                                                    </td>
                                                    <td>
                                                        <div class="action-buttons-inline">
                                                            <a href="{{ route('admin.group-loans.show', $loan) }}" 
                                                               class="btn-action primary" title="View Details">
                                                                <i class="fas fa-eye"></i>
                                                                <span>View</span>
                                                            </a>
                                                            @if($loan->loan_status === 'disbursed' && $loan->balance > 0)
                                                                <a href="{{ route('admin.group-loans.payment.form', [$loan->id, $loan->repaymentSchedules->where('status', 'pending')->first()->id ?? 0]) }}" 
                                                                   class="btn-action success" title="Record Payment">
                                                                    <i class="fas fa-money-bill"></i>
                                                                    <span>Pay</span>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $loans->links() }}
                                </div>
                            @else
                                <div class="empty-state text-center py-5">
                                    <i class="fas fa-list fa-4x text-muted mb-3"></i>
                                    <h4 class="text-muted">No Loans Found</h4>
                                    <p class="text-muted">No loans match your current filters.</p>
                                    <a href="{{ route('admin.group-loans.all') }}" class="btn btn-primary">
                                        <i class="fas fa-redo me-2"></i>
                                        Reset Filters
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Record Payment Modal -->
<div class="modal fade" id="recordPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-money-bill me-2"></i>
                    Record Loan Payment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="paymentForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="_method" value="POST">
                    <div class="mb-3">
                        <label class="form-label">Payment Amount (UGX)</label>
                        <input type="number" name="amount" class="form-control" min="100" step="100" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="savings_deduction">Savings Deduction</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Transaction Reference</label>
                        <input type="text" name="transaction_reference" class="form-control" placeholder="Optional reference number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Add any notes about this payment..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Record Payment</button>
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
    padding: 1rem;
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

.action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    color: white;
}

/* Filter Card */
.filter-card {
    width: 170%;
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 2rem;
}

.filter-card-body {
    padding: 2rem;
}

.filter-title {
    color: #333;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

/* Enhanced Data Card */
.data-card {
    width: 170%;
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow:hidden;
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

.loan-number {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
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

.rate-badge {
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

.balance-badge {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 15px;
    font-size: 0.7rem;
    font-weight: 600;
}

.progress-container {
    min-width: 100px;
}

.progress {
    height: 8px;
    border-radius: 4px;
    background-color: #e9ecef;
}

.progress-bar {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    border-radius: 4px;
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
        padding: 0.5rem;
    }
    
    .action-buttons-inline {
        flex-direction: column;
        gap: 0.25rem;
    }
}
</style>

<script>
function recordPayment(loanId) {
    document.getElementById('paymentForm').action = `/admin/loans/${loanId}/repayment`;
    new bootstrap.Modal(document.getElementById('recordPaymentModal')).show();
}
</script>
@endsection
