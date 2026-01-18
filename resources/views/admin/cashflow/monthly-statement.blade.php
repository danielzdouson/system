@php
use Carbon\Carbon;
@endphp

@extends('layouts.admin')

@section('title', 'Monthly Cashflow Statement')

@push('styles')
<style>
.dropdown {
    position: relative;
}

.dropdown-toggle {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
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
    background: linear-gradient(135deg, #0ea571 0%, #26d0ce 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(17, 153, 142, 0.15);
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
</style>
@endpush

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
                                    <i class="fas fa-file-invoice me-3"></i>
                                    Monthly Cashflow Statement
                                </h1>
                                <p class="text-white fs-5 mb-0 opacity-90">
                                    <i class="fas fa-chart-pie me-2"></i>
                                    Generate monthly cashflow statements for analysis
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
        <div class="container-fluid px-4" style="max-width: 1800px; margin: 0 auto;">
            <!-- Statement Generator Form -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="action-card">
                        <div class="action-card-body">
                            <h5 class="action-title">
                                <i class="fas fa-cog me-2"></i>
                                Statement Generator
                            </h5>
                            <form method="GET" action="{{ route('admin.cashflow.monthly-statement') }}" class="row g-3">
                                <div class="col-md-4">
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
                                <div class="col-md-3">
                                    <label class="form-label">Month</label>
                                    <select name="month" class="form-select" required>
                                        <option value="">Select Month</option>
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                                {{ Carbon::create()->month($i)->format('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="action-btn primary">
                                        <i class="fas fa-search me-2"></i>
                                        Generate Statement
                                    </button>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    @if($statement)
                                        <div class="dropdown">
                                            <button class="action-btn success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-download me-2"></i>
                                                Export
                                                <i class="fas fa-chevron-down ms-2"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="{{ route('admin.cashflow.export.monthly', ['fiscal_year_id' => request('fiscal_year_id'), 'month' => request('month')]) }}" class="dropdown-item">
                                                        <i class="fas fa-file-excel me-2"></i>
                                                        Export to Excel
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('admin.cashflow.export.monthly.pdf', ['fiscal_year_id' => request('fiscal_year_id'), 'month' => request('month')]) }}" class="dropdown-item">
                                                        <i class="fas fa-file-pdf me-2"></i>
                                                        Export to PDF
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
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
                                    Complete transaction list for the selected period
                                </div>
                                <div class="mt-3">
                                    <a href="/admin/cashflow/export-monthly-pdf?fiscal_year_id={{ request('fiscal_year_id') }}&month={{ request('month') }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-file-pdf me-2"></i>
                                        Download PDF
                                    </a>
                                </div>
                            </div>
                            <div class="data-card-body">
                                @if(isset($statement['transactions']) && count($statement['transactions']) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Description</th>
                                                    <th>Category</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Member</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($statement['transactions'] as $transaction)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($transaction['transaction_date'])->format('M d, Y') }}</td>
                                                        <td>{{ $transaction['description'] ?? 'N/A' }}</td>
                                                        <td>{{ $transaction['category'] }}</td>
                                                        <td class="{{ $transaction['transaction_type'] == 'INFLOW' ? 'text-success' : 'text-danger' }}">
                                                            {{ $transaction['transaction_type'] == 'INFLOW' ? '+' : '-' }}UGX {{ number_format($transaction['amount'], 0) }}
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-{{ $transaction['status'] == 'CLEARED' ? 'success' : 'warning' }} text-white">
                                                                {{ $transaction['status'] }}
                                                            </span>
                                                        </td>
                                                        <td>
    @if(isset($transaction['member']))
        {{ $transaction['member']['name'] ?? ($transaction['member']['first_name'] . ' ' . ($transaction['member']['last_name'] ?? '')) }}
    @else
        N/A
    @endif
</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Summary Calculations -->
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h6 class="card-title mb-3">
                                                        <i class="fas fa-calculator me-2"></i>
                                                        Monthly Summary Calculations
                                                    </h6>
                                                    @php
                                                        $totalInflows = collect($statement['transactions'])->where('transaction_type', 'INFLOW')->sum('amount');
                                                        $totalOutflows = collect($statement['transactions'])->where('transaction_type', 'OUTFLOW')->sum('amount');
                                                        $netCashflow = $totalInflows - $totalOutflows;
                                                        $transactionCount = count($statement['transactions']);
                                                        $openingBalance = $statement['summary']['opening_balance'] ?? 0;
                                                        $closingBalance = $openingBalance + $netCashflow;
                                                    @endphp
                                                    <div class="row text-center">
                                                        <div class="col-md-3 col-sm-6 mb-3">
                                                            <div class="p-3 border rounded">
                                                                <h6 class="text-success mb-1">Total Inflows</h6>
                                                                <h4 class="text-success">UGX {{ number_format($totalInflows, 0) }}</h4>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-sm-6 mb-3">
                                                            <div class="p-3 border rounded">
                                                                <h6 class="text-danger mb-1">Total Outflows</h6>
                                                                <h4 class="text-danger">UGX {{ number_format($totalOutflows, 0) }}</h4>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-sm-6 mb-3">
                                                            <div class="p-3 border rounded">
                                                                <h6 class="text-primary mb-1">Net Cashflow</h6>
                                                                <h4 class="text-primary">UGX {{ number_format($netCashflow, 0) }}</h4>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-sm-6 mb-3">
                                                            <div class="p-3 border rounded">
                                                                <h6 class="text-info mb-1">Transaction Count</h6>
                                                                <h4 class="text-info">{{ $transactionCount }}</h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Opening and Closing Balance -->
                                                    <div class="row mt-3">
                                                        <div class="col-md-4 mb-3">
                                                            <div class="p-3 border rounded bg-white">
                                                                <h6 class="text-secondary mb-1">Opening Balance</h6>
                                                                <h5 class="text-secondary">UGX {{ number_format($openingBalance, 0) }}</h5>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <div class="p-3 border rounded bg-white">
                                                                <h6 class="text-primary mb-1">Monthly Net Change</h6>
                                                                <h5 class="text-primary">UGX {{ number_format($netCashflow, 0) }}</h5>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <div class="p-3 border rounded bg-white">
                                                                <h6 class="text-success mb-1">Closing Balance</h6>
                                                                <h5 class="text-success">UGX {{ number_format($closingBalance, 0) }}</h5>
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
                                        <p class="mb-0">No transactions found for Fiscal Year {{ $statement['fiscal_year'] }}, Month {{ $statement['month'] }}.</p>
                                        <p class="text-muted">Try selecting a different period or check if transactions have been recorded.</p>
                                    </div>
                                @endif
                                    Overall cash position for the period
                                </div>
                            </div>
                            <div class="data-card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="stat-item">
                                            <h6>Opening Balance</h6>
                                            <h4 class="text-primary">UGX {{ number_format($statement['summary']['opening_balance'], 0) }}</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stat-item">
                                            <h6>Net Cashflow</h6>
                                            <h4 class="{{ $statement['summary']['net_cashflow'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                UGX {{ number_format($statement['summary']['net_cashflow'], 0) }}
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stat-item">
                                            <h6>Closing Balance</h6>
                                            <h4 class="text-info">UGX {{ number_format($statement['summary']['closing_balance'], 0) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-12">
                        <div class="data-card">
                            <div class="data-card-body text-center py-5">
                                <i class="fas fa-chart-line fa-3x text-gray-400 mb-3"></i>
                                <h4 class="text-gray-600">Generate Statement</h4>
                                <p class="text-gray-500">Select fiscal year and month to generate monthly cashflow statement.</p>
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
/* Enhanced Action Cards */
.action-card {
    width: 230%;
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
    width: 230%;
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
    width: 230%;
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

.form-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

/* Action Buttons */
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

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

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
    .action-card,
    .data-card {
        width: 100%;
    }
    
    .data-card-header {
        padding: 1.5rem;
    }
    
    .data-card-body {
        padding: 1rem;
    }
    
    .stat-item h4 {
        font-size: 1.2rem;
    }
}
</style>
@endsection
