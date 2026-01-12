@extends('layouts.admin')

@section('title', 'Member Accounts')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-2 text-white">
                            <i class="fas fa-users me-3"></i>
                            Member Accounts
                        </h1>
                        <p class="text-white fs-5 mb-0 opacity-90">
                            <i class="fas fa-wallet me-2"></i>
                            Manage member accounts and balances
                        </p>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('admin.accounts.summary') }}" 
                           class="btn btn-primary">
                            <i class="fas fa-chart-pie me-2"></i>
                            Account Summary
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (!$activeFiscalYear)
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>No Active Fiscal Year</h5>
                    <p class="mb-0">Please set up a fiscal year to view accounts.</p>
                </div>
            </div>
        </div>
    @else
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-alt me-2"></i>
                            Fiscal Year: {{ $activeFiscalYear->name }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($accounts->isEmpty())
                            <div class="text-center py-5">
                                <i class="fas fa-wallet fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Accounts Found</h5>
                                <p class="text-muted">No member accounts have been created for this fiscal year.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Member</th>
                                            <th>Total Deposited</th>
                                            <th>Total Distributed</th>
                                            <th>Current Balance</th>
                                            <th>Savings</th>
                                            <th>Welfare</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($accounts as $account)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center text-white me-3" style="width: 32px; height: 32px;">
                                                            {{ strtoupper(substr($account->member->name ?? 'Unknown', 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium">{{ $account->member->name ?? 'Unknown Member' }}</div>
                                                            <small class="text-muted">{{ $account->member->member_number ?? 'N/A' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ number_format($account->total_deposited, 2) }}</td>
                                                <td>{{ number_format($account->total_distributed, 2) }}</td>
                                                <td><strong>{{ number_format($account->current_balance, 2) }}</strong></td>
                                                <td>{{ number_format($account->savings_balance, 2) }}</td>
                                                <td>{{ number_format($account->welfare_balance, 2) }}</td>
                                                <td>
                                                    <a href="{{ route('admin.accounts.show', $account->member_id) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye me-1"></i>
                                                        View Details
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
