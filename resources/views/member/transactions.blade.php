@extends('layouts.member')

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card gradient-bg text-white border-0 animate__animated animate__fadeInDown">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2 animate__animated animate__fadeInLeft">
                                <i class="fas fa-exchange-alt me-3"></i>Transaction History
                            </h2>
                            <p class="mb-0 opacity-75 animate__animated animate__fadeInLeft animate__delay-1s">
                                View and manage all your financial transactions
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="animate__animated animate__fadeInRight">
                                <a href="{{ route('member.dashboard') }}" class="btn btn-light">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm animate__animated animate__fadeInUp">
                <div class="card-header bg-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-filter text-primary me-2"></i>
                            Filter Transactions
                        </h5>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="exportTransactions('pdf')">
                                <i class="fas fa-file-pdf me-1"></i>Export PDF
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm" onclick="exportTransactions('excel')">
                                <i class="fas fa-file-excel me-1"></i>Export Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('member.transactions') }}" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-select" id="type" name="type">
                                    <option value="">All Types</option>
                                    <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>Deposit</option>
                                    <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                                    <option value="loan_disbursement" {{ request('type') == 'loan_disbursement' ? 'selected' : '' }}>Loan Disbursement</option>
                                    <option value="loan_repayment" {{ request('type') == 'loan_repayment' ? 'selected' : '' }}>Loan Repayment</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="search" class="form-label">Search</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="search" name="search" 
                                           placeholder="Search description..." value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="fas fa-search me-2"></i>Search
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()">
                                    <i class="fas fa-redo me-2"></i>Reset
                                </button>
                            </div>
                        </div>
                        
                        @if(request()->hasAny(['date_from', 'date_to', 'type', 'search']))
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="alert alert-info d-flex align-items-center" role="alert">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <div class="flex-grow-1">
                                            <strong>Filters Applied:</strong>
                                            @if(request('date_from')) <span class="badge bg-primary me-1">From: {{ request('date_from') }}</span> @endif
                                            @if(request('date_to')) <span class="badge bg-primary me-1">To: {{ request('date_to') }}</span> @endif
                                            @if(request('type')) <span class="badge bg-primary me-1">Type: {{ ucfirst(request('type')) }}</span> @endif
                                            @if(request('search')) <span class="badge bg-primary me-1">Search: {{ request('search') }}</span> @endif
                                        </div>
                                        <a href="{{ route('member.transactions') }}" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-times me-1"></i>Clear All
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Summary Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 animate__animated animate__fadeInUp">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Deposits</h6>
                            <h5 class="mb-0 text-success">UGX {{ number_format($transactions->where('transaction_type', 'deposit')->sum('amount'), 0) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 animate__animated animate__fadeInUp animate__delay-1s">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-3">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Withdrawals</h6>
                            <h5 class="mb-0 text-danger">UGX {{ number_format($transactions->where('transaction_type', 'withdrawal')->sum('amount'), 0) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 animate__animated animate__fadeInUp animate__delay-2s">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 text-info rounded-circle p-3">
                                <i class="fas fa-list"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Transactions</h6>
                            <h5 class="mb-0 text-info">{{ $transactions->total() }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 animate__animated animate__fadeInUp animate__delay-3s">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                                <i class="fas fa-balance-scale"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Net Balance</h6>
                            <h5 class="mb-0 text-warning">UGX {{ number_format($transactions->sum('amount'), 0) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm animate__animated animate__fadeInUp">
                <div class="card-header bg-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-table text-primary me-2"></i>
                            Transactions ({{ $transactions->total() }} records)
                        </h5>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" style="width: auto;" onchange="changePerPage(this.value)">
                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 per page</option>
                                <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25 per page</option>
                                <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50 per page</option>
                                <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100 per page</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover" id="transactionsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th onclick="sortTable('date')" style="cursor: pointer;">
                                            <i class="fas fa-calendar me-1"></i>Date 
                                            <i class="fas fa-sort text-muted"></i>
                                        </th>
                                        <th onclick="sortTable('description')" style="cursor: pointer;">
                                            Description <i class="fas fa-sort text-muted"></i>
                                        </th>
                                        <th onclick="sortTable('type')" style="cursor: pointer;">
                                            Type <i class="fas fa-sort text-muted"></i>
                                        </th>
                                        <th>Reference</th>
                                        <th onclick="sortTable('amount')" style="cursor: pointer;">
                                            Amount <i class="fas fa-sort text-muted"></i>
                                        </th>
                                        <th>Balance</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr class="transaction-row" data-type="{{ $transaction->transaction_type }}">
                                            <td>
                                                <div class="fw-medium">{{ $transaction->created_at->format('M j, Y') }}</div>
                                                <small class="text-muted">{{ $transaction->created_at->format('H:i:s') }}</small>
                                            </td>
                                            <td>
                                                <div class="fw-medium">{{ $transaction->description ?? 'Transaction' }}</div>
                                                @if($transaction->cashflowTransaction)
                                                    <small class="text-muted">{{ $transaction->cashflowTransaction->description }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ getTransactionTypeColor($transaction->transaction_type) }} badge-lg">
                                                    <i class="fas fa-{{ $transaction->transaction_type == 'deposit' ? 'arrow-down' : 'arrow-up' }} me-1"></i>
                                                    {{ formatTransactionType($transaction->transaction_type) }}
                                                </span>
                                            </td>
                                            <td>
                                                <code class="text-muted">{{ $transaction->reference_number ?? 'TXN' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</code>
                                            </td>
                                            <td>
                                                <div class="fw-bold {{ $transaction->transaction_type == 'deposit' ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->transaction_type == 'deposit' ? '+' : '-' }} UGX {{ number_format($transaction->amount, 0) }}
                                                </div>
                                            </td>
                                            <td>
                                                <strong>UGX {{ number_format($transaction->balance ?? 0, 0) }}</strong>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary" onclick="viewTransaction({{ $transaction->id }})" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary" onclick="downloadReceipt({{ $transaction->id }})" title="Download Receipt">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Enhanced Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of {{ $transactions->total() }} entries
                            </div>
                            <div>
                                {{ $transactions->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="bg-light rounded-circle p-4 d-inline-block mb-3">
                                <i class="fas fa-inbox fa-4x text-muted"></i>
                            </div>
                            <h5 class="text-muted mb-3">No transactions found</h5>
                            <p class="text-muted mb-4">
                                @if(request()->hasAny(['date_from', 'date_to', 'type', 'search']))
                                    Try adjusting your filters or 
                                    <a href="{{ route('member.transactions') }}" class="text-primary">clear all filters</a>
                                @else
                                    You don't have any transactions yet. Start by making a deposit or applying for a loan.
                                @endif
                            </p>
                            @if(request()->hasAny(['date_from', 'date_to', 'type', 'search']))
                                <a href="{{ route('member.transactions') }}" class="btn btn-primary">
                                    <i class="fas fa-times me-2"></i>Clear Filters
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function clearSearch() {
    document.getElementById('search').value = '';
    document.getElementById('filterForm').submit();
}

function resetFilters() {
    window.location.href = '{{ route('member.transactions') }}';
}

function changePerPage(perPage) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', perPage);
    window.location.href = url.toString();
}

function exportTransactions(format) {
    const url = new URL(window.location);
    url.searchParams.set('export', format);
    window.open(url.toString(), '_blank');
}

function viewTransaction(id) {
    // Implement modal or redirect to transaction details
    alert('Transaction details for ID: ' + id);
}

function downloadReceipt(id) {
    // Implement receipt download
    alert('Download receipt for transaction ID: ' + id);
}

function sortTable(column) {
    const url = new URL(window.location);
    const currentSort = url.searchParams.get('sort');
    const currentOrder = url.searchParams.get('order', 'asc');
    
    if (currentSort === column) {
        url.searchParams.set('order', currentOrder === 'asc' ? 'desc' : 'asc');
    } else {
        url.searchParams.set('sort', column);
        url.searchParams.set('order', 'asc');
    }
    
    window.location.href = url.toString();
}

// Add row highlighting on hover
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.transaction-row');
    rows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
});
</script>

@php
function getTransactionTypeColor($type) {
    $colors = [
        'deposit' => 'success',
        'withdrawal' => 'danger',
        'loan_disbursement' => 'info',
        'loan_repayment' => 'warning',
        'fine_payment' => 'danger',
        'welfare_contribution' => 'primary'
    ];
    return $colors[$type] ?? 'secondary';
}

function formatTransactionType($type) {
    $formatted = [
        'deposit' => 'Deposit',
        'withdrawal' => 'Withdrawal',
        'loan_disbursement' => 'Loan Disbursement',
        'loan_repayment' => 'Loan Repayment',
        'fine_payment' => 'Fine Payment',
        'welfare_contribution' => 'Welfare Contribution'
    ];
    return $formatted[$type] ?? ucfirst($type);
}
@endphp
@endsection
