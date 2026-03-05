@extends('layouts.admin')

@section('title', 'Edit Fine')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Edit Fine
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.fines.update', $fine) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label">Member</label>
                                <input type="text" class="form-control" readonly 
                                       value="{{ $fine->member ? $fine->member->first_name . ' ' . $fine->member->last_name : 'Unknown Member' }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Fiscal Year</label>
                                <input type="text" class="form-control" readonly 
                                       value="{{ $fine->fiscalYear ? $fine->fiscalYear->name : 'Unknown Year' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Month</label>
                                <input type="text" class="form-control" readonly 
                                       value="{{ \Carbon\Carbon::create()->month($fine->month)->format('F') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Amount (UGX) *</label>
                                <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" 
                                       value="{{ old('amount', $fine->amount) }}" required min="0" step="100">
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reason *</label>
                                <select name="reason" class="form-select @error('reason') is-invalid @enderror" required>
                                    <option value="">Select Reason</option>
                                    <option value="missed_saving" {{ old('reason', $fine->reason) == 'missed_saving' ? 'selected' : '' }}>
                                        Missed Saving
                                    </option>
                                    <option value="late_payment" {{ old('reason', $fine->reason) == 'late_payment' ? 'selected' : '' }}>
                                        Late Payment
                                    </option>
                                    <option value="other" {{ old('reason', $fine->reason) == 'other' ? 'selected' : '' }}>
                                        Other
                                    </option>
                                </select>
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="3" placeholder="Optional description">{{ old('description', $fine->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Current Status</label>
                                <input type="text" class="form-control" readonly value="{{ ucfirst($fine->status) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Created Date</label>
                                <input type="text" class="form-control" readonly value="{{ $fine->created_at->format('M d, Y H:i') }}">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.fines.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Back to Fines
                            </a>
                            <div>
                                @if($fine->status !== 'paid')
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save me-2"></i>
                                        Update Fine
                                    </button>
                                @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        This fine has been paid and cannot be edited.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
