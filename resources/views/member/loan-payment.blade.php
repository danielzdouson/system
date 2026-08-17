@extends('layouts.member')

@section('content')
<div class="container-fluid p-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card gradient-bg text-white border-0">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-2">Make Loan Payment</h2>
                            <p class="mb-0 opacity-75">Loan #{{ $loan->loan_number }}</p>
                        </div>
                        <a href="{{ route('member.loans') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-2"></i>Back to Loans
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Payment Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <strong>Outstanding Balance:</strong>
                            <p class="text-danger h4">UGX {{ number_format($loan->balance, 0) }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Monthly Payment:</strong>
                            <p class="text-success h4">UGX {{ number_format($loan->monthly_payment, 0) }}</p>
                        </div>
                    </div>

                    <h6 class="mb-3">Next Payment Due</h6>
                    @if($loan->repaymentSchedules->where('status', 'pending')->first())
                        @php $nextPayment = $loan->repaymentSchedules->where('status', 'pending')->first(); @endphp
                        <div class="bg-light p-3 rounded mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Due Date:</strong>
                                    <p>{{ $nextPayment->due_date->format('M j, Y') }}</p>
                                </div>
                                <div class="col-md-4">
                                    <strong>Amount Due:</strong>
                                    <p class="text-primary">UGX {{ number_format($nextPayment->total_due, 0) }}</p>
                                </div>
                                <div class="col-md-4">
                                    <strong>Days Until Due:</strong>
                                    <p>{{ $nextPayment->due_date->diffInDays(now()) }} days</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-4">No pending payments</p>
                    @endif

                    <!-- Payment Form -->
                    <form id="loanPaymentForm">
                        @csrf
                        <input type="hidden" name="payment_type" value="loan_payment">
                        <input type="hidden" name="loan_id" value="{{ $loan->id }}">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Payment Amount (UGX)</label>
                                <div class="input-group">
                                    <span class="input-group-text">UGX</span>
                                    <input type="number" name="amount" class="form-control" 
                                           min="1000" max="{{ $loan->balance }}" step="1000" required
                                           value="{{ $loan->monthly_payment }}">
                                </div>
                                <small class="text-muted">Maximum: UGX {{ number_format($loan->balance, 0) }}</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Method</label>
                                <select name="payment_method" class="form-select" required onchange="togglePaymentMethodFields()">
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
                                    <select name="mobile_network" class="form-select">
                                        <option value="">Select Network</option>
                                        <option value="MTN">MTN Mobile Money</option>
                                        <option value="AIRTEL">Airtel Money</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone_number" class="form-control" 
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

                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="2" 
                                      placeholder="Add any notes about this payment"></textarea>
                        </div>

                        <button type="button" onclick="submitLoanPayment()" class="btn btn-primary w-100" id="submitBtn">
                            <i class="fas fa-lock me-2"></i>Proceed to Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-university me-2"></i>Payment Methods</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Available payment methods:</p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-mobile-alt text-success me-2"></i>Mobile Money (MTN/Airtel)</li>
                        <li class="mb-2"><i class="fas fa-credit-card text-primary me-2"></i>Card (Visa/Mastercard)</li>
                        <li class="mb-2"><i class="fas fa-university text-info me-2"></i>Bank Transfer</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePaymentMethodFields() {
    const method = document.querySelector('select[name="payment_method"]').value;
    const mobileFields = document.getElementById('mobileMoneyFields');
    const bankFields = document.getElementById('bankTransferFields');
    
    mobileFields.style.display = method === 'mobile_money' ? 'block' : 'none';
    bankFields.style.display = method === 'bank_transfer' ? 'block' : 'none';
}

async function submitLoanPayment() {
    const form = document.getElementById('loanPaymentForm');
    const formData = new FormData(form);
    
    const amount = parseFloat(formData.get('amount'));
    const maxAmount = parseFloat('{{ $loan->balance }}');
    
    if (amount > maxAmount) {
        alert('Payment amount cannot exceed outstanding balance');
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
