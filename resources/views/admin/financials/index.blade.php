@extends('layouts.admin')

@section('title', 'Financial Dashboard')

@section('content')
@php
    // Get real financial data
    $totalMembers = \App\Models\Member::count();
    
    // Calculate total savings from monthly savings (sum of all month columns)
    $monthlySavingsTotal = \App\Models\MonthlySaving::sum('jul_25') + 
                         \App\Models\MonthlySaving::sum('aug_25') + 
                         \App\Models\MonthlySaving::sum('sep_25') + 
                         \App\Models\MonthlySaving::sum('oct_25') + 
                         \App\Models\MonthlySaving::sum('nov_25') + 
                         \App\Models\MonthlySaving::sum('dec_25') + 
                         \App\Models\MonthlySaving::sum('jan_26') + 
                         \App\Models\MonthlySaving::sum('feb_26') + 
                         \App\Models\MonthlySaving::sum('mar_26') + 
                         \App\Models\MonthlySaving::sum('apr_26') + 
                         \App\Models\MonthlySaving::sum('may_26') + 
                         \App\Models\MonthlySaving::sum('jun_26');
    
    $totalSavings = $monthlySavingsTotal + \App\Models\MemberFinancial::sum('savings');
    $totalLoans = \App\Models\MemberLoanSummary::sum('total') + \App\Models\MemberFinancial::sum('loan_repayments');
    $totalWelfare = \App\Models\MemberFinancial::sum('welfare');
    $cashFlowIncome = \App\Models\CashFlow::where('type', 'income')->sum('amount');
    $cashFlowExpenses = \App\Models\CashFlow::where('type', 'expense')->sum('amount');
    $netCashFlow = $cashFlowIncome - $cashFlowExpenses;
    
    // Recent activities
    $recentSavings = \App\Models\MonthlySaving::with('member')->latest()->take(5)->get();
    $recentLoans = \App\Models\MemberLoanSummary::with('member')->latest()->take(5)->get();
    $recentCashFlow = \App\Models\CashFlow::latest()->take(5)->get();
@endphp

<!-- Financial Overview Cards -->
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:20px; margin-bottom:30px;">
    <!-- Total Members Card -->
    <div style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:white; padding:25px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
        <div style="display:flex; align-items:center; margin-bottom:10px;">
            <span style="font-size:28px; margin-right:15px;">👥</span>
            <div>
                <h4 style="margin:0; font-size:14px; opacity:0.9;">Total Members</h4>
                <p style="margin:5px 0 0 0; font-size:32px; font-weight:bold;">{{ $totalMembers }}</p>
            </div>
        </div>
    </div>

    <!-- Total Savings Card -->
    <div style="background:linear-gradient(135deg, #10b981 0%, #059669 100%); color:white; padding:25px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
        <div style="display:flex; align-items:center; margin-bottom:10px;">
            <span style="font-size:28px; margin-right:15px;">💰</span>
            <div>
                <h4 style="margin:0; font-size:14px; opacity:0.9;">Total Savings</h4>
                <p style="margin:5px 0 0 0; font-size:32px; font-weight:bold;">UGX {{ number_format($totalSavings, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Total Loans Card -->
    <div style="background:linear-gradient(135deg, #f59e0b 0%, #ef4444 100%); color:white; padding:25px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
        <div style="display:flex; align-items:center; margin-bottom:10px;">
            <span style="font-size:28px; margin-right:15px;">💳</span>
            <div>
                <h4 style="margin:0; font-size:14px; opacity:0.9;">Total Loans</h4>
                <p style="margin:5px 0 0 0; font-size:32px; font-weight:bold;">UGX {{ number_format($totalLoans, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Net Cash Flow Card -->
    <div style="background:linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color:white; padding:25px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
        <div style="display:flex; align-items:center; margin-bottom:10px;">
            <span style="font-size:28px; margin-right:15px;">💸</span>
            <div>
                <h4 style="margin:0; font-size:14px; opacity:0.9;">Net Cash Flow</h4>
                <p style="margin:5px 0 0 0; font-size:32px; font-weight:bold;">UGX {{ number_format($netCashFlow, 2) }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div style="background:white; padding:25px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.1); margin-bottom:30px;">
    <h3 style="margin:0 0 20px 0; color:#1f2937;">Quick Actions</h3>
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:15px;">
        <a href="{{ route('admin.financials.create') }}" style="display:flex; align-items:center; padding:20px; background:#f8fafc; border:2px solid #e5e7eb; border-radius:8px; text-decoration:none; color:#374151; transition:all 0.3s ease;">
            <span style="font-size:24px; margin-right:10px;">➕</span>
            <div>
                <div style="font-weight:600; color:#1f2937;">Add Financial Record</div>
                <div style="font-size:12px; color:#6b7280;">Record member contributions</div>
            </div>
        </a>
        
        <a href="{{ route('admin.cashflow.index') }}" style="display:flex; align-items:center; padding:20px; background:#f0fdf4; border:2px solid #bbf7d0; border-radius:8px; text-decoration:none; color:#166534; transition:all 0.3s ease;">
            <span style="font-size:24px; margin-right:10px;">💸</span>
            <div>
                <div style="font-weight:600; color:#166534;">Cash Flow</div>
                <div style="font-size:12px; color:#6b7280;">View all transactions</div>
            </div>
        </a>
        
        <a href="{{ route('admin.group-loans.index') }}" style="display:flex; align-items:center; padding:20px; background:#fef3c7; border:2px solid #fbbf24; border-radius:8px; text-decoration:none; color:#92400e; transition:all 0.3s ease;">
            <span style="font-size:24px; margin-right:10px;">📋</span>
            <div>
                <div style="font-weight:600; color:#92400e;">Loan Management</div>
                <div style="font-size:12px; color:#6b7280;">Manage member loans</div>
            </div>
        </a>

        <a href="{{ route('admin.monthly-savings.index') }}" style="display:flex; align-items:center; padding:20px; background:#f0f9ff; border:2px solid #7dd3fc; border-radius:8px; text-decoration:none; color:#075985; transition:all 0.3s ease;">
            <span style="font-size:24px; margin-right:10px;">📊</span>
            <div>
                <div style="font-weight:600; color:#075985;">Monthly Savings</div>
                <div style="font-size:12px; color:#6b7280;">View member savings</div>
            </div>
        </a>
    </div>
</div>

<!-- Financial Activity Overview -->
<div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; margin-bottom:30px;">
    <!-- Recent Savings -->
    <div style="background:white; padding:25px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.1);">
        <h4 style="margin:0 0 15px 0; color:#1f2937;">📈 Recent Savings</h4>
        @if($recentSavings->count() > 0)
            @foreach($recentSavings as $saving)
                <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #f3f4f6;">
                    <div>
                        <div style="font-weight:500; color:#374151;">{{ $saving->member->name ?? 'Unknown' }}</div>
                        <div style="font-size:12px; color:#6b7280;">{{ $saving->membership_number ?? 'N/A' }}</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="color:#059669; font-weight:600;">UGX {{ number_format($saving->getTotalMonthlyContributions(), 0) }}</div>
                    </div>
                </div>
            @endforeach
        @else
            <div style="text-align:center; padding:20px; color:#9ca3af; font-style:italic;">
                No recent savings
            </div>
        @endif
    </div>

    <!-- Recent Loans -->
    <div style="background:white; padding:25px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.1);">
        <h4 style="margin:0 0 15px 0; color:#1f2937;">💳 Recent Loans</h4>
        @if($recentLoans->count() > 0)
            @foreach($recentLoans as $loan)
                <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #f3f4f6;">
                    <div>
                        <div style="font-weight:500; color:#374151;">{{ $loan->member->name ?? 'Unknown' }}</div>
                        <div style="font-size:12px; color:#6b7280;">Loan Balance</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="color:#dc2626; font-weight:600;">UGX {{ number_format($loan->total, 0) }}</div>
                    </div>
                </div>
            @endforeach
        @else
            <div style="text-align:center; padding:20px; color:#9ca3af; font-style:italic;">
                No recent loans
            </div>
        @endif
    </div>

    <!-- Recent Cash Flow -->
    <div style="background:white; padding:25px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.1);">
        <h4 style="margin:0 0 15px 0; color:#1f2937;">💰 Recent Transactions</h4>
        @if($recentCashFlow->count() > 0)
            @foreach($recentCashFlow as $transaction)
                <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #f3f4f6;">
                    <div>
                        <div style="font-weight:500; color:#374151;">{{ $transaction->description }}</div>
                        <div style="font-size:12px; color:#6b7280;">{{ $transaction->transaction_date->format('M d, Y') }}</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-weight:600; {{ $transaction->type === 'income' ? 'color:#059669;' : 'color:#dc2626;' }}">
                            {{ $transaction->type === 'income' ? '+' : '-' }} UGX {{ number_format($transaction->amount, 0) }}
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div style="text-align:center; padding:20px; color:#9ca3af; font-style:italic;">
                No recent transactions
            </div>
        @endif
    </div>
</div>

<!-- Detailed Financial Summary -->
<div style="background:white; padding:25px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.1);">
    <h3 style="margin:0 0 20px 0; color:#1f2937;">📊 Financial Summary</h3>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:30px;">
        <!-- Income Section -->
        <div>
            <h4 style="margin:0 0 15px 0; color:#059669;">Income Sources</h4>
            <div style="background:#f0fdf4; padding:15px; border-radius:8px; margin-bottom:10px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="color:#6b7280;">Member Savings</span>
                    <span style="color:#059669; font-weight:600;">UGX {{ number_format($monthlySavingsTotal, 2) }}</span>
                </div>
            </div>
            <div style="background:#f0fdf4; padding:15px; border-radius:8px; margin-bottom:10px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="color:#6b7280;">Cash Flow Income</span>
                    <span style="color:#059669; font-weight:600;">UGX {{ number_format($cashFlowIncome, 2) }}</span>
                </div>
            </div>
            <div style="background:#f0fdf4; padding:15px; border-radius:8px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="color:#6b7280;">Total Income</span>
                    <span style="color:#059669; font-weight:600; font-size:18px;">UGX {{ number_format($monthlySavingsTotal + $cashFlowIncome, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Expenses Section -->
        <div>
            <h4 style="margin:0 0 15px 0; color:#dc2626;">Expenses</h4>
            <div style="background:#fef2f2; padding:15px; border-radius:8px; margin-bottom:10px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="color:#6b7280;">Loan Disbursements</span>
                    <span style="color:#dc2626; font-weight:600;">UGX {{ number_format($totalLoans, 2) }}</span>
                </div>
            </div>
            <div style="background:#fef2f2; padding:15px; border-radius:8px; margin-bottom:10px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="color:#6b7280;">Cash Flow Expenses</span>
                    <span style="color:#dc2626; font-weight:600;">UGX {{ number_format($cashFlowExpenses, 2) }}</span>
                </div>
            </div>
            <div style="background:#fef2f2; padding:15px; border-radius:8px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="color:#6b7280;">Total Expenses</span>
                    <span style="color:#dc2626; font-weight:600; font-size:18px;">UGX {{ number_format($totalLoans + $cashFlowExpenses, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
