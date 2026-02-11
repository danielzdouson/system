@extends('layouts.admin')

@section('title', 'Investment Portfolio Management')

@php
use App\Models\Investment;
@endphp

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Investment Portfolio Management</h4>
                <a href="{{ route('admin.investments.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Investment
                </a>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Invested</h5>
                            <h3>{{ number_format($stats['total_invested'], 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Current Value</h5>
                            <h3>{{ number_format($stats['total_current_value'], 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Returns</h5>
                            <h3>{{ number_format($stats['total_returns'], 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">ROI</h5>
                            <h3>{{ number_format($stats['total_roi'], 2) }}%</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.investments.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">All Status</option>
                                    @foreach(Investment::getStatuses() as $key => $value)
                                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="investment_type" class="form-label">Investment Type</label>
                                <select name="investment_type" id="investment_type" class="form-select">
                                    <option value="">All Types</option>
                                    @foreach(Investment::getInvestmentTypes() as $key => $value)
                                        <option value="{{ $key }}" {{ request('investment_type') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="institution" class="form-label">Institution</label>
                                <input type="text" name="institution" id="institution" class="form-control" 
                                       value="{{ request('institution') }}" placeholder="Search institution...">
                            </div>
                            <div class="col-md-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" name="search" id="search" class="form-control" 
                                       value="{{ request('search') }}" placeholder="Search investments...">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                <a href="{{ route('admin.investments.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Investments Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Institution</th>
                                    <th>Principal</th>
                                    <th>Current Value</th>
                                    <th>Returns</th>
                                    <th>ROI</th>
                                    <th>Status</th>
                                    <th>Maturity</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($investments as $investment)
                                    <tr>
                                        <td>
                                            <strong>{{ $investment->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $investment->reference_number }}</small>
                                        </td>
                                        <td>{!! $investment->type_badge !!}</td>
                                        <td>{{ $investment->institution }}</td>
                                        <td>{{ $investment->formatted_principal_amount }}</td>
                                        <td>{{ $investment->formatted_current_value }}</td>
                                        <td class="text-success">{{ $investment->formatted_total_returns }}</td>
                                        <td>
                                            <span class="badge {{ $investment->roi >= 0 ? 'bg-success' : 'bg-danger' }}">
                                                {{ number_format($investment->roi, 2) }}%
                                            </span>
                                        </td>
                                        <td>{!! $investment->status_badge !!}</td>
                                        <td>
                                            @if($investment->maturity_date)
                                                {{ $investment->maturity_date->format('M d, Y') }}
                                                @if($investment->days_to_maturity <= 30 && $investment->status == 'ACTIVE')
                                                    <br><small class="text-warning">{{ $investment->days_to_maturity }} days</small>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.investments.show', $investment) }}" 
                                                   class="btn btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.investments.edit', $investment) }}" 
                                                   class="btn btn-outline-secondary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($investment->status == 'ACTIVE' && $investment->is_matured)
                                                    <form method="POST" action="{{ route('admin.investments.mark-matured', $investment) }}" 
                                                          style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-warning" 
                                                                title="Mark as Matured" onclick="return confirm('Mark this investment as matured?')">
                                                            <i class="fas fa-calendar-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No investments found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Showing {{ $investments->firstItem() }} to {{ $investments->lastItem() }} 
                            of {{ $investments->total() }} investments
                        </div>
                        {{ $investments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
