@extends('layouts.admin')

@section('title', 'Create Fine')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-plus me-2"></i>
                        Create New Fine
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.fines.store') }}">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Member *</label>
                                <select name="member_id" class="form-select @error('member_id') is-invalid @enderror" required>
                                    <option value="">Select Member</option>
                                    @foreach($members as $member)
                                        <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                                            {{ $member->first_name }} {{ $member->last_name }} ({{ $member->national_id ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('member_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fiscal Year *</label>
                                <select name="fiscal_year_id" class="form-select @error('fiscal_year_id') is-invalid @enderror" required>
                                    <option value="">Select Fiscal Year</option>
                                    @foreach($fiscalYears as $year)
                                        <option value="{{ $year->id }}" {{ old('fiscal_year_id') == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('fiscal_year_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Month *</label>
                                <select name="month" class="form-select @error('month') is-invalid @enderror" required>
                                    <option value="">Select Month</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ old('month') == $i ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($i)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                                @error('month')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Amount (UGX) *</label>
                                <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" 
                                       value="{{ old('amount', 10000) }}" required min="0" step="100">
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Reason *</label>
                                <select name="reason" class="form-select @error('reason') is-invalid @enderror" required>
                                    <option value="">Select Reason</option>
                                    <option value="missed_saving" {{ old('reason') == 'missed_saving' ? 'selected' : '' }}>
                                        Missed Saving
                                    </option>
                                    <option value="late_payment" {{ old('reason') == 'late_payment' ? 'selected' : '' }}>
                                        Late Payment
                                    </option>
                                    <option value="other" {{ old('reason') == 'other' ? 'selected' : '' }}>
                                        Other
                                    </option>
                                </select>
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Description</label>
                                <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" 
                                       value="{{ old('description') }}" placeholder="Optional description">
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.fines.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Back to Fines
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Create Fine
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
