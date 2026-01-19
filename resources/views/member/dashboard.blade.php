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
                                <i class="fas fa-arrow-up"></i> +12% this month
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
            <div class="card border-0 shadow-sm h-100 animate__animated animate__fadeInUp animate__delay-3s">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 text-info rounded-circle p-3">
                                <i class="fas fa-credit-card fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Available Credit</h6>
                            <h4 class="mb-0 text-info">UGX {{ number_format($availableCredit, 0) }}</h4>
                            <small class="text-info">
                                <i class="fas fa-check-circle"></i> Ready to use
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
                            <a href="#" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4 text-decoration-none hover-scale">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3 mb-3">
                                    <i class="fas fa-plus-circle fa-2x text-primary"></i>
                                </div>
                                <strong>Apply for Loan</strong>
                                <small class="text-muted">Get instant approval</small>
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
                            <a href="#" class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4 text-decoration-none hover-scale">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3 mb-3">
                                    <i class="fas fa-file-download fa-2x text-success"></i>
                                </div>
                                <strong>Download Statement</strong>
                                <small class="text-muted">PDF export</small>
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
    </style>

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
                                                @if($transaction->cashflowTransaction)
                                                    <small class="text-muted">{{ $transaction->cashflowTransaction->description }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $transaction->transaction_type == 'deposit' ? 'success' : 'danger' }} badge-lg">
                                                    <i class="fas fa-{{ $transaction->transaction_type == 'deposit' ? 'arrow-down' : 'arrow-up' }} me-1"></i>
                                                    {{ ucfirst($transaction->transaction_type) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="fw-bold {{ $transaction->transaction_type == 'deposit' ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->transaction_type == 'deposit' ? '+' : '-' }} UGX {{ number_format($transaction->amount, 0) }}
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
@endsection
