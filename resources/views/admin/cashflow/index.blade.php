@extends('layouts.admin')

@section('title', 'Cashflow Management')

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
                                    <i class="fas fa-chart-line me-3"></i>
                                    Cashflow Management
                                </h1>
                                <p class="text-white fs-5 mb-0 opacity-90">
                                    <i class="fas fa-coins me-2"></i>
                                    Track and manage all cash flow transactions
                                </p>
                            </div>
                            <div class="text-end">
                                <div class="month-badge">
                                    <span class="badge bg-white text-info fs-6 px-3 py-2">
                                        <i class="fas fa-calendar me-2"></i>
                                        {{ $activeFiscalYear->name ?? 'No Active Fiscal Year' }}
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
            <!-- Filters Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="action-card">
                        <div class="action-card-body">
                            <h5 class="action-title">
                                <i class="fas fa-filter me-2"></i>
                                Filters & Search
                            </h5>
                            <form method="GET" action="{{ route('admin.cashflow.index') }}" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Fiscal Year</label>
                                    <select name="fiscal_year_id" class="form-select">
                                        <option value="">All Fiscal Years</option>
                                        @foreach($fiscalYears as $fiscalYear)
                                            <option value="{{ $fiscalYear->id }}" {{ request('fiscal_year_id') == $fiscalYear->id ? 'selected' : '' }}>
                                                {{ $fiscalYear->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Transaction Type</label>
                                    <select name="transaction_type" class="form-select">
                                        <option value="">All Types</option>
                                        <option value="INFLOW" {{ request('transaction_type') == 'INFLOW' ? 'selected' : '' }}>Inflow</option>
                                        <option value="OUTFLOW" {{ request('transaction_type') == 'OUTFLOW' ? 'selected' : '' }}>Outflow</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Category</label>
                                    <select name="category" class="form-select">
                                        <option value="">All Categories</option>
                                        <option value="OPERATING" {{ request('category') == 'OPERATING' ? 'selected' : '' }}>Operating</option>
                                        <option value="INVESTING" {{ request('category') == 'INVESTING' ? 'selected' : '' }}>Investing</option>
                                        <option value="FINANCING" {{ request('category') == 'FINANCING' ? 'selected' : '' }}>Financing</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Pending</option>
                                        <option value="CLEARED" {{ request('status') == 'CLEARED' ? 'selected' : '' }}>Cleared</option>
                                        <option value="RECONCILED" {{ request('status') == 'RECONCILED' ? 'selected' : '' }}>Reconciled</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

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
                                <a href="{{ route('admin.cashflow.monthly-statement') }}" class="action-btn primary">
                                    <i class="fas fa-file-invoice me-2"></i>
                                    Monthly Statement
                                </a>
                                <a href="{{ route('admin.cashflow.fiscal-year-statement') }}" class="action-btn success">
                                    <i class="fas fa-file-alt me-2"></i>
                                    Fiscal Year Statement
                                </a>
                                <a href="{{ route('admin.cashflow.create') }}" class="action-btn info">
                                    <i class="fas fa-plus me-2"></i>
                                    Add Transaction
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="row">
                <div class="col-12">
                    <div class="data-card">
                        <div class="data-card-header">
                            <h5 class="data-title">
                                <i class="fas fa-list me-2"></i>
                                Cashflow Transactions
                            </h5>
                            <div class="data-subtitle">
                                {{ $transactions->total() }} transactions found
                            </div>
                        </div>
                        <div class="data-card-body">
                            @if($transactions->count() > 0)
                                <div class="table-responsive">
                                    <table class="enhanced-table">
                                        <thead class="table-dark">
                                            <tr>
                                                <th><i class="fas fa-calendar me-2"></i> Date</th>
                                                <th><i class="fas fa-exchange-alt me-2"></i> Type</th>
                                                <th><i class="fas fa-tag me-2"></i> Category</th>
                                                <th><i class="fas fa-comment me-2"></i> Description</th>
                                                <th><i class="fas fa-coins me-2"></i> Amount</th>
                                                <th><i class="fas fa-credit-card me-2"></i> Method</th>
                                                <th><i class="fas fa-info-circle me-2"></i> Status</th>
                                                <th><i class="fas fa-cogs me-2"></i> Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($transactions as $transaction)
                                                <tr class="table-row-hover">
                                                    <td>
                                                        <span class="date-badge">
                                                            {{ $transaction->transaction_date->format('M d, Y') }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $transaction->type_badge }}</td>
                                                    <td>{{ $transaction->category_badge }}</td>
                                                    <td>
                                                        <div>
                                                            <strong>{{ $transaction->description }}</strong>
                                                            @if($transaction->reference_number)
                                                                <br><small class="text-muted">Ref: {{ $transaction->reference_number }}</small>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="amount-badge {{ $transaction->transaction_type == 'INFLOW' ? 'success' : 'danger' }}">
                                                            UGX {{ number_format($transaction->amount, 0) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $transaction->payment_method }}</td>
                                                    <td>{{ $transaction->status_badge }}</td>
                                                    <td>
                                                        <div class="action-buttons-inline">
                                                            @if($transaction->status == 'PENDING' && auth()->user()->can('approve-cashflow'))
                                                                <a href="{{ route('admin.cashflow.approve', $transaction->id) }}" class="btn-action success">
                                                                    <i class="fas fa-check"></i>
                                                                </a>
                                                            @endif
                                                            <a href="{{ route('admin.cashflow.show', $transaction->id) }}" class="btn-action primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Pagination -->
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $transactions->links() }}
                                </div>
                            @else
                                <div class="empty-state text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-gray-400 mb-3"></i>
                                    <h4 class="text-gray-600">No Transactions Found</h4>
                                    <p class="text-gray-500">No cashflow transactions match your current filters.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Styles -->
<style>
/* Enhanced Page Header */
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2rem 0;
}

.page-header h1 {
    font-size: 2rem;
    font-weight: 700;
}

.page-header p {
    font-size: 1.1rem;
    opacity: 0.9;
}

/* Date Badge */
.date-badge {
    background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
}

/* Amount Badge */
.amount-badge.success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 700;
    font-size: 0.9rem;
}

.amount-badge.danger {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 700;
    font-size: 0.9rem;
}

/* Action Buttons Inline */
.action-buttons-inline {
    display: flex;
    gap: 0.5rem;
}

.btn-action {
    padding: 0.4rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-action.success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.btn-action.success:hover {
    background: linear-gradient(135deg, #0ea571 0%, #26d0ce 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(17, 153, 142, 0.3);
}

.btn-action.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-action.primary:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6c5ce7 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

/* Responsive Design */
@media (max-width: 768px) {
    .action-buttons {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .btn-action {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection
