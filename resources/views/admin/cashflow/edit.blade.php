@extends('layouts.admin')

@section('title', 'Edit Cashflow Transaction')

@section('content')
<div class="content-wrapper">
    <!-- Enhanced Page Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="page-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h2 mb-2 text-white">
                                    <i class="fas fa-edit me-3"></i>
                                    Edit Cashflow Transaction
                                </h1>
                                <p class="text-white fs-5 mb-0 opacity-90">
                                    <i class="fas fa-coins me-2"></i>
                                    Modify existing cashflow transaction details
                                </p>
                            </div>
                            <div class="text-end">
                                <div class="month-badge">
                                    <span class="badge bg-white text-info fs-6 px-3 py-2">
                                        <i class="fas fa-calendar me-2"></i>
                                        Transaction #{{ $transaction->id }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <!-- Edit Transaction Form -->
            <div class="row">
                <div class="col-md-8">
                    <div class="data-card">
                        <div class="data-card-header">
                            <h5 class="data-title">
                                <i class="fas fa-edit me-2"></i>
                                Transaction Details
                            </h5>
                            <div class="data-subtitle">
                                Modify the cashflow transaction information
                            </div>
                        </div>
                        <div class="data-card-body">
                            <form method="POST" action="{{ route('admin.cashflow.update', $transaction->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Transaction Date *</label>
                                        <input type="date" name="transaction_date" class="form-control" value="{{ $transaction->transaction_date->format('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Transaction Type *</label>
                                        <select name="transaction_type" class="form-select" required>
                                            <option value="INFLOW" {{ $transaction->transaction_type == 'INFLOW' ? 'selected' : '' }}>Inflow</option>
                                            <option value="OUTFLOW" {{ $transaction->transaction_type == 'OUTFLOW' ? 'selected' : '' }}>Outflow</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Category *</label>
                                        <select name="category" class="form-select" required>
                                            <option value="OPERATING" {{ $transaction->category == 'OPERATING' ? 'selected' : '' }}>Operating Activities</option>
                                            <option value="INVESTING" {{ $transaction->category == 'INVESTING' ? 'selected' : '' }}>Investing Activities</option>
                                            <option value="FINANCING" {{ $transaction->category == 'FINANCING' ? 'selected' : '' }}>Financing Activities</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Subcategory *</label>
                                        <input type="text" name="subcategory" class="form-control" value="{{ $transaction->subcategory }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Amount *</label>
                                        <input type="number" name="amount" class="form-control" step="0.01" min="0" value="{{ $transaction->amount }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Payment Method</label>
                                        <input type="text" name="payment_method" class="form-control" value="{{ $transaction->payment_method }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Reference Type</label>
                                        <select name="reference_type" class="form-select">
                                            <option value="">Select Type</option>
                                            <option value="DEPOSIT" {{ $transaction->reference_type == 'DEPOSIT' ? 'selected' : '' }}>Deposit</option>
                                            <option value="LOAN_DISBURSEMENT" {{ $transaction->reference_type == 'LOAN_DISBURSEMENT' ? 'selected' : '' }}>Loan Disbursement</option>
                                            <option value="LOAN_REPAYMENT" {{ $transaction->reference_type == 'LOAN_REPAYMENT' ? 'selected' : '' }}>Loan Repayment</option>
                                            <option value="WELFARE_PAYMENT" {{ $transaction->reference_type == 'WELFARE_PAYMENT' ? 'selected' : '' }}>Welfare Payment</option>
                                            <option value="FINE_PAYMENT" {{ $transaction->reference_type == 'FINE_PAYMENT' ? 'selected' : '' }}>Fine Payment</option>
                                            <option value="EXPENSE" {{ $transaction->reference_type == 'EXPENSE' ? 'selected' : '' }}>Expense</option>
                                            <option value="OTHER" {{ $transaction->reference_type == 'OTHER' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Reference Number</label>
                                        <input type="text" name="reference_number" class="form-control" value="{{ $transaction->reference_number }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Fiscal Year</label>
                                        <select name="fiscal_year_id" class="form-select">
                                            <option value="">Select Fiscal Year</option>
                                            @foreach($fiscalYears as $fiscalYear)
                                                <option value="{{ $fiscalYear->id }}" {{ $transaction->fiscal_year_id == $fiscalYear->id ? 'selected' : '' }}>{{ $fiscalYear->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Member</label>
                                        <select name="member_id" class="form-select">
                                            <option value="">Select Member (Optional)</option>
                                            <option value="">No Member</option>
                                            @if($transaction->member)
                                                <option value="{{ $transaction->member->id }}" selected>{{ $transaction->member->first_name }} {{ $transaction->member->last_name }}</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" class="form-control" rows="3">{{ $transaction->description }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Notes</label>
                                        <textarea name="notes" class="form-control" rows="3">{{ $transaction->notes }}</textarea>
                                    </div>
                                </div>
                                
                                <div class="action-buttons mt-4">
                                    <button type="submit" class="action-btn primary">
                                        <i class="fas fa-save me-2"></i>
                                        Update Transaction
                                    </button>
                                    <a href="{{ route('admin.cashflow.show', $transaction->id) }}" class="action-btn info">
                                        <i class="fas fa-eye me-2"></i>
                                        View Details
                                    </a>
                                    <a href="{{ route('admin.cashflow.index') }}" class="action-btn secondary">
                                        <i class="fas fa-times me-2"></i>
                                        Cancel
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

<!-- Enhanced Styles -->
<style>
/* Form Styles */
.form-control {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-select {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Responsive Design */
@media (max-width: 768px) {
    .action-buttons {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .action-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection
