@extends('layouts.admin')

@section('title', 'Fines Management')

@section('content')
<div class="container-fluid">
    <!-- Enhanced Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-2 text-white">
                            <i class="fas fa-gavel me-3"></i>
                            Fines Management
                        </h1>
                        <p class="text-white fs-5 mb-0 opacity-90">
                            <i class="fas fa-chart-line me-2"></i>
                            Track and manage member fines for missed and late payments
                        </p>
                    </div>
                    <div class="text-end">
                        <div class="fiscal-year-badge">
                            <span class="badge bg-white text-warning fs-6 px-3 py-2">
                                <i class="fas fa-calendar-alt me-2"></i>
                                {{ $activeFiscalYear->name }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Fines Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card warning-gradient">
                <div class="stat-card-body">
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-content">
                        <h4 class="stat-title">Total Fines</h4>
                        <h2 class="stat-number">UGX {{ number_format($fines->sum('amount'), 0) }}</h2>
                    </div>
                    <div class="stat-trend">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card danger-gradient">
                <div class="stat-card-body">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h4 class="stat-title">Unpaid Fines</h4>
                        <h2 class="stat-number">UGX {{ number_format($fines->where('status', 'pending')->sum('amount'), 0) }}</h2>
                        <small class="stat-subtitle">{{ $fines->where('status', 'pending')->count() }} pending</small>
                    </div>
                    <div class="stat-trend">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card success-gradient">
                <div class="stat-card-body">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h4 class="stat-title">Paid Fines</h4>
                        <h2 class="stat-number">UGX {{ number_format($fines->where('status', 'paid')->sum('amount'), 0) }}</h2>
                        <small class="stat-subtitle">{{ $fines->where('status', 'paid')->count() }} paid</small>
                    </div>
                    <div class="stat-trend">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card info-gradient">
                <div class="stat-card-body">
                    <div class="stat-icon">
                        <i class="fas fa-hand-paper"></i>
                    </div>
                    <div class="stat-content">
                        <h4 class="stat-title">Waived Fines</h4>
                        <h2 class="stat-number">UGX {{ number_format($fines->where('status', 'waived')->sum('amount'), 0) }}</h2>
                        <small class="stat-subtitle">{{ $fines->where('status', 'waived')->count() }} waived</small>
                    </div>
                    <div class="stat-trend">
                        <i class="fas fa-minus-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="action-card">
                <div class="action-card-body">
                    <h5 class="action-title">
                        <i class="fas fa-rocket me-2"></i>
                        Quick Actions
                    </h5>
                    <div class="action-buttons">
                        <a href="{{ route('admin.group-savings.dashboard') }}" class="action-btn primary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Back to Dashboard
                        </a>
                        <a href="{{ route('admin.group-savings.pending') }}" class="action-btn warning">
                            <i class="fas fa-clock me-2"></i>
                            Pending Months
                        </a>
                        <a href="{{ route('admin.group-savings.create-deposit') }}" class="action-btn success">
                            <i class="fas fa-plus-circle me-2"></i>
                            New Deposit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Fines Table -->
    <div class="row">
        <div class="col-12">
            <div class="data-card">
                <div class="data-card-header">
                    <h5 class="data-title">
                        <i class="fas fa-list me-2"></i>
                        All Fines - {{ $activeFiscalYear->name }}
                    </h5>
                    <div class="data-subtitle">
                        Complete overview of all member fines and their status
                    </div>
                </div>
                <div class="data-card-body">
                    <div class="table-responsive">
                        <table class="enhanced-table">
                            <thead class="table-dark">
                                <tr>
                                    <th>
                                        <i class="fas fa-user me-2"></i>
                                        Member Name
                                    </th>
                                    <th>
                                        <i class="fas fa-id-card me-2"></i>
                                        National ID
                                    </th>
                                    <th>
                                        <i class="fas fa-calendar me-2"></i>
                                        Month
                                    </th>
                                    <th>
                                        <i class="fas fa-money-bill-wave me-2"></i>
                                        Amount
                                    </th>
                                    <th>
                                        <i class="fas fa-tag me-2"></i>
                                        Reason
                                    </th>
                                    <th>
                                        <i class="fas fa-comment me-2"></i>
                                        Description
                                    </th>
                                    <th>
                                        <i class="fas fa-info-circle me-2"></i>
                                        Status
                                    </th>
                                    <th>
                                        <i class="fas fa-calendar-plus me-2"></i>
                                        Created Date
                                    </th>
                                    <th>
                                        <i class="fas fa-cogs me-2"></i>
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fines as $fine)
                                    <tr class="table-row-hover">
                                        <td>
                                            <div class="member-info">
                                                @if($fine->member)
                                                    <a href="{{ route('admin.members.show', $fine->member->id) }}" class="member-link">
                                                        <strong>{{ $fine->member->first_name }} {{ $fine->member->last_name }}</strong>
                                                    </a>
                                                @else
                                                    <span class="text-muted">
                                                        <strong>Unknown Member (ID: {{ $fine->member_id }})</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($fine->member)
                                                <span class="id-badge">{{ $fine->member->national_id ?? 'N/A' }}</span>
                                            @else
                                                <span class="id-badge">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="month-info">
                                                {{ \Carbon\Carbon::create()->month($fine->month)->format('F') }} {{ $fine->month <= 6 ? '2025' : '2024' }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="amount-badge warning">
                                                UGX {{ number_format($fine->amount, 0) }}
                                            </span>
                                        </td>
                                        <td>
                                            @switch($fine->reason)
                                                @case('missed_saving')
                                                    <span class="reason-badge warning">Missed Saving</span>
                                                    @break
                                                @case('late_payment')
                                                    <span class="reason-badge info">Late Payment</span>
                                                    @break
                                                @default
                                                    <span class="reason-badge secondary">Other</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="description-text">
                                                <small>{{ $fine->description }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @switch($fine->status)
                                                @case('pending')
                                                    <span class="status-badge warning">Pending</span>
                                                    @break
                                                @case('paid')
                                                    <span class="status-badge success">Paid</span>
                                                    @break
                                                @case('waived')
                                                    <span class="status-badge info">Waived</span>
                                                    @break
                                                @default
                                                    <span class="status-badge secondary">Unknown</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="date-info">
                                                {{ $fine->created_at->format('M d, Y') }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="action-buttons-inline">
                                                @if($fine->status === 'pending')
                                                    <form method="POST" action="{{ route('admin.group-savings.fines.pay', $fine->id) }}" 
                                                          class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn-action success" 
                                                                onclick="return confirm('Mark this fine as paid?')">
                                                            <i class="fas fa-check"></i>
                                                            <span>Pay</span>
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.group-savings.fines.waive', $fine->id) }}" 
                                                          class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn-action warning" 
                                                                onclick="return confirm('Waive this fine?')">
                                                            <i class="fas fa-hand-paper"></i>
                                                            <span>Waive</span>
                                                        </button>
                                                    </form>
                                                @else
                                                    <div class="status-indicator">
                                                        @if($fine->status === 'paid')
                                                            <i class="fas fa-check-circle text-success"></i>
                                                            <span class="status-text">Paid</span>
                                                        @elseif($fine->status === 'waived')
                                                            <i class="fas fa-hand-paper text-info"></i>
                                                            <span class="status-text">Waived</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-dark">
                                <tr>
                                    <th colspan="3">Total</th>
                                    <th class="text-warning">UGX {{ number_format($fines->sum('amount'), 0) }}</th>
                                    <th colspan="5">{{ $fines->count() }} fines</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Enhanced Page Header */
.container-fluid {
    width: 900px;
    margin: 0 auto;
    padding: 2rem;
}
.page-header {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    box-shadow: 0 10px 30px rgba(240, 147, 251, 0.3);
}

.fiscal-year-badge .badge {
    border-radius: 50px;
    font-weight: 600;
}

/* Enhanced Statistics Cards */
.stat-card {
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

.stat-card-body {
    padding: 2rem;
    position: relative;
    display: flex;
    align-items: center;
    color: white;
}

.stat-icon {
    font-size: 3rem;
    opacity: 0.3;
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
}

.stat-content {
    flex: 1;
    z-index: 1;
}

.stat-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    opacity: 0.9;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
}

.stat-subtitle {
    font-size: 0.8rem;
    opacity: 0.8;
    display: block;
    margin-top: 0.25rem;
}

.stat-trend {
    position: absolute;
    top: 1rem;
    right: 1rem;
    font-size: 1.2rem;
    opacity: 0.7;
}

.warning-gradient {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.danger-gradient {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
}

.success-gradient {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.info-gradient {
    background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
}

/* Enhanced Action Cards */
.action-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
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

.action-btn.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.action-btn.success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
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

.id-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.month-info {
    font-weight: 500;
    color: #555;
}

.amount-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.amount-badge.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.reason-badge, .status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.reason-badge.warning, .status-badge.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.reason-badge.info, .status-badge.info {
    background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
    color: white;
}

.reason-badge.secondary, .status-badge.secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
}

.status-badge.success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.description-text {
    max-width: 200px;
    word-wrap: break-word;
}

.date-info {
    font-size: 0.9rem;
    color: #666;
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
}

.btn-action.success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.btn-action.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.status-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.status-text {
    font-size: 0.8rem;
    font-weight: 600;
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
        font-size: 2rem;
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
</style>
@endsection
