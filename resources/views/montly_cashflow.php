@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Monthly Cash Flow Statement</h3>

    <p><strong>Period:</strong> {{ $monthName }} {{ $year }}</p>

    <table class="table table-bordered">
        <tr>
            <th>Opening Balance</th>
            <td class="text-end">{{ number_format($openingBalance, 2) }}</td>
        </tr>
    </table>

    <h5>Cash Incoming</h5>
    <table class="table table-bordered">
        <tr><td>Savings</td><td class="text-end">{{ number_format($savings, 2) }}</td></tr>
        <tr><td>Welfare</td><td class="text-end">{{ number_format($welfare, 2) }}</td></tr>
        <tr><td>Fines</td><td class="text-end">{{ number_format($fines, 2) }}</td></tr>
        <tr><td>Loan Charges</td><td class="text-end">{{ number_format($loanCharges, 2) }}</td></tr>
        <tr><td>Education In</td><td class="text-end">{{ number_format($educationIn, 2) }}</td></tr>
        <tr><td>Loan Form Fee</td><td class="text-end">{{ number_format($loanFormFee, 2) }}</td></tr>
        <tr><td>Subscription Fee</td><td class="text-end">{{ number_format($subscriptionFee, 2) }}</td></tr>
        <tr><td>Investment Profits</td><td class="text-end">{{ number_format($investmentProfits, 2) }}</td></tr>
        <tr><td>Other Income</td><td class="text-end">{{ number_format($otherIncome, 2) }}</td></tr>

        <tr class="table-success">
            <th>Total Cash In</th>
            <th class="text-end">{{ number_format($totalCashIn, 2) }}</th>
        </tr>
    </table>

    <h5>Cash Outgoing</h5>
    <table class="table table-bordered">
        <tr><td>Education Out</td><td class="text-end">{{ number_format($educationOut, 2) }}</td></tr>
        <tr><td>Loan Distributed</td><td class="text-end">{{ number_format($loanDistributed, 2) }}</td></tr>
        <tr><td>Advertisement</td><td class="text-end">{{ number_format($advertisement, 2) }}</td></tr>
        <tr><td>Phone Expenses</td><td class="text-end">{{ number_format($phoneExpenses, 2) }}</td></tr>
        <tr><td>Rent</td><td class="text-end">{{ number_format($rent, 2) }}</td></tr>
        <tr><td>Transport</td><td class="text-end">{{ number_format($transport, 2) }}</td></tr>

        <tr class="table-danger">
            <th>Total Cash Out</th>
            <th class="text-end">{{ number_format($totalCashOut, 2) }}</th>
        </tr>
    </table>

    <table class="table table-bordered">
        <tr class="table-primary">
            <th>Closing Cash Balance</th>
            <th class="text-end">{{ number_format($closingBalance, 2) }}</th>
        </tr>
    </table>
</div>
@endsection
