

@section('title', 'Create Deposit')

@section('content')
<div>
    <!-- Page Header -->
    <div style="margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="margin: 0; font-size: 24px; color: #1f2937; display: flex; align-items: center;">
                    <i class="fas fa-plus-circle" style="color: #3b82f6; margin-right: 8px;"></i>
                    Create New Deposit
                </h2>
                <p style="margin: 5px 0 0 0; color: #6b7280;">Record a member deposit and distribute funds to different categories</p>
            </div>
            <a href="{{ route('admin.group-savings.dashboard') }}" style="background: #f3f4f6; color: #374151; padding: 10px 16px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; border: 1px solid #d1d5db;">
                <i class="fas fa-arrow-left" style="margin-right: 4px;"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <div style="display: flex; gap: 30px;">
        <div style="flex: 2;">
            <div style="background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); overflow: hidden;">
                <div style="background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); color: white; padding: 20px;">
                    <h5 style="margin: 0; font-size: 18px; display: flex; align-items: center;">
                        <i class="fas fa-wallet" style="margin-right: 8px;"></i>
                        Deposit Information
                    </h5>
                </div>
                <div style="padding: 25px;">
                    @if($activeFiscalYear)
                        <form method="POST" action="{{ route('admin.group-savings.store-deposit') }}" id="depositForm">
                            @csrf
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                                <div>
                                    <label for="member_id" style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">
                                        <i class="fas fa-user" style="color: #3b82f6; margin-right: 4px;"></i>
                                        Select Member
                                    </label>
                                    <select name="member_id" id="member_id" style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px;" required>
                                        <option value="">Choose a member...</option>
                                        @foreach($members as $member)
                                            <option value="{{ $member->id }}">
                                                {{ $member->first_name }} {{ $member->last_name }} ({{ $member->national_id }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="month" style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">
                                        <i class="fas fa-calendar" style="color: #3b82f6; margin-right: 4px;"></i>
                                        Select Month
                                    </label>
                                    <select name="month" id="month" style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px;" required>
                                        <option value="">Choose month...</option>
                                        @for($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}">
                                                {{ \Carbon\Carbon::create()->month($m)->format('F') }} {{ $m <= 6 ? '2025' : '2024' }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                                <div>
                                    <label for="deposit_date" style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">
                                        <i class="fas fa-calendar-alt" style="color: #3b82f6; margin-right: 4px;"></i>
                                        Deposit Date
                                    </label>
                                    <input type="date" name="deposit_date" id="deposit_date" 
                                           style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px;"
                                           value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                                    <small style="color: #6b7280; font-size: 12px;">Date when the member made the deposit</small>
                                </div>
                                
                                <div>
                                    <label for="amount" style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">
                                        <i class="fas fa-money-bill-wave" style="color: #10b981; margin-right: 4px;"></i>
                                        Deposit Amount (UGX)
                                    </label>
                                    <div style="display: flex;">
                                        <div style="background: #10b981; color: white; padding: 12px; border-radius: 8px 0 0 8px; display: flex; align-items: center; border: 2px solid #10b981;">
                                            <i class="fas fa-currency-sign"></i> UGX
                                        </div>
                                        <input type="number" name="amount" id="amount" 
                                               style="flex: 1; padding: 12px; border: 2px solid #e5e7eb; border-left: none; border-radius: 0 8px 8px 0; font-size: 14px;"
                                               step="0.01" min="0" placeholder="0.00" required>
                                    </div>
                                    <small style="color: #6b7280; font-size: 12px;">Enter the amount the member is depositing</small>
                                </div>
                            </div>
                            
                            <div style="margin-bottom: 20px;">
                                <label for="notes" style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">
                                    <i class="fas fa-sticky-note" style="color: #3b82f6; margin-right: 4px;"></i>
                                    Notes
                                </label>
                                <textarea name="notes" id="notes" 
                                          style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; resize: vertical;"
                                          rows="3" placeholder="Optional notes..."></textarea>
                            </div>
                            
                            <!-- Fiscal Year Info -->
                            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                                <div style="display: flex; align-items: center;">
                                    <i class="fas fa-info-circle" style="font-size: 24px; color: #3b82f6; margin-right: 12px;"></i>
                                    <div>
                                        <h6 style="margin: 0 0 5px 0; font-size: 14px; color: #1f2937;">Current Fiscal Year</h6>
                                        <p style="margin: 0; font-size: 13px; color: #6b7280;">
                                            <strong>{{ $activeFiscalYear->name }}</strong><br>
                                            <small>{{ $activeFiscalYear->start_date->format('M d, Y') }} - {{ $activeFiscalYear->end_date->format('M d, Y') }}</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Deadline Information -->
                            <div style="background: #fef3c7; border: 1px solid #fcd34d; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                                <div style="display: flex; align-items: center;">
                                    <i class="fas fa-clock" style="font-size: 24px; color: #f59e0b; margin-right: 12px;"></i>
                                    <div>
                                        <h6 class="mb-1">Payment Deadline</h6>
                                        <p class="mb-0">
                                            <small><strong>Monthly savings deadline: 5th of next month</strong><br>
                                            Late payments will incur UGX 100 per day (max UGX 5,000)<br>
                                            Missed savings will incur UGX 2,000 fine</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('admin.group-savings.dashboard') }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-times me-2"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary-gradient btn-lg">
                                    <i class="fas fa-save me-2"></i> Create Deposit
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-warning border-0 bg-light-warning">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle fa-2x text-warning me-3"></i>
                                <div>
                                    <h6 class="mb-1">No Active Fiscal Year</h6>
                                    <p class="mb-0">Please create an active fiscal year first before recording deposits.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Guidelines Card -->
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-question-circle me-2"></i>
                        Deposit Guidelines
                    </h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="fas fa-list-ol me-1"></i>
                        Process Flow:
                    </h6>
                    <ol class="small text-muted mb-4">
                        <li class="mb-2">
                            <i class="fas fa-user-check text-success me-1"></i>
                            Select the member making the deposit
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-calendar-check text-info me-1"></i>
                            Choose the month for the deposit
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-money-check text-warning me-1"></i>
                            Enter the deposit amount
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-save text-primary me-1"></i>
                            Submit to create the deposit
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-hand-holding-usd text-success me-1"></i>
                            Distribute funds to savings, welfare, fines, etc.
                        </li>
                    </ol>
                    
                    <h6 class="fw-bold text-warning mb-3">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Distribution Rules:
                    </h6>
                    <ul class="small text-muted">
                        <li class="mb-2">
                            <i class="fas fa-balance-scale text-info me-1"></i>
                            Total distribution must equal deposit amount
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-user-shield text-success me-1"></i>
                            Savings are tracked per member per month
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-users text-primary me-1"></i>
                            Welfare funds are group-based
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-gavel text-warning me-1"></i>
                            Fines are member-based
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Quick Stats
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h3 class="text-primary">{{ $members->count() }}</h3>
                        <small class="text-muted">Total Members</small>
                    </div>
                    <div class="text-center">
                        <h3 class="text-success">12</h3>
                        <small class="text-muted">Months in Fiscal Year</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.month-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.month-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.month-icon {
    transition: transform 0.3s ease;
}

.month-card:hover .month-icon {
    transform: scale(1.1);
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

.bg-light-info {
    background-color: rgba(54, 209, 220, 0.1);
    border-left: 4px solid #36d1dc;
}

.bg-light-warning {
    background-color: rgba(255, 193, 7, 0.1);
    border-left: 4px solid #ffc107;
}

.form-select-lg, .input-group-lg .form-control {
    font-size: 1rem;
    padding: 0.75rem 1rem;
}

.form-label {
    color: #495057;
    margin-bottom: 0.5rem;
}

.card {
    border-radius: 15px;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
    border-bottom: none;
}
</style>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-format amount input
    const amountInput = document.getElementById('amount');
    amountInput.addEventListener('blur', function() {
        if (this.value && !isNaN(this.value)) {
            this.value = parseFloat(this.value).toFixed(2);
        }
    });
});
</script>
@endsection
