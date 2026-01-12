@extends('layouts.admin')

@section('title', 'Loan Reports')

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
                                    <i class="fas fa-chart-bar me-3"></i>
                                    Loan Reports - {{ $activeFiscalYear->name }}
                                </h1>
                                <p class="text-white fs-5 mb-0 opacity-90">
                                    <i class="fas fa-analytics me-2"></i>
                                    Comprehensive loan analytics and reporting
                                </p>
                            </div>
                            <div class="text-end">
                                <div class="month-badge">
                                    <span class="badge bg-white text-success fs-6 px-3 py-2">
                                        <i class="fas fa-calendar me-2"></i>
                                        {{ $activeFiscalYear->name }}
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
                                <a href="{{ route('group-loans.index') }}" class="action-btn primary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Dashboard
                                </a>
                                <a href="{{ route('group-loans.requests') }}" class="action-btn success">
                                    <i class="fas fa-list me-2"></i>
                                    Loan Requests
                                </a>
                                <a href="{{ route('group-loans.all') }}" class="action-btn warning">
                                    <i class="fas fa-eye me-2"></i>
                                    All Loans
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fiscal Year Summary -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="data-card">
                        <div class="data-card-header">
                            <h5 class="data-title">
                                <i class="fas fa-chart-pie me-2"></i>
                                Fiscal Year Summary
                            </h5>
                            <div class="data-subtitle">
                                Overall loan performance for {{ $activeFiscalYear->name }}
                            </div>
                        </div>
                        <div class="data-card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="summary-card">
                                        <div class="summary-icon">
                                            <i class="fas fa-hand-holding-usd"></i>
                                        </div>
                                        <div class="summary-content">
                                            <h6>Total Loans</h6>
                                            <h3>{{ $fiscalYearSummary['total_loans'] }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="summary-card">
                                        <div class="summary-icon">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                        <div class="summary-content">
                                            <h6>Total Principal</h6>
                                            <h3>UGX {{ number_format($fiscalYearSummary['total_principal'], 0) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="summary-card">
                                        <div class="summary-icon">
                                            <i class="fas fa-percentage"></i>
                                        </div>
                                        <div class="summary-content">
                                            <h6>Total Interest</h6>
                                            <h3>UGX {{ number_format($fiscalYearSummary['total_interest'], 0) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="summary-card">
                                        <div class="summary-icon">
                                            <i class="fas fa-piggy-bank"></i>
                                        </div>
                                        <div class="summary-content">
                                            <h6>Total Repaid</h6>
                                            <h3>UGX {{ number_format($fiscalYearSummary['total_repaid'], 0) }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="summary-card">
                                        <div class="summary-icon">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                        <div class="summary-content">
                                            <h6>Total Penalties</h6>
                                            <h3>UGX {{ number_format($fiscalYearSummary['total_penalties'], 0) }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="summary-card">
                                        <div class="summary-icon">
                                            <i class="fas fa-chart-line"></i>
                                        </div>
                                        <div class="summary-content">
                                            <h6>Recovery Rate</h6>
                                            <h3>{{ $fiscalYearSummary['total_principal'] > 0 ? number_format(($fiscalYearSummary['total_repaid'] / $fiscalYearSummary['total_principal']) * 100, 1) : 0 }}%</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Member Loan Summary -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="data-card">
                        <div class="data-card-header">
                            <h5 class="data-title">
                                <i class="fas fa-users me-2"></i>
                                Member Loan Summary
                            </h5>
                            <div class="data-subtitle">
                                Loan history and performance per member
                            </div>
                        </div>
                        <div class="data-card-body">
                            @if($memberLoanSummary->count() > 0)
                                <div class="table-responsive">
                                    <table class="enhanced-table">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>
                                                    <i class="fas fa-user me-2"></i>
                                                    Member
                                                </th>
                                                <th>
                                                    <i class="fas fa-hashtag me-2"></i>
                                                    Loans
                                                </th>
                                                <th>
                                                    <i class="fas fa-money-bill-wave me-2"></i>
                                                    Total Borrowed
                                                </th>
                                                <th>
                                                    <i class="fas fa-piggy-bank me-2"></i>
                                                    Total Repaid
                                                </th>
                                                <th>
                                                    <i class="fas fa-balance-scale me-2"></i>
                                                    Outstanding
                                                </th>
                                                <th>
                                                    <i class="fas fa-percentage me-2"></i>
                                                    Recovery Rate
                                                </th>
                                                <th>
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    Status
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($memberLoanSummary as $member)
                                                <tr class="table-row-hover">
                                                    <td>
                                                        <div class="member-info">
                                                            <a href="{{ route('admin.members.show', $member->id) }}" class="member-link">
                                                                <strong>{{ $member->first_name }} {{ $member->last_name }}</strong>
                                                            </a>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="count-badge">{{ $member->loans->count() }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="amount-badge primary">
                                                            UGX {{ number_format($member->loans->sum('loan_amount'), 0) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="amount-badge success">
                                                            UGX {{ number_format($member->loans->sum('paid_amount'), 0) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="balance-badge">
                                                            UGX {{ number_format($member->loans->sum('balance'), 0) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="progress-container">
                                                            <div class="progress">
                                                                <div class="progress-bar" role="progressbar" 
                                                                     style="width: {{ $member->loans->sum('loan_amount') > 0 ? ($member->loans->sum('paid_amount') / $member->loans->sum('loan_amount')) * 100 : 0 }}%"
                                                                     aria-valuenow="{{ $member->loans->sum('loan_amount') > 0 ? ($member->loans->sum('paid_amount') / $member->loans->sum('loan_amount')) * 100 : 0 }}" 
                                                                     aria-valuemin="0" aria-valuemax="100">
                                                                    {{ $member->loans->sum('loan_amount') > 0 ? number_format(($member->loans->sum('paid_amount') / $member->loans->sum('loan_amount')) * 100, 1) : 0 }}%
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($member->loans->where('loan_status', 'disbursed')->count() > 0)
                                                            <span class="badge bg-warning">Active Loans</span>
                                                        @elseif($member->loans->where('loan_status', 'defaulted')->count() > 0)
                                                            <span class="badge bg-danger">Defaulted</span>
                                                        @else
                                                            <span class="badge bg-success">Good Standing</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="empty-state text-center py-5">
                                    <i class="fas fa-users fa-4x text-muted mb-3"></i>
                                    <h4 class="text-muted">No Member Data</h4>
                                    <p class="text-muted">No members have taken loans in this fiscal year.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Activity -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="data-card">
                        <div class="data-card-header">
                            <h5 class="data-title">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Monthly Loan Activity
                            </h5>
                            <div class="data-subtitle">
                                Loan disbursement trends by month
                            </div>
                        </div>
                        <div class="data-card-body">
                            @if($monthlyActivity->count() > 0)
                                <div class="table-responsive">
                                    <table class="enhanced-table">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>
                                                    <i class="fas fa-calendar me-2"></i>
                                                    Month
                                                </th>
                                                <th>
                                                    <i class="fas fa-hashtag me-2"></i>
                                                    Loans
                                                </th>
                                                <th>
                                                    <i class="fas fa-money-bill-wave me-2"></i>
                                                    Amount
                                                </th>
                                                <th>
                                                    <i class="fas fa-chart-line me-2"></i>
                                                    Trend
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($monthlyActivity as $activity)
                                                <tr class="table-row-hover">
                                                    <td>
                                                        <span class="month-badge">{{ date('F', mktime(0, 0, 0, $activity->month, 1)) }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="count-badge">{{ $activity->loans_count }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="amount-badge primary">
                                                            UGX {{ number_format($activity->total_amount, 0) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="trend-indicator">
                                                            @if($activity->loans_count > 5)
                                                                <i class="fas fa-arrow-up text-success"></i>
                                                                <span class="text-success">High</span>
                                                            @elseif($activity->loans_count > 2)
                                                                <i class="fas fa-minus text-warning"></i>
                                                                <span class="text-warning">Medium</span>
                                                            @else
                                                                <i class="fas fa-arrow-down text-danger"></i>
                                                                <span class="text-danger">Low</span>
                                                            @endif
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
                                    <h4 class="text-muted">No Monthly Data</h4>
                                    <p class="text-muted">No loan activity recorded for this fiscal year.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Options -->
            <div class="row">
                <div class="col-12">
                    <div class="export-card">
                        <div class="export-card-body">
                            <h5 class="export-title">
                                <i class="fas fa-download me-2"></i>
                                Export Reports
                            </h5>
                            <div class="export-buttons">
                                <button class="export-btn excel" onclick="exportReport('excel')">
                                    <i class="fas fa-file-excel me-2"></i>
                                    Export to Excel
                                </button>
                                <button class="export-btn pdf" onclick="exportReport('pdf')">
                                    <i class="fas fa-file-pdf me-2"></i>
                                    Export to PDF
                                </button>
                                <button class="export-btn print" onclick="window.print()">
                                    <i class="fas fa-print me-2"></i>
                                    Print Report
                                </button>
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

/* Enhanced Action Cards */
.action-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 2rem;
}

.action-card-body {
    padding: 2rem;
}

.action-title {
    color: #333;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
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

/* Summary Cards */
.summary-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.summary-icon {
    font-size: 2rem;
    color: #667eea;
    margin-right: 1rem;
    opacity: 0.7;
}

.summary-content h6 {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.summary-content h3 {
    color: #333;
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
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

.member-info strong {
    color: #333;
    font-weight: 600;
}

.count-badge {
    background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 15px;
    font-size: 0.7rem;
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

.balance-badge {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 15px;
    font-size: 0.7rem;
    font-weight: 600;
}

.month-badge {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 15px;
    font-size: 0.7rem;
    font-weight: 600;
}

.progress-container {
    min-width: 100px;
}

.progress {
    height: 8px;
    border-radius: 4px;
    background-color: #e9ecef;
}

.progress-bar {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    border-radius: 4px;
}

.trend-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Export Card */
.export-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 2rem;
}

.export-card-body {
    padding: 2rem;
}

.export-title {
    color: #333;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.export-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.export-btn {
    padding: 1rem 2rem;
    border-radius: 50px;
    border: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    color: white;
    cursor: pointer;
}

.export-btn.excel {
    background: linear-gradient(135deg, #217346 0%, #2a9d4a 100%);
}

.export-btn.pdf {
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
}

.export-btn.print {
    background: linear-gradient(135deg, #6b7280 0%, #9ca3af 100%);
}

.export-btn:hover {
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
    
    .page-header h1 {
        font-size: 1.5rem;
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
    
    .export-buttons {
        flex-direction: column;
    }
    
    .export-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
function exportReport(format) {
    // Implement export functionality
    alert(`Exporting report as ${format.toUpperCase()}...`);
    // You can implement actual export logic here
}
</script>
@endsection
