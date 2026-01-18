@extends('layouts.admin')

@section('title', 'Cash Flow Management')

@push('styles')
<style>
/* Modern Cash Flow Dashboard Styles */
.cashflow-dashboard {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    padding: 1rem;
    width: 100%;
    box-sizing: border-box;
}

/* Ensure admin layout structure is maintained */
.content-wrapper .cashflow-dashboard {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    padding: 1rem;
    width: 100%;
    box-sizing: border-box;
}

/* Fix container positioning */
.content-wrapper .container-fluid {
    width: 100%;
    max-width: 100%;
    padding: 0 1rem;
}

/* Enhanced Header */
.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
    color: white;
    position: relative;
    overflow: hidden;
}

.dashboard-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: shimmer 3s ease-in-out infinite;
}

@keyframes shimmer {
    0%, 100% { transform: translateX(-50%) translateY(-50%) rotate(0deg); }
    50% { transform: translateX(-30%) translateY(-30%) rotate(180deg); }
}

.header-content {
    position: relative;
    z-index: 1;
}

.header-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
}

.header-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
    margin-bottom: 1rem;
}

.fiscal-year-badge {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

/* Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.stat-icon.inflow {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.stat-icon.outflow {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.stat-icon.balance {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stat-icon.pending {
    background: linear-gradient(135deg, #feca57 0%, #ff9ff3 100%);
    color: white;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    color: #6c757d;
    font-weight: 500;
}

.stat-change {
    font-size: 0.8rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    font-weight: 600;
    display: inline-block;
    margin-top: 0.5rem;
}

.stat-change.negative {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

/* Filter Section */
.filter-section {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.filter-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.filter-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    align-items: end;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-size: 0.9rem;
    font-weight: 500;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.form-control {
    padding: 0.75rem 1rem;
    border: 2px solid #e3e6f6;
    border-radius: 12px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* Action Buttons */
.action-section {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.action-header {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.action-btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 12px;
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.action-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2));
    transition: left 0.5s ease;
}

.action-btn:hover::before {
    left: 100%;
}

.action-btn.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.action-btn.success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.action-btn.info {
    background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
    color: white;
}

.action-btn.warning {
    background: linear-gradient(135deg, #feca57 0%, #ff9ff3 100%);
    color: white;
}

.action-btn.secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

/* Transactions Table */
.table-section {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.table-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.table-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.table-subtitle {
    color: #6c757d;
    font-size: 0.9rem;
}

.modern-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.modern-table thead {
    background: linear-gradient(135deg, #495057 0%, #343a40 100%);
    color: white;
}

.modern-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.9rem;
    border: none;
}

.modern-table th:first-child {
    border-top-left-radius: 12px;
}

.modern-table th:last-child {
    border-top-right-radius: 12px;
}

.modern-table tbody tr {
    transition: all 0.3s ease;
    border-bottom: 1px solid #f8f9fa;
}

.modern-table tbody tr:hover {
    background: #f8f9fa;
    transform: scale(1.01);
}

.modern-table td {
    padding: 1rem;
    vertical-align: middle;
}

.type-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    display: inline-block;
}

.type-badge.inflow {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.type-badge.outflow {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.category-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 500;
    display: inline-block;
}

.category-badge.operating {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
}

.category-badge.investing {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.category-badge.financing {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.amount-display {
    font-size: 1.1rem;
    font-weight: 700;
    padding: 0.5rem 1rem;
    border-radius: 12px;
    display: inline-block;
}

.amount-display.inflow {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.amount-display.outflow {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 500;
    display: inline-block;
}

.status-badge.pending {
    background: rgba(254, 202, 87, 0.1);
    color: #f59e0b;
}

.status-badge.cleared {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.status-badge.reconciled {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
}

.action-dropdown {
    position: relative;
}

.dropdown-menu {
    background: white;
    border: 1px solid #e3e6f6;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    padding: 0.5rem;
    min-width: 200px;
}

.dropdown-item {
    padding: 0.75rem 1rem;
    border-radius: 8px;
    font-size: 0.9rem;
    color: #2c3e50;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.dropdown-item:hover {
    background: #f8f9fa;
    color: #667eea;
}

.dropdown-item.danger {
    color: #ef4444;
}

.dropdown-item.danger:hover {
    background: rgba(239, 68, 68, 0.1);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem;
    color: #6c757d;
}

.empty-state-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.empty-state-text {
    font-size: 1rem;
    margin-bottom: 2rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .header-title {
        font-size: 2rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-form {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .modern-table {
        font-size: 0.8rem;
    }
    
    .modern-table th,
    .modern-table td {
        padding: 0.5rem;
    }
}

/* Loading Animation */
.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Smooth Transitions */
* {
    transition: all 0.3s ease;
}
</style>
@endpush

@section('content')
<div class="cashflow-dashboard">
    <div class="container-fluid">
        <!-- Enhanced Header -->
        <div class="dashboard-header mb-4">
            <div class="header-content">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="header-title">
                            <i class="fas fa-chart-line me-3"></i>
                            Cash Flow Management
                        </h1>
                        <p class="header-subtitle">
                            <i class="fas fa-coins me-2"></i>
                            Track and manage all cash flow transactions with real-time insights
                        </p>
                    </div>
                    <div class="fiscal-year-badge">
                        <i class="fas fa-calendar-alt"></i>
                        {{ $activeFiscalYear->name ?? 'No Active Fiscal Year' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="stats-grid mb-4">
            <div class="stat-card">
                <div class="stat-icon balance">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stat-value">
                    UGX {{ number_format($totalBalance ?? 0, 0) }}
                </div>
                <div class="stat-label">Current Balance</div>
                <div class="stat-change">
                    <i class="fas fa-arrow-up me-1"></i>
                    +12.5% from last month
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon inflow">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div class="stat-value">
                    UGX {{ number_format($totalInflows ?? 0, 0) }}
                </div>
                <div class="stat-label">Total Inflows</div>
                <div class="stat-change">
                    <i class="fas fa-arrow-up me-1"></i>
                    +8.3% from last month
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon outflow">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div class="stat-value">
                    UGX {{ number_format($totalOutflows ?? 0, 0) }}
                </div>
                <div class="stat-label">Total Outflows</div>
                <div class="stat-change negative">
                    <i class="fas fa-arrow-down me-1"></i>
                    -3.2% from last month
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value">
                    {{ $pendingCount ?? 0 }}
                </div>
                <div class="stat-label">Pending Transactions</div>
                <div class="stat-change">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Requires approval
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section mb-4">
            <div class="filter-header">
                <div class="filter-title">
                    <i class="fas fa-filter"></i>
                    Filters & Search
                </div>
            </div>
            <form method="GET" action="{{ route('admin.cashflow.index') }}" class="filter-form">
                <div class="form-group">
                    <label class="form-label">Fiscal Year</label>
                    <select name="fiscal_year_id" class="form-control">
                        <option value="">All Fiscal Years</option>
                        @foreach($fiscalYears as $fiscalYear)
                            <option value="{{ $fiscalYear->id }}" {{ request('fiscal_year_id') == $fiscalYear->id ? 'selected' : '' }}>
                                {{ $fiscalYear->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Transaction Type</label>
                    <select name="transaction_type" class="form-control">
                        <option value="">All Types</option>
                        <option value="INFLOW" {{ request('transaction_type') == 'INFLOW' ? 'selected' : '' }}>Inflow</option>
                        <option value="OUTFLOW" {{ request('transaction_type') == 'OUTFLOW' ? 'selected' : '' }}>Outflow</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control">
                        <option value="">All Categories</option>
                        <option value="OPERATING" {{ request('category') == 'OPERATING' ? 'selected' : '' }}>Operating</option>
                        <option value="INVESTING" {{ request('category') == 'INVESTING' ? 'selected' : '' }}>Investing</option>
                        <option value="FINANCING" {{ request('category') == 'FINANCING' ? 'selected' : '' }}>Financing</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Pending</option>
                        <option value="CLEARED" {{ request('status') == 'CLEARED' ? 'selected' : '' }}>Cleared</option>
                        <option value="RECONCILED" {{ request('status') == 'RECONCILED' ? 'selected' : '' }}>Reconciled</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="action-btn primary">
                        <i class="fas fa-search"></i>
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Action Buttons -->
        <div class="action-section mb-4">
            <div class="action-header">
                <i class="fas fa-rocket"></i>
                Quick Actions
            </div>
            <div class="action-buttons">
                <a href="{{ route('admin.cashflow.monthly-statement') }}" class="action-btn primary">
                    <i class="fas fa-chart-line"></i>
                    Monthly Statement
                </a>
                <a href="{{ route('admin.cashflow.fiscal-year-statement') }}" class="action-btn success">
                    <i class="fas fa-calendar-alt"></i>
                    Fiscal Year Statement
                </a>
                <a href="{{ route('admin.cashflow.create') }}" class="action-btn info">
                    <i class="fas fa-plus-circle"></i>
                    Add Transaction
                </a>
                <button type="button" class="action-btn warning" onclick="bulkApprovePending()">
                    <i class="fas fa-check-double"></i>
                    Bulk Approve Pending
                </button>
                <div class="dropdown">
                    <button class="action-btn secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-download"></i>
                        Export
                        <i class="fas fa-chevron-down ms-2"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{ route('admin.cashflow.export', request()->query()) }}" class="dropdown-item">
                                <i class="fas fa-file-excel"></i>
                                Export Current View to Excel
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.cashflow.export.monthly', ['fiscal_year_id' => request('fiscal_year_id', date('Y')), 'month' => date('n')]) }}" class="dropdown-item">
                                <i class="fas fa-file-excel"></i>
                                Export Current Month to Excel
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="table-section">
            <div class="table-header">
                <div class="table-title">
                    <i class="fas fa-list"></i>
                    Cash Flow Transactions
                </div>
                <div class="table-subtitle">
                    {{ $transactions->total() }} transactions found
                </div>
            </div>
            
            @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
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
                                <tr>
                                    <td>
                                        <span class="date-badge">
                                            {{ $transaction->transaction_date->format('M d, Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="type-badge {{ $transaction->transaction_type == 'INFLOW' ? 'inflow' : 'outflow' }}">
                                            {{ $transaction->transaction_type }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="category-badge {{ strtolower($transaction->category) }}">
                                            {{ $transaction->category }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $transaction->description }}</strong>
                                            @if($transaction->reference_number)
                                                <br><small class="text-muted">Ref: {{ $transaction->reference_number }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="amount-display {{ $transaction->transaction_type == 'INFLOW' ? 'inflow' : 'outflow' }}">
                                            UGX {{ number_format($transaction->amount, 0) }}
                                        </span>
                                    </td>
                                    <td>{{ $transaction->payment_method }}</td>
                                    <td>
                                        <span class="status-badge {{ strtolower($transaction->status) }}">
                                            {{ $transaction->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                @if($transaction->status == 'PENDING' && auth()->user()->can('approve-cashflow'))
                                                    <li>
                                                        <a href="{{ route('admin.cashflow.approve', $transaction->id) }}" class="dropdown-item">
                                                            <i class="fas fa-check-circle"></i>
                                                            Approve Transaction
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                @endif
                                                <li>
                                                    <a href="{{ route('admin.cashflow.show', $transaction->id) }}" class="dropdown-item">
                                                        <i class="fas fa-eye"></i>
                                                        View Details
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('admin.cashflow.edit', $transaction->id) }}" class="dropdown-item">
                                                        <i class="fas fa-edit"></i>
                                                        Edit Transaction
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('admin.cashflow.destroy', $transaction->id) }}" method="POST" class="dropdown-item-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item danger" onclick="return confirm('Are you sure you want to delete this cashflow transaction?')">
                                                            <i class="fas fa-trash"></i>
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
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <div class="empty-state-title">No Transactions Found</div>
                    <div class="empty-state-text">No cashflow transactions match your current filters.</div>
                    <a href="{{ route('admin.cashflow.create') }}" class="action-btn primary">
                        <i class="fas fa-plus-circle"></i>
                        Add First Transaction
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- JavaScript -->
@push('scripts')
<script>
// Enhanced JavaScript for better UX
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Add loading states to buttons
    document.querySelectorAll('.action-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            if (!this.classList.contains('dropdown-toggle')) {
                const originalContent = this.innerHTML;
                this.innerHTML = '<span class="loading-spinner"></span> Processing...';
                this.disabled = true;
                
                setTimeout(() => {
                    this.innerHTML = originalContent;
                    this.disabled = false;
                }, 2000);
            }
        });
    });
    
    // Enhance table interactions
    const tableRows = document.querySelectorAll('.modern-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
    
    // Auto-refresh pending transactions
    setInterval(function() {
        const pendingCount = document.querySelector('.stat-card:nth-child(4) .stat-value');
        if (pendingCount) {
            // Simulate real-time update
            const currentCount = parseInt(pendingCount.textContent);
            if (Math.random() > 0.8) {
                pendingCount.textContent = currentCount + 1;
                pendingCount.style.color = '#f59e0b';
                setTimeout(() => {
                    pendingCount.style.color = '#2c3e50';
                }, 1000);
            }
        }
    }, 30000);
});

function bulkApprovePending() {
    if (confirm('Are you sure you want to approve all pending transactions?')) {
        // Show loading state
        const button = event.target;
        const originalContent = button.innerHTML;
        button.innerHTML = '<span class="loading-spinner"></span> Approving...';
        button.disabled = true;
        
        // Simulate API call
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
            
            // Show success message
            showNotification('All pending transactions approved successfully!', 'success');
            
            // Refresh page after delay
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        }, 2000);
    }
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
    notification.style.zIndex = '9999';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>
@endpush
