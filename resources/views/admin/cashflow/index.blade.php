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
                                    <i class="fas fa-chart-line me-2"></i>
                                    <span>Monthly Statement</span>
                                </a>
                                <a href="{{ route('admin.cashflow.fiscal-year-statement') }}" class="action-btn success">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    <span>Fiscal Year Statement</span>
                                </a>
                                <a href="{{ route('admin.cashflow.create') }}" class="action-btn info">
                                    <i class="fas fa-plus-circle me-2"></i>
                                    <span>Add Transaction</span>
                                </a>
                                <button type="button" class="action-btn warning" onclick="bulkApprovePending()">
                                    <i class="fas fa-check-double me-2"></i>
                                    <span>Bulk Approve Pending</span>
                                </button>
                                <div class="dropdown">
                                    <button class="action-btn secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-download me-2"></i>
                                        <span>Export</span>
                                        <i class="fas fa-chevron-down ms-2"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route('admin.cashflow.export', request()->query()) }}" class="dropdown-item">
                                                <i class="fas fa-file-excel me-2"></i>
                                                Export Current View to Excel
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.cashflow.export.monthly', ['fiscal_year_id' => request('fiscal_year_id', date('Y')), 'month' => date('n')]) }}" class="dropdown-item">
                                                <i class="fas fa-file-excel me-2"></i>
                                                Export Current Month to Excel
                                            </a>
                                        </li>
                                    </ul>
                                </div>
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
                                                    <td>{!! $transaction->type_badge !!}</td>
                                                    <td>{!! $transaction->category_badge !!}</td>
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
                                                    <td>{!! $transaction->status_badge !!}</td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                <i class="fas fa-ellipsis-h"></i>
                                                                Actions
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                @if($transaction->status == 'PENDING' && auth()->user()->can('approve-cashflow'))
                                                                    <li>
                                                                        <a href="{{ route('admin.cashflow.approve', $transaction->id) }}" class="dropdown-item approve-action">
                                                                            <i class="fas fa-check-circle me-2"></i>
                                                                            Approve Transaction
                                                                        </a>
                                                                    </li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                @endif
                                                                <li>
                                                                    <a href="{{ route('admin.cashflow.show', $transaction->id) }}" class="dropdown-item view-action">
                                                                        <i class="fas fa-eye me-2"></i>
                                                                        View Details
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="{{ route('admin.cashflow.edit', $transaction->id) }}" class="dropdown-item edit-action">
                                                                        <i class="fas fa-edit me-2"></i>
                                                                        Edit Transaction
                                                                    </a>
                                                                </li>
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li>
                                                                    <form action="{{ route('admin.cashflow.destroy', $transaction->id) }}" method="POST" class="dropdown-item-form">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item delete-action text-danger" onclick="return confirm('Are you sure you want to delete this cashflow transaction? This action cannot be undone.')">
                                                                        <i class="fas fa-trash me-2"></i>
                                                                        Delete Transaction
                                                                    </button>
                                                                </form>
                                                                </li>
                                                            </ul>
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

/* Enhanced Action Buttons */
.action-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    align-items: center;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 12px;
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.action-btn:before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1));
    transition: left 0.5s ease;
}

.action-btn:hover:before {
    left: 100%;
}

.action-btn.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
}

.action-btn.primary:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6c5ce7 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
}

.action-btn.success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(17, 153, 142, 0.2);
}

.action-btn.success:hover {
    background: linear-gradient(135deg, #0ea571 0%, #26d0ce 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(17, 153, 142, 0.3);
}

.action-btn.info {
    background: linear-gradient(135deg, #17a2b8 0%, #4c1d95 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(23, 162, 184, 0.2);
}

.action-btn.info:hover {
    background: linear-gradient(135deg, #138496 0%, #312e81 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(23, 162, 184, 0.3);
}

.action-btn.warning {
    background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.2);
}

.action-btn.warning:hover {
    background: linear-gradient(135deg, #dc2626 0%, #f59e0b 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(245, 158, 11, 0.3);
}

.action-btn.secondary {
    background: linear-gradient(135deg, #6c757d 0%, #5a1a72 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.2);
}

.action-btn.secondary:hover {
    background: linear-gradient(135deg, #5a1a72 0%, #495057 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(108, 117, 125, 0.3);
}

/* Dropdown Styles */
.dropdown {
    position: relative;
}

.dropdown-toggle {
    background: linear-gradient(135deg, #6c757d 0%, #5a1a72 100%);
    border: none;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.dropdown-toggle:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #495057 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.15);
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border: 1px solid #e3e6f6;
    border-radius: 8px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    min-width: 200px;
    padding: 0.5rem 0;
    margin-top: 0.25rem;
}

.dropdown-item {
    display: block;
    padding: 0.75rem 1rem;
    color: #495057;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.2s ease;
    border: none;
    background: transparent;
    width: 100%;
    text-align: left;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
    color: #0d6efd;
}

.dropdown-item-form {
    padding: 0;
    margin: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .action-buttons {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .action-btn {
        width: 100%;
        justify-content: center;
    }
}

// Bulk approve functionality
function bulkApprovePending() {
    if (confirm('Are you sure you want to approve all pending cashflow transactions? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.cashflow.bulk-approve") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        if (!dropdown.contains(event.target)) {
            const menu = dropdown.querySelector('.dropdown-menu');
            if (menu) {
                menu.style.display = 'none';
            }
        }
    });
});

// Toggle dropdown menus
document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
    toggle.addEventListener('click', function(e) {
        e.stopPropagation();
        const menu = this.parentElement.querySelector('.dropdown-menu');
        if (menu) {
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }
    });
});
</style>
@endsection
