@extends('layouts.member')

@section('title', 'Guarantee Loan Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Guarantee Loan Application</h2>
                <a href="{{ route('member.documents.pending-guarantees') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Pending
                </a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <!-- Loan Details -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-file-alt"></i> Loan Details</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Borrower:</strong></td>
                                    <td>{{ $borrower->first_name }} {{ $borrower->last_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Loan Amount:</strong></td>
                                    <td class="text-primary">{{ number_format($uploadedForm->loan_amount, 2) }} UGX</td>
                                </tr>
                                <tr>
                                    <td><strong>Guarantors Required:</strong></td>
                                    <td><span class="badge bg-info">{{ $uploadedForm->guarantors_required }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Currently Guaranteed:</strong></td>
                                    <td>{{ $uploadedForm->getGuaranteedPercentage() }}%</td>
                                </tr>
                                <tr>
                                    <td><strong>Still Needed:</strong></td>
                                    <td><span class="badge bg-warning">{{ (100 - $uploadedForm->getGuaranteedPercentage()) }}%</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Current Guarantors -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-users"></i> Current Guarantors</h5>
                        </div>
                        <div class="card-body">
                            @if($uploadedForm->guarantors->isEmpty())
                                <p class="text-muted">No guarantors yet.</p>
                            @else
                                @foreach ($uploadedForm->guarantors as $guarantor)
                                    <div class="border-bottom pb-2 mb-2">
                                        <strong>{{ $guarantor->guarantor->first_name }} {{ $guarantor->guarantor->last_name }}</strong>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Guaranteed: {{ $guarantor->guarantee_percentage }}%</span>
                                            <span class="badge bg-{{ $guarantor->getStatusColor() }}">{{ $guarantor->getStatusLabel() }}</span>
                                        </div>
                                        <small class="text-muted">{{ $guarantor->guaranteed_at->format('M d, Y H:i') }}</small>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Borrower's Loan History -->
            <div class="col-12 mt-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-history"></i> Borrower's Loan History</h5>
                    </div>
                    <div class="card-body">
                        @if($repaymentHistory->isEmpty())
                            <p class="text-muted">No previous loan history found.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Loan #</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Balance</th>
                                            <th>Repayments</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($repaymentHistory as $loan)
                                            <tr>
                                                <td>{{ $loan['loan_number'] ?? 'N/A' }}</td>
                                                <td>{{ number_format($loan['amount'], 2) }} UGX</td>
                                                <td>
                                                    <span class="badge bg-{{ $loan['status'] === 'active' ? 'warning' : ($loan['status'] === 'completed' ? 'success' : 'secondary') }}">
                                                        {{ $loan['status'] }}
                                                    </span>
                                                </td>
                                                <td>{{ number_format($loan['balance'], 2) }} UGX</td>
                                                <td>{{ $loan['repayments_count'] }} ({{ number_format($loan['total_repaid'], 2) }} UGX)</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Guarantee Form -->
            <div class="col-12 mt-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-handshake"></i> Guarantee This Loan</h5>
                    </div>
                    <div class="card-body">
                        @if(!$uploadedForm->canMemberGuarantee())
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>You are not eligible to guarantee this loan:</strong>
                                <ul>
                                    @if(!$member->isEligibleToGuarantee())
                                        <li>You have overdue loans or too many active guarantees</li>
                                    @endif
                                    @if($member->getMaxGuaranteeAmount() <= 0)
                                        <li>You need to have savings to guarantee loans</li>
                                    @endif
                                </ul>
                            </div>
                        @else
                            <form action="{{ route('member.documents.confirm-guarantee', $uploadedForm) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="guarantee_percentage" class="form-label">Guarantee Percentage (%) *</label>
                                        <input type="number" class="form-control" id="guarantee_percentage" name="guarantee_percentage" 
                                               min="1" max="100" step="1" required>
                                        <div class="form-text">Enter percentage you want to guarantee (1-100%)</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="guaranteed_amount" class="form-label">Guaranteed Amount</label>
                                        <input type="text" class="form-control" id="guaranteed_amount" readonly 
                                               value="{{ number_format($uploadedForm->loan_amount * (request('guarantee_percentage', 1) / 100, 2) }} UGX">
                                        <div class="form-text">Amount you'll be guaranteeing</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label for="confirmation" class="form-label">Guarantee Statement *</label>
                                        <textarea class="form-control" id="confirmation" name="confirmation" rows="3" required
                                                  placeholder="I confirm I will guarantee this loan..."></textarea>
                                        <div class="form-text">Explain why you're guaranteeing this loan</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Your maximum guarantee amount:</strong> {{ number_format($member->getMaxGuaranteeAmount(), 2) }} UGX (50% of your savings)
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-check"></i> Confirm Guarantee
                                        </button>
                                        <a href="{{ route('member.documents.pending-guarantees') }}" class="btn btn-secondary btn-lg">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('guarantee_percentage').addEventListener('input', function() {
    const percentage = parseFloat(this.value) || 0;
    const loanAmount = {{ $uploadedForm->loan_amount }};
    const guaranteedAmount = (loanAmount * percentage) / 100;
    document.getElementById('guaranteed_amount').value = guaranteedAmount.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }) + ' UGX';
});
</script>
@endpush
