@extends('layouts.admin')

@section('title', 'Account Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-2 text-white">
                            <i class="fas fa-user me-3"></i>
                            Account Details - {{ $member->name }}
                        </h1>
                        <p class="text-white fs-5 mb-0 opacity-90">
                            <i class="fas fa-wallet me-2"></i>
                            Complete account information and transaction history
                        </p>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('admin.accounts.index') }}" 
                           class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Back to Accounts
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Fiscal Year: {{ $currentFiscalYear->name ?? 'N/A' }}
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Member Information -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-user me-2"></i>
                        Member Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <p><strong>Name:</strong><br>{{ $member->name }}</p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Member Number:</strong><br>{{ $member->member_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Phone:</strong><br>{{ $member->phone ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Email:</strong><br>{{ $member->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Summary -->
    @if ($account)
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-piggy-bank fa-2x mb-2"></i>
                    <h5>{{ number_format($account->total_deposited, 2) }}</h5>
                    <p class="mb-0">Total Deposited</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-hand-holding-usd fa-2x mb-2"></i>
                    <h5>{{ number_format($account->total_distributed, 2) }}</h5>
                    <p class="mb-0">Total Distributed</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-wallet fa-2x mb-2"></i>
                    <h5>{{ number_format($account->current_balance, 2) }}</h5>
                    <p class="mb-0">Current Balance</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-coins fa-2x mb-2"></i>
                    <h5>{{ number_format($account->savings_balance, 2) }}</h5>
                    <p class="mb-0">Savings Balance</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance Breakdown -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-chart-pie me-2"></i>
                        Balance Breakdown
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h6>Savings Balance</h6>
                                <h4>{{ number_format($account->savings_balance, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h6>Welfare Balance</h6>
                                <h4>{{ number_format($account->welfare_balance, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h6>Fines Balance</h6>
                                <h4>{{ number_format($account->fines_balance, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h6>Other Balance</h6>
                                <h4>{{ number_format($account->other_balance, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Transaction History -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-history me-2"></i>
                        Transaction History
                    </h5>
                </div>
                <div class="card-body">
                    @if ($deposits->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Transactions Found</h5>
                            <p class="text-muted">No transactions have been recorded for this member.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Month</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($deposits as $deposit)
                                        <tr>
                                            <td>{{ $deposit->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-plus me-1"></i>
                                                    Deposit
                                                </span>
                                            </td>
                                            <td><strong>{{ number_format($deposit->amount, 2) }}</strong></td>
                                            <td>{{ $deposit->month ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $deposit->distributions->count() > 0 ? 'info' : 'warning' }}">
                                                    {{ $deposit->distributions->count() > 0 ? 'Distributed' : 'Pending' }}
                                                </span>
                                            </td>
                                        </tr>
                                        
                                        <!-- Show distributions for this deposit -->
                                        @foreach ($deposit->distributions as $distribution)
                                            <tr class="table-secondary">
                                                <td>{{ $distribution->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-minus me-1"></i>
                                                        Distribution
                                                    </span>
                                                </td>
                                                <td><strong>-{{ number_format($distribution->amount, 2) }}</strong></td>
                                                <td>{{ $distribution->distribution_type ?? 'General' }}</td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>
                                                        Completed
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
