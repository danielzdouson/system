@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Create New Investment</h4>
                <a href="{{ route('admin.investments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Investments
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.investments.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Investment Name *</label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="investment_type" class="form-label">Investment Type *</label>
                                    <select name="investment_type" id="investment_type" class="form-select @error('investment_type') is-invalid @enderror" required>
                                        <option value="">Select Investment Type</option>
                                        @foreach($investmentTypes as $key => $value)
                                            <option value="{{ $key }}" {{ old('investment_type') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('investment_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="institution" class="form-label">Financial Institution *</label>
                                    <input type="text" name="institution" id="institution" class="form-control @error('institution') is-invalid @enderror" 
                                           value="{{ old('institution') }}" required>
                                    @error('institution')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="reference_number" class="form-label">Reference Number *</label>
                                    <input type="text" name="reference_number" id="reference_number" class="form-control @error('reference_number') is-invalid @enderror" 
                                           value="{{ old('reference_number') }}" required>
                                    @error('reference_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="principal_amount" class="form-label">Principal Amount *</label>
                                    <input type="number" name="principal_amount" id="principal_amount" class="form-control @error('principal_amount') is-invalid @enderror" 
                                           value="{{ old('principal_amount') }}" step="0.01" min="0" required>
                                    @error('principal_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="interest_rate" class="form-label">Interest Rate (%)</label>
                                    <input type="number" name="interest_rate" id="interest_rate" class="form-control @error('interest_rate') is-invalid @enderror" 
                                           value="{{ old('interest_rate') }}" step="0.01" min="0" max="100">
                                    @error('interest_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="account_number" class="form-label">Account Number</label>
                                    <input type="text" name="account_number" id="account_number" class="form-control @error('account_number') is-invalid @enderror" 
                                           value="{{ old('account_number') }}">
                                    @error('account_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="investment_date" class="form-label">Investment Date *</label>
                                    <input type="date" name="investment_date" id="investment_date" class="form-control @error('investment_date') is-invalid @enderror" 
                                           value="{{ old('investment_date') ?? now()->format('Y-m-d') }}" required>
                                    @error('investment_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="maturity_date" class="form-label">Maturity Date</label>
                                    <input type="date" name="maturity_date" id="maturity_date" class="form-control @error('maturity_date') is-invalid @enderror" 
                                           value="{{ old('maturity_date') }}">
                                    @error('maturity_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" 
                                      rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.investments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Investment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date for maturity date based on investment date
    const investmentDate = document.getElementById('investment_date');
    const maturityDate = document.getElementById('maturity_date');
    
    investmentDate.addEventListener('change', function() {
        if (this.value) {
            maturityDate.min = this.value;
        }
    });
});
</script>
@endsection
@endsection
