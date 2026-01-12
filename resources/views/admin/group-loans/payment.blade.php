@extends('layouts.admin')

@section('title', 'Record Loan Payment')

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
                                    <i class="fas fa-money-check-alt me-3"></i>
                                    Record Payment - Installment #{{ $schedule->installment_number }}
                                </h1>
                                <p class="text-white fs-5 mb-0 opacity-90">
                                    <i class="fas fa-info-circle me-2"></i>
                                    {{ $loan->member->first_name }} {{ $loan->member->last_name }} - {{ $loan->loan_number ?? 'LN-' . $loan->id }}
                                </p>
                            </div>
                            <div class="text-end">
                                <div class="month-badge">
                                    <span class="badge bg-white text-success fs-6 px-3 py-2">
                                        <i class="fas fa-calendar me-2"></i>
                                        Due: {{ $schedule->due_date->format('M d, Y') }}
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
            <!-- Payment Summary Card -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="data-card">
                        <div class="data-card-header">
                            <h5 class="data-title">
                                <i class="fas fa-file-invoice-dollar me-2"></i>
                                Installment Details
                            </h5>
                            <div class="data-subtitle">
                                Payment breakdown for this installment
                            </div>
                        </div>
                        <div class="data-card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Principal Due</label>
                                        <h5 class="text-primary">UGX {{ number_format($schedule->principal_due, 0) }}</h5>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Interest Due</label>
                                        <h5 class="text-warning">UGX {{ number_format($schedule->interest_due, 0) }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Total Due</label>
                                        <h5 class="text-success">UGX {{ number_format($schedule->total_due, 0) }}</h5>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Current Status</label>
                                        <div>{!! $schedule->getStatusBadge() !!}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Due Date</label>
                                <h6>{{ $schedule->due_date->format('M d, Y') }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="data-card">
                        <div class="data-card-header">
                            <h5 class="data-title">
                                <i class="fas fa-chart-line me-2"></i>
                                Loan Summary
                            </h5>
                            <div class="data-subtitle">
                                Current loan status and balance
                            </div>
                        </div>
                        <div class="data-card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Total Loan Amount</label>
                                        <h5 class="text-primary">UGX {{ number_format($loan->loan_amount ?? $loan->principal_amount, 0) }}</h5>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Current Balance</label>
                                        <h5 class="text-danger">UGX {{ number_format($loan->balance, 0) }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Amount Paid</label>
                                        <h5 class="text-success">UGX {{ number_format($loan->paid_amount, 0) }}</h5>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Progress</label>
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: {{ $loan->getProgressPercentage() }}%">
                                                {{ number_format($loan->getProgressPercentage(), 1) }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="row">
                <div class="col-12">
                    <div class="data-card">
                        <div class="data-card-header">
                            <h5 class="data-title">
                                <i class="fas fa-edit me-2"></i>
                                Record Payment
                            </h5>
                            <div class="data-subtitle">
                                Enter payment details for this installment
                            </div>
                        </div>
                        <div class="data-card-body">
                            <form action="{{ route('admin.group-loans.payment.record', [$loan->id, $schedule->id]) }}" method="POST">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="principal_paid" class="form-label">Principal Amount Paid</label>
                                            <div class="input-group">
                                                <span class="input-group-text">UGX</span>
                                                <input type="number" name="principal_paid" id="principal_paid" 
                                                       class="form-control" step="0.01" min="0" 
                                                       value="{{ $schedule->principal_due }}" required>
                                            </div>
                                            <small class="text-muted">Due: UGX {{ number_format($schedule->principal_due, 0) }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="interest_paid" class="form-label">Interest Amount Paid</label>
                                            <div class="input-group">
                                                <span class="input-group-text">UGX</span>
                                                <input type="number" name="interest_paid" id="interest_paid" 
                                                       class="form-control" step="0.01" min="0" 
                                                       value="{{ $schedule->interest_due }}" required>
                                            </div>
                                            <small class="text-muted">Due: UGX {{ number_format($schedule->interest_due, 0) }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="penalty_paid" class="form-label">Penalty Amount Paid</label>
                                            <div class="input-group">
                                                <span class="input-group-text">UGX</span>
                                                <input type="number" name="penalty_paid" id="penalty_paid" 
                                                       class="form-control" step="0.01" min="0" value="0">
                                            </div>
                                            <small class="text-muted">Optional penalty amount</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="payment_date" class="form-label">Payment Date</label>
                                            <input type="date" name="payment_date" id="payment_date" 
                                                   class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="payment_method" class="form-label">Payment Method</label>
                                            <select name="payment_method" id="payment_method" class="form-select" required>
                                                <option value="">Select Method</option>
                                                <option value="cash">Cash</option>
                                                <option value="bank_transfer">Bank Transfer</option>
                                                <option value="mobile_money">Mobile Money</option>
                                                <option value="cheque">Cheque</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Total Payment</label>
                                            <div class="input-group">
                                                <span class="input-group-text">UGX</span>
                                                <input type="text" id="total_payment" class="form-control" readonly 
                                                       value="{{ number_format($schedule->principal_due + $schedule->interest_due, 0) }}">
                                            </div>
                                            <small class="text-success">Auto-calculated</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Payment Notes</label>
                                            <textarea name="notes" id="notes" class="form-control" rows="3" 
                                                      placeholder="Add any notes about this payment..."></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Payment Summary:</strong> 
                                            This payment will reduce the loan balance from 
                                            <strong>UGX {{ number_format($loan->balance, 0) }}</strong> to 
                                            <strong>UGX {{ number_format(max(0, $loan->balance - ($schedule->principal_due + $schedule->interest_due + ($request->penalty_paid ?? 0))), 0) }}</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="action-buttons">
                                    <button type="submit" class="action-btn success">
                                        <i class="fas fa-save me-2"></i>
                                        Record Payment
                                    </button>
                                    <a href="{{ route('admin.group-loans.show', $loan->id) }}" class="action-btn primary">
                                        <i class="fas fa-arrow-left me-2"></i>
                                        Back to Loan Details
                                    </a>
                                    <button type="button" class="action-btn warning" onclick="window.print()">
                                        <i class="fas fa-print me-2"></i>
                                        Print Receipt
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Enhanced Page Header */
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    margin-bottom: 2rem;
}

.month-badge .badge {
    border-radius: 50px;
    font-weight: 600;
}

/* Enhanced Data Card */
.data-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 2rem;
}

.data-card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
}

.data-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.data-subtitle {
    opacity: 0.9;
    margin: 0;
}

.data-card-body {
    padding: 2rem;
}

/* Form Enhancements */
.form-control, .form-select {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.input-group-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 10px 0 0 10px;
}

.input-group .form-control {
    border-radius: 0 10px 10px 0;
}

/* Progress Bar */
.progress {
    background-color: #e9ecef;
    border-radius: 50px;
    overflow: hidden;
}

.progress-bar {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    transition: width 0.6s ease;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.action-btn {
    padding: 1rem 2rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    color: white;
    border: none;
    cursor: pointer;
}

.action-btn.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.action-btn.success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.action-btn.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    color: white;
}

/* Alert Styling */
.alert {
    border-radius: 15px;
    border: none;
    padding: 1rem 1.5rem;
}

.alert-info {
    background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
    color: white;
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header {
        padding: 1.5rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .action-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
// Auto-calculate total payment
document.addEventListener('DOMContentLoaded', function() {
    const principalInput = document.getElementById('principal_paid');
    const interestInput = document.getElementById('interest_paid');
    const penaltyInput = document.getElementById('penalty_paid');
    const totalInput = document.getElementById('total_payment');

    function calculateTotal() {
        const principal = parseFloat(principalInput.value) || 0;
        const interest = parseFloat(interestInput.value) || 0;
        const penalty = parseFloat(penaltyInput.value) || 0;
        const total = principal + interest + penalty;
        
        totalInput.value = total.toLocaleString('en-US', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
        
        // Update payment summary dynamically
        const currentBalance = parseFloat('{{ $loan->balance }}');
        const newBalance = Math.max(0, currentBalance - total);
        const summaryElement = document.querySelector('.alert-info strong:last-child');
        if (summaryElement) {
            summaryElement.textContent = 'UGX ' + newBalance.toLocaleString('en-US', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }
    }

    principalInput.addEventListener('input', calculateTotal);
    interestInput.addEventListener('input', calculateTotal);
    penaltyInput.addEventListener('input', calculateTotal);
    
    // Initial calculation
    calculateTotal();
});
</script>
@endsection
