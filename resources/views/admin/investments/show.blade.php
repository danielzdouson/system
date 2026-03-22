@extends('layouts.admin')

@section('title', 'Investment Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Investment Details</h4>
                <div>
                    <a href="{{ route('admin.investments.edit', $investment) }}" class="btn btn-secondary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('admin.investments.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Back to Investments
                    </a>
                </div>
            </div>

            <!-- Investment Overview -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Investment Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $investment->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td>{!! $investment->type_badge !!}</td>
                                </tr>
                                <tr>
                                    <td><strong>Institution:</strong></td>
                                    <td>{{ $investment->institution }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Reference Number:</strong></td>
                                    <td>{{ $investment->reference_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Account Number:</strong></td>
                                    <td>{{ $investment->account_number ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>{!! $investment->status_badge !!}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Financial Summary</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Principal Amount:</strong></td>
                                    <td>{{ $investment->formatted_principal_amount }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Current Value:</strong></td>
                                    <td>{{ $investment->formatted_current_value }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Returns:</strong></td>
                                    <td class="text-success">{{ $investment->formatted_total_returns }}</td>
                                </tr>
                                <tr>
                                    <td><strong>ROI:</strong></td>
                                    <td>
                                        <span class="badge {{ $investment->roi >= 0 ? 'bg-success' : 'bg-danger' }}">
                                            {{ number_format($investment->roi, 2) }}%
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Interest Rate:</strong></td>
                                    <td>{{ $investment->interest_rate ? number_format($investment->interest_rate, 2) . '%' : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Annualized ROI:</strong></td>
                                    <td>{{ number_format($investment->annualized_roi, 2) }}%</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Investment Timeline -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Timeline</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Investment Date:</strong></td>
                                    <td>{{ $investment->investment_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Maturity Date:</strong></td>
                                    <td>
                                        {{ $investment->maturity_date ? $investment->maturity_date->format('M d, Y') : '-' }}
                                        @if($investment->days_to_maturity > 0 && $investment->status == 'ACTIVE')
                                            <br><small class="text-info">{{ $investment->days_to_maturity }} days remaining</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Expected Returns:</strong></td>
                                    <td>{{ number_format($investment->expected_returns, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Actions</h5>
                        </div>
                        <div class="card-body">
                            @if($investment->status == 'ACTIVE')
                                @if($investment->is_matured)
                                    <form method="POST" action="{{ route('admin.investments.mark-matured', $investment) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-warning" onclick="return confirm('Mark this investment as matured?')">
                                            <i class="fas fa-calendar-check"></i> Mark as Matured
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.investments.close', $investment) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Close this investment?')">
                                        <i class="fas fa-times-circle"></i> Close Investment
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Transaction Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Add Transaction</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.investments.add-transaction', $investment) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <label for="transaction_type" class="form-label">Transaction Type *</label>
                                <select name="transaction_type" id="transaction_type" class="form-select @error('transaction_type') is-invalid @enderror" required>
                                    <option value="">Select Type</option>
                                    @foreach($transactionTypes as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                @error('transaction_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label for="amount" class="form-label">Amount *</label>
                                <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" 
                                       step="0.01" min="0" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label for="transaction_date" class="form-label">Date *</label>
                                <input type="date" name="transaction_date" id="transaction_date" class="form-control @error('transaction_date') is-invalid @enderror" 
                                       value="{{ old('transaction_date') ?? now()->format('Y-m-d') }}" required>
                                @error('transaction_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label for="reference_number" class="form-label">Reference</label>
                                <input type="text" name="reference_number" id="reference_number" class="form-control @error('reference_number') is-invalid @enderror" 
                                       value="{{ old('reference_number') }}">
                                @error('reference_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="description" class="form-label">Description</label>
                                <input type="text" name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                       value="{{ old('description') }}">
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Transaction
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Transaction History -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Transaction History</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Running Balance</th>
                                    <th>Accumulated Returns</th>
                                    <th>Reference</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($investment->transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->transaction_date->format('M d, Y') }}</td>
                                        <td>{!! $transaction->transaction_type_badge !!}</td>
                                        <td>{{ $transaction->description ?: '-' }}</td>
                                        <td class="{{ $transaction->isInflow() ? 'text-success' : 'text-danger' }}">
                                            {{ $transaction->isInflow() ? '+' : '-' }}{{ $transaction->formatted_amount }}
                                        </td>
                                        <td>{{ $transaction->formatted_running_balance }}</td>
                                        <td class="text-success">{{ $transaction->formatted_accumulated_returns }}</td>
                                        <td>{{ $transaction->reference_number ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No transactions found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($investment->notes)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Notes</h5>
                    </div>
                    <div class="card-body">
                        <p>{{ $investment->notes }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
