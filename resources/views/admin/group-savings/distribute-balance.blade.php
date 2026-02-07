<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distribute Balance</title>
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
                        <i class="fas fa-wallet text-info me-2"></i>
                        Distribute from Account Balance
                    </h2>
                    <p class="text-muted mb-0">Allocate available account balance to different categories</p>
                </div>
                <a href="{{ route('admin.group-savings.monthly', $month) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Monthly View
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Member Info Card -->
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>
                        Member Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small">Member Name</label>
                                <p class="fw-bold mb-2">
                                    <i class="fas fa-user text-primary me-1"></i>
                                    <a href="{{ route('admin.members.show', $member->id) }}" class="member-link">
                                        {{ $member->first_name }} {{ $member->last_name }}
                                    </a>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small">Month</label>
                                <p class="fw-bold mb-2">
                                    <i class="fas fa-calendar text-info me-1"></i>
                                    {{ $monthName }} {{ $month <= 6 ? '2025' : '2024' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small">Available Balance</label>
                                <p class="fw-bold text-info mb-2">
                                    <i class="fas fa-wallet text-info me-1"></i>
                                    UGX {{ number_format($availableBalance, 0) }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small">Fiscal Year</label>
                                <p class="fw-bold mb-2">
                                    <i class="fas fa-calendar-alt text-success me-1"></i>
                                    {{ $activeFiscalYear->name }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Distribution Form Card -->
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-sliders-h me-2"></i>
                        Balance Distribution Form
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.group-savings.store-balance-distribution', [$member->id, $month]) }}" id="distributionForm">
                        @csrf
                        <input type="hidden" name="distribution_note" value="Balance distribution for {{ $monthName }} {{ $month <= 6 ? '2025' : '2024' }}">
                        
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
                                               step="0.01" min="0" max="{{ $availableBalance }}" required>
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
                                               step="0.01" min="0" max="{{ $availableBalance }}" required>
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
                                               step="0.01" min="0" max="{{ $availableBalance }}" required>
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
                                               step="0.01" min="0" max="{{ $availableBalance }}" required>
                                        <span class="input-group-text">UGX</span>
                                    </div>
                                    <small class="text-muted">Amount allocated to other funds</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-4 mt-2">
                            <div class="col-12">
                                <label for="distribution_note" class="form-label fw-semibold">
                                    <i class="fas fa-comment text-primary me-1"></i>
                                    Distribution Note (Optional)
                                </label>
                                <input type="text" name="distribution_note" id="distribution_note" class="form-control" 
                                       value="Balance distribution for {{ $monthName }} {{ $month <= 6 ? '2025' : '2024' }}"
                                       placeholder="Add a note for this distribution">
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
                                        <div class="summary-value text-info">UGX <span id="available_balance">{{ number_format($availableBalance, 0) }}</span></div>
                                        <div class="summary-label">Available Balance</div>
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
                                        <div class="summary-value text-warning">UGX <span id="remaining_balance">{{ number_format($availableBalance, 0) }}</span></div>
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
                            <a href="{{ route('admin.group-savings.monthly', $month) }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-times me-2"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-info-gradient btn-lg" id="submit_btn">
                                <i class="fas fa-check me-2"></i> Distribute Balance
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Unpaid Fines Card -->
            @if($unpaidFines->count() > 0)
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-header bg-gradient-warning text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-gavel me-2"></i>
                        Unpaid Fines for {{ $monthName }}
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($unpaidFines as $fine)
                        <div class="fine-item mb-3 p-3 border rounded bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>UGX {{ number_format($fine->amount, 0) }}</strong>
                                    <br><small class="text-muted">{{ $fine->description }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-warning">Pending</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="text-center mt-3">
                        <small class="text-muted">Total unpaid: UGX {{ number_format($unpaidFines->sum('amount'), 0) }}</small>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Tips Card -->
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        Balance Distribution Tips
                    </h5>
                </div>
                <div class="card-body">
                    <div class="tip-item mb-3">
                        <h6 class="fw-bold text-info mb-2">
                            <i class="fas fa-balance-scale me-1"></i>
                            Balance Rule
                        </h6>
                        <p class="small text-muted">Total distribution must not exceed available account balance.</p>
                    </div>
                    <div class="tip-item mb-3">
                        <h6 class="fw-bold text-success mb-2">
                            <i class="fas fa-piggy-bank me-1"></i>
                            Virtual Deposit
                        </h6>
                        <p class="small text-muted">A virtual deposit record will be created for tracking purposes.</p>
                    </div>
                    <div class="tip-item">
                        <h6 class="fw-bold text-warning mb-2">
                            <i class="fas fa-gavel me-1"></i>
                            Fine Payment
                        </h6>
                        <p class="small text-muted">Any fines amount will automatically pay outstanding fines for this month.</p>
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
    border-color: #36d1dc;
    box-shadow: 0 0 0 0.2rem rgba(54, 209, 220, 0.25);
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

.fine-item {
    transition: all 0.3s ease;
}

.fine-item:hover {
    background-color: rgba(240, 147, 251, 0.1) !important;
    transform: translateX(5px);
}

.tip-item {
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(0,0,0,0.1);
}

.tip-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.btn-info-gradient {
    background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
    border: none;
    color: white;
    transition: all 0.3s ease;
}

.btn-info-gradient:hover {
    background: linear-gradient(135deg, #5b86e5 0%, #36d1dc 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(54, 209, 220, 0.4);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
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
document.addEventListener('DOMContentLoaded', function() {
    const availableBalance = {{ $availableBalance }};
    const inputs = ['savings_amount', 'welfare_amount', 'fines_amount', 'other_amount'];
    
    function updateSummary() {
        let totalDistributed = 0;
        inputs.forEach(id => {
            const value = parseFloat(document.getElementById(id).value) || 0;
            totalDistributed += value;
        });
        
        const remaining = availableBalance - totalDistributed;
        
        document.getElementById('available_balance').textContent = availableBalance.toLocaleString();
        document.getElementById('total_distributed').textContent = totalDistributed.toLocaleString();
        document.getElementById('remaining_balance').textContent = remaining.toLocaleString();
        
        const statusEl = document.getElementById('distribution_status');
        const submitBtn = document.getElementById('submit_btn');
        
        if (Math.abs(remaining) < 0.01) {
            statusEl.textContent = 'Perfect';
            statusEl.className = 'badge bg-success';
            submitBtn.disabled = false;
            submitBtn.classList.remove('btn-warning');
            submitBtn.classList.add('btn-info-gradient');
        } else if (remaining < 0) {
            statusEl.textContent = 'Over';
            statusEl.className = 'badge bg-danger';
            submitBtn.disabled = true;
            submitBtn.classList.add('btn-warning');
            submitBtn.classList.remove('btn-info-gradient');
        } else {
            statusEl.textContent = 'Remaining';
            statusEl.className = 'badge bg-warning';
            submitBtn.disabled = false;
            submitBtn.classList.add('btn-warning');
            submitBtn.classList.remove('btn-info-gradient');
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
});
</script>
</body>
</html>
