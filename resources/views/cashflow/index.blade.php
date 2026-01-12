@extends('layouts.admin')

@section('title', 'Cash Flow Transactions')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-2 text-white">
                            <i class="fas fa-exchange-alt me-3"></i>
                            Cash Flow Transactions
                        </h1>
                        <p class="text-white fs-5 mb-0 opacity-90">
                            <i class="fas fa-list me-2"></i>
                            Individual Transaction Management
                        </p>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('admin.cashflow.monthly') }}" class="btn btn-light me-2">
                            <i class="fas fa-chart-line me-2"></i>
                            Monthly Dashboard
                        </a>
                        <a href="{{ route('admin.cashflow.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>
                            Add Transaction
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-arrow-up fa-2x"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="card-title">Total Income</h5>
                            <h3>UGX {{ number_format($transactions->where('type', 'income')->sum('amount'), 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-arrow-down fa-2x"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="card-title">Total Expenses</h5>
                            <h3>UGX {{ number_format($transactions->where('type', 'expense')->sum('amount'), 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-balance-scale fa-2x"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="card-title">Net Balance</h5>
                            <h3>UGX {{ number_format($transactions->where('type', 'income')->sum('amount') - $transactions->where('type', 'expense')->sum('amount'), 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-receipt fa-2x"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="card-title">Total Transactions</h5>
                            <h3>{{ $transactions->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-filter me-2"></i>
                        Filter Transactions
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.cashflow.index') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="type" class="form-label">Type</label>
                                <select name="type" id="type" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Income</option>
                                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expense</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="category" class="form-label">Category</label>
                                <select name="category" id="category" class="form-select">
                                    <option value="">All Categories</option>
                                    @foreach($categories['income'] as $key => $label)
                                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                    @foreach($categories['expense'] as $key => $label)
                                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">To Date</label>
                                <div class="input-group">
                                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="form-control">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                        @if(request()->query->count() > 0)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <a href="{{ route('admin.cashflow.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Clear Filters
                                    </a>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>
                        Cash Flow Transactions
                    </h5>
                    <div>
                        <a href="{{ route('admin.cashflow.download', request()->query()) }}" class="btn btn-success">
                            <i class="fas fa-download me-2"></i>
                            Export to Excel
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Category</th>
                                        <th>Type</th>
                                        <th>Payment Method</th>
                                        <th class="text-end">Amount</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->transaction_date->format('M d, Y') }}</td>
                                            <td>{{ $transaction->description }}</td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $categories[$transaction->type][$transaction->category] ?? $transaction->category }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($transaction->type == 'income')
                                                    <span class="badge bg-success">Income</span>
                                                @else
                                                    <span class="badge bg-danger">Expense</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $paymentMethods[$transaction->payment_method] ?? $transaction->payment_method }}
                                                </span>
                                            </td>
                                            <td class="text-end fw-bold">
                                                @if($transaction->type == 'income')
                                                    <span class="text-success">+UGX {{ number_format($transaction->amount, 0) }}</span>
                                                @else
                                                    <span class="text-danger">-UGX {{ number_format($transaction->amount, 0) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.cashflow.edit', $transaction->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.cashflow.destroy', $transaction->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this transaction?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <span class="text-muted">
                                    Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of {{ $transactions->total() }} transactions
                                </span>
                            </div>
                            <div>
                                {{ $transactions->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No transactions found</h5>
                            <p class="text-muted">
                                @if(request()->query->count() > 0)
                                    No transactions match your current filters. 
                                    <a href="{{ route('admin.cashflow.index') }}" class="text-primary">Clear filters</a> to see all transactions.
                                @else
                                    No cash flow transactions have been recorded yet. 
                                    <a href="{{ route('admin.cashflow.create') }}" class="text-primary">Add your first transaction</a> to get started.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 2rem;
    color: white;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-3px);
}

.table th {
    border-top: none;
    font-weight: 600;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

/* Smaller pagination buttons */
.pagination {
    margin-bottom: 0;
}

.pagination .page-link {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.2;
    border-radius: 0.25rem;
    min-width: auto;
    height: auto;
}

.pagination .page-item {
    margin: 0 1px;
}

.pagination .page-item:first-child .page-link,
.pagination .page-item:last-child .page-link {
    padding: 0.25rem 0.6rem;
}
</style>
@endsection
