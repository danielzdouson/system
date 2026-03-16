@extends('layouts.admin')

@section('title', 'Cash Flow Management')

@push('styles')
<!-- Chart.js for data visualization -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Flatpickr for date range picker -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<style>
/* Enhanced Chart Styles */
.chart-container {
    width:550px;
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
    height: 400px;
    position: relative;
}

.chart-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.chart-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

@media (max-width: 1024px) {
    .chart-grid {
        grid-template-columns: 1fr;
    }
}

/* Skeleton Loading */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

.skeleton-row {
    height: 60px;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
    margin-bottom: 0.5rem;
    border-radius: 8px;
}

/* Enhanced Filter Section */
.filter-section {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.filter-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.filter-presets {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.preset-btn {
    padding: 0.5rem 1rem;
    border: 1px solid #e3e6f6;
    border-radius: 20px;
    background: white;
    color: #6c757d;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.preset-btn:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.preset-btn.active {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

/* Modern Cash Flow Dashboard Styles */
.cashflow-dashboard {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    padding: 1rem;
    width: 100%;
    box-sizing: border-box;
}

/* Ensure admin layout structure is maintained */
.content-wrapper .cashflow-dashboard {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    padding: 1rem;
    width: 100%;
    box-sizing: border-box;
}

/* Fix container positioning */
.content-wrapper .container-fluid {
    width: 100%;
    max-width: 100%;
    padding: 0 1rem;
}

/* Enhanced Header */
.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
    color: white;
    position: relative;
    overflow: hidden;
}

.dashboard-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: shimmer 3s ease-in-out infinite;
}

@keyframes shimmer {
    0%, 100% { transform: translateX(-50%) translateY(-50%) rotate(0deg); }
    50% { transform: translateX(-30%) translateY(-30%) rotate(180deg); }
}

.header-content {
    position: relative;
    z-index: 1;
}

.header-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
}

.header-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
    margin-bottom: 1rem;
}

.fiscal-year-badge {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

/* Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.stat-icon.inflow {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.stat-icon.outflow {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.stat-icon.balance {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stat-icon.pending {
    background: linear-gradient(135deg, #feca57 0%, #ff9ff3 100%);
    color: white;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    color: #6c757d;
    font-weight: 500;
}

.stat-change {
    font-size: 0.8rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    font-weight: 600;
    display: inline-block;
    margin-top: 0.5rem;
}

.stat-change.negative {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

/* Filter Section */
.filter-section {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.filter-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.filter-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    align-items: end;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-size: 0.9rem;
    font-weight: 500;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.form-control {
    padding: 0.75rem 1rem;
    border: 2px solid #e3e6f6;
    border-radius: 12px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* Action Buttons */
.action-section {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.action-header {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

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

.action-btn.info {
    background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
    color: white;
}

.action-btn.warning {
    background: linear-gradient(135deg, #feca57 0%, #ff9ff3 100%);
    color: white;
}

.action-btn.secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

/* Transactions Table */
.table-section {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.table-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.table-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.table-subtitle {
    color: #6c757d;
    font-size: 0.9rem;
}

.modern-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.modern-table thead {
    background: linear-gradient(135deg, #495057 0%, #343a40 100%);
    color: white;
}

.modern-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.9rem;
    border: none;
}

.modern-table th:first-child {
    border-top-left-radius: 12px;
}

.modern-table th:last-child {
    border-top-right-radius: 12px;
}

.modern-table tbody tr {
    transition: all 0.3s ease;
    border-bottom: 1px solid #f8f9fa;
}

.modern-table tbody tr:hover {
    background: #f8f9fa;
    transform: scale(1.01);
}

.modern-table td {
    padding: 1rem;
    vertical-align: middle;
}

.type-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    display: inline-block;
}

.type-badge.inflow {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.type-badge.outflow {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.category-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 500;
    display: inline-block;
}

.category-badge.operating {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
}

.category-badge.investing {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.category-badge.financing {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.amount-display {
    font-size: 1.1rem;
    font-weight: 700;
    padding: 0.5rem 1rem;
    border-radius: 12px;
    display: inline-block;
}

.amount-display.inflow {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.amount-display.outflow {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 500;
    display: inline-block;
}

.status-badge.pending {
    background: rgba(254, 202, 87, 0.1);
    color: #f59e0b;
}

.status-badge.cleared {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.status-badge.reconciled {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
}

.action-dropdown {
    position: relative;
}

.dropdown-menu {
    background: white;
    border: 1px solid #e3e6f6;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    padding: 0.5rem;
    min-width: 200px;
}

.dropdown-item {
    padding: 0.75rem 1rem;
    border-radius: 8px;
    font-size: 0.9rem;
    color: #2c3e50;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.dropdown-item:hover {
    background: #f8f9fa;
    color: #667eea;
}

.dropdown-item.danger {
    color: #ef4444;
}

.dropdown-item.danger:hover {
    background: rgba(239, 68, 68, 0.1);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem;
    color: #6c757d;
}

.empty-state-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.empty-state-text {
    font-size: 1rem;
    margin-bottom: 2rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .header-title {
        font-size: 1.5rem;
    }
    
    .header-subtitle {
        font-size: 0.9rem;
    }
    
    .fiscal-year-badge {
        font-size: 0.8rem;
        padding: 0.5rem 1rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    .stat-card {
        padding: 1rem;
    }
    
    .stat-value {
        font-size: 1.5rem;
    }
    
    .chart-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .chart-container {
        height: 300px;
        padding: 1rem;
    }
    
    .filter-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .filter-presets {
        width: 100%;
        justify-content: space-between;
    }
    
    .preset-btn {
        font-size: 0.7rem;
        padding: 0.4rem 0.8rem;
        flex: 1;
        text-align: center;
    }
    
    .filter-form {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .action-btn {
        width: 100%;
        justify-content: center;
        padding: 1rem;
        font-size: 0.9rem;
    }
    
    .modern-table {
        font-size: 0.8rem;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .modern-table th,
    .modern-table td {
        padding: 0.5rem;
        vertical-align: top;
    }
    
    .modern-table th:nth-child(n+4),
    .modern-table td:nth-child(n+4) {
        display: none;
    }
    
    .modern-table th:nth-child(3)::after,
    .modern-table td:nth-child(3)::after {
        content: "";
        display: block;
        height: 1rem;
    }
    
    /* Mobile transaction cards */
    @media (max-width: 640px) {
        .table-responsive {
            overflow-x: auto;
        }
        
        .modern-table {
            min-width: 600px;
        }
        
        .mobile-transaction-card {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #667eea;
        }
        
        .mobile-transaction-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }
        
        .mobile-transaction-amount {
            font-size: 1.2rem;
            font-weight: 700;
        }
        
        .mobile-transaction-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
            font-size: 0.85rem;
        }
        
        .mobile-transaction-detail {
            display: flex;
            flex-direction: column;
        }
        
        .mobile-transaction-label {
            color: #6c757d;
            font-size: 0.75rem;
            margin-bottom: 0.25rem;
        }
    }
    
    /* Touch-friendly interactions */
    .action-btn,
    .preset-btn,
    .dropdown-toggle {
        min-height: 44px;
        min-width: 44px;
    }
    
    .dropdown-menu {
        min-width: 200px;
        max-width: 90vw;
    }
    
    .dropdown-item {
        padding: 1rem;
        min-height: 44px;
        display: flex;
        align-items: center;
    }
    
    /* Enhanced touch feedback */
    .action-btn:active,
    .preset-btn:active {
        transform: scale(0.95);
    }
    
    /* Better mobile charts */
    .chart-container canvas {
        max-height: 250px;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-presets {
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .preset-btn {
        flex: 0 0 calc(50% - 0.25rem);
        margin-bottom: 0.5rem;
    }
    
    .header-content {
        text-align: center;
    }
    
    .header-content .d-flex {
        flex-direction: column;
        gap: 1rem;
    }
}

/* Loading Animation */
.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Smooth Transitions */
* {
    transition: all 0.3s ease;
}

/* Hide header and sidebar on cashflow page */
.header {
    visibility: hidden;
    height: 0;
    padding: 0;
    margin: 0;
    overflow: hidden;
}

.sidebar {
    visibility: hidden;
    width: 0;
    padding: 0;
    margin: 0;
    overflow: hidden;
}

.content {
    width: 100% !important;
    margin-left: 0 !important;
}

.cashflow-dashboard {
    margin-bottom: -900px !important;
}
</style>
@endpush

@section('content')
<div class="cashflow-dashboard">
    <div class="container-fluid">
        <!-- Enhanced Header -->
        <div class="dashboard-header mb-4">
            <div class="header-content">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="header-title">
                            <i class="fas fa-chart-line me-3"></i>
                            Cash Flow Management
                        </h1>
                        <p class="header-subtitle">
                            <i class="fas fa-coins me-2"></i>
                            Track and manage all cash flow transactions with real-time insights
                        </p>
                    </div>
                    <div class="fiscal-year-badge">
                        <i class="fas fa-calendar-alt"></i>
                        {{ $activeFiscalYear->name ?? 'No Active Fiscal Year' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="stats-grid mb-4">
            <div class="stat-card">
                <div class="stat-icon balance">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stat-value">
                    UGX {{ number_format($totals['totalBalance'] ?? 0, 0) }}
                </div>
                <div class="stat-label">Current Balance</div>
                <div class="stat-change {{ $totals['balanceChange'] >= 0 ? '' : 'negative' }}">
                    <i class="fas fa-arrow-{{ $totals['balanceChange'] >= 0 ? 'up' : 'down' }} me-1"></i>
                    {{ $totals['balanceChange'] >= 0 ? '+' : '' }}{{ number_format($totals['balanceChange'], 1) }}% from last month
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon inflow">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div class="stat-value">
                    UGX {{ number_format($totals['totalInflows'] ?? 0, 0) }}
                </div>
                <div class="stat-label">Total Inflows</div>
                <div class="stat-change {{ $totals['inflowChange'] >= 0 ? '' : 'negative' }}">
                    <i class="fas fa-arrow-{{ $totals['inflowChange'] >= 0 ? 'up' : 'down' }} me-1"></i>
                    {{ $totals['inflowChange'] >= 0 ? '+' : '' }}{{ number_format($totals['inflowChange'], 1) }}% from last month
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon outflow">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div class="stat-value">
                    UGX {{ number_format($totals['totalOutflows'] ?? 0, 0) }}
                </div>
                <div class="stat-label">Total Outflows</div>
                <div class="stat-change {{ $totals['outflowChange'] >= 0 ? '' : 'negative' }}">
                    <i class="fas fa-arrow-{{ $totals['outflowChange'] >= 0 ? 'up' : 'down' }} me-1"></i>
                    {{ $totals['outflowChange'] >= 0 ? '+' : '' }}{{ number_format($totals['outflowChange'], 1) }}% from last month
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value">
                    {{ $totals['pendingCount'] ?? 0 }}
                </div>
                <div class="stat-label">Pending Transactions</div>
                <div class="stat-change">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Requires approval
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section mb-4">
            <div class="filter-header">
                <div class="filter-title">
                    <i class="fas fa-filter"></i>
                    Filters & Search
                </div>
                <div class="filter-presets">
                    <button type="button" class="preset-btn" data-preset="today">Today</button>
                    <button type="button" class="preset-btn" data-preset="week">This Week</button>
                    <button type="button" class="preset-btn" data-preset="month">This Month</button>
                    <button type="button" class="preset-btn" data-preset="quarter">This Quarter</button>
                    <button type="button" class="preset-btn" data-preset="year">This Year</button>
                </div>
            </div>
            <form method="GET" action="{{ route('admin.cashflow.index') }}" class="filter-form">
                <div class="form-group" style="display: none;">
                    <label class="form-label">Date Range</label>
                    <input type="text" id="dateRange" name="date_range" class="form-control" placeholder="Select date range" value="{{ request('date_range') }}">
                    <input type="hidden" id="dateFrom" name="date_from" value="{{ request('date_from') }}">
                    <input type="hidden" id="dateTo" name="date_to" value="{{ request('date_to') }}">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Transaction Type</label>
                    <select name="transaction_type" class="form-control">
                        <option value="">All Types</option>
                        <option value="INFLOW" {{ request('transaction_type') == 'INFLOW' ? 'selected' : '' }}>Inflow</option>
                        <option value="OUTFLOW" {{ request('transaction_type') == 'OUTFLOW' ? 'selected' : '' }}>Outflow</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control">
                        <option value="">All Categories</option>
                        <option value="OPERATING" {{ request('category') == 'OPERATING' ? 'selected' : '' }}>Operating</option>
                        <option value="INVESTING" {{ request('category') == 'INVESTING' ? 'selected' : '' }}>Investing</option>
                        <option value="FINANCING" {{ request('category') == 'FINANCING' ? 'selected' : '' }}>Financing</option>
                    </select>
                </div>
                
                <div class="form-group" style="display: none;">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="cleared" {{ request('status') == 'cleared' ? 'selected' : '' }}>Cleared</option>
                        <option value="reconciled" {{ request('status') == 'reconciled' ? 'selected' : '' }}>Reconciled</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search description, reference...">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="action-btn primary">
                        <i class="fas fa-search"></i>
                        Apply Filters
                    </button>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="action-section mb-4">
                <div class="action-header">
                    <i class="fas fa-rocket"></i>
                    Quick Actions
                </div>
                <div class="action-buttons">
                    <a href="{{ route('admin.cashflow.monthly-statement') }}" class="action-btn primary">
                        <i class="fas fa-chart-line"></i>
                        Monthly Statement
                    </a>
                    <a href="{{ route('admin.cashflow.fiscal-year-statement') }}" class="action-btn success">
                        <i class="fas fa-calendar-alt"></i>
                        Fiscal Year Statement
                    </a>
                    <a href="{{ route('admin.cashflow.create') }}" class="action-btn info">
                        <i class="fas fa-plus-circle"></i>
                        Add Transaction
                    </a>
                    <div class="dropdown">
                        <button class="action-btn secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-download"></i>
                            Export
                            <i class="fas fa-chevron-down ms-2"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('admin.cashflow.export', request()->all()) }}" class="dropdown-item">
                                    <i class="fas fa-file-excel text-success"></i>
                                    Export All to Excel
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.cashflow.export.monthly', ['fiscal_year_id' => request('fiscal_year_id', date('Y')), 'month' => date('n')]) }}" class="dropdown-item">
                                    <i class="fas fa-file-excel"></i>
                                    Export Current Month to Excel
                                </a>
                            </li>
                        </ul>
                    </div>
                    <a href="{{ route('dashboard') }}" class="action-btn secondary">
                        <i class="fas fa-arrow-left"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>

        <!-- Transactions Table -->
        <div class="table-section">
            <div class="table-header">
                <div class="table-title">
                    <i class="fas fa-list"></i>
                    Cash Flow Transactions
                </div>
                <div class="table-subtitle">
                    <span id="transactionCount">{{ $initialTransactions->total() }}</span> transactions found
                </div>
            </div>
            
            <div id="transactionsContainer">
                @if($initialTransactions->count() > 0)
                    <div class="table-responsive">
                        <table class="modern-table" id="transactionsTable">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-calendar me-2"></i> Date</th>
                                    <th><i class="fas fa-exchange-alt me-2"></i> Type</th>
                                    <th><i class="fas fa-tag me-2"></i> Category</th>
                                    <th><i class="fas fa-comment me-2"></i> Description</th>
                                    <th><i class="fas fa-coins me-2"></i> Amount</th>
                                    <th><i class="fas fa-credit-card me-2"></i> Method</th>
                                    <th><i class="fas fa-info-circle me-2"></i> Status</th>
                                    <th><i class="fas fa-database me-2"></i> Source</th>
                                    <th><i class="fas fa-cogs me-2"></i> Actions</th>
                                </tr>
                            </thead>
                            <tbody id="transactionsBody">
                                @foreach($initialTransactions as $transaction)
                                    @include('admin.cashflow.partials.transaction-row', ['transaction' => $transaction])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Load More Button -->
                    <div class="text-center mt-4" id="loadMoreContainer">
                        @if($initialTransactions->hasMorePages())
                            <button type="button" id="loadMoreBtn" class="action-btn primary" data-page="2">
                                <i class="fas fa-plus-circle"></i>
                                Load More Transactions
                            </button>
                        @endif
                    </div>
                    
                    <!-- Loading Skeleton -->
                    <div id="loadingSkeleton" style="display: none;">
                        @for($i = 0; $i < 5; $i++)
                            <div class="skeleton-row"></div>
                        @endfor
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="empty-state-title">Search Results Not Available</div>
                        <div class="empty-state-text">No transactions found matching your search criteria. Please try different search terms.</div>
                        <a href="{{ route('admin.cashflow.index') }}" class="action-btn primary">
                            <i class="fas fa-undo"></i>
                            Clear Search
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
@push('scripts')
<script>
// Enhanced JavaScript for better UX
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date range picker
    initializeDateRangePicker();
    
    // Initialize filter form
    initializeFilterForm();
    
    // Initialize lazy loading
    initializeLazyLoading();
    
    // Initialize filter presets
    initializeFilterPresets();
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Add loading states to buttons (exclude dropdown toggles, load more, and submit buttons)
    document.querySelectorAll('.action-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            // Skip if it's a dropdown toggle, load more button, or form submit button
            if (this.classList.contains('dropdown-toggle') || 
                this.id.includes('loadMore') || 
                this.type === 'submit') {
                return;
            }
            
            const originalContent = this.innerHTML;
            this.innerHTML = '<span class="loading-spinner"></span> Processing...';
            this.disabled = true;
            
            setTimeout(() => {
                this.innerHTML = originalContent;
                this.disabled = false;
            }, 2000);
        });
    });
    
    // Auto-refresh pending transactions every 30 seconds
    setInterval(updatePendingCount, 30000);
});

// Initialize Date Range Picker
function initializeDateRangePicker() {
    const dateRangeInput = document.getElementById('dateRange');
    const dateFromInput = document.getElementById('dateFrom');
    const dateToInput = document.getElementById('dateTo');
    
    // Only initialize if input is visible
    if (!dateRangeInput || dateRangeInput.offsetParent === null) {
        return;
    }
    
    flatpickr(dateRangeInput, {
        mode: 'range',
        dateFormat: 'Y-m-d',
        onChange: function(selectedDates, dateStr) {
            if (selectedDates.length === 2) {
                dateFromInput.value = selectedDates[0].toISOString().split('T')[0];
                dateToInput.value = selectedDates[1].toISOString().split('T')[0];
            }
        }
    });
}

// Initialize Filter Form
function initializeFilterForm() {
    const filterForm = document.querySelector('.filter-form');
    
    // Handle filter changes for real-time updates (dropdowns only)
    const filterInputs = filterForm.querySelectorAll('select');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);
            window.location.href = `{{ route('admin.cashflow.index') }}?${params.toString()}`;
        });
    });
    
    // Handle search input - submit only on Enter key
    const searchInput = filterForm.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData);
                window.location.href = `{{ route('admin.cashflow.index') }}?${params.toString()}`;
            }
        });
    }
}

// Initialize Filter Presets
function initializeFilterPresets() {
    const presetButtons = document.querySelectorAll('.preset-btn');
    
    presetButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            presetButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            const preset = this.dataset.preset;
            const dateRangeInput = document.getElementById('dateRange');
            const dateFromInput = document.getElementById('dateFrom');
            const dateToInput = document.getElementById('dateTo');
            
            const today = new Date();
            let fromDate, toDate;
            
            switch(preset) {
                case 'today':
                    fromDate = toDate = today;
                    break;
                case 'week':
                    fromDate = new Date(today.setDate(today.getDate() - today.getDay()));
                    toDate = new Date(today.setDate(today.getDate() - today.getDay() + 6));
                    break;
                case 'month':
                    fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    toDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    break;
                case 'quarter':
                    const quarter = Math.floor(today.getMonth() / 3);
                    fromDate = new Date(today.getFullYear(), quarter * 3, 1);
                    toDate = new Date(today.getFullYear(), quarter * 3 + 3, 0);
                    break;
                case 'year':
                    fromDate = new Date(today.getFullYear(), 0, 1);
                    toDate = new Date(today.getFullYear(), 11, 31);
                    break;
            }
            
            dateFromInput.value = fromDate.toISOString().split('T')[0];
            dateToInput.value = toDate.toISOString().split('T')[0];
            dateRangeInput.value = fromDate.toISOString().split('T')[0] + ' to ' + toDate.toISOString().split('T')[0];
            
            // Submit the form after setting the dates
            const filterForm = document.querySelector('.filter-form');
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);
            window.location.href = `{{ route('admin.cashflow.index') }}?${params.toString()}`;
        });
    });
}

// Initialize Lazy Loading
function initializeLazyLoading() {
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    const loadingSkeleton = document.getElementById('loadingSkeleton');
    const transactionsBody = document.getElementById('transactionsBody');
    const loadMoreContainer = document.getElementById('loadMoreContainer');
    
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            const page = parseInt(this.dataset.page);
            
            // Show loading skeleton
            loadingSkeleton.style.display = 'block';
            this.style.display = 'none';
            
            // Get current filter values
            const formData = new FormData(document.querySelector('form'));
            const params = new URLSearchParams(formData);
            params.set('load_transactions', '1');
            params.set('page', page);
            
            // Load more transactions
            fetch(`{{ route('admin.cashflow.load') }}?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    // Append new transactions
                    data.transactions.forEach(transaction => {
                        const row = createTransactionRow(transaction);
                        transactionsBody.appendChild(row);
                    });
                    
                    // Update transaction count
                    const countElement = document.getElementById('transactionCount');
                    countElement.textContent = data.pagination.total;
                    
                    // Hide loading skeleton
                    loadingSkeleton.style.display = 'none';
                    
                    // Update or hide load more button
                    if (data.pagination.has_more) {
                        this.dataset.page = page + 1;
                        this.style.display = 'inline-block';
                    } else {
                        loadMoreContainer.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error loading transactions:', error);
                    loadingSkeleton.style.display = 'none';
                    this.style.display = 'inline-block';
                });
        });
    }
}

// Create Transaction Row
function createTransactionRow(transaction) {
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <span class="date-badge">
                ${new Date(transaction.transaction_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
            </span>
        </td>
        <td>
            <span class="type-badge ${transaction.type === 'income' || transaction.type === 'INFLOW' ? 'inflow' : 'outflow'}">
                ${transaction.type.toUpperCase()}
            </span>
        </td>
        <td>
            <span class="category-badge ${transaction.category.toLowerCase()}">
                ${transaction.category}
            </span>
        </td>
        <td>
            <div>
                <strong>${transaction.description}</strong>
                ${transaction.reference_number ? `<br><small class="text-muted">Ref: ${transaction.reference_number}</small>` : ''}
                ${transaction.member_id ? `<br><small class="text-info">Member ID: ${transaction.member_id}</small>` : ''}
            </div>
        </td>
        <td>
            <span class="amount-display ${transaction.type === 'income' || transaction.type === 'INFLOW' ? 'inflow' : 'outflow'}">
                UGX ${parseInt(transaction.amount).toLocaleString()}
            </span>
        </td>
        <td>${transaction.payment_method || '-'}</td>
        <td>
            <span class="status-badge ${transaction.status.toLowerCase()}">
                ${transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1)}
            </span>
        </td>
        <td>
            <span class="badge bg-info">
                ${transaction.transaction_source || 'Manual Entry'}
            </span>
        </td>
        <td>
            <div class="dropdown">
                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-ellipsis-h"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="#" class="dropdown-item" onclick="showTransactionDetails('${transaction.source_model}', ${transaction.id})">
                            <i class="fas fa-eye"></i>
                            View Details
                        </a>
                    </li>
                    ${transaction.status === 'PENDING' ? `
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a href="#" class="dropdown-item" onclick="approveTransaction('${transaction.source_model}', ${transaction.id})">
                                <i class="fas fa-check-circle"></i>
                                Approve Transaction
                            </a>
                        </li>
                    ` : ''}
                </ul>
            </div>
        </td>
    `;
    return row;
}

// Update Pending Count
function updatePendingCount() {
    fetch('/admin/cashflow-dashboard/data')
        .then(response => response.json())
        .then(data => {
            const pendingCount = document.querySelector('.stat-card:nth-child(4) .stat-value');
            if (pendingCount && data.pendingCount !== undefined) {
                const currentCount = parseInt(pendingCount.textContent);
                if (data.pendingCount !== currentCount) {
                    pendingCount.textContent = data.pendingCount;
                    pendingCount.style.color = '#f59e0b';
                    setTimeout(() => {
                        pendingCount.style.color = '#2c3e50';
                    }, 1000);
                }
            }
        })
        .catch(error => console.error('Error updating pending count:', error));
}

function bulkApprovePending() {
    if (confirm('Are you sure you want to approve all pending transactions?')) {
        // Show loading state
        const button = event.target;
        const originalContent = button.innerHTML;
        button.innerHTML = '<span class="loading-spinner"></span> Approving...';
        
        // Submit form
        fetch('/admin/cashflow/bulk-approve', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                transaction_ids: getPendingTransactionIds()
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error approving transactions: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error approving transactions');
        })
        .finally(() => {
            button.innerHTML = originalContent;
        });
    }
}

function showTransactionDetails(sourceModel, transactionId) {
    // Redirect to appropriate detail page based on source model
    let url;
    switch(sourceModel) {
        case 'CashFlow':
            url = `/admin/cashflow/${transactionId}`;
            break;
        case 'CashflowTransaction':
            url = `/admin/cashflow/${transactionId}`;
            break;
        case 'Deposit':
            url = `/admin/group-savings/deposit/${transactionId}`;
            break;
        case 'Distribution':
            url = `/admin/group-savings/distribution/${transactionId}`;
            break;
        case 'Loan':
            url = `/admin/group-loans/${transactionId}`;
            break;
        case 'LoanRepayment':
            url = `/admin/group-loans/repayment/${transactionId}`;
            break;
        case 'Fine':
            url = `/admin/fines/${transactionId}`;
            break;
        case 'WelfareFund':
            url = `/admin/welfare/${transactionId}`;
            break;
        case 'LoanPenalty':
            url = `/admin/loans/penalty/${transactionId}`;
            break;
        default:
            url = `/admin/cashflow/${transactionId}`;
    }
    window.location.href = url;
}

function approveTransaction(sourceModel, transactionId) {
    if (confirm('Are you sure you want to approve this transaction?')) {
        let url;
        switch(sourceModel) {
            case 'CashflowTransaction':
                url = `/admin/cashflow/${transactionId}/approve`;
                break;
            case 'CashFlow':
                url = `/admin/cashflow/${transactionId}/approve`;
                break;
            default:
                alert('This transaction type cannot be approved from this interface.');
                return;
        }
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error approving transaction: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error approving transaction');
        });
    }
}

function getPendingTransactionIds() {
    const pendingIds = [];
    document.querySelectorAll('tr').forEach(row => {
        const statusBadge = row.querySelector('.status-badge');
        if (statusBadge && statusBadge.textContent.toLowerCase().includes('pending')) {
            const transactionId = row.querySelector('[onclick*="showTransactionDetails"]').getAttribute('onclick').match(/\d+/)[0];
            pendingIds.push(parseInt(transactionId));
        }
    });
    return pendingIds;
}
</script>
@endpush
