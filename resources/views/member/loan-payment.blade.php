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
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> This is a placeholder page. Payment functionality will be implemented by the administrator. Please contact the SACCO office to make your loan payment.
                    </div>

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
                        <div class="bg-light p-3 rounded">
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
                        <p class="text-muted">No pending payments</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-university me-2"></i>Payment Methods</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Contact the SACCO office for payment options:</p>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-money-bill-wave text-success me-2"></i>Cash Payment</li>
                        <li class="mb-2"><i class="fas fa-university text-primary me-2"></i>Bank Transfer</li>
                        <li class="mb-2"><i class="fas fa-mobile-alt text-info me-2"></i>Mobile Money</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
