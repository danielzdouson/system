@extends('layouts.admin')

@section('title', 'Cashflow Transaction Details')

@section('content')
<div class="content-wrapper">
    <!-- Enhanced Page Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="page-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h2 mb-2 text-white">
                                    <i class="fas fa-eye me-3"></i>
                                    Transaction Details
                                </h1>
                                <p class="text-white fs-5 mb-0 opacity-90">
                                    <i class="fas fa-info-circle me-2"></i>
                                    View detailed information about this cashflow transaction
                                </p>
                            </div>
                            <div class="text-end">
                                <div class="month-badge">
                                    <span class="badge bg-white text-info fs-6 px-3 py-2">
                                        <i class="fas fa-calendar me-2"></i>
                                        {{ $transaction->transaction_date->format('F Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <!-- Transaction Details Card -->
            <div class="row">
                <div class="col-md-8">
                    <div class="data-card">
                        <div class="data-card-header">
                            <h5 class="data-title">
                                <i class="fas fa-info-circle me-2"></i>
                                Transaction Information
                            </h5>
                            <div class="data-subtitle">
                                Complete details of the cashflow transaction
                            </div>
                        </div>
                        <div class="data-card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <h6>Transaction ID</h6>
                                        <p>{{ $transaction->id }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <h6>Transaction Date</h6>
                                        <p>{{ $transaction->transaction_date->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <h6>Type</h6>
                                        <p>{!! $transaction->type_badge !!}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <h6>Category</h6>
                                        <p>{!! $transaction->category_badge !!}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <h6>Amount</h6>
                                        <p class="{{ $transaction->transaction_type == 'INFLOW' ? 'text-success' : 'text-danger' }}">
                                            UGX {{ number_format($transaction->amount, 0) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <h6>Payment Method</h6>
                                        <p>{{ $transaction->payment_method }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <h6>Status</h6>
                                        <p>{!! $transaction->status_badge !!}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <h6>Reference Number</h6>
                                        <p>{{ $transaction->reference_number }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <h6>Reference Type</h6>
                                        <p>{{ $transaction->reference_type }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <h6>Created By</h6>
                                        <p>{{ $transaction->creator ? $transaction->creator->name : 'System' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <h6>Created At</h6>
                                        <p>{{ $transaction->created_at->format('M d, Y H:i:s') }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <h6>Approved By</h6>
                                        <p>{{ $transaction->approver ? $transaction->approver->name : 'Not Approved' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <h6>Approved At</h6>
                                        <p>{{ $transaction->approved_at ? $transaction->approved_at->format('M d, Y H:i:s') : 'Not Approved' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Related Information -->
                <div class="col-md-4">
                    <div class="data-card">
                        <div class="data-card-header">
                            <h5 class="data-title">
                                <i class="fas fa-link me-2"></i>
                                Related Information
                            </h5>
                            <div class="data-subtitle">
                                Source transaction and related records
                            </div>
                        </div>
                        <div class="data-card-body">
                            @if($transaction->member)
                                <div class="detail-item">
                                    <h6>Member</h6>
                                    <p>
                                        <a href="{{ route('admin.members.show', $transaction->member->id) }}" class="text-primary">
                                            {{ $transaction->member->first_name }} {{ $transaction->member->last_name }}
                                        </a>
                                    </p>
                                </div>
                            @endif
                            
                            @if($transaction->fiscal_year)
                                <div class="detail-item">
                                    <h6>Fiscal Year</h6>
                                    <p>{{ $transaction->fiscal_year->name }}</p>
                                </div>
                            @endif
                            
                            <div class="detail-item">
                                <h6>Description</h6>
                                <p>{{ $transaction->description ?: 'No description provided' }}</p>
                            </div>
                            
                            <div class="detail-item">
                                <h6>Notes</h6>
                                <p>{{ $transaction->notes ?: 'No notes available' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row">
                <div class="col-12">
                    <div class="action-card">
                        <div class="action-card-body">
                            <h5 class="action-title">
                                <i class="fas fa-cogs me-2"></i>
                                Actions
                            </h5>
                            <div class="action-buttons">
                                <a href="{{ route('admin.cashflow.index') }}" class="action-btn primary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Back to Transactions
                                </a>
                                @if($transaction->status == 'PENDING' && auth()->user()->can('approve-cashflow'))
                                    <a href="{{ route('admin.cashflow.approve', $transaction->id) }}" class="action-btn success">
                                        <i class="fas fa-check me-2"></i>
                                        Approve Transaction
                                    </a>
                                @endif
                                <a href="{{ route('admin.cashflow.index') }}" class="action-btn info">
                                    <i class="fas fa-list me-2"></i>
                                    View All Transactions
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Styles -->
<style>
/* Detail Items */
.detail-item {
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
    border-left: 4px solid #007bff;
}

.detail-item h6 {
    font-size: 0.9rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.detail-item p {
    margin: 0;
    font-size: 0.95rem;
    color: #212529;
}

.detail-item a {
    color: #007bff;
    text-decoration: none;
}

.detail-item a:hover {
    text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 768px) {
    .detail-item {
        padding: 0.75rem;
        margin-bottom: 1rem;
    }
    
    .detail-item h6 {
        font-size: 0.8rem;
    }
    
    .detail-item p {
        font-size: 0.85rem;
    }
}
</style>
@endsection
