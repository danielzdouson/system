@extends('layouts.admin')

@section('title', 'Monthly Cash Flow Dashboard')

@php
use Carbon\Carbon;
@endphp

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-2 text-white">
                            <i class="fas fa-chart-line me-3"></i>
                            Monthly Cash Flow Dashboard
                        </h1>
                        <p class="text-white fs-5 mb-0 opacity-90">
                            <i class="fas fa-calendar-alt me-2"></i>
                            {{ $monthlyData['monthName'] }} {{ $currentYear }} - Financial Overview
                        </p>
                    </div>
                    <div class="text-end">
                        <div class="month-selector">
                            <select class="form-select" onchange="changeMonth(this.value)">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $currentMonth == $m ? 'selected' : '' }}>
                                        {{ Carbon::create($currentYear, $m, 1)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-arrow-up fa-2x"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="card-title">Total Income</h5>
                            <h3>UGX {{ number_format($monthlyData['totalIncome'], 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-arrow-down fa-2x"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="card-title">Total Expenses</h5>
                            <h3>UGX {{ number_format($monthlyData['totalExpenses'], 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card {{ $monthlyData['netCashFlow'] >= 0 ? 'bg-primary' : 'bg-warning' }} text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-balance-scale fa-2x"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="card-title">Net Cash Flow</h5>
                            <h3>UGX {{ number_format($monthlyData['netCashFlow'], 0) }}</h3>
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
                            <i class="fas fa-calendar fa-2x"></i>
                        </div>
                        <div class="ms-3">
                            <h5 class="card-title">YTD Summary</h5>
                            <h3>UGX {{ number_format($ytdSummary['netCashFlow'], 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SACCO Specific Breakdown -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-coins me-2"></i>
                        Income Breakdown
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <p class="mb-2">
                                <strong>Member Deposits:</strong><br>
                                <span class="text-success">UGX {{ number_format($monthlyData['monthlyDeposits'], 0) }}</span>
                            </p>
                            <p class="mb-2">
                                <strong>Loan Repayments:</strong><br>
                                <span class="text-success">UGX {{ number_format($monthlyData['monthlyLoanRepayments'], 0) }}</span>
                            </p>
                            <p class="mb-2">
                                <strong>Fine Payments:</strong><br>
                                <span class="text-success">UGX {{ number_format($monthlyData['monthlyFines'], 0) }}</span>
                            </p>
                        </div>
                        <div class="col-6">
                            <p class="mb-2">
                                <strong>Other Income:</strong><br>
                                <span class="text-success">UGX {{ number_format($monthlyData['cashFlowTransactions']->where('type', 'income')->sum('amount'), 0) }}</span>
                            </p>
                            <hr>
                            <p class="mb-2">
                                <strong>Total Income:</strong><br>
                                <span class="text-success fs-5">UGX {{ number_format($monthlyData['totalIncome'], 0) }}</span>
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
                        <i class="fas fa-shopping-cart me-2"></i>
                        Expense Breakdown
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <p class="mb-2">
                                <strong>Loan Disbursements:</strong><br>
                                <span class="text-danger">UGX {{ number_format($monthlyData['monthlyLoanDisbursements'], 0) }}</span>
                            </p>
                            <p class="mb-2">
                                <strong>Welfare Payments:</strong><br>
                                <span class="text-danger">UGX {{ number_format($monthlyData['monthlyWelfarePayments'], 0) }}</span>
                            </p>
                        </div>
                        <div class="col-6">
                            <p class="mb-2">
                                <strong>Other Expenses:</strong><br>
                                <span class="text-danger">UGX {{ number_format($monthlyData['cashFlowTransactions']->where('type', 'expense')->sum('amount'), 0) }}</span>
                            </p>
                            <hr>
                            <p class="mb-2">
                                <strong>Total Expenses:</strong><br>
                                <span class="text-danger fs-5">UGX {{ number_format($monthlyData['totalExpenses'], 0) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Trends Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-chart-area me-2"></i>
                        Monthly Trends - {{ $currentYear }}
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyTrendsChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Year-to-Date Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-calendar-check me-2"></i>
                        Year-to-Date Summary - {{ $currentYear }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h6>Total Income YTD</h6>
                                <h4 class="text-success">UGX {{ number_format($ytdSummary['totalIncome'], 0) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h6>Total Expenses YTD</h6>
                                <h4 class="text-danger">UGX {{ number_format($ytdSummary['totalExpenses'], 0) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h6>Net Cash Flow YTD</h6>
                                <h4 class="{{ $ytdSummary['netCashFlow'] >= 0 ? 'text-primary' : 'text-warning' }}">
                                    UGX {{ number_format($ytdSummary['netCashFlow'], 0) }}
                                </h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h6>Total Deposits YTD</h6>
                                <h4 class="text-info">UGX {{ number_format($ytdSummary['totalDeposits'], 0) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-3">
                        <a href="{{ route('admin.cashflow.index') }}" class="btn btn-secondary">
                            <i class="fas fa-list me-2"></i>
                            View All Transactions
                        </a>
                        <a href="{{ route('admin.cashflow.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Add Transaction
                        </a>
                        <a href="{{ route('admin.cashflow.download') }}" class="btn btn-success">
                            <i class="fas fa-download me-2"></i>
                            Export to Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 2rem;
    color: white;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.month-selector select {
    width: 150px;
    padding: 8px 12px;
    border: none;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    font-weight: 500;
}

.month-selector select option {
    background: #667eea;
    color: white;
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-3px);
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: none;
    border-radius: 15px 15px 0 0;
}
</style>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function changeMonth(month) {
    const url = new URL(window.location);
    url.searchParams.set('month', month);
    window.location.href = url.toString();
}

// Monthly Trends Chart
const ctx = document.getElementById('monthlyTrendsChart').getContext('2d');
const monthlyTrends = @json($monthlyTrends);

new Chart(ctx, {
    type: 'line',
    data: {
        labels: monthlyTrends.map(t => t.monthName),
        datasets: [
            {
                label: 'Income',
                data: monthlyTrends.map(t => t.income),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            },
            {
                label: 'Expenses',
                data: monthlyTrends.map(t => t.expenses),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1
            },
            {
                label: 'Net Cash Flow',
                data: monthlyTrends.map(t => t.netCashFlow),
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'UGX ' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>
@endsection
