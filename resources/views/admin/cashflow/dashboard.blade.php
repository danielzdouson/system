@extends('layouts.admin')

@section('title', 'Cashflow Dashboard')

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
                                    Cashflow Dashboard
                                </h1>
                                <p class="text-white fs-5 mb-0 opacity-90">
                                    <i class="fas fa-tachometer-alt me-2"></i>
                                    Real-time cash position and trend analysis
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
            <!-- Current Balance Card -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="stat-card primary-gradient">
                        <div class="stat-card-body">
                            <div class="stat-icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="stat-content">
                                <h4 class="stat-title">Current Balance</h4>
                                <h2 class="stat-number">UGX {{ number_format($currentBalance, 0) }}</h2>
                            </div>
                            <div class="stat-trend">
                                <i class="fas fa-sync-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Cash Position Trend -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="data-card">
                        <div class="data-card-header">
                            <h5 class="data-title">
                                <i class="fas fa-chart-area-line me-2"></i>
                                7-Day Cash Position Trend
                            </h5>
                            <div class="data-subtitle">
                                Daily cash position changes over the last week
                            </div>
                        </div>
                        <div class="data-card-body">
                            <div class="chart-container" style="height: 300px;">
                                <canvas id="cashPositionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-card success-gradient">
                        <div class="stat-card-body">
                            <div class="stat-icon">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                            <div class="stat-content">
                                <h4 class="stat-title">Total Inflows</h4>
                                <h2 class="stat-number">UGX {{ number_format($cashPositionTrend->sum('inflow'), 0) }}</h2>
                            </div>
                        </div>
                    </div>
                <div class="col-md-3">
                    <div class="stat-card warning-gradient">
                        <div class="stat-card-body">
                            <div class="stat-icon">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                            <div class="stat-content">
                                <h4 class="stat-title">Total Outflows</h4>
                                <h2 class="stat-number">UGX {{ number_format($cashPositionTrend->sum('outflow'), 0) }}</h2>
                            </div>
                        </div>
                    </div>
                <div class="col-md-3">
                    <div class="stat-card info-gradient">
                        <div class="stat-card-body">
                            <div class="stat-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="stat-content">
                                <h4 class="stat-title">Net Movement</h4>
                                <h2 class="stat-number">UGX {{ number_format($cashPositionTrend->sum('inflow') - $cashPositionTrend->sum('outflow'), 0) }}</h2>
                            </div>
                        </div>
                    </div>
                <div class="col-md-3">
                    <div class="stat-card secondary-gradient">
                        <div class="stat-card-body">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-content">
                                <h4 class="stat-title">Transactions</h4>
                                <h2 class="stat-number">{{ count($cashPositionTrend) }}</h2>
                            </div>
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
                                <a href="{{ route('admin.cashflow.index') }}" class="action-btn primary">
                                    <i class="fas fa-list me-2"></i>
                                    View All Transactions
                                </a>
                                <a href="{{ route('admin.cashflow.monthly-statement') }}" class="action-btn success">
                                    <i class="fas fa-file-invoice me-2"></i>
                                    Monthly Statement
                                </a>
                                <a href="{{ route('admin.cashflow.fiscal-year-statement') }}" class="action-btn info">
                                    <i class="fas fa-file-alt me-2"></i>
                                    Fiscal Year Statement
                                </a>
                                <button onclick="refreshCashPosition()" class="action-btn warning">
                                    <i class="fas fa-sync-alt me-2"></i>
                                    Refresh Data
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Styles -->
<style>
/* Stat Cards */
.stat-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    margin-bottom: 1.5rem;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.stat-card-body {
    padding: 2rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    font-size: 2.5rem;
    color: white;
    opacity: 0.8;
}

.stat-content {
    flex: 1;
}

.stat-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1;
}

.stat-trend {
    font-size: 1.5rem;
    color: white;
    opacity: 0.7;
}

/* Chart Container */
.chart-container {
    position: relative;
    background: white;
    border-radius: 15px;
    padding: 1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .stat-card-body {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .action-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<!-- JavaScript -->
<script>
// Cash position data
let cashPositionData = @json($cashPositionTrend);

// Initialize Chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('cashPositionChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: cashPositionData.map(item => item.date),
                datasets: [{
                    label: 'Cash Balance',
                    data: cashPositionData.map(item => item.balance),
                    borderColor: 'rgb(102, 126, 234)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                return 'Balance: UGX ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Amount (UGX)'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'UGX ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
});

// Refresh cash position
function refreshCashPosition() {
    fetch('{{ route("admin.cashflow.position") }}')
        .then(response => response.json())
        .then(data => {
            // Update balance display
            document.querySelector('.stat-number').textContent = 'UGX ' + data.formatted_balance;
            
            // Update chart
            if (window.cashflowChart) {
                window.cashflowChart.data.datasets[0].data = data.trend.map(item => item.balance);
                window.cashflowChart.data.labels = data.trend.map(item => item.date);
                window.cashflowChart.update();
            }
        })
        .catch(error => console.error('Error refreshing cash position:', error));
}
</script>
@endsection
