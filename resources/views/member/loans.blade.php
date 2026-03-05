@extends('layouts.member')

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card gradient-bg text-white border-0 animate__animated animate__fadeInDown">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2 animate__animated animate__fadeInLeft">
                                <i class="fas fa-hand-holding-usd me-3"></i>My Loans
                            </h2>
                            <p class="mb-0 opacity-75 animate__animated animate__fadeInLeft animate__delay-1s">
                                Manage and track your loan portfolio efficiently
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="animate__animated animate__fadeInRight">
                                <a href="{{ route('member.dashboard') }}" class="btn btn-light">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loan Summary Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 animate__animated animate__fadeInUp">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Active Loans</h6>
                            <h4 class="mb-0 text-success">{{ $activeLoans->count() }}</h4>
                            <small class="text-success">Currently running</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 animate__animated animate__fadeInUp animate__delay-1s">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 text-info rounded-circle p-3">
                                <i class="fas fa-trophy"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Completed Loans</h6>
                            <h4 class="mb-0 text-info">{{ $completedLoans->count() }}</h4>
                            <small class="text-info">Successfully paid</small>
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
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Outstanding</h6>
                            <h4 class="mb-0 text-danger">UGX {{ number_format($activeLoans->sum('balance'), 0) }}</h4>
                            <small class="text-danger">Across all loans</small>
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
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Next Payment</h6>
                            <h4 class="mb-0 text-warning">
                                @if($activeLoans->isNotEmpty() && $activeLoans->first()->repaymentSchedules->isNotEmpty())
                                    UGX {{ number_format($activeLoans->first()->repaymentSchedules->where('status', 'pending')->first()->amount ?? 0, 0) }}
                                @else
                                    -
                                @endif
                            </h4>
                            <small class="text-warning">Upcoming due</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Loans -->
    @if($activeLoans->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm animate__animated animate__fadeInUp">
                    <div class="card-header bg-white border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-circle text-success me-2"></i>
                                Active Loans ({{ $activeLoans->count() }})
                            </h5>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary" onclick="exportLoans('active')">
                                    <i class="fas fa-download me-1"></i>Export Active
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @foreach($activeLoans as $loan)
                            <div class="mb-5 pb-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <!-- Loan Header -->
                                        <div class="d-flex justify-content-between align-items-start mb-4">
                                            <div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <h5 class="mb-0 me-3">Loan #{{ $loan->loan_number }}</h5>
                                                    <div class="d-flex gap-2">
                                                        <span class="badge bg-success badge-lg">
                                                            <i class="fas fa-play-circle me-1"></i>Active
                                                        </span>
                                                        <span class="badge bg-info">
                                                            <i class="fas fa-percentage me-1"></i>{{ $loan->interest_type }}
                                                        </span>
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-clock me-1"></i>{{ $loan->duration_months }} months
                                                        </span>
                                                    </div>
                                                </div>
                                                <p class="text-muted mb-3">{{ $loan->loan_purpose }}</p>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted d-block mb-1">Outstanding Balance</small>
                                                <h3 class="text-danger mb-0">UGX {{ number_format($loan->balance, 0) }}</h3>
                                                <small class="text-muted">of UGX {{ number_format($loan->total_repayable, 0) }}</small>
                                            </div>
                                        </div>

                                        <!-- Enhanced Progress Section -->
                                        <div class="mb-4">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0">Repayment Progress</h6>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-{{ getProgressColor($loan) }}">
                                                        {{ getLoanStatus($loan) }}
                                                    </span>
                                                    <strong class="text-primary">{{ round(($loan->total_repayment / $loan->total_repayable) * 100) }}%</strong>
                                                </div>
                                            </div>
                                            <div class="progress" style="height: 12px;">
                                                <?php 
                                                $progress = ($loan->total_repayment / $loan->total_repayable) * 100;
                                                $progress = min(100, max(0, $progress));
                                                ?>
                                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-{{ getProgressColor($loan) }}" 
                                                     style="width: {{ $progress }}%" role="progressbar">
                                                    {{ round($progress) }}%
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-2">
                                                <small class="text-muted">Paid: UGX {{ number_format($loan->total_repayment, 0) }}</small>
                                                <small class="text-muted">Remaining: UGX {{ number_format($loan->balance, 0) }}</small>
                                            </div>
                                        </div>

                                        <!-- Loan Details Grid -->
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-3">
                                                <div class="bg-light rounded p-3 text-center">
                                                    <small class="text-muted d-block">Original Amount</small>
                                                    <strong class="text-primary">UGX {{ number_format($loan->loan_amount, 0) }}</strong>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="bg-light rounded p-3 text-center">
                                                    <small class="text-muted d-block">Interest Rate</small>
                                                    <strong class="text-info">{{ $loan->interest_rate }}%</strong>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="bg-light rounded p-3 text-center">
                                                    <small class="text-muted d-block">Monthly Payment</small>
                                                    <strong class="text-success">UGX {{ number_format($loan->monthly_payment, 0) }}</strong>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="bg-light rounded p-3 text-center">
                                                    <small class="text-muted d-block">Next Payment</small>
                                                    <strong class="text-warning">
                                                        @if($loan->repaymentSchedules->isNotEmpty())
                                                            {{ $loan->repaymentSchedules->first()->due_date->format('M j, Y') }}
                                                        @else
                                                            No pending
                                                        @endif
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-primary btn-sm" onclick="makePayment({{ $loan->id }})">
                                                <i class="fas fa-money-bill-wave me-2"></i>Make Payment
                                            </button>
                                            <button type="button" class="btn btn-outline-info btn-sm" onclick="viewLoanDetails({{ $loan->id }})">
                                                <i class="fas fa-eye me-2"></i>View Details
                                            </button>
                                            <button type="button" class="btn btn-outline-success btn-sm" onclick="downloadStatement({{ $loan->id }})">
                                                <i class="fas fa-download me-2"></i>Download Statement
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-4">
                                        <!-- Enhanced Repayment Schedule -->
                                        <div class="bg-light rounded p-4">
                                            <h6 class="mb-3">
                                                <i class="fas fa-calendar-alt me-2"></i>
                                                Repayment Schedule
                                            </h6>
                                            @if($loan->repaymentSchedules->isNotEmpty())
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>Due Date</th>
                                                                <th class="text-end">Amount</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($loan->repaymentSchedules->take(6) as $schedule)
                                                                <tr class="{{ $schedule->due_date->isPast() && $schedule->status == 'pending' ? 'table-danger' : '' }}">
                                                                    <td>
                                                                        <div class="fw-medium">{{ $schedule->due_date->format('M j') }}</div>
                                                                        <small class="text-muted">{{ $schedule->due_date->format('Y') }}</small>
                                                                    </td>
                                                                    <td class="text-end">
                                                                        <strong>UGX {{ number_format($schedule->amount, 0) }}</strong>
                                                                    </td>
                                                                    <td>
                                                                        <span class="badge bg-{{ getScheduleStatusColor($schedule) }} badge-sm">
                                                                            <i class="fas fa-{{ getScheduleIcon($schedule) }} me-1"></i>
                                                                            {{ ucfirst($schedule->status) }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                @if($loan->repaymentSchedules->count() > 6)
                                                    <div class="text-center mt-3">
                                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="viewAllSchedules({{ $loan->id }})">
                                                            <i class="fas fa-list me-1"></i>View All ({{ $loan->repaymentSchedules->count() }} payments)
                                                        </button>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="text-center py-3">
                                                    <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                                                    <p class="text-muted mb-0">No repayment schedule available</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Completed Loans -->
    @if($completedLoans->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm animate__animated animate__fadeInUp">
                    <div class="card-header bg-white border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Completed Loans ({{ $completedLoans->count() }})
                            </h5>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-success" onclick="exportLoans('completed')">
                                    <i class="fas fa-download me-1"></i>Export Completed
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th><i class="fas fa-hashtag me-1"></i>Loan #</th>
                                        <th>Purpose</th>
                                        <th><i class="fas fa-money-bill me-1"></i>Original Amount</th>
                                        <th><i class="fas fa-hand-holding-usd me-1"></i>Total Repaid</th>
                                        <th><i class="fas fa-calendar-check me-1"></i>Completed Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($completedLoans as $loan)
                                        <tr>
                                            <td>
                                                <strong class="text-primary">#{{ $loan->loan_number }}</strong>
                                            </td>
                                            <td>
                                                <div class="fw-medium">{{ $loan->loan_purpose }}</div>
                                                <small class="text-muted">{{ $loan->duration_months }} months at {{ $loan->interest_rate }}%</small>
                                            </td>
                                            <td>
                                                <strong>UGX {{ number_format($loan->loan_amount, 0) }}</strong>
                                            </td>
                                            <td>
                                                <div class="text-success fw-bold">UGX {{ number_format($loan->total_repayment, 0) }}</div>
                                                <small class="text-muted">+{{ number_format((($loan->total_repayment - $loan->loan_amount) / $loan->loan_amount) * 100, 1) }}% interest</small>
                                            </td>
                                            <td>
                                                <div class="fw-medium">{{ $loan->completed_at ? $loan->completed_at->format('M j, Y') : 'N/A' }}</div>
                                                <small class="text-muted">{{ $loan->completed_at ? $loan->completed_at->diffForHumans() : '' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-success badge-lg">
                                                    <i class="fas fa-check-circle me-1"></i>Completed
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary" onclick="viewLoanDetails({{ $loan->id }})" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-success" onclick="downloadCertificate({{ $loan->id }})" title="Download Clearance Certificate">
                                                        <i class="fas fa-certificate"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- No Loans State -->
    @if($activeLoans->count() == 0 && $completedLoans->count() == 0)
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm animate__animated animate__fadeInUp">
                    <div class="card-body text-center py-5">
                        <div class="bg-light rounded-circle p-4 d-inline-block mb-4">
                            <i class="fas fa-hand-holding-usd fa-4x text-muted"></i>
                        </div>
                        <h4 class="text-muted mb-3">No Loans Found</h4>
                        <p class="text-muted mb-4">You don't have any active or completed loans.</p>
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-primary" onclick="applyForLoan()">
                                <i class="fas fa-plus me-2"></i>Apply for Loan
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="learnMore()">
                                <i class="fas fa-info-circle me-2"></i>Learn More
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function makePayment(loanId) {
    // Redirect to payment page or show payment modal
    window.location.href = '/member/loans/' + loanId + '/payment';
}

function viewLoanDetails(loanId) {
    // Redirect to loan details page
    window.location.href = '/member/loans/' + loanId + '/details';
}

function downloadStatement(loanId) {
    // Trigger statement download
    window.open('/member/loans/' + loanId + '/statement', '_blank');
}

function downloadCertificate(loanId) {
    // Trigger clearance certificate download
    window.open('/member/loans/' + loanId + '/certificate', '_blank');
}

function viewAllSchedules(loanId) {
    // Redirect to full schedule page
    window.location.href = '/member/loans/' + loanId + '/schedule';
}

function exportLoans(type) {
    // Trigger export functionality
    window.open('/member/loans/export?type=' + type, '_blank');
}

function applyForLoan() {
    // Redirect to loan application page
    window.location.href = '/member/loans/apply';
}

function learnMore() {
    // Show loan information modal or redirect to info page
    window.location.href = '/member/loans/info';
}

// Add interactive hover effects
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>

@php
function getProgressColor($loan) {
    $progress = ($loan->total_repayment / $loan->total_repayable) * 100;
    if ($progress >= 75) return 'success';
    if ($progress >= 50) return 'info';
    if ($progress >= 25) return 'warning';
    return 'danger';
}

function getLoanStatus($loan) {
    $progress = ($loan->total_repayment / $loan->total_repayable) * 100;
    if ($progress >= 100) return 'Completed';
    if ($progress >= 75) return 'Nearly Paid';
    if ($progress >= 50) return 'Half Paid';
    if ($progress >= 25) return 'Good Progress';
    return 'Just Started';
}

function getScheduleStatusColor($schedule) {
    if ($schedule->status == 'paid') return 'success';
    if ($schedule->due_date->isPast()) return 'danger';
    if ($schedule->due_date->diffInDays(now()) <= 7) return 'warning';
    return 'info';
}

function getScheduleIcon($schedule) {
    if ($schedule->status == 'paid') return 'check-circle';
    if ($schedule->due_date->isPast()) return 'exclamation-triangle';
    if ($schedule->due_date->diffInDays(now()) <= 7) return 'clock';
    return 'calendar';
}
@endphp
@endsection
