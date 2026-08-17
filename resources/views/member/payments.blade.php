@extends('layouts.member')

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card gradient-bg text-white border-0">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-2">Make Payments</h2>
                            <p class="mb-0 opacity-75">Pay your loans, fines, or make deposits online</p>
                        </div>
                        <a href="{{ route('member.payment.history') }}" class="btn btn-light">
                            <i class="fas fa-history me-2"></i>Payment History
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Options -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-3 d-inline-block mb-3">
                        <i class="fas fa-hand-holding-usd fa-2x"></i>
                    </div>
                    <h5 class="mb-2">Loan Payment</h5>
                    <p class="text-muted small mb-3">Repay your outstanding loans</p>
                    @if($activeLoans->count() > 0)
                        <select id="loanSelect" class="form-select mb-2">
                            <option value="">Select Loan</option>
                            @foreach($activeLoans as $loan)
                                <option value="{{ $loan->id }}" data-balance="{{ $loan->balance }}" data-number="{{ $loan->loan_number }}">
                                    Loan #{{ $loan->loan_number }} - Balance: UGX {{ number_format($loan->balance, 0) }}
                                </option>
                            @endforeach
                        </select>
                        <button onclick="showLoanPaymentForm()" class="btn btn-primary w-100">
                            <i class="fas fa-credit-card me-2"></i>Pay Loan
                        </button>
                    @else
                        <button class="btn btn-outline-secondary w-100" disabled>
                            <i class="fas fa-check me-2"></i>No Active Loans
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3 d-inline-block mb-3">
                        <i class="fas fa-gavel fa-2x"></i>
                    </div>
                    <h5 class="mb-2">Fine Payment</h5>
                    <p class="text-muted small mb-3">Pay your outstanding fines</p>
                    <button onclick="showPaymentForm('fine_payment')" class="btn btn-warning w-100">
                        <i class="fas fa-credit-card me-2"></i>Pay Fine
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 d-inline-block mb-3">
                        <i class="fas fa-piggy-bank fa-2x"></i>
                    </div>
                    <h5 class="mb-2">Savings Deposit</h5>
                    <p class="text-muted small mb-3">Add to your savings account</p>
                    <button onclick="showPaymentForm('savings_deposit')" class="btn btn-success w-100">
                        <i class="fas fa-credit-card me-2"></i>Deposit Savings
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Form Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-credit-card me-2"></i>
                        <span id="paymentModalTitle">Make Payment</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="paymentForm">
                        @csrf
                        <input type="hidden" id="paymentType" name="payment_type" value="">
                        <input type="hidden" id="loanId" name="loan_id" value="">

                        <!-- Payment Details -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Amount (UGX)</label>
                                <div class="input-group">
                                    <span class="input-group-text">UGX</span>
                                    <input type="number" id="amount" name="amount" class="form-control" 
                                           min="1000" step="1000" required>
                                </div>
                                <small class="text-muted">Minimum: UGX 1,000</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Method</label>
                                <select id="paymentMethod" name="payment_method" class="form-select" required onchange="togglePaymentMethodFields()">
                                    <option value="">Select Method</option>
                                    <option value="mobile_money">Mobile Money</option>
                                    <option value="card">Card (Visa/Mastercard)</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                            </div>
                        </div>

                        <!-- Mobile Money Fields -->
                        <div id="mobileMoneyFields" class="mb-3" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Mobile Network</label>
                                    <select id="mobileNetwork" name="mobile_network" class="form-select">
                                        <option value="">Select Network</option>
                                        <option value="MTN">MTN Mobile Money</option>
                                        <option value="AIRTEL">Airtel Money</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" id="phoneNumber" name="phone_number" class="form-control" 
                                           placeholder="256700000000" pattern="^256\d{9}$">
                                    <small class="text-muted">Format: 256XXXXXXXXX</small>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Transfer Details -->
                        <div id="bankTransferFields" class="mb-3" style="display: none;">
                            <div class="alert alert-info">
                                <h6 class="alert-heading"><i class="fas fa-university me-2"></i>Bank Transfer Details</h6>
                                @if($bankDetails->where('mobile_money_provider', null)->count() > 0)
                                    @foreach($bankDetails->where('mobile_money_provider', null) as $bank)
                                        <div class="mb-2">
                                            <strong>{{ $bank->bank_name }}</strong><br>
                                            Account Name: {{ $bank->account_name }}<br>
                                            Account Number: {{ $bank->account_number }}<br>
                                            @if($bank->branch) Branch: {{ $bank->branch }}<br> @endif
                                            @if($bank->swift_code) SWIFT: {{ $bank->swift_code }} @endif
                                        </div>
                                    @endforeach
                                @else
                                    <p class="mb-0">No bank details configured. Please contact admin.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea id="notes" name="notes" class="form-control" rows="2" 
                                      placeholder="Add any notes about this payment"></textarea>
                        </div>

                        <!-- Payment Summary -->
                        <div class="alert alert-secondary">
                            <h6 class="alert-heading mb-2">Payment Summary</h6>
                            <div class="d-flex justify-content-between">
                                <span>Amount:</span>
                                <strong>UGX <span id="summaryAmount">0</span></strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Method:</span>
                                <strong id="summaryMethod">-</strong>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" onclick="submitPayment()" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-lock me-2"></iProceed to Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-history text-info me-2"></i>Recent Payments
                    </h5>
                    <a href="{{ route('member.payment.history') }}" class="btn btn-sm btn-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    @if($payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>
                                                <small>{{ $payment->transaction_reference }}</small>
                                            </td>
                                            <td>{{ $payment->payment_type_label }}</td>
                                            <td>UGX {{ number_format($payment->amount, 0) }}</td>
                                            <td>{{ $payment->payment_method_label }}</td>
                                            <td>{!! $payment->status_badge !!}</td>
                                            <td>{{ $payment->created_at->format('M j, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="bg-light rounded-circle p-4 d-inline-block mb-3">
                                <i class="fas fa-inbox fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">No payments yet</h5>
                            <p class="text-muted">Your payment history will appear here</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showPaymentForm(type) {
    document.getElementById('paymentType').value = type;
    document.getElementById('loanId').value = '';
    
    const titles = {
        'fine_payment': 'Pay Fine',
        'savings_deposit': 'Deposit Savings',
        'education': 'Education Payment',
        'other': 'Other Payment'
    };
    
    document.getElementById('paymentModalTitle').textContent = titles[type] || 'Make Payment';
    
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    modal.show();
}

function showLoanPaymentForm() {
    const loanSelect = document.getElementById('loanSelect');
    const selectedOption = loanSelect.options[loanSelect.selectedIndex];
    
    if (!selectedOption.value) {
        alert('Please select a loan first');
        return;
    }
    
    document.getElementById('paymentType').value = 'loan_payment';
    document.getElementById('loanId').value = selectedOption.value;
    document.getElementById('amount').value = selectedOption.dataset.balance;
    document.getElementById('amount').max = selectedOption.dataset.balance;
    
    document.getElementById('paymentModalTitle').textContent = 'Pay Loan #' + selectedOption.dataset.number;
    
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    modal.show();
}

function togglePaymentMethodFields() {
    const method = document.getElementById('paymentMethod').value;
    const mobileFields = document.getElementById('mobileMoneyFields');
    const bankFields = document.getElementById('bankTransferFields');
    
    mobileFields.style.display = method === 'mobile_money' ? 'block' : 'none';
    bankFields.style.display = method === 'bank_transfer' ? 'block' : 'none';
    
    // Update summary
    const methodLabels = {
        'mobile_money': 'Mobile Money',
        'card': 'Card',
        'bank_transfer': 'Bank Transfer'
    };
    document.getElementById('summaryMethod').textContent = methodLabels[method] || '-';
}

document.getElementById('amount').addEventListener('input', function() {
    document.getElementById('summaryAmount').textContent = Number(this.value).toLocaleString();
});

async function submitPayment() {
    const form = document.getElementById('paymentForm');
    const formData = new FormData(form);
    
    // Validate
    if (!formData.get('payment_type') || !formData.get('amount') || !formData.get('payment_method')) {
        alert('Please fill in all required fields');
        return;
    }
    
    if (formData.get('payment_method') === 'mobile_money' && !formData.get('phone_number')) {
        alert('Please enter your phone number for mobile money');
        return;
    }
    
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    
    try {
        const response = await fetch('{{ route('member.payment.initiate') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Redirect to Pesapal payment page
            window.location.href = data.redirect_url;
        } else {
            alert('Payment failed: ' + (data.error || 'Unknown error'));
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-lock me-2"></i>Proceed to Payment';
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-lock me-2"></i>Proceed to Payment';
    }
}
</script>
@endsection
