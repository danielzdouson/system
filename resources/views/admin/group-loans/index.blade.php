@extends('layouts.admin')

@section('title', 'Loan Management')

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
                                    <i class="fas fa-hand-holding-usd me-3"></i>
                                    Loan Management - {{ $currentFiscalYear->name }}
                                </h1>
                                <p class="text-white fs-5 mb-0 opacity-90">
                                    <i class="fas fa-chart-line me-2"></i>
                                    Comprehensive loan management system for {{ $currentFiscalYear->name }}
                                </p>
                            </div>
                            <div class="text-end">
                                <div class="month-badge">
                                    <span class="badge bg-white text-success fs-6 px-3 py-2">
                                        <i class="fas fa-calendar me-2"></i>
                                        {{ $currentFiscalYear->name }}
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
            <!-- Enhanced Loan Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card primary-gradient">
                    <div class="stat-card-body">
                        <div class="stat-icon">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <div class="stat-content">
                            <h4 class="stat-title">Total Loans</h4>
                            <h2 class="stat-number">{{ $totalLoans }}</h2>
                        </div>
                        <div class="stat-trend">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card success-gradient">
                    <div class="stat-card-body">
                        <div class="stat-icon">
                            <i class="fas fa-play-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h4 class="stat-title">Active Loans</h4>
                            <h2 class="stat-number">{{ $activeLoans }}</h2>
                        </div>
                        <div class="stat-trend">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card info-gradient">
                    <div class="stat-card-body">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h4 class="stat-title">Completed</h4>
                            <h2 class="stat-number">{{ $completedLoans }}</h2>
                        </div>
                        <div class="stat-trend">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card warning-gradient">
                    <div class="stat-card-body">
                        <div class="stat-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-content">
                            <h4 class="stat-title">Defaulted</h4>
                            <h2 class="stat-number">{{ $defaultedLoans }}</h2>
                        </div>
                        <div class="stat-trend">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Financial Summary -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="stat-card primary-gradient">
                        <div class="stat-card-body">
                            <div class="stat-icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="stat-content">
                                <h4 class="stat-title">Total Disbursed</h4>
                                <h2 class="stat-number">UGX {{ number_format($totalDisbursed, 0) }}</h2>
                            </div>
                            <div class="stat-trend">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="stat-card success-gradient">
                        <div class="stat-card-body">
                            <div class="stat-icon">
                                <i class="fas fa-piggy-bank"></i>
                            </div>
                            <div class="stat-content">
                                <h4 class="stat-title">Total Repaid</h4>
                                <h2 class="stat-number">UGX {{ number_format($totalRepaid, 0) }}</h2>
                            </div>
                            <div class="stat-trend">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="stat-card warning-gradient">
                        <div class="stat-card-body">
                            <div class="stat-icon">
                                <i class="fas fa-balance-scale"></i>
                            </div>
                            <div class="stat-content">
                                <h4 class="stat-title">Outstanding</h4>
                                <h2 class="stat-number">UGX {{ number_format($totalOutstanding, 0) }}</h2>
                            </div>
                            <div class="stat-trend">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Action Buttons -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="action-card">
                        <div class="action-card-body">
                            <h5 class="action-title">
                                <i class="fas fa-rocket me-2"></i>
                                Quick Actions
                            </h5>
                            <div class="action-buttons">
                                <a href="{{ route('admin.group-loans.requests') }}" class="action-btn primary">
                                    <i class="fas fa-list me-2"></i>
                                    Loan Requests
                                </a>
                                <a href="{{ route('admin.group-loans.all') }}" class="action-btn success">
                                    <i class="fas fa-eye me-2"></i>
                                    All Loans
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

            <!-- Recent Loans Table -->
            <div class="row" >
                <div class="col-12" >
                    <div class="data-card" >
                        <div class="data-card-header">
                            <h5 class="data-title">
                                <i class="fas fa-history me-2"></i>
                                Recent Loans
                            </h5>
                            <div class="data-subtitle">
                                Latest loan disbursements and activities
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
                                                    Amount
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
                                                                <strong>{{ $loan->member->first_name }} {{ $loan->member->last_name }}</strong>
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
                                                        {!! $loan->getStatusBadge() !!}
                                                    </td>
                                                    <td>
                                                        <div class="action-buttons-inline">
                                                            <a href="{{ route('admin.group-loans.show', $loan) }}" 
                                                               class="btn-action primary" title="View Details">
                                                                <i class="fas fa-eye"></i>
                                                                <span>View</span>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($loans->hasPages())
                                    <div class="d-flex justify-content-center mt-4">
                                        {{ $loans->links() }}
                                    </div>
                                @endif
                            @else
                                <div class="empty-state text-center py-5">
                                    <i class="fas fa-hand-holding-usd fa-4x text-muted mb-3"></i>
                                    <h4 class="text-muted">No Loans Yet</h4>
                                    <p class="text-muted">No loans have been disbursed in this fiscal year.</p>
                                    <a href="{{ route('admin.group-loans.requests') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>
                                        View Loan Requests
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

<style>
/* Enhanced Page Header */
.content{
    width: 900px;
}

.container-fluid {
    width: 850px;
    margin: 0 auto;
    padding: 2rem;
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

/* Enhanced Statistics Cards */
.stat-card {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
}

.stat-card-body {
    padding: 0.75rem;
    position: relative;
    display: flex;
    align-items: center;
    color: white;
}

.stat-icon {
    font-size: 1.2rem;
    opacity: 0.8;
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
}

.stat-content {
    flex: 1;
    z-index: 1;
}

.stat-title {
    font-size: 0.7rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    opacity: 0.9;
}

.stat-number {
    font-size: 1rem;
    font-weight: 700;
    margin: 0;
}

.stat-trend {
    position: absolute;
    top: 0.5rem;
    right: 0.75rem;
    font-size: 0.7rem;
    opacity: 0.6;
}

.primary-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.success-gradient {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.info-gradient {
    background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
}

.warning-gradient {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

/* Enhanced Action Cards */
.action-card {
    width: 100%;
    max-width: 100%;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    margin-bottom: 1rem;
}

.action-card-body {
    padding: 1rem;
}

.action-title {
    color: #333;
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.action-buttons {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.action-btn {
    padding: 0.75rem 1.25rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    color: white;
    font-size: 0.85rem;
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

/* Enhanced Data Card */
.data-card {
    width: 100%;
    max-width: 100%;
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
    width: 100%;
    max-width: 100%;
    min-width: auto;
    table-layout: auto;
}

.table-responsive {
    overflow-x: auto;
    max-width: 100%;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.enhanced-table thead th {
    border: none;
    padding: 0.5rem 0.75rem;
    font-weight: 600;
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    white-space: nowrap;
    text-align: left;
    min-width: 60px;
    font-size: 0.85rem;
}

.enhanced-table thead th:nth-child(1) { min-width: 60px; } /* Loan # */
.enhanced-table thead th:nth-child(2) { min-width: 150px; } /* Member */
.enhanced-table thead th:nth-child(3) { min-width: 80px; } /* Amount */
.enhanced-table thead th:nth-child(4) { min-width: 60px; } /* Rate */
.enhanced-table thead th:nth-child(5) { min-width: 80px; } /* Disbursed */
.enhanced-table thead th:nth-child(6) { min-width: 70px; } /* Status */
.enhanced-table thead th:nth-child(7) { min-width: 70px; } /* Actions */

.enhanced-table tbody tr {
    transition: all 0.3s ease;
}

.table-row-hover:hover {
    background-color: rgba(102, 126, 234, 0.1);
    transform: scale(1.01);
}

.enhanced-table td {
    padding: 0.5rem 0.75rem;
    vertical-align: middle;
    border-bottom: 1px solid #e9ecef;
    white-space: normal;
    overflow: visible;
    text-overflow: clip;
    max-width: none;
    font-size: 0.85rem;
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
}

.amount-badge.primary {
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
    
    .stat-number {
        font-size: 1rem;
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

/* Grid Layout for Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.stats-grid .stat-card {
    min-height: 120px;
    padding: 1rem;
}

@media (max-width: 1200px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .stat-card-body {
        padding: 1rem;
    }
    
    .stat-icon {
        font-size: 2rem;
    }
    
    .stat-title {
        font-size: 0.8rem;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .stat-card-body {
        padding: 1.2rem;
    }
    
    .stat-icon {
        font-size: 2.2rem;
    }
    
    .stat-title {
        font-size: 0.85rem;
    }
    
    .stat-number {
        font-size: 1.6rem;
    }
}
</style>
@endsection
