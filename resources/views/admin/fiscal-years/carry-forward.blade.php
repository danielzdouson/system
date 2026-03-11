@extends('layouts.app')

@section('title', 'Carry Forward Fiscal Year')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Carry Forward Items</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.fiscal-years.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Fiscal Years
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> Carry Forward Summary</h5>
                        <p>You are about to carry forward items from <strong>{{ $fromFiscalYear->name }}</strong> to <strong>{{ $toFiscalYear->name }}</strong>.</p>
                        <p>This will transfer the following items to the new fiscal year:</p>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-hand-holding-usd"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Loans</span>
                                    <span class="info-box-number">{{ $summary['total_loans'] }}</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-info" style="width: {{ $summary['total_loans'] > 0 ? 100 : 0 }}%"></div>
                                    </div>
                                    <span class="progress-description">
                                        Total: {{ number_format($summary['total_loan_amount'], 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Fines</span>
                                    <span class="info-box-number">{{ $summary['total_fines'] }}</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-warning" style="width: {{ $summary['total_fines'] > 0 ? 100 : 0 }}%"></div>
                                    </div>
                                    <span class="progress-description">
                                        Total: {{ number_format($summary['total_fine_amount'], 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-chart-line"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Investments</span>
                                    <span class="info-box-number">{{ $summary['total_investments'] }}</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" style="width: {{ $summary['total_investments'] > 0 ? 100 : 0 }}%"></div>
                                    </div>
                                    <span class="progress-description">
                                        Total: {{ number_format($summary['total_investment_value'], 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-calculator"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Grand Total</span>
                                    <span class="info-box-number">{{ $summary['total_loans'] + $summary['total_fines'] + $summary['total_investments'] }}</span>
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                                    </div>
                                    <span class="progress-description">
                                        {{ number_format($summary['total_loan_amount'] + $summary['total_fine_amount'] + $summary['total_investment_value'], 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Items -->
                    @if($summary['total_loans'] > 0)
                    <div class="mt-4">
                        <h5><i class="fas fa-hand-holding-usd"></i> Loans to Carry Forward</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Loan Number</th>
                                        <th>Member</th>
                                        <th>Balance</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($summary['eligible_items']['loans'] as $loan)
                                    <tr>
                                        <td>{{ $loan->loan_number }}</td>
                                        <td>{{ $loan->member->name }}</td>
                                        <td>{{ number_format($loan->balance, 2) }}</td>
                                        <td>{!! $loan->getStatusBadge() !!}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    @if($summary['total_fines'] > 0)
                    <div class="mt-4">
                        <h5><i class="fas fa-exclamation-triangle"></i> Fines to Carry Forward</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Member</th>
                                        <th>Amount</th>
                                        <th>Reason</th>
                                        <th>Month</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($summary['eligible_items']['fines'] as $fine)
                                    <tr>
                                        <td>{{ $fine->member->name }}</td>
                                        <td>{{ number_format($fine->amount, 2) }}</td>
                                        <td>{{ $fine->reason }}</td>
                                        <td>{{ $fine->getMonthName($fine->month) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    @if($summary['total_investments'] > 0)
                    <div class="mt-4">
                        <h5><i class="fas fa-chart-line"></i> Long-term Investments to Mark</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Current Value</th>
                                        <th>Maturity Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($summary['eligible_items']['investments'] as $investment)
                                    <tr>
                                        <td>{{ $investment->name }}</td>
                                        <td>{{ $investment->getTypeBadgeAttribute() }}</td>
                                        <td>{{ number_format($investment->current_value, 2) }}</td>
                                        <td>{{ $investment->maturity_date ? $investment->maturity_date->format('Y-m-d') : 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Confirmation Form -->
                    <div class="mt-4">
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-exclamation-triangle"></i> Confirmation Required</h5>
                            <p>This action will permanently move the items listed above to the new fiscal year. This action cannot be easily undone.</p>
                            <p>Please confirm that you want to proceed with the carry forward operation.</p>
                        </div>

                        <form method="POST" action="{{ route('admin.fiscal-years.carry-forward.process', [$fromFiscalYear->id, $toFiscalYear->id]) }}">
                            @csrf
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="confirm" name="confirm" value="1" required>
                                    <label class="custom-control-label" for="confirm">
                                        I confirm that I want to carry forward these items to {{ $toFiscalYear->name }}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="btn-group">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-exchange-alt"></i> Process Carry Forward
                                </button>
                                <a href="{{ route('admin.fiscal-years.index') }}" class="btn btn-default">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
