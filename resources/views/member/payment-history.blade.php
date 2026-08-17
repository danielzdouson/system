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
                            <h2 class="mb-2">Payment History</h2>
                            <p class="mb-0 opacity-75">View all your payment transactions</p>
                        </div>
                        <a href="{{ route('member.payments') }}" class="btn btn-light">
                            <i class="fas fa-plus me-2"></i>Make Payment
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('member.payment.history') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
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
                            <div class="col-md-3">
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

    <!-- Payment History -->
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
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Completed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>
                                                <small class="text-muted">{{ $payment->transaction_reference }}</small>
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
                                                @if($payment->completed_at)
                                                    {{ $payment->completed_at->format('M j, Y H:i') }}
                                                @else
                                                    <span class="text-muted">-</span>
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
                            <p class="text-muted">Your payment history will appear here once you make payments</p>
                            <a href="{{ route('member.payments') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Make Your First Payment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
