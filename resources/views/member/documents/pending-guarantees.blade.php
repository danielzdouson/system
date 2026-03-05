@extends('layouts.member')

@section('title', 'Pending Guarantees')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Pending Loan Guarantees</h2>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Help members get loans!</strong> When you guarantee a loan, you're vouching for the borrower's ability to repay.
            </div>

            @if($availableGuarantees->isEmpty())
                <div class="text-center py-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-5">
                            <i class="fas fa-handshake fa-4x text-success mb-4"></i>
                            <h4 class="text-muted mb-3">No Pending Guarantees Available</h4>
                            <p class="text-muted mb-4">
                                Great news! All loan applications currently have sufficient guarantors, 
                                or there are no new loan applications waiting for guarantees at the moment.
                            </p>
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="alert alert-light border">
                                        <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>What happens next?</h6>
                                        <ul class="text-start mb-0">
                                            <li>New loan applications will appear here as soon as members submit them</li>
                                            <li>You'll be notified when new guarantee opportunities become available</li>
                                            <li>Check back regularly to help fellow members secure their loans</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('member.documents.index') }}" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-file-alt me-2"></i>Browse Documents
                                </a>
                                <a href="{{ route('member.documents.guarantor-history') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-history me-2"></i>My Guarantee History
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    @foreach ($availableGuarantees as $form)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $form->member->first_name }} {{ $form->member->last_name }}</h5>
                                    <p class="text-muted small mb-2">Loan Application</p>
                                    
                                    <div class="mb-3">
                                        <strong>Loan Amount:</strong>
                                        <span class="text-primary">{{ number_format($form->loan_amount, 2) }} UGX</span>
                                    </div>

                                    <div class="mb-3">
                                        <strong>Guarantors Needed:</strong>
                                        <span class="badge bg-info">{{ $form->guarantors_required }}</span>
                                    </div>

                                    <div class="mb-3">
                                        <strong>Current Guarantees:</strong>
                                        <span class="badge bg-success">{{ $form->guarantors->count() }}</span>
                                    </div>

                                    <div class="mb-3">
                                        <strong>Progress:</strong>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar {{ $form->getGuaranteeProgressColor() }}" role="progressbar" 
                                                 style="width: {{ $form->getGuaranteedPercentage() }}%">
                                                {{ $form->getGuaranteedPercentage() }}%
                                            </div>
                                        </div>
                                        <small>{{ $form->getGuaranteedPercentage() }}% Complete</small>
                                    </div>

                                    <div class="mb-3">
                                        <strong>Still Needed:</strong>
                                        <span class="badge bg-warning">{{ (100 - $form->getGuaranteedPercentage()) }}%</span>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <a href="{{ route('member.documents.guarantee-details', $form) }}" class="btn btn-primary">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                        
                                        @if($form->canMemberGuarantee())
                                            <a href="{{ route('member.documents.guarantee-details', $form) }}" class="btn btn-success">
                                                <i class="fas fa-handshake"></i> Guarantee This Loan
                                            </a>
                                        @else
                                            <button class="btn btn-secondary" disabled>
                                                <i class="fas fa-ban"></i> Not Eligible
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
