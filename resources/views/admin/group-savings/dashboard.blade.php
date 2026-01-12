@extends('layouts.admin')

@section('title', 'Group Savings Management')

@section('content')
<div class="container-fluid">
    <!-- Enhanced Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-2 text-white">
                            <i class="fas fa-users-cog me-3"></i>
                            Group Savings Management
                        </h1>
                        <p class="text-white fs-5 mb-0 opacity-90">
                            <i class="fas fa-chart-line me-2"></i>
                            Comprehensive savings tracking and financial management
                        </p>
                    </div>
                    <div class="text-end">
                        <!-- Fiscal Year Selector -->
                        <div class="fiscal-year-selector mb-3">
                            <select class="form-select" onchange="changeFiscalYear(this.value)" style="width: 250px;">
                                <option value="">Select Fiscal Year</option>
                                @foreach($allFiscalYears as $year)
                                    <option value="{{ $year->id }}" {{ $selectedYear == $year->id ? 'selected' : '' }}
                                            class="{{ $year->start_date <= $currentDate && $year->end_date >= $currentDate ? 'current-year' : '' }}">
                                        {{ $year->name }} ({{ $year->start_date->format('Y') }}-{{ $year->end_date->format('Y') }})
                                        @if($year->start_date <= $currentDate && $year->end_date >= $currentDate)
                                            <span class="badge bg-success ms-2">Current</span>
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        @if($activeFiscalYear)
                            <div class="fiscal-year-badge">
                                <span class="badge bg-white text-primary fs-6 px-3 py-2">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    {{ $activeFiscalYear->name }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($activeFiscalYear)
        <!-- Enhanced Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card primary-gradient">
                <div class="stat-card-body">
                    <div class="stat-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="stat-content">
                        <h4 class="stat-title">Total Deposits</h4>
                        <h2 class="stat-number">UGX {{ number_format($totalDeposits, 0) }}</h2>
                    </div>
                    <div class="stat-trend">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card success-gradient">
                <div class="stat-card-body">
                    <div class="stat-icon">
                        <i class="fas fa-piggy-bank"></i>
                    </div>
                    <div class="stat-content">
                        <h4 class="stat-title">Total Savings</h4>
                        <h2 class="stat-number">UGX {{ number_format($totalSavings, 0) }}</h2>
                    </div>
                    <div class="stat-trend">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card info-gradient">
                <div class="stat-card-body">
                    <div class="stat-icon">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <div class="stat-content">
                        <h4 class="stat-title">Total Welfare</h4>
                        <h2 class="stat-number">UGX {{ number_format($totalWelfare, 0) }}</h2>
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
                        <h4 class="stat-title">Total Fines</h4>
                        <h2 class="stat-number">UGX {{ number_format($totalFines, 0) }}</h2>
                    </div>
                    <div class="stat-trend">
                        <i class="fas fa-exclamation-triangle"></i>
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
                            <a href="{{ route('admin.group-savings.create-deposit') }}" class="action-btn primary">
                                <i class="fas fa-plus-circle me-2"></i>
                                New Deposit
                            </a>
                            <a href="{{ route('admin.group-savings.pending') }}" class="action-btn warning">
                                <i class="fas fa-clock me-2"></i>
                                Pending Months
                                <span class="badge bg-white text-warning ms-2">{{ $pendingMonthsCount }}</span>
                            </a>
                            <a href="{{ route('admin.group-savings.fines') }}" class="action-btn danger">
                                <i class="fas fa-gavel me-2"></i>
                                Fines Management
                                <span class="badge bg-white text-danger ms-2">{{ $unpaidFinesCount }} unpaid</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Monthly Overview -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="monthly-card">
                    <div class="monthly-card-header">
                        <h5 class="monthly-title">
                            <i class="fas fa-calendar-alt me-2"></i>
                            Monthly Overview - {{ $activeFiscalYear->name }}
                        </h5>
                        <div class="monthly-subtitle">
                            Click any month to view detailed breakdown
                        </div>
                    </div>
                    <div class="monthly-card-body">
                        <div class="months-grid">
                            @if(isset($monthsInFiscalYear))
                                @foreach($monthsInFiscalYear as $monthData)
                                    <div class="month-item {{ $monthData['is_current'] ? 'current-month' : '' }} {{ $monthData['is_past'] ? 'past-month' : '' }} {{ $monthData['is_future'] ? 'future-month' : '' }}">
                                        <a href="{{ route('admin.group-savings.monthly', $monthData['month']) }}" class="month-link">
                                            <div class="month-icon">
                                                <i class="fas fa-calendar-day"></i>
                                            </div>
                                            <div class="month-name">{{ $monthData['name'] }}</div>
                                            <div class="month-year">{{ $monthData['year'] }}</div>
                                            <div class="month-status">
                                                @if($monthData['is_current'])
                                                    <span class="status-indicator current">Current</span>
                                                @elseif($monthData['is_past'])
                                                    <span class="status-indicator past">Past</span>
                                                @else
                                                    <span class="status-indicator future">Future</span>
                                                @endif
                                            </div>
                                            <div class="month-action">
                                                <i class="fas fa-chart-line"></i>
                                                View Details
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            @else
                                @for($month = 1; $month <= 12; $month++)
                                    <div class="month-item">
                                        <a href="{{ route('admin.group-savings.monthly', ['month' => $month, 'fiscal_year' => $selectedYear]) }}" class="month-link">
                                            <div class="month-icon">
                                                <i class="fas fa-calendar-day"></i>
                                            </div>
                                            <div class="month-name">{{ \Carbon\Carbon::create()->month($month)->format('F') }}</div>
                                            <div class="month-year">
                                                @if($activeFiscalYear)
                                                    @php
                                                        // Determine the correct year based on fiscal year date range
                                                        $monthDate = \Carbon\Carbon::create($activeFiscalYear->start_date->year, $month, 1);
                                                        if ($month < $activeFiscalYear->start_date->month) {
                                                            $monthDate->addYear();
                                                        }
                                                        $displayYear = $monthDate->format('Y');
                                                    @endphp
                                                    {{ $displayYear }}
                                                @else
                                                    {{ $currentDate->format('Y') }}
                                                @endif
                                            </div>
                                            <div class="month-action">
                                                <i class="fas fa-chart-line"></i>
                                                View Details
                                            </div>
                                        </a>
                                    </div>
                                @endfor
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- Enhanced No Fiscal Year Alert with Create Option -->
        <div class="row">
            <div class="col-12">
                <div class="alert-card">
                    <div class="alert-card-body">
                        <div class="alert-icon">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <div class="alert-content">
                            <h5 class="alert-title">No Fiscal Year Found</h5>
                            <p class="alert-message">
                                You need to create a fiscal year to start managing group savings. 
                                Fiscal years help organize your savings data by calendar periods.
                            </p>
                            <div class="d-flex gap-3">
                                <button class="btn btn-alert" data-bs-toggle="modal" data-bs-target="#createFiscalYearModal">
                                    <i class="fas fa-plus me-2"></i>
                                    Create Fiscal Year
                                </button>
                                <a href="#" class="btn btn-outline-light">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Learn More
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Fiscal Year Modal -->
        <div class="modal fade" id="createFiscalYearModal" tabindex="-1" aria-labelledby="createFiscalYearModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="createFiscalYearModalLabel">
                            <i class="fas fa-calendar-plus me-2"></i>
                            Create New Fiscal Year
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.fiscal-years.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="fiscalYearName" class="form-label">Fiscal Year Name</label>
                                <input type="text" class="form-control" id="fiscalYearName" name="name" 
                                       placeholder="e.g., 2025-2026" required>
                                <div class="form-text">Enter a descriptive name for this fiscal year</div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="startDate" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="startDate" name="start_date" required>
                                    <div class="form-text">When this fiscal year begins</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="endDate" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="endDate" name="end_date" required>
                                    <div class="form-text">When this fiscal year ends</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <div class="form-text">Set this fiscal year as active to start using it</div>
                            </div>
                            
                            <div class="alert alert-info">
                                <h6 class="alert-heading">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Fiscal Year Guidelines
                                </h6>
                                <ul class="mb-0">
                                    <li>Fiscal years typically span 12 months</li>
                                    <li>Start and end dates should align with your SACCO's calendar</li>
                                    <li>Only one fiscal year should be active at a time</li>
                                    <li>Current date: {{ now()->format('M d, Y') }}</li>
                                </ul>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Create Fiscal Year
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
/* Enhanced Page Header */
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

.stat-trend {
    position: absolute;
    top: 1rem;
    right: 1rem;
    font-size: 1.2rem;
    opacity: 0.7;
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

.action-btn.danger {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
}

.action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    color: white;
}

/* Enhanced Monthly Overview */
.monthly-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.monthly-card-header {
    background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
    color: white;
    padding: 2rem;
}

.monthly-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.monthly-subtitle {
    opacity: 0.9;
    margin: 0;
}

.monthly-card-body {
    padding: 2rem;
}

.months-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1.5rem;
}

.month-item {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.month-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.month-link {
    display: block;
    padding: 1.5rem;
    text-align: center;
    text-decoration: none;
    color: #333;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    transition: all 0.3s ease;
}

.month-link:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.month-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: #667eea;
    transition: all 0.3s ease;
}

.month-link:hover .month-icon {
    color: white;
    transform: scale(1.1);
}

.month-name {
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.month-year {
    font-size: 0.9rem;
    opacity: 0.7;
    margin-bottom: 1rem;
}

.month-action {
    font-size: 0.8rem;
    font-weight: 600;
    opacity: 0;
    transition: all 0.3s ease;
}

.month-link:hover .month-action {
    opacity: 1;
}

/* Enhanced Month Status Indicators */
.month-status {
    margin-bottom: 0.5rem;
}

.status-indicator {
    display: inline-block;
    padding: 0.2rem 0.6rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-indicator.current {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    animation: pulse-current 2s infinite;
}

.status-indicator.past {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.status-indicator.future {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
}

/* Enhanced Month Items */
.month-item.current-month {
    border: 2px solid #11998e;
    box-shadow: 0 8px 25px rgba(17, 153, 142, 0.3);
}

.month-item.current-month:hover {
    transform: translateY(-8px) scale(1.05);
    box-shadow: 0 15px 35px rgba(17, 153, 142, 0.4);
}

.month-item.past-month {
    opacity: 0.8;
}

.month-item.future-month {
    opacity: 0.6;
}

.month-item.future-month:hover {
    opacity: 1;
}

/* Pulse Animation for Current Month */
@keyframes pulse-current {
    0% {
        box-shadow: 0 0 0 0 rgba(17, 153, 142, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(17, 153, 142, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(17, 153, 142, 0);
    }
}

/* Enhanced Alert Card */
.alert-card {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.alert-card-body {
    padding: 2rem;
    display: flex;
    align-items: center;
    color: white;
}

.alert-icon {
    font-size: 4rem;
    opacity: 0.3;
    margin-right: 2rem;
}

.alert-content {
    flex: 1;
}

.alert-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.alert-message {
    margin-bottom: 1.5rem;
    opacity: 0.9;
}

.btn-alert {
    background: white;
    color: #f5576c;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-alert:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    color: #f5576c;
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
    
    .months-grid {
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 1rem;
    }
    
    .monthly-card-header {
        padding: 1.5rem;
    }
    
    .monthly-card-body {
        padding: 1.5rem;
    }
    
    .month-link {
        padding: 1rem;
    }
    
    .alert-card-body {
        flex-direction: column;
        text-align: center;
    }
    
    .alert-icon {
        margin-right: 0;
        margin-bottom: 1rem;
    }
}

/* Fiscal Year Selector */
.fiscal-year-selector select {
    border-radius: 10px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.9);
    color: #333;
    font-weight: 600;
}

.fiscal-year-selector select:focus {
    background: rgba(255, 255, 255, 1);
    border-color: rgba(255, 255, 255, 0.8);
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}

.fiscal-year-selector select option {
    background: white;
    color: #333;
    padding: 8px;
}

.fiscal-year-selector select option:hover {
    background: #f8f9fa;
}

/* Current Year Indicator */
.fiscal-year-selector .current-year {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;
    color: white !important;
    font-weight: 700;
}

.fiscal-year-selector .badge.bg-success {
    background: #28a745;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
}

/* CSV Export Section */
.btn-outline-primary {
    background: transparent;
    border: 2px solid #667eea;
    color: #667eea;
    font-weight: 600;
}

.btn-outline-primary:hover {
    background: #667eea;
    color: white;
}

.alert-heading {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.badge {
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
    font-weight: 600;
    border-radius: 50px;
}

/* Grid Layout for Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stats-grid .stat-card {
    min-height: 180px;
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

<!-- JavaScript for Fiscal Year Switching -->
<script>
function changeFiscalYear(yearId) {
    if (yearId) {
        window.location.href = `/admin/group-savings/dashboard?fiscal_year=${yearId}`;
    } else {
        window.location.href = '/admin/group-savings/dashboard';
    }
}
</script>
@endsection
