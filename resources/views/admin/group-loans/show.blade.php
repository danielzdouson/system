@extends('layouts.admin')

@section('title', 'Loan Details')

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
                                    <i class="fas fa-eye me-3"></i>
                                    Loan Details - {{ $loan->loan_number ?? 'LN-' . $loan->id }}
                                </h1>
                                <p class="text-white fs-5 mb-0 opacity-90">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Complete loan information and payment schedule
                                </p>
                            </div>
                            <div class="text-end">
                                <div class="month-badge">
                                    <span class="badge bg-white text-success fs-6 px-3 py-2">
                                        <i class="fas fa-calendar me-2"></i>
                                        {{ $loan->disbursement_date->format('M d, Y') }}
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
            <!-- Loan Summary Card -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="data-card">
                        <div class="data-card-header">
                            <h5 class="data-title">
                                <i class="fas fa-hand-holding-usd me-2"></i>
                                Loan Summary
                            </h5>
                            <div class="data-subtitle">
                                Basic loan information and current status
                            </div>
                        </div>
                        <div class="data-card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Loan Number</label>
                                        <h5 class="text-primary">{{ $loan->loan_number ?? 'LN-' . $loan->id }}</h5>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Member</label>
                                        <h5>
                                            <a href="{{ route('admin.members.show', $loan->member->id) }}" class="member-link">
                                                {{ $loan->member->first_name }} {{ $loan->member->last_name }}
                                            </a>
                                        </h5>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Status</label>
                                        <div>{!! $loan->getStatusBadge() !!}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Principal Amount</label>
                                        <h5 class="text-primary">UGX {{ number_format($loan->loan_amount ?? $loan->principal_amount, 0) }}</h5>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Interest Rate</label>
                                        <h5>{{ $loan->interest_rate }}%</h5>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Loan Term</label>
                                        <h5>{{ $loan->loan_term ?? $loan->duration_months }} months</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Total Repayable</label>
                                        <h5 class="text-success">UGX {{ number_format($loan->total_repayment ?? $loan->total_repayable, 0) }}</h5>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Monthly Payment</label>
                                        <h5 class="text-info">UGX {{ number_format($loan->monthly_payment ?? $loan->monthly_installment, 0) }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Total Interest</label>
                                        <h5 class="text-warning">UGX {{ number_format($loan->total_interest, 0) }}</h5>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Balance</label>
                                        <h5 class="text-danger">UGX {{ number_format($loan->balance, 0) }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Disbursement Date</label>
                                        <h6>{{ $loan->disbursement_date->format('M d, Y') }}</h6>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">First Payment Date</label>
                                        <h6>{{ $loan->first_payment_date->format('M d, Y') }}</h6>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Maturity Date</label>
                                        <h6>{{ $loan->maturity_date->format('M d, Y') }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Purpose</label>
                                <p class="form-control-static">{{ $loan->loan_purpose ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Schedule -->
            <div class="row">
                <div class="col-12">
                    <div class="data-card">
                        <div class="data-card-header">
                            <h5 class="data-title">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Payment Schedule
                            </h5>
                            <div class="data-subtitle">
                                Detailed repayment schedule with payment status
                            </div>
                        </div>
                        <div class="data-card-body">
                            @if($loan->repaymentSchedules->count() > 0)
                                <div class="table-responsive">
                                    <table class="enhanced-table">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>
                                                    <i class="fas fa-hashtag me-2"></i>
                                                    Installment
                                                </th>
                                                <th>
                                                    <i class="fas fa-calendar me-2"></i>
                                                    Due Date
                                                </th>
                                                <th>
                                                    <i class="fas fa-money-bill-wave me-2"></i>
                                                    Principal Due
                                                </th>
                                                <th>
                                                    <i class="fas fa-percentage me-2"></i>
                                                    Interest Due
                                                </th>
                                                <th>
                                                    <i class="fas fa-calculator me-2"></i>
                                                    Total Due
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
                                            @foreach($loan->repaymentSchedules as $schedule)
                                                <tr class="table-row-hover">
                                                    <td>
                                                        <span class="installment-number">#{{ $schedule->installment_number }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="date-badge">{{ $schedule->due_date->format('M d, Y') }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="amount-badge primary">
                                                            UGX {{ number_format($schedule->principal_due, 0) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="amount-badge warning">
                                                            UGX {{ number_format($schedule->interest_due, 0) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="amount-badge success">
                                                            UGX {{ number_format($schedule->total_due, 0) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        {!! $schedule->getStatusBadge() !!}
                                                    </td>
                                                    <td>
                                                        <div class="action-buttons-inline">
                                                            @if($schedule->status === 'pending')
                                                                <a href="{{ route('admin.group-loans.payment.form', [$loan->id, $schedule->id]) }}" class="btn-action success" title="Record Payment">
                                                                    <i class="fas fa-money-check-alt"></i>
                                                                    <span>Pay</span>
                                                                </a>
                                                            @endif
                                                            <button class="btn-action primary" title="View Details">
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
                            @else
                                <div class="empty-state text-center py-5">
                                    <i class="fas fa-calendar-alt fa-4x text-muted mb-3"></i>
                                    <h4 class="text-muted">No Payment Schedule</h4>
                                    <p class="text-muted">Payment schedule has not been generated for this loan.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="action-card">
                        <div class="action-card-body">
                            <div class="action-buttons">
                                <a href="{{ route('admin.group-loans.index') }}" class="action-btn primary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Back to Dashboard
                                </a>
                                <a href="{{ route('admin.group-loans.all') }}" class="action-btn success">
                                    <i class="fas fa-list me-2"></i>
                                    All Loans
                                </a>
                                @if($loan->loan_status === 'disbursed' || $loan->status === 'active')
                                    <button class="action-btn warning">
                                        <i class="fas fa-money-check-alt me-2"></i>
                                        Record Payment
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Enhanced Page Header */
.container-fluid{
    width:500px;
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

.installment-number {
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
}

.amount-badge.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.amount-badge.success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.amount-badge.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.date-badge {
    background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
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

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    color: white;
}

/* Action Cards */
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

.action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
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
    
    .action-buttons {
        flex-direction: column;
    }
    
    .action-btn {
        width: 100%;
        justify-content: center;
    }
    
    .enhanced-table {
        font-size: 0.8rem;
    }
    
    .enhanced-table th,
    .enhanced-table td {
        padding: 0.5rem;
    }
}
</style>
@endsection
