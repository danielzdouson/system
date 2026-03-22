@extends('layouts.admin')

@section('title', 'Uploaded Forms')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Uploaded Loan Forms</h2>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Document</th>
                                    <th>Loan Amount</th>
                                    <th>Guarantors</th>
                                    <th>Guaranteed %</th>
                                    <th>Status</th>
                                    <th>Uploaded</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($uploadedForms as $form)
                                    <tr>
                                        <td>
                                            <a href="#" class="text-decoration-none">
                                                {{ $form->member->first_name }} {{ $form->member->last_name }}
                                            </a>
                                        </td>
                                        <td>{{ $form->document->title }}</td>
                                        <td>{{ number_format($form->loan_amount, 2) }} UGX</td>
                                        <td>
                                            <span class="badge bg-info">{{ $form->guarantors->count() }}/{{ $form->guarantors_required }}</span>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar {{ $form->getGuaranteeProgressColor() }}" role="progressbar" 
                                                     style="width: {{ $form->getGuaranteedPercentage() }}%">
                                                    {{ $form->getGuaranteedPercentage() }}%
                                                </div>
                                            </div>
                                            <small>{{ $form->getGuaranteedPercentage() }}% Complete</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $form->getStatusColor() }}">
                                                {{ $form->getStatusLabel() }}
                                            </span>
                                        </td>
                                        <td>{{ $form->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.documents.review-form', $form) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Review
                                                </a>
                                                <a href="{{ route('admin.documents.view-guarantors', $form) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-users"></i> Guarantors
                                                </a>
                                                <a href="{{ route('admin.documents.download-uploaded-form', $form) }}" class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No uploaded forms found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($uploadedForms->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $uploadedForms->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
