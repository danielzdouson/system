@extends('layouts.admin')

@section('title', 'Financial Reports Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Financial Reports Dashboard</h1>
    <p class="text-gray-600 mt-2">
        @if($activeFiscalYear)
            Fiscal Year: {{ $activeFiscalYear->name }} ({{ $activeFiscalYear->start_date->format('M d, Y') }} - {{ $activeFiscalYear->end_date->format('M d, Y') }})
        @else
            No Active Fiscal Year
        @endif
    </p>
</div>

<!-- Key Financial Metrics -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Assets -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Assets</p>
                <p class="text-2xl font-bold text-gray-900">KES {{ number_format($metrics['total_deposits'] + $metrics['total_loan_repayments'], 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">Deposits + Repayments</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Total Liabilities -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Liabilities</p>
                <p class="text-2xl font-bold text-gray-900">KES {{ number_format($metrics['outstanding_loan_balance'], 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">Outstanding Loans</p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            Member Reports
    </h3>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div>
            <h4 class="text-sm font-medium text-gray-700 mb-3">Monthly Deposits</h4>
            <div class="space-y-2">
                @forelse($monthlyTrends['deposits'] as $deposit)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Month {{ $deposit->month }}</span>
                        <span class="font-semibold">KES {{ number_format($deposit->total, 2) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No deposit data available</p>
                @endforelse
            </div>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-700 mb-3">Monthly Loan Disbursements</h4>
            <div class="space-y-2">
                @forelse($monthlyTrends['loans'] as $loan)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Month {{ $loan->month }}</span>
                        <span class="font-semibold">KES {{ number_format($loan->total, 2) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No loan data available</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
        </svg>
        Quick Actions
    </h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('admin.reports.members') }}" class="bg-blue-50 hover:bg-blue-100 text-blue-700 p-4 rounded-lg text-center transition-colors">
            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span class="text-sm font-medium">Member Reports</span>
        </a>
        <a href="{{ route('admin.reports.savings') }}" class="bg-green-50 hover:bg-green-100 text-green-700 p-4 rounded-lg text-center transition-colors">
            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-medium">Savings Reports</span>
        </a>
        <a href="{{ route('admin.reports.loans') }}" class="bg-purple-50 hover:bg-purple-100 text-purple-700 p-4 rounded-lg text-center transition-colors">
            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
            </svg>
            <span class="text-sm font-medium">Loan Reports</span>
        </a>
        <a href="{{ route('admin.reports.cashflow') }}" class="bg-orange-50 hover:bg-orange-100 text-orange-700 p-4 rounded-lg text-center transition-colors">
            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
            </svg>
            <span class="text-sm font-medium">Cash Flow Reports</span>
        </a>
    </div>
</div>

@endsection
