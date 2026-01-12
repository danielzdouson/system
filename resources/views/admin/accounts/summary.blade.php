@extends('layouts.admin')

@section('title', 'Account Summary')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-2 text-white">
                            <i class="fas fa-chart-pie me-3"></i>
                            Account Summary
                        </h1>
                        <p class="text-white fs-5 mb-0 opacity-90">
                            <i class="fas fa-analytics me-2"></i>
                            Overview of all member accounts and financial statistics
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

    @if (!$activeFiscalYear)
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>No Active Fiscal Year</h5>
                    <p class="mb-0">Please set up a fiscal year to view account summary.</p>
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
                </div>
            </div>
        </div>

        @if ($summary)
            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="card-title">{{ $summary['total_members'] }}</h5>
                                    <p class="card-text">Total Members</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-piggy-bank fa-2x"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="card-title">{{ number_format($summary['total_deposited'], 0) }}</h5>
                                    <p class="card-text">Total Deposited</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-hand-holding-usd fa-2x"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="card-title">{{ number_format($summary['total_distributed'], 0) }}</h5>
                                    <p class="card-text">Total Distributed</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-wallet fa-2x"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="card-title">{{ number_format($summary['total_balance'], 0) }}</h5>
                                    <p class="card-text">Total Balance</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Balance Breakdown -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-chart-pie me-2"></i>
                                Balance Breakdown
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <p class="mb-2">
                                        <strong>Savings Balance:</strong><br>
                                        {{ number_format($summary['total_savings_balance'], 2) }}
                                    </p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-2">
                                        <strong>Welfare Balance:</strong><br>
                                        {{ number_format($summary['total_welfare_balance'], 2) }}
                                    </p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-2">
                                        <strong>Fines Balance:</strong><br>
                                        {{ number_format($summary['total_fines_balance'], 2) }}
                                    </p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-2">
                                        <strong>Other Balance:</strong><br>
                                        {{ number_format($summary['total_other_balance'], 2) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-chart-bar me-2"></i>
                                Member Statistics
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <p class="mb-2">
                                        <strong>Members with Balance:</strong><br>
                                        {{ $summary['members_with_balance'] }}
                                    </p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-2">
                                        <strong>Members with Zero Balance:</strong><br>
                                        {{ $summary['members_with_zero_balance'] }}
                                    </p>
                                </div>
                                <div class="col-12">
                                    <p class="mb-2">
                                        <strong>Average Balance:</strong><br>
                                        {{ $summary['total_members'] > 0 ? number_format($summary['total_balance'] / $summary['total_members'], 2) : '0.00' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
