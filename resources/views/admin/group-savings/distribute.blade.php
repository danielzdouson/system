<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distribute Deposit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-1">
                        <i class="fas fa-hand-holding-usd text-primary me-2"></i>
                        Distribute Deposit Funds
                    </h2>
                    <p class="text-muted mb-0">Allocate deposit amount to different categories</p>
                </div>
                <a href="{{ route('admin.group-savings.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Deposit Info Card -->
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Deposit Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small">Member</label>
                                <p class="fw-bold mb-2">
                                    <i class="fas fa-user text-primary me-1"></i>
                                    <a href="{{ route('admin.members.show', $deposit->member->id) }}" class="member-link">
                                        {{ $deposit->member->first_name }} {{ $deposit->member->last_name }}
                                    </a>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small">Month</label>
                                <p class="fw-bold mb-2">
                                    <i class="fas fa-calendar text-info me-1"></i>
                                    {{ \Carbon\Carbon::create()->month($deposit->month)->format('F') }} {{ $deposit->month <= 6 ? '2025' : '2024' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small">Total Deposit</label>
                                <p class="fw-bold text-success mb-2">
                                    <i class="fas fa-money-bill-wave text-success me-1"></i>
                                    UGX {{ number_format($deposit->amount, 0) }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small">Available Balance</label>
                                <p class="fw-bold text-primary mb-2">
                                    <i class="fas fa-wallet text-primary me-1"></i>
                                    UGX {{ number_format($deposit->balance, 0) }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @if($deposit->notes)
                        <div class="row">
                            <div class="col-12">
                                <div class="info-item">
                                    <label class="text-muted small">Notes</label>
                                    <p class="mb-2">
                                        <i class="fas fa-sticky-note text-warning me-1"></i>
                                        {{ $deposit->notes }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Distribution Form Card -->
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-sliders-h me-2"></i>
                        Distribution Form
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.group-savings.store-distribution', $deposit->id) }}" id="distributionForm">
                        @csrf
                        <input type="hidden" name="distribution_month" value="{{ $deposit->month }}">
                        <input type="hidden" name="distribution_note" value="Distribution from deposit #{{ $deposit->id }}">
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="distribution-input">
                                    <label for="savings_amount" class="form-label fw-semibold">
                                        <i class="fas fa-piggy-bank text-success me-1"></i>
                                        Savings Amount
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-success text-white">
                                            <i class="fas fa-piggy-bank"></i>
                                        </span>
                                        <input type="number" name="savings_amount" id="savings_amount" class="form-control" 
                                               step="0.01" min="0" max="{{ $availableBalance ?? $deposit->balance }}" required>
                                        <span class="input-group-text">UGX</span>
                                    </div>
                                    <small class="text-muted">Amount allocated to member's savings</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="distribution-input">
                                    <label for="welfare_amount" class="form-label fw-semibold">
                                        <i class="fas fa-hands-helping text-info me-1"></i>
                                        Welfare Amount
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-info text-white">
                                            <i class="fas fa-hands-helping"></i>
                                        </span>
                                        <input type="number" name="welfare_amount" id="welfare_amount" class="form-control" 
                                               step="0.01" min="0" max="{{ $availableBalance ?? $deposit->balance }}" required>
                                        <span class="input-group-text">UGX</span>
                                    </div>
                                    <small class="text-muted">Amount allocated to group welfare fund</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-4 mt-2">
                            <div class="col-md-6">
                                <div class="distribution-input">
                                    <label for="fines_amount" class="form-label fw-semibold">
                                        <i class="fas fa-gavel text-warning me-1"></i>
                                        Fines Amount
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-warning text-dark">
                                            <i class="fas fa-gavel"></i>
                                        </span>
                                        <input type="number" name="fines_amount" id="fines_amount" class="form-control" 
                                               step="0.01" min="0" max="{{ $availableBalance ?? $deposit->balance }}" required>
                                        <span class="input-group-text">UGX</span>
                                    </div>
                                    <small class="text-muted">Amount allocated to fines payment</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="distribution-input">
                                    <label for="other_amount" class="form-label fw-semibold">
                                        <i class="fas fa-ellipsis-h text-secondary me-1"></i>
                                        Other Amount
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-secondary text-white">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </span>
                                        <input type="number" name="other_amount" id="other_amount" class="form-control" 
                                               step="0.01" min="0" max="{{ $availableBalance ?? $deposit->balance }}" required>
                                        <span class="input-group-text">UGX</span>
                                    </div>
                                    <small class="text-muted">Amount allocated to other funds</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-4 mt-2">
                            <div class="col-12">
                                <label for="other_description" class="form-label fw-semibold">
                                    <i class="fas fa-comment text-primary me-1"></i>
                                    Other Description (if applicable)
                                </label>
                                <input type="text" name="other_description" id="other_description" class="form-control" 
                                       placeholder="Describe what the 'other' amount is for">
                            </div>
                        </div>
                        
                        <!-- Distribution Summary -->
                        <div class="alert alert-info border-0 bg-light-info mt-4">
                            <h6 class="fw-bold text-info mb-3">
                                <i class="fas fa-chart-pie me-1"></i>
                                Distribution Summary
                            </h6>
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="summary-item">
                                        <div class="summary-value text-primary">UGX <span id="total_deposit">{{ number_format($deposit->balance, 0) }}</span></div>
                                        <div class="summary-label">Total Deposit</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="summary-item">
                                        <div class="summary-value text-success">UGX <span id="total_distributed">0</span></div>
                                        <div class="summary-label">Total Distributed</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="summary-item">
                                        <div class="summary-value text-warning">UGX <span id="remaining_balance">{{ number_format($deposit->balance, 0) }}</span></div>
                                        <div class="summary-label">Remaining</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="summary-item">
                                        <div class="summary-label">Status</div>
                                        <div id="distribution_status" class="badge bg-warning">Pending</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.group-savings.dashboard') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-times me-2"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary-gradient btn-lg" id="submit_btn">
                                <i class="fas fa-check me-2"></i> Distribute Funds
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Previous Distributions Card -->
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>
                        Previous Distributions
                    </h5>
                </div>
                <div class="card-body">
                    @if($deposit->distributions->count() > 0)
                        @foreach($deposit->distributions as $distribution)
                            <div class="distribution-item mb-3 p-3 border rounded bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper me-3">
                                            @switch($distribution->type)
                                                @case('savings')
                                                    <i class="fas fa-piggy-bank text-success fa-lg"></i>
                                                    @break
                                                @case('welfare')
                                                    <i class="fas fa-hands-helping text-info fa-lg"></i>
                                                    @break
                                                @case('fines')
                                                    <i class="fas fa-gavel text-warning fa-lg"></i>
                                                    @break
                                                @default
                                                    <i class="fas fa-ellipsis-h text-secondary fa-lg"></i>
                                            @endswitch
                                        </div>
                                        <div>
                                            <strong>{{ ucfirst($distribution->type) }}</strong>
                                            @if($distribution->description)
                                                <br><small class="text-muted">{{ $distribution->description }}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <strong class="text-primary">UGX {{ number_format($distribution->amount, 0) }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No distributions made yet.</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Tips Card -->
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-gradient-warning text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        Distribution Tips
                    </h5>
                </div>
                <div class="card-body">
                    <div class="tip-item mb-3">
                        <h6 class="fw-bold text-warning mb-2">
                            <i class="fas fa-balance-scale me-1"></i>
                            Balance Rule
                        </h6>
                        <p class="small text-muted">Total distribution must equal the available balance for complete distribution.</p>
                    </div>
                    <div class="tip-item mb-3">
                        <h6 class="fw-bold text-info mb-2">
                            <i class="fas fa-piggy-bank me-1"></i>
                            Savings Priority
                        </h6>
                        <p class="small text-muted">Member savings are tracked per month and contribute to their total savings.</p>
                    </div>
                    <div class="tip-item">
                        <h6 class="fw-bold text-success mb-2">
                            <i class="fas fa-users me-1"></i>
                            Welfare Fund
                        </h6>
                        <p class="small text-muted">Welfare funds are group-based and can be used for member assistance programs.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>

.distribution-input {
    position: relative;
}

.distribution-input .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.info-item {
    margin-bottom: 1rem;
}

.info-item label {
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.summary-item {
    padding: 1rem;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.5);
}

.summary-value {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.summary-label {
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
}

.distribution-item {
    transition: all 0.3s ease;
}

.distribution-item:hover {
    background-color: rgba(102, 126, 234, 0.1) !important;
    transform: translateX(5px);
}

.icon-wrapper {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.8);
}

.tip-item {
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(0,0,0,0.1);
}

.tip-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.btn-primary-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    transition: all 0.3s ease;
}

.btn-primary-gradient:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.bg-light-info {
    background-color: rgba(54, 209, 220, 0.1);
    border-left: 4px solid #36d1dc;
}

.card {
    border-radius: 15px;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
    border-bottom: none;
}
.container-fluid{
    margin-bottom: -1200px;
    padding-left: -30px;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Prevent back button navigation
history.pushState(null, null, location.href);
window.onpopstate = function () {
    history.go(1);
    alert('You cannot go back from this page. The deposit has already been created and requires distribution.');
};

document.addEventListener('DOMContentLoaded', function() {
    const totalDeposit = {{ $deposit->balance }};
    const availableBalance = {{ $availableBalance ?? $deposit->balance }};
    const inputs = ['savings_amount', 'welfare_amount', 'fines_amount', 'other_amount'];
    
    function updateSummary() {
        let totalDistributed = 0;
        inputs.forEach(id => {
            const value = parseFloat(document.getElementById(id).value) || 0;
            totalDistributed += value;
        });
        
        const remaining = availableBalance - totalDistributed;
        
        document.getElementById('total_deposit').textContent = availableBalance.toLocaleString();
        document.getElementById('total_distributed').textContent = totalDistributed.toLocaleString();
        document.getElementById('remaining_balance').textContent = remaining.toLocaleString();
        
        const statusEl = document.getElementById('distribution_status');
        const submitBtn = document.getElementById('submit_btn');
        
        if (Math.abs(remaining) < 0.01) {
            statusEl.textContent = 'Perfect';
            statusEl.className = 'badge bg-success';
            submitBtn.disabled = false;
            submitBtn.classList.remove('btn-warning');
            submitBtn.classList.add('btn-primary-gradient');
        } else if (remaining < 0) {
            statusEl.textContent = 'Over';
            statusEl.className = 'badge bg-danger';
            submitBtn.disabled = true;
            submitBtn.classList.add('btn-warning');
            submitBtn.classList.remove('btn-primary-gradient');
        } else {
            statusEl.textContent = 'Remaining';
            statusEl.className = 'badge bg-warning';
            submitBtn.disabled = false;
            submitBtn.classList.add('btn-warning');
            submitBtn.classList.remove('btn-primary-gradient');
        }
    }
    
    // Auto-format inputs
    inputs.forEach(id => {
        const input = document.getElementById(id);
        input.addEventListener('input', function() {
            updateSummary();
        });
        
        input.addEventListener('blur', function() {
            if (this.value && !isNaN(this.value)) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    });
    
    updateSummary();
    
    // Warn before page refresh/close
    window.addEventListener('beforeunload', function(e) {
        e.preventDefault();
        e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
    });
});
</script>
</body>
</html>
