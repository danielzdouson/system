@extends('layouts.admin')

@section('title', 'Cash Flow Dashboard')

@push('styles')
<style>
/* Dashboard Cards */
.dashboard-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.1);
    border: none;
    color: white;
    overflow: hidden;
    transition: transform 0.3s ease;
}

.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.15);
}

.dashboard-card-header {
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.1);
}

.dashboard-card-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.dashboard-card-value {
    font-size: 2rem;
    font-weight: 700;
    margin: 0.5rem 0 0 0;
}

.dashboard-card-subtitle {
    font-size: 0.9rem;
    opacity: 0.8;
    margin: 0;
}

.dashboard-card-trend {
    font-size: 0.8rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.2);
}

.trend-up {
    background: rgba(16, 185, 129, 0.2);
    color: #10b981;
}

.trend-down {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
}

.trend-stable {
    background: rgba(108, 117, 125, 0.2);
    color: #6c757d;
}

/* Today's Totals */
.totals-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    border: 1px solid #e3e6f6;
    overflow: hidden;
}

.totals-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #dee2e6;
}

.totals-title {
    font-size: 1rem;
    font-weight: 600;
    color: #495057;
    margin: 0;
}

.total-item {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f8f9fa;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background-color 0.2s ease;
}

.total-item:hover {
    background-color: #f8f9fa;
}

.total-item:last-child {
    border-bottom: none;
}

.total-label {
    font-size: 0.9rem;
    color: #6c757d;
    font-weight: 500;
}

.total-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: #2c3e50;
}

.total-value.inflow {
    color: #10b981;
}

.total-value.outflow {
    color: #dc3545;
}

/* Chart Container */
.chart-container {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    border: 1px solid #e3e6f6;
    padding: 1.5rem;
}

/* Transactions Table */
.transactions-table {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    border: 1px solid #e3e6f6;
    overflow: hidden;
}

.transactions-header {
    background: linear-gradient(135deg, #495057 0%, #343a40 100%);
    color: white;
    padding: 1rem 1.5rem;
}

.transactions-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0;
}

.transaction-row {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f8f9fa;
    transition: background-color 0.2s ease;
}

.transaction-row:hover {
    background-color: #f8f9fa;
}

.transaction-row:last-child {
    border-bottom: none;
}

.transaction-type {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.type-inflow {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.type-outflow {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

.transaction-amount {
    font-size: 1.1rem;
    font-weight: 700;
}

.amount-inflow {
    color: #10b981;
}

.amount-outflow {
    color: #dc3545;
}

.source-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 5px;
    font-size: 0.75rem;
    font-weight: 500;
    background: #6c757d;
    color: white;
}

/* Responsive Design */
@media (max-width: 768px) {
    .dashboard-card-value {
        font-size: 1.5rem;
    }
    
    .total-value {
        font-size: 1rem;
    }
    
    .transaction-row {
        padding: 0.75rem 1rem;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-1">
                        <i class="fas fa-chart-line text-primary me-2"></i>
                        Cash Flow Dashboard
                    </h2>
                    <p class="text-muted mb-0">Real-time financial overview and analysis</p>
                </div>
                <div class="d-flex gap-2">
                    <!-- Fiscal Year Filter -->
                    <select id="fiscalYearFilter" class="form-select">
                        <option value="">Select Fiscal Year</option>
                        @if($dashboardData['fiscal_year'])
                            <option value="{{ $dashboardData['fiscal_year']->id }}" selected>
                                {{ $dashboardData['fiscal_year']->name }}
                            </option>
                        @endif
                    </select>
                    
                    <!-- Month Filter -->
                    <select id="monthFilter" class="form-select">
                        <option value="">Select Month</option>
                        @foreach($dashboardData['filters']['available_months'] as $month)
                            <option value="{{ $month['value'] }}" {{ $month['value'] == $dashboardData['filters']['current_month'] ? 'selected' : '' }}>
                                {{ $month['name'] }}
                            </option>
                        @endforeach
                    </select>
                    
                    <!-- Refresh Button -->
                    <button id="refreshDashboard" class="btn btn-primary">
                        <i class="fas fa-sync-alt me-1"></i>
                        Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Overview Section -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h5 class="dashboard-card-title">
                        <i class="fas fa-wallet me-2"></i>
                        Current Balance
                    </h5>
                </div>
                <div class="text-center py-3">
                    <div class="dashboard-card-value">
                        UGX {{ number_format($dashboardData['overview']['current_balance'], 0) }}
                    </div>
                    <div class="dashboard-card-subtitle">
                        Available Funds
                    </div>
                    <div class="dashboard-card-trend {{ $dashboardData['overview']['balance_trend'] == 'increasing' ? 'trend-up' : ($dashboardData['overview']['balance_trend'] == 'decreasing' ? 'trend-down' : 'trend-stable') }}">
                        <i class="fas fa-arrow-{{ $dashboardData['overview']['balance_trend'] == 'increasing' ? 'up' : ($dashboardData['overview']['balance_trend'] == 'decreasing' ? 'down' : 'right') }}"></i>
                        {{ ucfirst($dashboardData['overview']['balance_trend']) }}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h5 class="dashboard-card-title">
                        <i class="fas fa-arrow-down text-success me-2"></i>
                        Today's Inflows
                    </h5>
                </div>
                <div class="text-center py-3">
                    <div class="dashboard-card-value text-success">
                        +UGX {{ number_format($dashboardData['overview']['today_inflows'], 0) }}
                    </div>
                    <div class="dashboard-card-subtitle">
                        Cash Received
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h5 class="dashboard-card-title">
                        <i class="fas fa-arrow-up text-danger me-2"></i>
                        Today's Outflows
                    </h5>
                </div>
                <div class="text-center py-3">
                    <div class="dashboard-card-value text-danger">
                        -UGX {{ number_format($dashboardData['overview']['today_outflows'], 0) }}
                    </div>
                    <div class="dashboard-card-subtitle">
                        Cash Paid Out
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="dashboard-card">
                <div class="dashboard-card-header">
                    <h5 class="dashboard-card-title">
                        <i class="fas fa-balance-scale text-{{ $dashboardData['overview']['net_cashflow'] >= 0 ? 'success' : 'danger' }} me-2"></i>
                        Net Cashflow
                    </h5>
                </div>
                <div class="text-center py-3">
                    <div class="dashboard-card-value text-{{ $dashboardData['overview']['net_cashflow'] >= 0 ? 'success' : 'danger' }}">
                        {{ $dashboardData['overview']['net_cashflow'] >= 0 ? '+' : '' }}UGX {{ number_format(abs($dashboardData['overview']['net_cashflow']), 0) }}
                    </div>
                    <div class="dashboard-card-subtitle">
                        Today's Change
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Totals Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="totals-card">
                <div class="totals-header">
                    <h5 class="totals-title">
                        <i class="fas fa-calendar-day me-2"></i>
                        Today's Transaction Totals
                    </h5>
                </div>
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="total-item">
                            <div class="total-label">
                                <i class="fas fa-piggy-bank me-1"></i>
                                Savings Deposits
                            </div>
                            <div class="total-value inflow">
                                UGX {{ number_format($dashboardData['today_totals']['savings_deposits'], 0) }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="total-item">
                            <div class="total-label">
                                <i class="fas fa-hand-holding-usd me-1"></i>
                                Loan Repayments
                            </div>
                            <div class="total-value inflow">
                                UGX {{ number_format($dashboardData['today_totals']['loan_repayments'], 0) }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="total-item">
                            <div class="total-label">
                                <i class="fas fa-money-bill-wave me-1"></i>
                                Member Withdrawals
                            </div>
                            <div class="total-value outflow">
                                UGX {{ number_format($dashboardData['today_totals']['member_withdrawals'], 0) }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="total-item">
                            <div class="total-label">
                                <i class="fas fa-exchange-alt me-1"></i>
                                Manual Transactions
                            </div>
                            <div class="total-value">
                                UGX {{ number_format($dashboardData['today_totals']['manual_inflows'] + $dashboardData['today_totals']['manual_outflows'], 0) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Cash Flow Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="chart-container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        {{ $dashboardData['monthly_summary']['month_name'] }} - Daily Cash Flow
                    </h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary" id="toggleChartType">
                            <i class="fas fa-chart-bar me-1"></i>
                            Bar Chart
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" id="toggleChartType">
                            <i class="fas fa-chart-line me-1"></i>
                            Line Chart
                        </button>
                    </div>
                </div>
                
                <!-- Chart Canvas -->
                <div style="height: 300px; position: relative;">
                    <canvas id="cashflowChart"></canvas>
                </div>
                
                <!-- Chart Summary -->
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="text-center">
                            <small class="text-muted">Total Inflows</small>
                            <div class="h5 text-success">UGX {{ number_format($dashboardData['monthly_summary']['total_inflows'], 0) }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <small class="text-muted">Total Outflows</small>
                            <div class="h5 text-danger">UGX {{ number_format($dashboardData['monthly_summary']['total_outflows'], 0) }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <small class="text-muted">Net Cashflow</small>
                            <div class="h5 text-{{ $dashboardData['monthly_summary']['net_cashflow'] >= 0 ? 'success' : 'danger' }}">
                                {{ $dashboardData['monthly_summary']['net_cashflow'] >= 0 ? '+' : '' }}UGX {{ number_format(abs($dashboardData['monthly_summary']['net_cashflow']), 0) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions Table -->
    <div class="row">
        <div class="col-12">
            <div class="transactions-table">
                <div class="transactions-header">
                    <h5 class="transactions-title">
                        <i class="fas fa-list me-2"></i>
                        Recent Transactions
                    </h5>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Source</th>
                                <th>Member</th>
                            </tr>
                        </thead>
                        <tbody id="transactionsTableBody">
                            @forelse(empty($dashboardData['recent_transactions']))
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                        <div>No recent transactions found</div>
                                    </td>
                                </tr>
                            @else
                                @foreach($dashboardData['recent_transactions'] as $transaction)
                                    <tr>
                                        <td>{{ $transaction['date'] }}</td>
                                        <td>{{ $transaction['description'] }}</td>
                                        <td>
                                            <span class="transaction-type {{ $transaction['transaction_type'] == 'INFLOW' ? 'type-inflow' : 'type-outflow' }}">
                                                {{ $transaction['transaction_type'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="transaction-amount {{ $transaction['transaction_type'] == 'INFLOW' ? 'amount-inflow' : 'amount-outflow' }}">
                                                UGX {{ number_format($transaction['amount'], 0) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="source-badge">{{ $transaction['source_module'] }}</span>
                                        </td>
                                        <td>
                                            @if($transaction['member_info'])
                                                {{ $transaction['member_info']['name'] }}
                                            @else
                                                <span class="text-muted">System</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Dashboard data
let dashboardData = @json($dashboardData);

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
    setupEventListeners();
    renderChart();
});

function initializeDashboard() {
    // Set fiscal year and month filters
    const fiscalYearSelect = document.getElementById('fiscalYearFilter');
    const monthSelect = document.getElementById('monthFilter');
    
    if (fiscalYearSelect && dashboardData.fiscal_year) {
        fiscalYearSelect.value = dashboardData.fiscal_year.id;
    }
    
    if (monthSelect && dashboardData.filters) {
        monthSelect.value = dashboardData.filters.current_month;
    }
}

function setupEventListeners() {
    // Fiscal year change
    document.getElementById('fiscalYearFilter').addEventListener('change', function() {
        const fiscalYearId = this.value;
        const currentMonth = document.getElementById('monthFilter').value;
        
        if (fiscalYearId) {
            loadFiscalYearMonths(fiscalYearId);
            refreshDashboard(fiscalYearId, currentMonth);
        }
    });
    
    // Month change
    document.getElementById('monthFilter').addEventListener('change', function() {
        const fiscalYearId = document.getElementById('fiscalYearFilter').value;
        const month = this.value;
        refreshDashboard(fiscalYearId, month);
    });
    
    // Refresh button
    document.getElementById('refreshDashboard').addEventListener('click', function() {
        const fiscalYearId = document.getElementById('fiscalYearFilter').value;
        const month = document.getElementById('monthFilter').value;
        refreshDashboard(fiscalYearId, month);
    });
}

function loadFiscalYearMonths(fiscalYearId) {
    fetch(`/admin/cashflow-dashboard/months?fiscal_year_id=${fiscalYearId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const monthSelect = document.getElementById('monthFilter');
                monthSelect.innerHTML = '<option value="">Select Month</option>';
                
                data.data.forEach(month => {
                    const option = document.createElement('option');
                    option.value = month.value;
                    option.textContent = month.name;
                    monthSelect.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Error loading months:', error));
}

function refreshDashboard(fiscalYearId, month) {
    // Show loading state
    showLoadingState();
    
    fetch(`/admin/cashflow-dashboard/data?fiscal_year_id=${fiscalYearId}&month=${month}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                dashboardData = data.data;
                updateDashboardUI();
                renderChart();
            }
        })
        .catch(error => {
            console.error('Error refreshing dashboard:', error);
            hideLoadingState();
        });
}

function updateDashboardUI() {
    // Update overview cards
    updateOverviewCards();
    
    // Update today's totals
    updateTodayTotals();
    
    // Update transactions table
    updateTransactionsTable();
}

function updateOverviewCards() {
    const overview = dashboardData.overview;
    
    // Update current balance
    const balanceElement = document.querySelector('.dashboard-card-value');
    if (balanceElement) {
        balanceElement.textContent = `UGX ${formatNumber(overview.current_balance)}`;
    }
    
    // Update trend indicator
    const trendElement = document.querySelector('.dashboard-card-trend');
    if (trendElement) {
        trendElement.className = `dashboard-card-trend trend-${overview.balance_trend}`;
        trendElement.innerHTML = `
            <i class="fas fa-arrow-${overview.balance_trend === 'increasing' ? 'up' : (overview.balance_trend === 'decreasing' ? 'down' : 'right')}"></i>
            ${ucfirst(overview.balance_trend)}
        `;
    }
}

function updateTodayTotals() {
    const totals = dashboardData.today_totals;
    
    // Update each total item
    const totalElements = document.querySelectorAll('.total-value');
    if (totalElements.length >= 4) {
        totalElements[0].textContent = `UGX ${formatNumber(totals.savings_deposits)}`;
        totalElements[1].textContent = `UGX ${formatNumber(totals.loan_repayments)}`;
        totalElements[2].textContent = `UGX ${formatNumber(totals.member_withdrawals)}`;
        totalElements[3].textContent = `UGX ${formatNumber(totals.manual_inflows + totals.manual_outflows)}`;
    }
}

function updateTransactionsTable() {
    const tbody = document.getElementById('transactionsTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (!dashboardData.recent_transactions || dashboardData.recent_transactions.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <div>No recent transactions found</div>
                </td>
            </tr>
        `;
        return;
    }
    
    dashboardData.recent_transactions.forEach(transaction => {
        const row = document.createElement('tr');
        row.className = 'transaction-row';
        row.innerHTML = `
            <td>${transaction.date}</td>
            <td>${transaction.description}</td>
            <td>
                <span class="transaction-type ${transaction.transaction_type === 'INFLOW' ? 'type-inflow' : 'type-outflow'}">
                    ${transaction.transaction_type}
                </span>
            </td>
            <td>
                <span class="transaction-amount ${transaction.transaction_type === 'INFLOW' ? 'amount-inflow' : 'amount-outflow'}">
                    UGX ${formatNumber(transaction.amount)}
                </span>
            </td>
            <td>
                <span class="source-badge">${transaction.source_module}</span>
            </td>
            <td>
                ${transaction.member_info ? transaction.member_info.name : '<span class="text-muted">System</span>'}
            </td>
        `;
        tbody.appendChild(row);
    });
}

function renderChart() {
    const ctx = document.getElementById('cashflowChart');
    if (!ctx || !dashboardData.monthly_summary.daily_data.length) return;
    
    const dailyData = dashboardData.monthly_summary.daily_data;
    const labels = dailyData.map(item => item.date);
    const inflowData = dailyData.map(item => item.inflows);
    const outflowData = dailyData.map(item => item.outflows);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Inflows',
                    data: inflowData,
                    backgroundColor: 'rgba(16, 185, 129, 0.6)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Outflows',
                    data: outflowData,
                    backgroundColor: 'rgba(220, 53, 69, 0.6)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'UGX ' + formatNumber(value);
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': UGX ' + formatNumber(context.parsed.y);
                        }
                    }
                }
            }
        }
    });
}

function formatNumber(num) {
    return new Intl.NumberFormat('en-UG', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(num);
}

function showLoadingState() {
    // Add loading overlay
    const dashboard = document.querySelector('.container-fluid');
    if (dashboard) {
        const loadingOverlay = document.createElement('div');
        loadingOverlay.id = 'loadingOverlay';
        loadingOverlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        `;
        
        loadingOverlay.innerHTML = `
            <div style="color: white; font-size: 1.2rem; margin-bottom: 1rem;">
                <i class="fas fa-sync-alt fa-spin fa-2x"></i>
                <div>Loading Dashboard...</div>
            </div>
        `;
        
        dashboard.appendChild(loadingOverlay);
    }
}

function hideLoadingState() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        loadingOverlay.remove();
    }
}
</script>
@endpush
