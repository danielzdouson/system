@extends('layouts.admin')

@section('title', 'Carry Forward Fiscal Year')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="h3 mb-0">
                    <i class="fas fa-exchange-alt text-primary me-2"></i>
                    Carry Forward Items
                </h2>
                <a href="{{ route('admin.fiscal-years.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Fiscal Years
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Carry Forward Summary
                    </h4>
                </div>
                
                <div class="card-body">
                    <div class="alert alert-info border-0 bg-light">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle fa-2x text-info me-3"></i>
                            <div>
                                <h5 class="alert-heading mb-2">Fiscal Year Transition</h5>
                                <p class="mb-1">You are about to carry forward items from <span class="badge bg-primary">{{ $fromFiscalYear->name }}</span> to <span class="badge bg-success">{{ $toFiscalYear->name }}</span>.</p>
                                <p class="mb-0">This will transfer the following items to the new fiscal year:</p>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <div class="icon-box bg-info bg-gradient text-white rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="fas fa-hand-holding-usd fa-lg"></i>
                                        </div>
                                    </div>
                                    <h5 class="card-title text-info">Loans</h5>
                                    <h3 class="card-text fw-bold">{{ $summary['total_loans'] }}</h3>
                                    <p class="text-muted small mb-0">Total: {{ number_format($summary['total_loan_amount'], 2) }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <div class="icon-box bg-warning bg-gradient text-white rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="fas fa-exclamation-triangle fa-lg"></i>
                                        </div>
                                    </div>
                                    <h5 class="card-title text-warning">Fines</h5>
                                    <h3 class="card-text fw-bold">{{ $summary['total_fines'] }}</h3>
                                    <p class="text-muted small mb-0">Total: {{ number_format($summary['total_fine_amount'], 2) }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <div class="icon-box bg-success bg-gradient text-white rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="fas fa-chart-line fa-lg"></i>
                                        </div>
                                    </div>
                                    <h5 class="card-title text-success">Investments</h5>
                                    <h3 class="card-text fw-bold">{{ $summary['total_investments'] }}</h3>
                                    <p class="text-muted small mb-0">Total: {{ number_format($summary['total_investment_value'], 2) }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <div class="icon-box bg-primary bg-gradient text-white rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="fas fa-calculator fa-lg"></i>
                                        </div>
                                    </div>
                                    <h5 class="card-title text-primary">Grand Total</h5>
                                    <h3 class="card-text fw-bold">{{ $summary['total_loans'] + $summary['total_fines'] + $summary['total_investments'] }}</h3>
                                    <p class="text-muted small mb-0">{{ number_format($summary['total_loan_amount'] + $summary['total_fine_amount'] + $summary['total_investment_value'], 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Items -->
                    @if($summary['total_loans'] > 0)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0 text-info">
                                <i class="fas fa-hand-holding-usd me-2"></i> Loans to Carry Forward
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th><i class="fas fa-hashtag me-1"></i> Loan Number</th>
                                            <th><i class="fas fa-user me-1"></i> Member</th>
                                            <th><i class="fas fa-dollar-sign me-1"></i> Balance</th>
                                            <th><i class="fas fa-info-circle me-1"></i> Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($summary['eligible_items']['loans'] as $loan)
                                        <tr>
                                            <td><span class="badge bg-secondary">{{ $loan->loan_number }}</span></td>
                                            <td>{{ $loan->member->name }}</td>
                                            <td class="fw-bold text-primary">{{ number_format($loan->balance, 2) }}</td>
                                            <td>{!! $loan->getStatusBadge() !!}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($summary['total_fines'] > 0)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0 text-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i> Fines to Carry Forward
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th><i class="fas fa-user me-1"></i> Member</th>
                                            <th><i class="fas fa-dollar-sign me-1"></i> Amount</th>
                                            <th><i class="fas fa-comment me-1"></i> Reason</th>
                                            <th><i class="fas fa-calendar me-1"></i> Month</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($summary['eligible_items']['fines'] as $fine)
                                        <tr>
                                            <td>{{ $fine->member->name }}</td>
                                            <td class="fw-bold text-warning">{{ number_format($fine->amount, 2) }}</td>
                                            <td>{{ $fine->reason }}</td>
                                            <td><span class="badge bg-info">{{ $fine->getMonthName($fine->month) }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($summary['total_investments'] > 0)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0 text-success">
                                <i class="fas fa-chart-line me-2"></i> Long-term Investments to Mark
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th><i class="fas fa-tag me-1"></i> Name</th>
                                            <th><i class="fas fa-list me-1"></i> Type</th>
                                            <th><i class="fas fa-dollar-sign me-1"></i> Current Value</th>
                                            <th><i class="fas fa-calendar-alt me-1"></i> Maturity Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($summary['eligible_items']['investments'] as $investment)
                                        <tr>
                                            <td>{{ $investment->name }}</td>
                                            <td>{!! $investment->getTypeBadgeAttribute() !!}</td>
                                            <td class="fw-bold text-success">{{ number_format($investment->current_value, 2) }}</td>
                                            <td>{{ $investment->maturity_date ? $investment->maturity_date->format('Y-m-d') : '<span class="text-muted">N/A</span>' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Confirmation Form -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-warning bg-gradient text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i> Confirmation Required
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning border-0 bg-light">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-exclamation-triangle fa-2x text-warning me-3 mt-1"></i>
                                    <div>
                                        <h6 class="alert-heading mb-2">Important Notice</h6>
                                        <p class="mb-2">This action will permanently move the items listed above to the new fiscal year. This action cannot be easily undone.</p>
                                        <p class="mb-0">Please confirm that you want to proceed with the carry forward operation.</p>
                                    </div>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('admin.fiscal-years.carry-forward.process', [$fromFiscalYear->id, $toFiscalYear->id]) }}">
                                @csrf
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="confirm" name="confirm" value="1" required>
                                    <label class="form-check-label" for="confirm">
                                        <strong>I confirm that I want to carry forward these items to {{ $toFiscalYear->name }}</strong>
                                    </label>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-warning btn-lg">
                                        <i class="fas fa-exchange-alt me-2"></i> Process Carry Forward
                                    </button>
                                    <a href="{{ route('admin.fiscal-years.index') }}" class="btn btn-outline-secondary btn-lg">
                                        <i class="fas fa-times me-2"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
