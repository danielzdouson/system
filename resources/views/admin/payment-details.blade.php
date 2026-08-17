@extends('layouts.admin')

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card gradient-bg text-white border-0">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-2">Payment Details</h2>
                            <p class="mb-0 opacity-75">Reference: {{ $payment->transaction_reference }}</p>
                        </div>
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-2"></i>Back to Payments
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Details -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Payment Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Transaction Reference</label>
                            <p class="fw-bold">{{ $payment->transaction_reference }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Pesapal Tracking ID</label>
                            <p class="fw-bold">{{ $payment->pesapal_tracking_id ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Amount</label>
                            <p class="fw-bold text-success h4">UGX {{ number_format($payment->amount, 0) }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Currency</label>
                            <p class="fw-bold">{{ $payment->currency }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Payment Type</label>
                            <p class="fw-bold">{{ $payment->payment_type_label }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Payment Method</label>
                            <p class="fw-bold">
                                @if($payment->payment_method === 'mobile_money' && $payment->mobile_network)
                                    <span class="badge bg-primary">{{ $payment->mobile_network }}</span>
                                @else
                                    {{ $payment->payment_method_label }}
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Status</label>
                            <p class="fw-bold">{!! $payment->status_badge !!}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Created At</label>
                            <p class="fw-bold">{{ $payment->created_at->format('M j, Y H:i:s') }}</p>
                        </div>
                        @if($payment->completed_at)
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Completed At</label>
                            <p class="fw-bold text-success">{{ $payment->completed_at->format('M j, Y H:i:s') }}</p>
                        </div>
                        @endif
                    </div>

                    @if($payment->notes)
                    <div class="mt-3">
                        <label class="text-muted">Notes</label>
                        <p>{{ $payment->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Member Information -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Member Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Name</label>
                            <p class="fw-bold">{{ $payment->member->first_name }} {{ $payment->member->last_name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Member ID</label>
                            <p class="fw-bold">#{{ str_pad($payment->member->id, 6, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Email</label>
                            <p class="fw-bold">{{ $payment->member->email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Phone</label>
                            <p class="fw-bold">{{ $payment->member->phone_number ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Payment Method Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Payment Method Details</h5>
                </div>
                <div class="card-body">
                    @if($payment->payment_method === 'mobile_money')
                        <div class="mb-3">
                            <label class="text-muted">Mobile Network</label>
                            <p class="fw-bold">{{ $payment->mobile_network }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted">Phone Number</label>
                            <p class="fw-bold">{{ $payment->phone_number }}</p>
                        </div>
                    @elseif($payment->payment_method === 'bank_transfer' && $payment->admin_bank_details)
                        @foreach($payment->admin_bank_details as $key => $value)
                        <div class="mb-3">
                            <label class="text-muted">{{ ucfirst(str_replace('_', ' ', $key)) }}</label>
                            <p class="fw-bold">{{ $value }}</p>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted">No additional details available</p>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Actions</h5>
                </div>
                <div class="card-body">
                    @if($payment->status !== 'completed')
                        <button onclick="refreshStatus()" class="btn btn-warning w-100 mb-2">
                            <i class="fas fa-sync me-2"></i>Refresh Status
                        </button>
                    @endif
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function refreshStatus() {
    if (!confirm('Refresh payment status from Pesapal?')) return;
    
    try {
        const response = await fetch('{{ route('admin.payments.refresh-status', $payment->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Status refreshed successfully!');
            location.reload();
        } else {
            alert('Failed to refresh status: ' + (data.error || 'Unknown error'));
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
    }
}
</script>
@endsection
