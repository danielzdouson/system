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
                            <h2 class="mb-2">Payment Management</h2>
                            <p class="mb-0 opacity-75">Monitor and manage all online payments</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.payments.bank-details') }}" class="btn btn-light me-2">
                                <i class="fas fa-university me-2"></i>Bank Details
                            </a>
                            <a href="{{ route('admin.payments.export', $request->all()) }}" class="btn btn-light">
                                <i class="fas fa-download me-2"></i>Export
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Total</h6>
                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Pending</h6>
                    <h3 class="mb-0 text-warning">{{ $stats['pending'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Processing</h6>
                    <h3 class="mb-0 text-info">{{ $stats['processing'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Completed</h6>
                    <h3 class="mb-0 text-success">{{ $stats['completed'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Failed</h6>
                    <h3 class="mb-0 text-danger">{{ $stats['failed'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Total Amount</h6>
                    <h5 class="mb-0 text-success">UGX {{ number_format($stats['total_amount'], 0) }}</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.payments.index') }}">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">Payment Type</label>
                                <select name="payment_type" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="loan_payment" {{ request('payment_type') == 'loan_payment' ? 'selected' : '' }}>Loan Payment</option>
                                    <option value="fine_payment" {{ request('payment_type') == 'fine_payment' ? 'selected' : '' }}>Fine Payment</option>
                                    <option value="savings_deposit" {{ request('payment_type') == 'savings_deposit' ? 'selected' : '' }}>Savings Deposit</option>
                                    <option value="education" {{ request('payment_type') == 'education' ? 'selected' : '' }}>Education</option>
                                    <option value="other" {{ request('payment_type') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">From Date</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">To Date</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Reference, name...">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-2"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    @if($payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Member</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>
                                                <small class="text-muted">{{ $payment->transaction_reference }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $payment->member->first_name }} {{ $payment->member->last_name }}</strong>
                                                <br><small class="text-muted">#{{ $payment->member->id }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $payment->payment_type_label }}</span>
                                            </td>
                                            <td>
                                                <strong>UGX {{ number_format($payment->amount, 0) }}</strong>
                                            </td>
                                            <td>
                                                @if($payment->payment_method === 'mobile_money' && $payment->mobile_network)
                                                    <span class="badge bg-primary">{{ $payment->mobile_network }}</span>
                                                @else
                                                    {{ $payment->payment_method_label }}
                                                @endif
                                            </td>
                                            <td>{!! $payment->status_badge !!}</td>
                                            <td>{{ $payment->created_at->format('M j, Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($payment->status !== 'completed')
                                                    <button onclick="refreshStatus({{ $payment->id }})" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-sync"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <small class="text-muted">Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} payments</small>
                            {{ $payments->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="bg-light rounded-circle p-4 d-inline-block mb-3">
                                <i class="fas fa-inbox fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">No payments found</h5>
                            <p class="text-muted">Payment transactions will appear here once members make payments</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function refreshStatus(paymentId) {
    if (!confirm('Refresh payment status from Pesapal?')) return;
    
    const btn = event.target.closest('button');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    try {
        const response = await fetch(`{{ route('admin.payments.refresh-status', ':id') }}`.replace(':id', paymentId), {
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
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-sync"></i>';
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-sync"></i>';
    }
}
</script>
@endsection
