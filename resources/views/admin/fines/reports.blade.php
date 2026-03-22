@extends('layouts.admin')

@section('title', 'Fine Reports & Analytics')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-2 text-white">
                            <i class="fas fa-chart-pie me-3"></i>
                            Fine Reports & Analytics
                        </h1>
                        <p class="text-white fs-5 mb-0 opacity-90">
                            <i class="fas fa-analytics me-2"></i>
                            Comprehensive fine analysis and reporting
                        </p>
                    </div>
                    <div class="text-end">
                        <div class="fiscal-year-selector">
                            <form method="GET" class="d-inline">
                                <select name="fiscal_year" class="form-select form-select-sm" onchange="this.form.submit()">
                                    @foreach($allFiscalYears as $year)
                                        <option value="{{ $year->id }}" 
                                                {{ $activeFiscalYear && $activeFiscalYear->id == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($activeFiscalYear && isset($reports))
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card primary-gradient">
                    <div class="stat-card-body">
                        <div class="stat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-content">
                            <h4 class="stat-title">Total Fines</h4>
                            <h2 class="stat-number">{{ $reports['monthly']->sum('count') }}</h2>
                            <small class="stat-subtitle">UGX {{ number_format($reports['monthly']->sum('total'), 0) }}</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card danger-gradient">
                    <div class="stat-card-body">
                        <div class="stat-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-content">
                            <h4 class="stat-title">Avg Fine Amount</h4>
                            <h2 class="stat-number">UGX {{ number_format($reports['monthly']->avg('total'), 0) }}</h2>
                            <small class="stat-subtitle">Per month</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card success-gradient">
                    <div class="stat-card-body">
                        <div class="stat-icon">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="stat-content">
                            <h4 class="stat-title">Collection Rate</h4>
                            <h2 class="stat-number">{{ $this->getCollectionRate($reports) }}%</h2>
                            <small class="stat-subtitle">Paid vs Total</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card info-gradient">
                    <div class="stat-card-body">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-content">
                            <h4 class="stat-title">Active Months</h4>
                            <h2 class="stat-number">{{ $reports['monthly']->count() }}</h2>
                            <small class="stat-subtitle">With fines</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="data-card">
                    <div class="data-card-header">
                        <h5 class="data-title">
                            <i class="fas fa-chart-bar me-2"></i>
                            Monthly Fine Trends
                        </h5>
                    </div>
                    <div class="data-card-body">
                        <canvas id="monthlyChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="data-card">
                    <div class="data-card-header">
                        <h5 class="data-title">
                            <i class="fas fa-chart-pie me-2"></i>
                            Fine Reasons
                        </h5>
                    </div>
                    <div class="data-card-body">
                        <canvas id="reasonChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Tables -->
        <div class="row">
            <div class="col-md-6">
                <div class="data-card">
                    <div class="data-card-header">
                        <h5 class="data-title">
                            <i class="fas fa-calendar me-2"></i>
                            Monthly Breakdown
                        </h5>
                    </div>
                    <div class="data-card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Fines</th>
                                        <th>Amount</th>
                                        <th>Avg</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports['monthly'] as $monthly)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::create()->month($monthly->month)->format('F') }}</td>
                                            <td>{{ $monthly->count }}</td>
                                            <td>UGX {{ number_format($monthly->total, 0) }}</td>
                                            <td>UGX {{ number_format($monthly->total / $monthly->count, 0) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="data-card">
                    <div class="data-card-header">
                        <h5 class="data-title">
                            <i class="fas fa-tag me-2"></i>
                            Reason Analysis
                        </h5>
                    </div>
                    <div class="data-card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Reason</th>
                                        <th>Fines</th>
                                        <th>Amount</th>
                                        <th>%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports['reasons'] as $reason)
                                        <tr>
                                            <td>
                                                @switch($reason->reason)
                                                    @case('missed_saving')
                                                        <span class="badge bg-warning">Missed Saving</span>
                                                        @break
                                                    @case('late_payment')
                                                        <span class="badge bg-info">Late Payment</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">Other</span>
                                                @endswitch
                                            </td>
                                            <td>{{ $reason->count }}</td>
                                            <td>UGX {{ number_format($reason->total, 0) }}</td>
                                            <td>{{ $this->getPercentage($reason->count, $reports['reasons']->sum('count')) }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="data-card">
                    <div class="data-card-header">
                        <h5 class="data-title">
                            <i class="fas fa-info-circle me-2"></i>
                            Status Overview
                        </h5>
                    </div>
                    <div class="data-card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>Fines</th>
                                        <th>Amount</th>
                                        <th>%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports['status'] as $status)
                                        <tr>
                                            <td>
                                                @switch($status->status)
                                                    @case('pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                        @break
                                                    @case('paid')
                                                        <span class="badge bg-success">Paid</span>
                                                        @break
                                                    @case('waived')
                                                        <span class="badge bg-info">Waived</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">Unknown</span>
                                                @endswitch
                                            </td>
                                            <td>{{ $status->count }}</td>
                                            <td>UGX {{ number_format($status->total, 0) }}</td>
                                            <td>{{ $this->getPercentage($status->count, $reports['status']->sum('count')) }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="data-card">
                    <div class="data-card-header">
                        <h5 class="data-title">
                            <i class="fas fa-users me-2"></i>
                            Top Members
                        </h5>
                    </div>
                    <div class="data-card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Member</th>
                                        <th>Fines</th>
                                        <th>Amount</th>
                                        <th>Avg</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports['top_members'] as $member)
                                        <tr>
                                            <td>
                                                @if($member->member)
                                                    {{ $member->member->first_name }} {{ $member->member->last_name }}
                                                @else
                                                    Unknown Member
                                                @endif
                                            </td>
                                            <td>{{ $member->count }}</td>
                                            <td>UGX {{ number_format($member->total, 0) }}</td>
                                            <td>UGX {{ number_format($member->total / $member->count, 0) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No Fiscal Year Selected -->
        <div class="row">
            <div class="col-12">
                <div class="empty-state-card">
                    <div class="empty-state-content">
                        <i class="fas fa-chart-pie fa-4x text-muted mb-3"></i>
                        <h3 class="text-muted">No Fiscal Year Selected</h3>
                        <p class="text-muted">Please select a fiscal year to view fine reports and analytics.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@if($activeFiscalYear && isset($reports))
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Monthly Chart
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: @json($reports['monthly']->pluck('month')->map(function($month) {
            return \Carbon\Carbon::create()->month($month)->format('F');
        })),
        datasets: [{
            label: 'Fine Amount',
            data: @json($reports['monthly']->pluck('total')),
            borderColor: 'rgb(102, 126, 234)',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            tension: 0.1
        }, {
            label: 'Fine Count',
            data: @json($reports['monthly']->pluck('count')),
            borderColor: 'rgb(240, 147, 251)',
            backgroundColor: 'rgba(240, 147, 251, 0.1)',
            tension: 0.1,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                grid: {
                    drawOnChartArea: false,
                },
            },
        }
    }
});

// Reason Chart
const reasonCtx = document.getElementById('reasonChart').getContext('2d');
new Chart(reasonCtx, {
    type: 'doughnut',
    data: {
        labels: @json($reports['reasons']->pluck('reason')->map(function($reason) {
            return ucfirst(str_replace('_', ' ', $reason));
        })),
        datasets: [{
            data: @json($reports['reasons']->pluck('count')),
            backgroundColor: [
                'rgba(240, 147, 251, 0.8)',
                'rgba(54, 209, 220, 0.8)',
                'rgba(108, 117, 125, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>
@endif

@php
function getCollectionRate($reports) {
    $paid = $reports['status']->where('status', 'paid')->sum('count');
    $total = $reports['status']->sum('count');
    return $total > 0 ? round(($paid / $total) * 100, 1) : 0;
}

function getPercentage($value, $total) {
    return $total > 0 ? round(($value / $total) * 100, 1) : 0;
}
@endphp

<style>
.container-fluid {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.fiscal-year-selector .form-select {
    width: 200px;
}

/* Statistics Cards */
.stat-card {
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    height: 100%;
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

.primary-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.danger-gradient { background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); }
.success-gradient { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.info-gradient { background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%); }

/* Data Cards */
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
    padding: 1.5rem;
}

.data-title {
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0;
}

.data-card-body {
    padding: 1.5rem;
}

/* Empty State */
.empty-state-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    text-align: center;
    padding: 4rem 2rem;
}

/* Responsive */
@media (max-width: 768px) {
    .container-fluid { padding: 1rem; }
    .stat-number { font-size: 2rem; }
}
</style>
@endsection
