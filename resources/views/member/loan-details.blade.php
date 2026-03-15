@extends('layouts.member')

@section('content')
<div class="container-fluid p-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card gradient-bg text-white border-0">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-2">Loan Details - #{{ $loan->loan_number }}</h2>
                            <p class="mb-0 opacity-75">Complete information about your loan</p>
                        </div>
                        <a href="{{ route('member.loans') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-2"></i>Back to Loans
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Loan Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <strong>Loan Number:</strong>
                            <p>{{ $loan->loan_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Purpose:</strong>
                            <p>{{ $loan->loan_purpose }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Original Amount:</strong>
                            <p class="text-primary">UGX {{ number_format($loan->loan_amount, 0) }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Total Repayable:</strong>
                            <p class="text-danger">UGX {{ number_format($loan->total_repayable, 0) }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Interest Rate:</strong>
                            <p>{{ $loan->interest_rate }}% ({{ $loan->interest_type }})</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Duration:</strong>
                            <p>{{ $loan->duration_months }} months</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Monthly Payment:</strong>
                            <p class="text-success">UGX {{ number_format($loan->monthly_payment, 0) }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong>
                            <p><span class="badge bg-{{ $loan->status == 'active' ? 'success' : 'primary' }}">{{ ucfirst($loan->status) }}</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Repayment Schedule</h5>
                </div>
                <div class="card-body">
                    @if($loan->repaymentSchedules->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Due Date</th>
                                        <th>Principal</th>
                                        <th>Interest</th>
                                        <th>Total Due</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($loan->repaymentSchedules as $schedule)
                                        <tr class="{{ $schedule->status == 'paid' ? 'table-success' : ($schedule->isOverdue() ? 'table-danger' : '') }}">
                                            <td>{{ $schedule->installment_number }}</td>
                                            <td>{{ $schedule->due_date->format('M j, Y') }}</td>
                                            <td>UGX {{ number_format($schedule->principal_due, 0) }}</td>
                                            <td>UGX {{ number_format($schedule->interest_due, 0) }}</td>
                                            <td><strong>UGX {{ number_format($schedule->total_due, 0) }}</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $schedule->status == 'paid' ? 'success' : ($schedule->isOverdue() ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($schedule->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No repayment schedule available</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Progress</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h3 class="text-primary">{{ round(($loan->total_repayment / $loan->total_repayable) * 100) }}%</h3>
                        <p class="text-muted">Repayment Progress</p>
                    </div>
                    <div class="progress mb-3" style="height: 20px;">
                        <div class="progress-bar bg-success" style="width: {{ ($loan->total_repayment / $loan->total_repayable) * 100 }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Paid:</span>
                        <strong class="text-success">UGX {{ number_format($loan->total_repayment, 0) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Remaining:</span>
                        <strong class="text-danger">UGX {{ number_format($loan->balance, 0) }}</strong>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('member.loans.payment', $loan->id) }}" class="btn btn-primary">
                            <i class="fas fa-money-bill-wave me-2"></i>Make Payment
                        </a>
                        <a href="{{ route('member.loans.statement', $loan->id) }}" class="btn btn-outline-success" target="_blank">
                            <i class="fas fa-download me-2"></i>Download Statement
                        </a>
                        <a href="{{ route('member.loans.schedule', $loan->id) }}" class="btn btn-outline-info">
                            <i class="fas fa-calendar me-2"></i>Full Schedule
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
