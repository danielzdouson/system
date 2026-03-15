@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-2 text-white">
                            <i class="fas fa-tachometer-alt me-3"></i>
                            System Dashboard
                        </h1>
                        <p class="text-white fs-5 mb-0 opacity-90">
                            <i class="fas fa-chart-line me-2"></i>
                            Overview of your SACCO system
                        </p>
                    </div>
                    <div class="text-end">
                        @if($currentFiscalYear)
                            <div class="fiscal-year-badge">
                                <span class="badge bg-white text-primary fs-6 px-3 py-2">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    {{ $currentFiscalYear->name }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($currentFiscalYear)
        <!-- Statistics Tiles -->
        <div class="row mb-4 g-3">
            <div class="col-md-3">
                <div class="stat-tile">
                    <div class="stat-tile-header">
                        <i class="fas fa-users"></i>
                        <span>Total Members</span>
                    </div>
                    <div class="stat-tile-body">
                        <h3>{{ number_format($stats['total_members'], 0) }}</h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-tile success">
                    <div class="stat-tile-header">
                        <i class="fas fa-piggy-bank"></i>
                        <span>Total Savings</span>
                    </div>
                    <div class="stat-tile-body">
                        <h3>UGX {{ number_format($stats['total_savings'], 0) }}</h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-tile info">
                    <div class="stat-tile-header">
                        <i class="fas fa-hand-holding-usd"></i>
                        <span>Active Loans</span>
                    </div>
                    <div class="stat-tile-body">
                        <h3>{{ number_format($stats['active_loans'], 0) }}</h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-tile warning">
                    <div class="stat-tile-header">
                        <i class="fas fa-clock"></i>
                        <span>Pending Requests</span>
                    </div>
                    <div class="stat-tile-body">
                        <h3>{{ number_format($stats['pending_loan_requests'], 0) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Statistics Tiles -->
        <div class="row mb-4 g-3">
            <div class="col-md-6">
                <div class="stat-tile loan-tile">
                    <div class="stat-tile-header">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Total Loans Amount</span>
                    </div>
                    <div class="stat-tile-body">
                        <h3>UGX {{ number_format($stats['total_loans_amount'], 0) }}</h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="stat-tile fine-tile">
                    <div class="stat-tile-header">
                        <i class="fas fa-gavel"></i>
                        <span>Total Fines</span>
                    </div>
                    <div class="stat-tile-body">
                        <h3>UGX {{ number_format($stats['total_fines'], 0) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history me-2"></i>
                            Recent Activities
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($stats['recent_activities']->count() > 0)
                            <div class="timeline">
                                @foreach($stats['recent_activities'] as $activity)
                                    <div class="timeline-item">
                                        <div class="timeline-icon">
                                            {{ $activity['icon'] }}
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="timeline-title">{{ $activity['description'] }}</h6>
                                            <p class="timeline-amount">UGX {{ number_format($activity['amount'], 0) }}</p>
                                            <small class="timeline-date">{{ $activity['date']->format('M d, Y H:i') }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No recent activities found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- No Fiscal Year Alert -->
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>No Active Fiscal Year</h5>
                    <p class="mb-0">Please create an active fiscal year to start managing your SACCO system.</p>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
/* Page Header */
.container-fluid {
    padding: 0;
    width: 850px;
}
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.fiscal-year-badge .badge {
    border-radius: 50px;
    font-weight: 600;
}

/* Statistics Tiles */
.stat-tile {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    overflow: hidden;
    border-left: 4px solid #667eea;
}

.stat-tile:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.stat-tile.success {
    border-left-color: #10b981;
}

.stat-tile.info {
    border-left-color: #3b82f6;
}

.stat-tile.warning {
    border-left-color: #f59e0b;
}

.stat-tile.loan-tile {
    border-left-color: #8b5cf6;
    background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
}

.stat-tile.fine-tile {
    border-left-color: #ef4444;
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
}

.stat-tile-header {
    padding: 1rem 1.5rem 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-tile-header i {
    font-size: 1.25rem;
    color: #667eea;
}

.stat-tile.success .stat-tile-header i {
    color: #10b981;
}

.stat-tile.info .stat-tile-header i {
    color: #3b82f6;
}

.stat-tile.warning .stat-tile-header i {
    color: #f59e0b;
}

.stat-tile.loan-tile .stat-tile-header i {
    color: #8b5cf6;
}

.stat-tile.fine-tile .stat-tile-header i {
    color: #ef4444;
}

.stat-tile-body {
    padding: 0.5rem 1.5rem 1.5rem;
}

.stat-tile-body h3 {
    margin: 0;
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
}

.stat-tile.loan-tile .stat-tile-body h3 {
    color: #6b21a8;
}

.stat-tile.fine-tile .stat-tile-body h3 {
    color: #b91c1c;
}

/* Timeline */
.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-icon {
    position: absolute;
    left: -2.5rem;
    top: 0;
    width: 2rem;
    height: 2rem;
    background: #667eea;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.8rem;
}

.timeline-content {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 10px;
    border-left: 3px solid #667eea;
}

.timeline-title {
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.timeline-amount {
    margin-bottom: 0.25rem;
    font-weight: 700;
    color: #667eea;
}

.timeline-date {
    color: #6c757d;
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
    
    .stat-card-body {
        padding: 1.5rem;
    }
}
</style>
@endsection
