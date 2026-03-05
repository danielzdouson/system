@extends('layouts.member')

@section('content')
<div class="container-fluid p-4">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card gradient-bg text-white border-0 animate__animated animate__fadeInDown">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-3">
                                <div class="user-avatar me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                    {{ strtoupper(substr($member->first_name, 0, 1)) }}
                                </div>
                                <div>
                                    <h2 class="mb-1 animate__animated animate__fadeInLeft">Welcome back, {{ $member->first_name }}!</h2>
                                    <p class="mb-0 opacity-75 animate__animated animate__fadeInLeft animate__delay-1s">
                                        <i class="fas fa-chart-line me-2"></i>Track your financial journey
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="animate__animated animate__fadeInRight">
                                <small class="d-block opacity-75 mb-1">Account Number</small>
                                <div class="d-inline-block bg-white bg-opacity-20 rounded px-3 py-1">
                                    <strong class="fs-5">#{{ str_pad($member->id, 6, '0', STR_PAD_LEFT) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Summary Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 animate__animated animate__fadeInUp animate__delay-1s">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                                <i class="fas fa-piggy-bank fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">My Savings</h6>
                            <h4 class="mb-0 text-success">UGX {{ number_format($totalSavings, 0) }}</h4>
                            <small class="text-success">
                                @if($monthlySavingsGrowth > 0)
                                    <i class="fas fa-arrow-up"></i> +{{ number_format($monthlySavingsGrowth, 1) }}% this month
                                @elseif($monthlySavingsGrowth < 0)
                                    <i class="fas fa-arrow-down"></i> {{ number_format($monthlySavingsGrowth, 1) }}% this month
                                @else
                                    <i class="fas fa-minus"></i> No change this month
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 animate__animated animate__fadeInUp animate__delay-2s">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-3">
                                <i class="fas fa-hand-holding-usd fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Active Loans</h6>
                            <h4 class="mb-0 text-danger">UGX {{ number_format($totalLoans, 0) }}</h4>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> {{ $activeLoans->count() }} active
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 animate__animated animate__fadeInUp animate__delay-4s">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                                <i class="fas fa-calendar-alt fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Next Payment</h6>
                            @if($nextPayment)
                                <h4 class="mb-0 text-warning">UGX {{ number_format($nextPayment['amount'], 0) }}</h4>
                                <small class="text-warning">
                                    <i class="fas fa-clock"></i> {{ $nextPayment['due_date']->format('M j, Y') }}
                                </small>
                            @else
                                <h4 class="mb-0 text-muted">-</h4>
                                <small class="text-muted">No pending payments</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm animate__animated animate__fadeInUp animate__delay-5s">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt text-warning me-2"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="#" onclick="showLoanApplicationModal()" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4 text-decoration-none hover-scale">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3 mb-3">
                                    <i class="fas fa-plus-circle fa-2x text-primary"></i>
                                </div>
                                <strong>Apply for Loan</strong>
                                <small class="text-muted">Check eligibility</small>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('member.transactions') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4 text-decoration-none hover-scale">
                                <div class="bg-info bg-opacity-10 rounded-circle p-3 mb-3">
                                    <i class="fas fa-history fa-2x text-info"></i>
                                </div>
                                <strong>View Transactions</strong>
                                <small class="text-muted">Complete history</small>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="#" onclick="downloadStatement()" class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4 text-decoration-none hover-scale">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3 mb-3">
                                    <i class="fas fa-file-download fa-2x text-success"></i>
                                </div>
                                <strong>Download Statement</strong>
                                <small class="text-muted">Full statement</small>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('profile.edit') }}" class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4 text-decoration-none hover-scale">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3 mb-3">
                                    <i class="fas fa-user-edit fa-2x text-warning"></i>
                                </div>
                                <strong>Update Profile</strong>
                                <small class="text-muted">Personal details</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-scale {
            transition: all 0.3s ease;
        }
        .hover-scale:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
        }
        
        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 2rem;
            border-radius: 10px;
            max-width: 500px;
            width: 90%;
        }
    </style>

    <!-- Comprehensive Financial Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm animate__animated animate__fadeInUp animate__delay-7s">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line text-primary me-2"></i>
                        Complete Financial Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Shares Information -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="bg-light rounded p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-2">
                                        <i class="fas fa-certificate"></i>
                                    </div>
                                    <h6 class="mb-0">Shares Information</h6>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Total Shares</small>
                                    <div class="fw-bold text-primary">
                                        @if($totalShares > 0)
                                            {{ number_format($totalShares, 2) }}%
                                        @else
                                            <span class="text-muted">No shares yet</span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <small class="text-muted">Shares on Hold</small>
                                    <div class="fw-bold text-warning">
                                        @if($sharesOnHold > 0)
                                            {{ number_format($sharesOnHold, 2) }}%
                                        @else
                                            <span class="text-muted">None on hold</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fines Information -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="bg-light rounded p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2 me-2">
                                        <i class="fas fa-gavel"></i>
                                    </div>
                                    <h6 class="mb-0">Fines Status</h6>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Pending Fines</small>
                                    <div class="fw-bold text-danger">
                                        @if($totalFines > 0)
                                            UGX {{ number_format($totalFines, 0) }}
                                        @else
                                            <span class="text-success">No pending fines</span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <small class="text-muted">Paid Fines</small>
                                    <div class="fw-bold text-success">
                                        @if($paidFines > 0)
                                            UGX {{ number_format($paidFines, 0) }}
                                        @else
                                            <span class="text-muted">None paid</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Deposits -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="bg-light rounded p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-2">
                                        <i class="fas fa-coins"></i>
                                    </div>
                                    <h6 class="mb-0">Total Deposits</h6>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">All Deposits Made</small>
                                    <div class="fw-bold text-success">
                                        @if($totalDeposits > 0)
                                            UGX {{ number_format($totalDeposits, 0) }}
                                        @else
                                            <span class="text-muted">No deposits yet</span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <small class="text-muted">Savings Contributions</small>
                                    <div class="fw-bold text-info">
                                        @if($totalContributions > 0)
                                            UGX {{ number_format($totalContributions, 0) }}
                                        @else
                                            <span class="text-muted">No contributions</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Group Investments -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="bg-light rounded p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-info bg-opacity-10 text-info rounded-circle p-2 me-2">
                                        <i class="fas fa-chart-pie"></i>
                                    </div>
                                    <h6 class="mb-0">Group Investments</h6>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Principal Invested</small>
                                    <div class="fw-bold text-info">
                                        @if($totalInvestmentPrincipal > 0)
                                            UGX {{ number_format($totalInvestmentPrincipal, 0) }}
                                        @else
                                            <span class="text-muted">No investments</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Total Inflows</small>
                                    <div class="fw-bold text-success">
                                        @if($totalInvestmentInflows > 0)
                                            UGX {{ number_format($totalInvestmentInflows, 0) }}
                                        @else
                                            <span class="text-muted">No inflows yet</span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <small class="text-muted">Return on Investment</small>
                                    <div class="fw-bold {{ $roi >= 0 ? 'text-success' : 'text-danger' }}">
                                        @if($totalInvestmentPrincipal > 0)
                                            {{ number_format($roi, 2) }}%
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Investment Details -->
                    @if($groupInvestments->isNotEmpty())
                        <div class="mt-4">
                            <h6 class="mb-3">
                                <i class="fas fa-briefcase me-2"></i>
                                Investment Portfolio Details
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Investment Name</th>
                                            <th>Type</th>
                                            <th>Principal</th>
                                            <th>Returns</th>
                                            <th>Status</th>
                                            <th>Inflows</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($groupInvestments as $investment)
                                            <tr>
                                                <td>
                                                    <div class="fw-medium">{{ $investment->name }}</div>
                                                    <small class="text-muted">{{ $investment->institution }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $investment->investment_type }}</span>
                                                </td>
                                                <td>
                                                    <div class="fw-bold">UGX {{ number_format($investment->principal_amount, 0) }}</div>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-success">UGX {{ number_format($investment->total_returns, 0) }}</div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $investment->status == 'ACTIVE' ? 'success' : 'secondary' }}">
                                                        {{ $investment->status }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-info">UGX {{ number_format($investment->transactions->sum('amount'), 0) }}</div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="mt-3 text-center text-muted">
                            <small>No group investments found</small>
                        </div>
                    @endif

                    <!-- Debug Information (remove in production) -->
                    <div class="mt-4">
                        <details>
                            <summary class="text-muted small cursor-pointer">Debug Information</summary>
                            <div class="mt-2 p-3 bg-light rounded">
                                <small class="text-muted">
                                    Member ID: {{ $member->id }}<br>
                                    Member Account ID: {{ $memberAccount ? $memberAccount->id : 'None' }}<br>
                                    Fiscal Year ID: {{ $currentFiscalYear ? $currentFiscalYear->id : 'None' }}<br>
                                    Total Shares: {{ $totalShares }}<br>
                                    Shares on Hold: {{ $sharesOnHold }}<br>
                                    Total Fines: {{ $totalFines }}<br>
                                    Paid Fines: {{ $paidFines }}<br>
                                    Total Deposits: {{ $totalDeposits }}<br>
                                    Total Contributions: {{ $totalContributions }}<br>
                                    Actual Savings: {{ $actualSavings ?? 0 }}<br>
                                    Account Savings: {{ $totalSavings }}<br>
                                    Investment Count: {{ $groupInvestments->count() }}<br>
                                    Investment Principal: {{ $totalInvestmentPrincipal }}<br>
                                    Investment Inflows: {{ $totalInvestmentInflows }}<br>
                                    Monthly Growth: {{ $monthlySavingsGrowth }}%
                                </small>
                            </div>
                        </details>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Insights Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm animate__animated animate__fadeInUp animate__delay-6s">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie text-info me-2"></i>
                        Financial Insights
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="text-center">
                <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-block mb-2">
                    <i class="fas fa-percentage fa-2x text-primary"></i>
                </div>
                <h6 class="text-muted">Loan-to-Savings Ratio</h6>
                <h4 class="text-primary">
                    @if($totalSavings > 0)
                        {{ round(($totalLoans / $totalSavings) * 100, 1) }}%
                    @else
                        N/A
                    @endif
                </h4>
                <small class="text-muted">
                    @if($totalSavings > 0 && ($totalLoans / $totalSavings) <= 0.5)
                        <i class="fas fa-check-circle text-success"></i> Healthy
                    @elseif($totalSavings > 0 && ($totalLoans / $totalSavings) <= 0.8)
                        <i class="fas fa-exclamation-triangle text-warning"></i> Moderate
                    @else
                        <i class="fas fa-times-circle text-danger"></i> High Risk
                    @endif
                </small>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="text-center">
                <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-block mb-2">
                    <i class="fas fa-wallet fa-2x text-success"></i>
                </div>
                <h6 class="text-muted">Net Worth</h6>
                <h4 class="text-success">UGX {{ number_format($netWorth ?? 0, 0) }}</h4>
                <small class="text-muted">Total assets minus liabilities</small>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="text-center">
                <div class="bg-info bg-opacity-10 rounded-circle p-3 d-inline-block mb-2">
                    <i class="fas fa-chart-line fa-2x text-info"></i>
                </div>
                <h6 class="text-muted">Investment ROI</h6>
                <h4 class="text-info">
                    @if($totalInvestmentPrincipal > 0)
                        {{ number_format($roi, 1) }}%
                    @else
                        N/A
                    @endif
                </h4>
                <small class="text-muted">Return on investments</small>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="text-center">
                <div class="bg-warning bg-opacity-10 rounded-circle p-3 d-inline-block mb-2">
                    <i class="fas fa-award fa-2x text-warning"></i>
                </div>
                <h6 class="text-muted">Member Since</h6>
                <h4 class="text-warning">{{ $member->created_at->format('Y') }}</h4>
                <small class="text-muted">{{ $member->created_at->diffForHumans() }}</small>
            </div>
        </div>
    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Transactions -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm animate__animated animate__fadeInLeft">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-exchange-alt text-info me-2"></i>
                        Recent Transactions
                    </h5>
                    <a href="{{ route('member.transactions') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye me-1"></i>View All
                    </a>
                </div>
                <div class="card-body">
                    @if($recentTransactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-calendar me-1"></i>Date</th>
                                        <th>Description</th>
                                        <th>Type</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransactions as $transaction)
                                        <tr class="transaction-row">
                                            <td>
                                                <small>{{ $transaction->created_at->format('M j, Y') }}</small>
                                                <br><span class="text-muted">{{ $transaction->created_at->format('H:i') }}</span>
                                            </td>
                                            <td>
                                                <div class="fw-medium">{{ $transaction->description ?? 'Transaction' }}</div>
                                                <small class="text-muted">{{ $transaction->reference ?? 'No reference' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $transaction->type == 'deposit' ? 'success' : 'danger' }} badge-lg">
                                                    <i class="fas fa-{{ $transaction->type == 'deposit' ? 'arrow-down' : 'arrow-up' }} me-1"></i>
                                                    {{ ucfirst($transaction->type) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="fw-bold {{ $transaction->type == 'deposit' ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->type == 'deposit' ? '+' : '-' }} UGX {{ number_format($transaction->amount, 0) }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="bg-light rounded-circle p-4 d-inline-block mb-3">
                                <i class="fas fa-inbox fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">No transactions found</h5>
                            <p class="text-muted">Your transaction history will appear here</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Active Loans Summary -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm animate__animated animate__fadeInRight">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-hand-holding-usd text-danger me-2"></i>
                        Active Loans
                    </h5>
                    <a href="{{ route('member.loans') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye me-1"></i>View All
                    </a>
                </div>
                <div class="card-body">
                    @if($activeLoans->count() > 0)
                        @foreach($activeLoans as $loan)
                            <div class="mb-4 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="mb-1">Loan #{{ $loan->loan_number }}</h6>
                                        <p class="text-muted mb-2 small">{{ $loan->loan_purpose }}</p>
                                        <div class="d-flex gap-1">
                                            <span class="badge bg-primary">Active</span>
                                            <span class="badge bg-info">{{ $loan->interest_type }}</span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block">Balance</small>
                                        <strong class="text-danger">UGX {{ number_format($loan->balance, 0) }}</strong>
                                    </div>
                                </div>
                                
                                <!-- Enhanced Progress Bar -->
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="text-muted">Repayment Progress</small>
                                        <small class="text-muted fw-bold">{{ round(($loan->total_repayment / $loan->total_repayable) * 100) }}%</small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <?php 
                                        $progress = ($loan->total_repayment / $loan->total_repayable) * 100;
                                        $progress = min(100, max(0, $progress));
                                        ?>
                                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                                             style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>
                                
                                <div class="row g-2 text-center">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Original</small>
                                        <strong>UGX {{ number_format($loan->loan_amount, 0) }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Monthly</small>
                                        <strong>UGX {{ number_format($loan->monthly_payment, 0) }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <div class="bg-light rounded-circle p-4 d-inline-block mb-3">
                                <i class="fas fa-hand-holding-usd fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">No active loans</h5>
                            <p class="text-muted">Apply for a loan to get started</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loan Application Modal -->
<div id="loanModal" class="modal-overlay">
    <div class="modal-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="fas fa-hand-holding-usd me-2"></i>
                Apply for Loan
            </h4>
            <button type="button" onclick="closeLoanModal()" class="btn btn-close"></button>
        </div>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Loan applications are currently being processed. Please contact the office for assistance.
        </div>
        
        <div class="mb-3">
            <label class="form-label">Current Savings Balance</label>
            <div class="form-control bg-light">
                UGX {{ number_format($totalSavings, 0) }}
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Active Loans</label>
            <div class="form-control bg-light">
                {{ $activeLoans->count() }} active loan(s)
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <button type="button" onclick="closeLoanModal()" class="btn btn-secondary">Close</button>
            <button type="button" onclick="contactOffice()" class="btn btn-primary">
                <i class="fas fa-phone me-2"></i>Contact Office
            </button>
        </div>
    </div>
</div>

<!-- Statement Download Modal -->
<div id="statementModal" class="modal-overlay">
    <div class="modal-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="fas fa-file-download me-2"></i>
                Download Statement
            </h4>
            <button type="button" onclick="closeStatementModal()" class="btn btn-close"></button>
        </div>
        
        <form method="POST" action="{{ route('member.statement.download') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Statement Type</label>
                <select name="type" class="form-select" required>
                    <option value="full">Full Statement</option>
                    <option value="savings">Savings Only</option>
                    <option value="loans">Loan Statement</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Format</label>
                <select name="format" class="form-select" required>
                    <option value="pdf">PDF</option>
                    <option value="excel">Excel</option>
                </select>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from_date" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to_date" class="form-control" required>
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <button type="button" onclick="closeStatementModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-download me-2"></i>Download
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showLoanApplicationModal() {
    document.getElementById('loanModal').style.display = 'block';
}

function closeLoanModal() {
    document.getElementById('loanModal').style.display = 'none';
}

function downloadStatement() {
    document.getElementById('statementModal').style.display = 'block';
}

function closeStatementModal() {
    document.getElementById('statementModal').style.display = 'none';
}

function contactOffice() {
    window.location.href = 'mailto:office@sacco.com?subject=Loan Application Inquiry';
}

// Close modals when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        event.target.style.display = 'none';
    }
}
</script>
@endsection
