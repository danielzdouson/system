@extends('layouts.admin')

@section('title', 'Fiscal Years Management')

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
                                    <i class="fas fa-calendar-alt me-3"></i>
                                    Fiscal Years Management
                                </h1>
                                <p class="text-white fs-5 mb-0 opacity-90">
                                    <i class="fas fa-cog me-2"></i>
                                    Manage fiscal years and financial periods
                                </p>
                            </div>
                            <div class="text-end">
                                <a href="{{ route('admin.fiscal-years.create') }}" class="btn btn-success me-2">
                                    <i class="fas fa-plus-circle me-2"></i>
                                    Create New Fiscal Year
                                </a>
                                <a href="{{ route('admin.fiscal-years.carry-forward.history') }}" class="btn btn-info">
                                    <i class="fas fa-history me-2"></i>
                                    Carry Forward History
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid px-4" style="max-width: 1400px; margin: 0 auto;">
            <!-- Fiscal Years List -->
            <div class="row">
                <div class="col-12">
                    <div class="data-card">
                        <div class="data-card-header">
                            <h5 class="data-title">
                                <i class="fas fa-list me-2"></i>
                                All Fiscal Years
                            </h5>
                            <div class="data-subtitle">
                                Manage and configure fiscal year periods
                            </div>
                        </div>
                        <div class="data-card-body">
                            @if(count($fiscalYears) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Name</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Status</th>
                                                <th>Duration</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($fiscalYears as $fiscalYear)
                                                <tr class="{{ session('current_fiscal_year_id') == $fiscalYear->id ? 'table-primary' : '' }}">
                                                    <td>
                                                        <strong>{{ $fiscalYear->name }}</strong>
                                                        @if($fiscalYear->status == 'active')
                                                            <span class="badge bg-success ms-2">Active Status</span>
                                                        @endif
                                                        @if(session('current_fiscal_year_id') == $fiscalYear->id)
                                                            <span class="badge bg-primary ms-2">Viewing</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($fiscalYear->start_date)->format('M d, Y') }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($fiscalYear->end_date)->format('M d, Y') }}</td>
                                                    <td>
                                                        <span class="badge {{ $fiscalYear->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                            <i class="fas {{ $fiscalYear->status == 'active' ? 'fa-check-circle' : 'fa-pause-circle' }} me-1"></i>
                                                            {{ ucfirst($fiscalYear->status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            {{ \Carbon\Carbon::parse($fiscalYear->start_date)->diffInDays(\Carbon\Carbon::parse($fiscalYear->end_date)) }} days
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            {{-- Activate/View Button --}}
                                                            @if(session('current_fiscal_year_id') != $fiscalYear->id)
                                                                <form action="{{ route('admin.fiscal-years.activate', $fiscalYear) }}" method="POST" style="display: inline-block;">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-outline-primary" title="View this fiscal year">
                                                                        <i class="fas fa-eye"></i> View
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <button class="btn btn-sm btn-primary" title="Currently viewing" disabled>
                                                                    <i class="fas fa-check"></i> Current
                                                                </button>
                                                            @endif
                                                            
                                                            <a href="{{ route('admin.fiscal-years.edit', $fiscalYear) }}" 
                                                               class="btn btn-sm btn-outline-secondary" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            
                                                            @if($fiscalYear->status == 'inactive' && $activeFiscalYear)
                                                                <a href="{{ route('admin.fiscal-years.carry-forward', [$activeFiscalYear->id, $fiscalYear->id]) }}" 
                                                                   class="btn btn-sm btn-outline-warning" title="Carry Forward from Active">
                                                                    <i class="fas fa-exchange-alt"></i>
                                                                </a>
                                                            @endif
                                                            
                                                            @if($fiscalYear->status == 'inactive')
                                                                <form action="{{ route('admin.fiscal-years.destroy', $fiscalYear) }}" 
                                                                      method="POST" style="display: inline-block;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" 
                                                                            class="btn btn-sm btn-outline-danger" 
                                                                            title="Delete"
                                                                            onclick="return confirm('Are you sure you want to delete fiscal year "{{ $fiscalYear->name }}"? This action cannot be undone.')">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <button class="btn btn-sm btn-outline-secondary" 
                                                                        title="Cannot delete active fiscal year"
                                                                        disabled>
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-calendar-times fa-3x text-gray-400 mb-3"></i>
                                    <h4 class="text-gray-600">No Fiscal Years Found</h4>
                                    <p class="text-gray-500 mb-4">Get started by creating your first fiscal year.</p>
                                    <a href="{{ route('admin.fiscal-years.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus-circle me-2"></i>
                                        Create Fiscal Year
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            @if(count($fiscalYears) > 0)
                <div class="row mt-4">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="summary-calc-card">
                            <h6 class="text-muted mb-1">Total Fiscal Years</h6>
                            <h4 class="text-primary">{{ count($fiscalYears) }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="summary-calc-card">
                            <h6 class="text-muted mb-1">Active Fiscal Year</h6>
                            <h4 class="text-success">{{ $fiscalYears->where('status', 'active')->count() }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="summary-calc-card">
                            <h6 class="text-muted mb-1">Inactive Fiscal Years</h6>
                            <h4 class="text-secondary">{{ $fiscalYears->where('status', 'inactive')->count() }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="summary-calc-card">
                            <h6 class="text-muted mb-1">Current Period</h6>
                            <h4 class="text-info">
                                @if($activeFiscalYear)
                                    {{ $activeFiscalYear->name }}
                                @else
                                    None
                                @endif
                            </h4>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Enhanced Styles -->
<style>
/* Data Cards */
.content{
    width: 800px;
}
.data-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    padding: 0;
    margin-bottom: 2rem;
    overflow: hidden;
    width: 800px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.data-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.data-card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    text-align: center;
}

.data-title {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.data-subtitle {
    opacity: 0.9;
    font-size: 0.9rem;
}

.data-card-body {
    padding: 2rem;
}

/* Table Styling */
.table {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    border: 1px solid #e3e6f6;
}

.table thead th {
    background: linear-gradient(135deg, #f8f9ff 0%, #e8ecff 100%);
    color: #4a5568;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 1rem 0.75rem;
    border-bottom: 2px solid #667eea;
}

.table tbody td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f3f9;
    font-size: 0.9rem;
}

.table tbody tr {
    transition: all 0.2s ease;
    background: white;
}

.table tbody tr:hover {
    background: #f8f9ff;
    transform: translateX(3px);
}

/* Summary Cards */
.summary-calc-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid #e3e6f6;
    transition: all 0.3s ease;
    text-align: center;
}

.summary-calc-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(102, 126, 234, 0.15);
    border-color: #667eea;
}

.summary-calc-card h6 {
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.75rem;
    opacity: 0.8;
}

.summary-calc-card h4 {
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
}

/* Buttons */
.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border: none;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
}

/* Responsive Design */
@media (max-width: 768px) {
    .data-card-body {
        padding: 1rem;
    }
    
    .table {
        font-size: 0.8rem;
    }
    
    .btn-group {
        flex-direction: column;
    }
}
</style>
@endsection
