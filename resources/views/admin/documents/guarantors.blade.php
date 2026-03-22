@extends('layouts.admin')

@section('title', 'Loan Guarantors')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Loan Application Guarantors</h2>
                <div>
                    <a href="{{ route('admin.documents.uploaded-forms') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Uploaded Forms
                    </a>
                    <a href="{{ route('admin.documents.review-form', $uploadedForm) }}" class="btn btn-primary ms-2">
                        <i class="fas fa-eye"></i> Review Application
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle me-2"></i>Application Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Applicant:</strong><br>
                            {{ $uploadedForm->member->first_name }} {{ $uploadedForm->member->last_name }}
                        </div>
                        <div class="col-md-3">
                            <strong>Loan Amount:</strong><br>
                            UGX {{ number_format($uploadedForm->loan_amount, 2) }}
                        </div>
                        <div class="col-md-3">
                            <strong>Guarantors:</strong><br>
                            {{ $uploadedForm->guarantors->count() }} / {{ $uploadedForm->guarantors_required }}
                        </div>
                        <div class="col-md-3">
                            <strong>Status:</strong><br>
                            <span class="badge bg-{{ $uploadedForm->getStatusColor() }}">
                                {{ $uploadedForm->getStatusLabel() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5><i class="fas fa-users me-2"></i>Guarantor Details</h5>
                </div>
                <div class="card-body">
                    @if($guarantors->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Guarantor Name</th>
                                        <th>Member Number</th>
                                        <th>Guarantee %</th>
                                        <th>Guaranteed Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($guarantors as $guarantor)
                                        <tr>
                                            <td>
                                                {{ $guarantor->guarantor->first_name }} {{ $guarantor->guarantor->last_name }}
                                            </td>
                                            <td>{{ $guarantor->guarantor->membership_number ?? 'N/A' }}</td>
                                            <td>{{ $guarantor->guarantee_percentage }}%</td>
                                            <td>UGX {{ number_format($guarantor->guaranteed_amount, 2) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $guarantor->getStatusColor() }}">
                                                    {{ $guarantor->guarantee_status }}
                                                </span>
                                            </td>
                                            <td>{{ $guarantor->created_at->format('M d, Y') }}</td>
                                            <td>
                                                @if($guarantor->guarantee_confirmation)
                                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                                            data-bs-toggle="modal" data-bs-target="#confirmationModal{{ $guarantor->id }}">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Total Guaranteed:</strong> 
                                    <span class="badge bg-success">
                                        {{ $uploadedForm->getGuaranteedPercentage() }}% 
                                        (UGX {{ number_format($uploadedForm->guarantors->sum('guaranteed_amount'), 2) }})
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Remaining Needed:</strong> 
                                    <span class="badge bg-warning">
                                        {{ (100 - $uploadedForm->getGuaranteedPercentage()) }}% 
                                        (UGX {{ number_format($uploadedForm->loan_amount * (100 - $uploadedForm->getGuaranteedPercentage()) / 100, 2) }})
                                    </span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Guarantors Yet</h5>
                            <p class="text-muted">This loan application hasn't received any guarantors yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modals -->
@foreach($guarantors as $guarantor)
    @if($guarantor->guarantee_confirmation)
    <div class="modal fade" id="confirmationModal{{ $guarantor->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Guarantee Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <strong>Guarantor:</strong><br>
                        {{ $guarantor->guarantor->first_name }} {{ $guarantor->guarantor->last_name }}
                    </div>
                    <div class="mb-3">
                        <strong>Guarantee Amount:</strong><br>
                        UGX {{ number_format($guarantor->guaranteed_amount, 2) }} ({{ $guarantor->guarantee_percentage }}%)
                    </div>
                    <div class="mb-3">
                        <strong>Confirmation Statement:</strong><br>
                        <div class="p-3 bg-light rounded">
                            {{ $guarantor->guarantee_confirmation }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <strong>Date Confirmed:</strong><br>
                        {{ $guarantor->guaranteed_at->format('M d, Y H:i') }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach
@endsection
