@extends('layouts.admin')

@section('title', 'Fiscal Year Cashflow Statement')

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
                                    <i class="fas fa-file-alt me-3"></i>
                                    Fiscal Year Cashflow Statement
                                </h1>
                                <p class="text-white fs-5 mb-0 opacity-90">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    Comprehensive annual cashflow analysis and reporting
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
        <div class="container-fluid px-4" style="max-width: 1400px; margin: 0 auto;">
            <!-- Statement Generator Form -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="action-card">
                        <div class="action-card-body">
                            <h5 class="action-title">
                                <i class="fas fa-cog me-2"></i>
                                Statement Generator
                            </h5>
                            <form method="GET" action="{{ route('admin.cashflow.fiscal-year-statement') }}" class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label">Fiscal Year</label>
                                    <select name="fiscal_year_id" class="form-select" required>
                                        <option value="">Select Fiscal Year</option>
                                        @foreach($fiscalYears as $fiscalYear)
                                            <option value="{{ $fiscalYear->id }}" {{ request('fiscal_year_id') == $fiscalYear->id ? 'selected' : '' }}>
                                                {{ $fiscalYear->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="action-btn primary">
                                        <i class="fas fa-search me-2"></i>
                                        Generate Statement
                                    </button>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    @if($statement)
                                        <a href="{{ route('admin.cashflow.export.fiscal-year', ['fiscal_year_id' => request('fiscal_year_id')]) }}" class="action-btn success">
                                            <i class="fas fa-download me-2"></i>
                                            Export PDF
                                        </a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statement Display -->
            @if($statement)
                <!-- Transaction List -->
                <div class="row">
                    <div class="col-12">
                        <div class="data-card">
                            <div class="data-card-header">
                                <h5 class="data-title">
                                    <i class="fas fa-list me-2"></i>
                                    All Transactions for {{ $statement['period'] }}
                                </h5>
                                <div class="data-subtitle">
                                    Complete transaction list for the fiscal year
                                </div>
                                <div class="mt-3">
                                    <a href="/admin/cashflow/export-fiscal-year?fiscal_year_id={{ request('fiscal_year_id') }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-file-pdf me-2"></i>
                                        Download PDF
                                    </a>
                                </div>
                            </div>
                            <div class="data-card-body">
                                @php
                                    // Combine all transactions from all categories
                                    $allTransactions = [];
                                    
                                    // Get operating transactions
                                    if (isset($statement['operating_activities']['details'])) {
                                        foreach ($statement['operating_activities']['details'] as $detail) {
                                            foreach ($detail['transactions'] as $transaction) {
                                                $allTransactions[] = $transaction;
                                            }
                                        }
                                    }
                                    
                                    // Get investing transactions
                                    if (isset($statement['investing_activities']['details'])) {
                                        foreach ($statement['investing_activities']['details'] as $detail) {
                                            foreach ($detail['transactions'] as $transaction) {
                                                $allTransactions[] = $transaction;
                                            }
                                        }
                                    }
                                    
                                    // Get financing transactions
                                    if (isset($statement['financing_activities']['details'])) {
                                        foreach ($statement['financing_activities']['details'] as $detail) {
                                            foreach ($detail['transactions'] as $transaction) {
                                                $allTransactions[] = $transaction;
                                            }
                                        }
                                    }
                                    
                                    // Sort transactions by date
                                    usort($allTransactions, function($a, $b) {
                                        return strtotime($b['transaction_date']) - strtotime($a['transaction_date']);
                                    });
                                @endphp
                                
                                @if(count($allTransactions) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Description</th>
                                                    <th>Category</th>
                                                    <th>Subcategory</th>
                                                    <th>Amount</th>
                                                    <th>Type</th>
                                                    <th>Status</th>
                                                    <th>Member</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($allTransactions as $transaction)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($transaction['transaction_date'])->format('M d, Y') }}</td>
                                                        <td>{{ $transaction['description'] }}</td>
                                                        <td>{{ $transaction['category'] }}</td>
                                                        <td>{{ $transaction['subcategory'] }}</td>
                                                        <td class="{{ $transaction['transaction_type'] == 'INFLOW' ? 'text-success' : 'text-danger' }}">
                                                            UGX {{ number_format($transaction['amount'], 0) }}
                                                        </td>
                                                        <td>
                                                            <span class="badge {{ $transaction['transaction_type'] == 'INFLOW' ? 'bg-success' : 'bg-danger' }}">
                                                                {{ $transaction['transaction_type'] }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge {{ $transaction['status'] == 'CLEARED' ? 'bg-success' : 'bg-warning' }}">
                                                                {{ $transaction['status'] }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if(isset($transaction['member']))
                                                                {{ $transaction['member']['first_name'] ?? '' }} {{ $transaction['member']['last_name'] ?? '' }}
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Fiscal Year Summary Calculations -->
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h6 class="card-title mb-3">
                                                        <i class="fas fa-calculator me-2"></i>
                                                        Fiscal Year Summary Calculations
                                                    </h6>
                                                    @php
                                                        $totalInflows = collect($allTransactions)->where('transaction_type', 'INFLOW')->sum('amount');
                                                        $totalOutflows = collect($allTransactions)->where('transaction_type', 'OUTFLOW')->sum('amount');
                                                        $netCashflow = $totalInflows - $totalOutflows;
                                                        $transactionCount = count($allTransactions);
                                                    @endphp
                                                    <div class="row text-center">
                                                        <div class="col-md-3 col-sm-6 mb-3">
                                                            <div class="summary-calc-card text-success">
                                                                <h6 class="text-success mb-1">Total Inflows</h6>
                                                                <h4 class="text-success">UGX {{ number_format($totalInflows, 0) }}</h4>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-sm-6 mb-3">
                                                            <div class="summary-calc-card text-danger">
                                                                <h6 class="text-danger mb-1">Total Outflows</h6>
                                                                <h4 class="text-danger">UGX {{ number_format($totalOutflows, 0) }}</h4>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-sm-6 mb-3">
                                                            <div class="summary-calc-card text-primary">
                                                                <h6 class="text-primary mb-1">Net Cashflow</h6>
                                                                <h4 class="text-primary">UGX {{ number_format($netCashflow, 0) }}</h4>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-sm-6 mb-3">
                                                            <div class="summary-calc-card text-info">
                                                                <h6 class="text-info mb-1">Transaction Count</h6>
                                                                <h4 class="text-info">{{ $transactionCount }}</h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <h5><i class="fas fa-exclamation-triangle me-2"></i>No Transactions Found</h5>
                                        <p class="mb-0">No transactions found for Fiscal Year {{ $statement['fiscal_year'] }}.</p>
                                        <p class="text-muted">Try selecting a different fiscal year or check if transactions have been recorded.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-12">
                        <div class="data-card">
                            <div class="data-card-body text-center py-5">
                                <i class="fas fa-chart-bar fa-3x text-gray-400 mb-3"></i>
                                <h4 class="text-gray-600">Generate Statement</h4>
                                <p class="text-gray-500">Select fiscal year to generate comprehensive cashflow statement.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Enhanced Styles -->
<style>
/* Stat Items */
.stat-item {
    text-align: center;
    padding: 1rem;
}

.stat-item h6 {
    font-size: 0.9rem;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.stat-item h4 {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .data-card-body {
        padding: 1rem;
    }
    
    .stat-item h4 {
        font-size: 1.2rem;
    }
}
</style>
@endsection

<!-- Enhanced Styles -->
<style>
/* Enhanced Action Cards */
.action-card {
    width: 120%;
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.action-card-body {
    padding: 1rem;
}

.action-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Enhanced Data Card */
.data-card {
    width: 120%;
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    padding: 0;
    margin-bottom: 2rem;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    min-height: 400px;
    display: flex;
    flex-direction: column;
}

.data-card.summary-card {
    width: 120%;
    margin: 0 auto;
}

.data-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.data-card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    text-align: center;
    flex-shrink: 0;
}

.data-card-body {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

/* Form Controls */
.form-select {
    border: 2px solid #e3e6f6;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 12px;
    padding: 0.75rem 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

/* Enhanced Table Styling */
.table {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    border: 1px solid #e3e6f6;
}

.table thead th {
    background: linear-gradient(135deg, #f8f9ff 0%, #e8ecff 100%);
    color: #4a5568;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 1rem 0.75rem;
    border-bottom: 2px solid #667eea;
    white-space: nowrap;
}

.table tbody td {
    padding: 0.875rem 0.75rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f3f9;
    font-size: 0.9rem;
    line-height: 1.5;
}

.table tbody tr {
    transition: all 0.2s ease;
    background: white;
}

.table tbody tr:hover {
    background: #f8f9ff;
    transform: translateX(3px);
}

.table tbody tr:last-child td {
    border-bottom: none;
}

.table .badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.625rem;
    border-radius: 6px;
    font-weight: 500;
}

.table-responsive {
    border-radius: 12px;
    overflow: hidden;
}

/* Transaction amount styling */
.table td.text-success {
    font-weight: 600;
    color: #10b981 !important;
}

.table td.text-danger {
    font-weight: 600;
    color: #ef4444 !important;
}

/* Status badges */
.badge.bg-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
}

.badge.bg-danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
}

.badge.bg-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
}

/* Enhanced Summary Calculations */
.card.bg-light {
    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%) !important;
    border: 1px solid #e3e6f6;
    border-radius: 16px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
    overflow: hidden;
}

.card.bg-light .card-body {
    padding: 2rem;
}

.card.bg-light .card-title {
    color: #4a5568;
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card.bg-light .card-title i {
    color: #667eea;
}

/* Summary Calculation Cards */
.summary-calc-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid #e3e6f6;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.summary-calc-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.summary-calc-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(102, 126, 234, 0.15);
    border-color: #667eea;
}

.summary-calc-card:hover::before {
    opacity: 1;
}

.summary-calc-card h6 {
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.75rem;
    opacity: 0.8;
}

.summary-calc-card h4 {
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
}

/* Color Variants */
.summary-calc-card.text-success h6 {
    color: #10b981;
}

.summary-calc-card.text-success h4 {
    color: #059669;
}

.summary-calc-card.text-danger h6 {
    color: #ef4444;
}

.summary-calc-card.text-danger h4 {
    color: #dc2626;
}

.summary-calc-card.text-primary h6 {
    color: #3b82f6;
}

.summary-calc-card.text-primary h4 {
    color: #2563eb;
}

.summary-calc-card.text-info h6 {
    color: #06b6d4;
}

.summary-calc-card.text-info h4 {
    color: #0891b2;
}

/* White Background Cards */
.summary-calc-card.bg-white {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    border: 1px solid #e3e6f6;
}

.summary-calc-card.bg-white h6 {
    color: #64748b;
}

.summary-calc-card.bg-white h5 {
    color: #1e293b;
    font-size: 1.4rem;
    font-weight: 600;
}

/* Responsive Design */
@media (max-width: 768px) {
    .data-card-body {
        padding: 1rem;
    }
    
    .stat-item h4 {
        font-size: 1.2rem;
    }
}
