@extends('layouts.admin')

@section('title', 'Create Cashflow Transaction')

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
                                    <i class="fas fa-plus me-3"></i>
                                    Create Cashflow Transaction
                                </h1>
                                <p class="text-white fs-5 mb-0 opacity-90">
                                    <i class="fas fa-coins me-2"></i>
                                    Add a new cashflow transaction to the system
                                </p>
                            </div>
                            <div class="text-end">
                                <div class="month-badge">
                                    <span class="badge bg-white text-info fs-6 px-3 py-2">
                                        <i class="fas fa-calendar me-2"></i>
                                        Manual Entry
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
            <!-- Create Transaction Form -->
            <div class="row">
                <div class="col-md-8">
                    <div class="data-card">
                        <div class="data-card-header">
                            <h5 class="data-title">
                                <i class="fas fa-edit me-2"></i>
                                Transaction Details
                            </h5>
                            <div class="data-subtitle">
                                Enter the cashflow transaction information
                            </div>
                        </div>
                        <div class="data-card-body">
                            <!-- Error Alert -->
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</h5>
                                    <ul class="mb-0 mt-2">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif
                            
                            <form method="POST" action="{{ route('admin.cashflow.store') }}">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Transaction Date *</label>
                                        <input type="date" name="transaction_date" class="form-control @error('transaction_date') is-invalid @enderror" value="{{ old('transaction_date') }}" required>
                                        @error('transaction_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Transaction Type *</label>
                                        <select name="transaction_type" class="form-select @error('transaction_type') is-invalid @enderror" required>
                                            <option value="">Select Type</option>
                                            <option value="INFLOW" {{ old('transaction_type') == 'INFLOW' ? 'selected' : '' }}>Inflow</option>
                                            <option value="OUTFLOW" {{ old('transaction_type') == 'OUTFLOW' ? 'selected' : '' }}>Outflow</option>
                                        </select>
                                        @error('transaction_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Category *</label>
                                        <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                            <option value="">Select Category</option>
                                            <option value="OPERATING" {{ old('category') == 'OPERATING' ? 'selected' : '' }}>Operating Activities</option>
                                            <option value="INVESTING" {{ old('category') == 'INVESTING' ? 'selected' : '' }}>Investing Activities</option>
                                            <option value="FINANCING" {{ old('category') == 'FINANCING' ? 'selected' : '' }}>Financing Activities</option>
                                        </select>
                                        @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Subcategory *</label>
                                        <input type="text" name="subcategory" class="form-control @error('subcategory') is-invalid @enderror" placeholder="e.g., Member Savings, Loan Disbursement" value="{{ old('subcategory') }}" required>
                                        @error('subcategory')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Amount *</label>
                                        <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" step="0.01" min="0" placeholder="0.00" value="{{ old('amount') }}" required>
                                        @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Payment Method</label>
                                        <input type="text" name="payment_method" class="form-control @error('payment_method') is-invalid @enderror" placeholder="e.g., Bank Transfer, Cash" value="{{ old('payment_method') }}">
                                        @error('payment_method')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Reference Type</label>
                                        <select name="reference_type" class="form-select @error('reference_type') is-invalid @enderror">
                                            <option value="">Select Type</option>
                                            <option value="DEPOSIT" {{ old('reference_type') == 'DEPOSIT' ? 'selected' : '' }}>Deposit</option>
                                            <option value="LOAN_DISBURSEMENT" {{ old('reference_type') == 'LOAN_DISBURSEMENT' ? 'selected' : '' }}>Loan Disbursement</option>
                                            <option value="LOAN_REPAYMENT" {{ old('reference_type') == 'LOAN_REPAYMENT' ? 'selected' : '' }}>Loan Repayment</option>
                                            <option value="WELFARE_PAYMENT" {{ old('reference_type') == 'WELFARE_PAYMENT' ? 'selected' : '' }}>Welfare Payment</option>
                                            <option value="FINE_PAYMENT" {{ old('reference_type') == 'FINE_PAYMENT' ? 'selected' : '' }}>Fine Payment</option>
                                            <option value="EXPENSE" {{ old('reference_type') == 'EXPENSE' ? 'selected' : '' }}>Expense</option>
                                            <option value="OTHER" {{ old('reference_type') == 'OTHER' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('reference_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Reference Number</label>
                                        <input type="text" name="reference_number" class="form-control @error('reference_number') is-invalid @enderror" placeholder="e.g., DEP-000001" value="{{ old('reference_number') }}">
                                        @error('reference_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Fiscal Year</label>
                                        <select name="fiscal_year_id" class="form-select @error('fiscal_year_id') is-invalid @enderror">
                                            <option value="">Select Fiscal Year</option>
                                            @foreach($fiscalYears as $fiscalYear)
                                                <option value="{{ $fiscalYear->id }}" {{ old('fiscal_year_id') == $fiscalYear->id ? 'selected' : '' }}>{{ $fiscalYear->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('fiscal_year_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Member</label>
                                        <select name="member_id" class="form-select @error('member_id') is-invalid @enderror">
                                            <option value="">Select Member (Optional)</option>
                                            <option value="">No Member</option>
                                        </select>
                                        @error('member_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Enter transaction description">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Notes</label>
                                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Additional notes or comments">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="action-buttons mt-4">
                                    <button type="submit" class="action-btn primary">
                                        <i class="fas fa-save me-2"></i>
                                        Create Transaction
                                    </button>
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

/* Invalid Field Styles */
.form-control.is-invalid, .form-select.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.form-control.is-invalid:focus, .form-select.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

/* Alert Styles */
.alert {
    position: relative;
    padding: 1rem 1.5rem;
    margin-bottom: 1rem;
    border: 1px solid transparent;
    border-radius: 10px;
}

.alert-danger {
    color: #842029;
    background-color: #f8d7da;
    border-color: #f5c2c7;
}

.alert h5 {
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.alert ul {
    padding-left: 1.25rem;
}

.alert ul li {
    margin-bottom: 0.25rem;
}

.btn-close {
    position: absolute;
    top: 0.75rem;
    right: 1rem;
    padding: 0.25rem;
    background: transparent;
    border: 0;
    cursor: pointer;
    opacity: 0.5;
}

.btn-close:hover {
    opacity: 1;
}
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
