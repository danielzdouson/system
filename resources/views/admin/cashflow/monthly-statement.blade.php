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
        <div class="container-fluid">
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
                <div class="row">
                    <!-- Operating Activities -->
                    <div class="col-md-4 mb-4">
                        <div class="data-card">
                            <div class="data-card-header">
                                <h5 class="data-title">
                                    <i class="fas fa-cogs me-2"></i>
                                    Operating Activities
                                </h5>
                                <div class="data-subtitle">
                                    Day-to-day operations and member transactions
                                </div>
                            </div>
                            <div class="data-card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <h6 class="text-success">Inflows</h6>
                                        <h3 class="text-success">UGX {{ number_format($statement['operating_activities']['inflows'], 0) }}</h3>
                                    </div>
                                    <div class="col-6">
                                        <h6 class="text-danger">Outflows</h6>
                                        <h3 class="text-danger">UGX {{ number_format($statement['operating_activities']['outflows'], 0) }}</h3>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6 class="text-primary">Net Operating Cashflow</h6>
                                        <h3 class="{{ $statement['operating_activities']['net'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            UGX {{ number_format($statement['operating_activities']['net'], 0) }}
                                        </h3>
                                    </div>
                                </div>
                                @if(!empty($statement['operating_activities']['details']))
                                    <div class="mt-3">
                                        <p class="text-muted">No detailed operating transactions found.</p>
                                    </div>
                                @else
                                    <div class="table-responsive mt-3">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Subcategory</th>
                                                    <th>Inflows</th>
                                                    <th>Outflows</th>
                                                    <th>Net</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($statement['operating_activities']['details'] as $detail)
                                                    <tr>
                                                        <td>{{ $detail['subcategory'] }}</td>
                                                        <td class="text-success">UGX {{ number_format($detail['inflows'], 0) }}</td>
                                                        <td class="text-danger">UGX {{ number_format($detail['outflows'], 0) }}</td>
                                                        <td class="{{ $detail['net'] >= 0 ? 'text-success' : 'text-danger' }}">UGX {{ number_format($detail['net'], 0) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Investing Activities -->
                    <div class="col-md-4 mb-4">
                        <div class="data-card">
                            <div class="data-card-header">
                                <h5 class="data-title">
                                    <i class="fas fa-chart-line me-2"></i>
                                    Investing Activities
                                </h5>
                                <div class="data-subtitle">
                                    Investments and asset management
                                </div>
                            </div>
                            <div class="data-card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <h6 class="text-info">Inflows</h6>
                                        <h3 class="text-info">UGX {{ number_format($statement['investing_activities']['inflows'], 0) }}</h3>
                                    </div>
                                    <div class="col-6">
                                        <h6 class="text-warning">Outflows</h6>
                                        <h3 class="text-warning">UGX {{ number_format($statement['investing_activities']['outflows'], 0) }}</h3>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6 class="text-primary">Net Investing Cashflow</h6>
                                        <h3 class="{{ $statement['investing_activities']['net'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            UGX {{ number_format($statement['investing_activities']['net'], 0) }}
                                        </h3>
                                    </div>
                                </div>
                                @if(!empty($statement['investing_activities']['details']))
                                    <div class="mt-3">
                                        <p class="text-muted">No investing transactions found.</p>
                                    </div>
                                @else
                                    <div class="table-responsive mt-3">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Subcategory</th>
                                                    <th>Inflows</th>
                                                    <th>Outflows</th>
                                                    <th>Net</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($statement['investing_activities']['details'] as $detail)
                                                    <tr>
                                                        <td>{{ $detail['subcategory'] }}</td>
                                                        <td class="text-info">UGX {{ number_format($detail['inflows'], 0) }}</td>
                                                        <td class="text-warning">UGX {{ number_format($detail['outflows'], 0) }}</td>
                                                        <td class="{{ $detail['net'] >= 0 ? 'text-success' : 'text-danger' }}">UGX {{ number_format($detail['net'], 0) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Financing Activities -->
                    <div class="col-md-4 mb-4">
                        <div class="data-card">
                            <div class="data-card-header">
                                <h5 class="data-title">
                                    <i class="fas fa-hand-holding-usd me-2"></i>
                                    Financing Activities
                                </h5>
                                <div class="data-subtitle">
                                    Loans and repayments
                                </div>
                            </div>
                            <div class="data-card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <h6 class="text-warning">Inflows</h6>
                                        <h3 class="text-warning">UGX {{ number_format($statement['financing_activities']['inflows'], 0) }}</h3>
                                    </div>
                                    <div class="col-6">
                                        <h6 class="text-primary">Outflows</h6>
                                        <h3 class="text-primary">UGX {{ number_format($statement['financing_activities']['outflows'], 0) }}</h3>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6 class="text-info">Net Financing Cashflow</h6>
                                        <h3 class="{{ $statement['financing_activities']['net'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            UGX {{ number_format($statement['financing_activities']['net'], 0) }}
                                        </h3>
                                    </div>
                                </div>
                                @if(!empty($statement['financing_activities']['details']))
                                    <div class="mt-3">
                                        <p class="text-muted">No financing transactions found.</p>
                                    </div>
                                @else
                                    <div class="table-responsive mt-3">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Subcategory</th>
                                                    <th>Inflows</th>
                                                    <th>Outflows</th>
                                                    <th>Net</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($statement['financing_activities']['details'] as $detail)
                                                    <tr>
                                                        <td>{{ $detail['subcategory'] }}</td>
                                                        <td class="text-warning">UGX {{ number_format($detail['inflows'], 0) }}</td>
                                                        <td class="text-primary">UGX {{ number_format($detail['outflows'], 0) }}</td>
                                                        <td class="{{ $detail['net'] >= 0 ? 'text-success' : 'text-danger' }}">UGX {{ number_format($detail['net'], 0) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="col-md-12 mb-4">
                        <div class="data-card">
                            <div class="data-card-header">
                                <h5 class="data-title">
                                    <i class="fas fa-chart-pie me-2"></i>
                                    Cashflow Summary
                                </h5>
                                <div class="data-subtitle">
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
