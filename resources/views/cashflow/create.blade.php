@extends('layouts.admin')

@section('title', 'Add New Transaction')

@section('content')
<div class="card">
    <h3>Add New Transaction</h3>
    
    <div style="margin-bottom:20px;">
        <a href="{{ route('admin.cashflow.index') }}" style="color:#6b7280; text-decoration:none;">
            ← Back to Transactions
        </a>
    </div>

    <form action="{{ route('admin.cashflow.store') }}" method="POST">
        @csrf
        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:500;">Transaction Date</label>
            <input type="date" name="transaction_date" required
                   style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:4px;">
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:500;">Description</label>
            <input type="text" name="description" required
                   style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:4px;">
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:500;">Type</label>
            <select name="type" required
                    style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:4px;">
                <option value="">Select Type</option>
                <option value="income">Income</option>
                <option value="expense">Expense</option>
            </select>
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:500;">Category</label>
            <select name="category" required
                    style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:4px;">
                <option value="">Select Category</option>
                <option value="member_savings">Member Savings</option>
                <option value="loan_repayment">Loan Repayment</option>
                <option value="operational_expense">Operational Expense</option>
                <option value="administrative_cost">Administrative Cost</option>
            </select>
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:500;">Amount</label>
            <input type="number" step="0.01" name="amount" required
                   style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:4px;">
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:500;">Payment Method</label>
            <select name="payment_method" required
                    style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:4px;">
                <option value="">Select Method</option>
                <option value="cash">Cash</option>
                <option value="bank_transfer">Bank Transfer</option>
                <option value="mobile_money">Mobile Money</option>
                <option value="cheque">Cheque</option>
            </select>
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:5px; font-weight:500;">Notes</label>
            <textarea name="notes" rows="3"
                      style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:4px;"></textarea>
        </div>

        <button type="submit"
                style="padding:10px 18px; background:#1f2937; color:white; border:none; border-radius:4px;">
            Save Transaction
        </button>
    </form>
</div>

@endsection
