@extends('layouts.admin')

@section('title', 'Fine Details - ' . $fine->description)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-2 text-white">
                            <i class="fas fa-gavel me-3"></i>
                            Fine Details
                        </h1>
                        <p class="text-white fs-5 mb-0 opacity-90">
                            <i class="fas fa-info-circle me-2"></i>
                            Complete information about this fine
                        </p>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('admin.fines.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-2"></i>
                            Back to Fines
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fine Details Card -->
    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>
                        Fine Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Fine ID:</label>
                                <p class="form-control-plaintext">#{{ $fine->id }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Member:</label>
                                <p class="form-control-plaintext">
                                    @if($fine->member)
                                        {{ $fine->member->first_name }} {{ $fine->member->last_name }}
                                        ({{ $fine->member->national_id ?? 'No National ID' }})
                                    @else
                                        <span class="text-danger">Member not found</span>
                                    @endif
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Amount:</label>
                                <p class="form-control-plaintext fs-5 text-primary fw-bold">
                                    UGX {{ number_format($fine->amount, 2) }}
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Reason:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-{{ $fine->reason == 'missed_saving' ? 'warning' : ($fine->reason == 'late_payment' ? 'danger' : 'info') }}">
                                        {{ ucfirst($fine->reason) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-{{ $fine->status == 'paid' ? 'success' : ($fine->status == 'waived' ? 'secondary' : 'warning') }} fs-6">
                                        {{ ucfirst($fine->status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Fiscal Year:</label>
                                <p class="form-control-plaintext">
                                    {{ $fine->fiscalYear ? $fine->fiscalYear->name : 'Not specified' }}
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Month:</label>
                                <p class="form-control-plaintext">{{ \Carbon\Carbon::create()->month($fine->month)->format('F') }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Created:</label>
                                <p class="form-control-plaintext">{{ $fine->created_at->format('M d, Y - H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Description:</label>
                                <p class="form-control-plaintext">{{ $fine->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Actions Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-tools me-2"></i>
                        Actions
                    </h5>
                </div>
                <div class="card-body">
                    @if($fine->status == 'pending')
                        <form action="{{ route('admin.fines.pay', $fine) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-money-bill-wave me-2"></i>
                                Mark as Paid
                            </button>
                        </form>
                        <form action="{{ route('admin.fines.waive', $fine) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Are you sure you want to waive this fine?')">
                                <i class="fas fa-hand-paper me-2"></i>
                                Waive Fine
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('admin.fines.edit', $fine) }}" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-edit me-2"></i>
                        Edit Fine
                    </a>
                    
                    @if($fine->status == 'pending')
                        <form action="{{ route('admin.fines.destroy', $fine) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to delete this fine?')">
                                <i class="fas fa-trash me-2"></i>
                                Delete Fine
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Created By Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>
                        Created By
                    </h5>
                </div>
                <div class="card-body">
                    @if($fine->creator)
                        <p class="mb-1"><strong>Name:</strong> {{ $fine->creator->name }}</p>
                        <p class="mb-0"><strong>Email:</strong> {{ $fine->creator->email }}</p>
                    @else
                        <p class="text-muted mb-0">System generated</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Section -->
    @if($fine->payments && $fine->payments->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-money-bill-wave me-2"></i>
                            Payment History
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Payment Date</th>
                                        <th>Received By</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($fine->payments as $payment)
                                        <tr>
                                            <td>#{{ $payment->id }}</td>
                                            <td class="fw-bold text-success">UGX {{ number_format($payment->amount, 2) }}</td>
                                            <td>{{ ucfirst($payment->payment_method) }}</td>
                                            <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                            <td>
                                                @if($payment->receiver)
                                                    {{ $payment->receiver->name }}
                                                @else
                                                    <span class="text-muted">Not specified</span>
                                                @endif
                                            </td>
                                            <td>{{ $payment->notes ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
