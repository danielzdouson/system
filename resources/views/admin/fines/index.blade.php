@extends('layouts.admin')

@section('title', 'Fines Management - Enhanced System')

@section('content')
<div class="container-fluid">
    <!-- Enhanced Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-2 text-white">
                            <i class="fas fa-gavel me-3"></i>
                            Enhanced Fines Management
                        </h1>
                        <p class="text-white fs-5 mb-0 opacity-90">
                            <i class="fas fa-chart-line me-2"></i>
                            Comprehensive fine tracking and management system
                        </p>
                    </div>
                    <div class="text-end">
                        <div class="fiscal-year-selector">
                            <form method="GET" class="d-inline">
                                <select name="fiscal_year" class="form-select form-select-sm" onchange="this.form.submit()">
                                    @foreach($allFiscalYears as $year)
                                        <option value="{{ $year->id }}" 
                                                {{ $activeFiscalYear && $activeFiscalYear->id == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($activeFiscalYear)
        <!-- Enhanced Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card primary-gradient">
                    <div class="stat-card-body">
                        <div class="stat-icon">
                            <i class="fas fa-gavel"></i>
                        </div>
                        <div class="stat-content">
                            <h4 class="stat-title">Total Fines</h4>
                            <h2 class="stat-number">{{ $statistics['total_fines'] }}</h2>
                            <small class="stat-subtitle">UGX {{ number_format($statistics['total_amount'], 0) }}</small>
                        </div>
                        <div class="stat-trend">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card danger-gradient">
                    <div class="stat-card-body">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <h4 class="stat-title">Pending</h4>
                            <h2 class="stat-number">{{ $statistics['pending_fines'] }}</h2>
                            <small class="stat-subtitle">UGX {{ number_format($statistics['pending_amount'], 0) }}</small>
                        </div>
                        <div class="stat-trend">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card success-gradient">
                    <div class="stat-card-body">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h4 class="stat-title">Paid</h4>
                            <h2 class="stat-number">{{ $statistics['paid_fines'] }}</h2>
                            <small class="stat-subtitle">UGX {{ number_format($statistics['paid_amount'], 0) }}</small>
                        </div>
                        <div class="stat-trend">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card info-gradient">
                    <div class="stat-card-body">
                        <div class="stat-icon">
                            <i class="fas fa-hand-paper"></i>
                        </div>
                        <div class="stat-content">
                            <h4 class="stat-title">Waived</h4>
                            <h2 class="stat-number">{{ $statistics['waived_fines'] }}</h2>
                            <small class="stat-subtitle">UGX {{ number_format($statistics['waived_amount'], 0) }}</small>
                        </div>
                        <div class="stat-trend">
                            <i class="fas fa-minus-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Action Buttons -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="action-card">
                    <div class="action-card-body">
                        <h5 class="action-title">
                            <i class="fas fa-rocket me-2"></i>
                            Quick Actions
                        </h5>
                        <div class="action-buttons">
                            <a href="{{ route('admin.fines.create') }}" class="action-btn primary">
                                <i class="fas fa-plus me-2"></i>
                                New Fine
                            </a>
                            <button type="button" class="action-btn info" data-bs-toggle="modal" data-bs-target="#autoApplyModal">
                                <i class="fas fa-magic me-2"></i>
                                Auto Apply
                            </button>
                            <a href="{{ route('admin.fines.export') }}?fiscal_year={{ $activeFiscalYear->id }}" class="action-btn success">
                                <i class="fas fa-download me-2"></i>
                                Export
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="filter-card">
                    <div class="filter-card-header">
                        <h5 class="filter-title">
                            <i class="fas fa-filter me-2"></i>
                            Filters
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearFilters()">
                            <i class="fas fa-times me-1"></i>
                            Clear
                        </button>
                    </div>
                    <div class="filter-card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="waived" {{ request('status') == 'waived' ? 'selected' : '' }}>Waived</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Reason</label>
                                <select name="reason" class="form-select">
                                    <option value="">All Reasons</option>
                                    <option value="missed_saving" {{ request('reason') == 'missed_saving' ? 'selected' : '' }}>Missed Saving</option>
                                    <option value="late_payment" {{ request('reason') == 'late_payment' ? 'selected' : '' }}>Late Payment</option>
                                    <option value="other" {{ request('reason') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Month</label>
                                <select name="month" class="form-select">
                                    <option value="">All Months</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($i)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control" placeholder="Member name or ID..." value="{{ request('search') }}">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>
                                    Apply Filters
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Fines Table -->
        <div class="row">
            <div class="col-12">
                <div class="data-card">
                    <div class="data-card-header">
                        <h5 class="data-title">
                            <i class="fas fa-list me-2"></i>
                            All Fines - {{ $activeFiscalYear->name }}
                        </h5>
                        <div class="data-subtitle">
                            {{ $fines->count() }} fines found
                        </div>
                    </div>
                    <div class="data-card-body">
                        <div class="table-responsive">
                            <table class="enhanced-table">
                                <thead class="table-dark">
                                    <tr>
                                        <th>
                                            <i class="fas fa-user me-2"></i>
                                            Member
                                        </th>
                                        <th>
                                            <i class="fas fa-calendar me-2"></i>
                                            Month
                                        </th>
                                        <th>
                                            <i class="fas fa-money-bill-wave me-2"></i>
                                            Amount
                                        </th>
                                        <th>
                                            <i class="fas fa-tag me-2"></i>
                                            Reason
                                        </th>
                                        <th>
                                            <i class="fas fa-comment me-2"></i>
                                            Description
                                        </th>
                                        <th>
                                            <i class="fas fa-info-circle me-2"></i>
                                            Status
                                        </th>
                                        <th>
                                            <i class="fas fa-calendar-plus me-2"></i>
                                            Created
                                        </th>
                                        <th>
                                            <i class="fas fa-cogs me-2"></i>
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($fines as $fine)
                                        <tr class="table-row-hover">
                                            <td>
                                                <div class="member-info">
                                                    @if($fine->member)
                                                        <a href="{{ route('admin.members.show', $fine->member->id) }}" class="member-link">
                                                            <strong>{{ $fine->member->first_name }} {{ $fine->member->last_name }}</strong>
                                                        </a>
                                                        <br>
                                                        <small class="text-muted">{{ $fine->member->national_id ?? 'N/A' }}</small>
                                                    @else
                                                        <span class="text-muted">
                                                            <strong>Unknown Member (ID: {{ $fine->member_id }})</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="month-info">
                                                    {{ \Carbon\Carbon::create()->month($fine->month)->format('F') }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="amount-badge warning">
                                                    UGX {{ number_format($fine->amount, 0) }}
                                                </span>
                                            </td>
                                            <td>
                                                @switch($fine->reason)
                                                    @case('missed_saving')
                                                        <span class="reason-badge warning">Missed Saving</span>
                                                        @break
                                                    @case('late_payment')
                                                        <span class="reason-badge info">Late Payment</span>
                                                        @break
                                                    @default
                                                        <span class="reason-badge secondary">Other</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <div class="description-text">
                                                    <small>{{ Str::limit($fine->description, 50) }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @switch($fine->status)
                                                    @case('pending')
                                                        <span class="status-badge warning">Pending</span>
                                                        @break
                                                    @case('paid')
                                                        <span class="status-badge success">Paid</span>
                                                        @break
                                                    @case('waived')
                                                        <span class="status-badge info">Waived</span>
                                                        @break
                                                    @default
                                                        <span class="status-badge secondary">Unknown</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <div class="date-info">
                                                    {{ $fine->created_at->format('M d, Y') }}
                                                    <br>
                                                    <small class="text-muted">{{ $fine->created_at->format('H:i') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="action-buttons-inline">
                                                    <a href="{{ route('admin.fines.edit', $fine) }}" class="btn-action primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    @if($fine->status === 'pending')
                                                        <button type="button" class="btn-action success" title="Mark as Paid" 
                                                                data-bs-toggle="modal" data-bs-target="#payFineModal"
                                                                onclick="setFineId({{ $fine->id }})">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" class="btn-action warning" title="Waive Fine"
                                                                data-bs-toggle="modal" data-bs-target="#waiveFineModal"
                                                                onclick="setFineId({{ $fine->id }})">
                                                            <i class="fas fa-hand-paper"></i>
                                                        </button>
                                                    @endif
                                                    
                                                    @if($fine->status !== 'paid')
                                                        <form method="POST" action="{{ route('admin.fines.destroy', $fine) }}" 
                                                              class="d-inline" onsubmit="return confirm('Delete this fine?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn-action danger" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">No fines found</h5>
                                                <p class="text-muted">Try adjusting your filters or create a new fine.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Showing {{ $fines->firstItem() }} to {{ $fines->lastItem() }} of {{ $fines->total() }} fines
                            </div>
                            {{ $fines->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No Fiscal Year Selected -->
        <div class="row">
            <div class="col-12">
                <div class="empty-state-card">
                    <div class="empty-state-content">
                        <i class="fas fa-calendar-alt fa-4x text-muted mb-3"></i>
                        <h3 class="text-muted">No Fiscal Year Selected</h3>
                        <p class="text-muted">Please select a fiscal year to view and manage fines.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Pay Fine Modal -->
<div class="modal fade" id="payFineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark Fine as Paid</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="payFineForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="fine_id" id="payFineId">
                    <div class="mb-3">
                        <label class="form-label">Payment Method (Optional)</label>
                        <input type="text" name="payment_method" class="form-control" placeholder="e.g., Cash, Bank Transfer">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Notes (Optional)</label>
                        <textarea name="payment_notes" class="form-control" rows="3" placeholder="Add any payment details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Mark as Paid</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Waive Fine Modal -->
<div class="modal fade" id="waiveFineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Waive Fine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="waiveFineForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="fine_id" id="waiveFineId">
                    <div class="mb-3">
                        <label class="form-label">Waiver Reason *</label>
                        <textarea name="waiver_reason" class="form-control" rows="3" required placeholder="Reason for waiving this fine..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Waive Fine</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Apply Modal -->
<div class="modal fade" id="bulkApplyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Apply Fines</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.fines.bulk-apply') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fiscal Year *</label>
                                <select name="fiscal_year_id" class="form-select" required>
                                    <option value="">Select Fiscal Year</option>
                                    @foreach($allFiscalYears as $year)
                                        <option value="{{ $year->id }}" {{ $activeFiscalYear && $activeFiscalYear->id == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Month *</label>
                                <select name="month" class="form-select" required>
                                    <option value="">Select Month</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}">{{ \Carbon\Carbon::create()->month($i)->format('F') }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fine Type *</label>
                                <select name="fine_type" class="form-select" required>
                                    <option value="missed_saving">Missed Saving</option>
                                    <option value="late_payment">Late Payment</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Amount (UGX) *</label>
                                <input type="number" name="amount" class="form-control" required min="0" step="100" value="10000">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Members *</label>
                        <div class="member-selection">
                            @if(isset($allMembers))
                                @foreach($allMembers as $member)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="members[]" value="{{ $member->id }}" id="member_{{ $member->id }}">
                                        <label class="form-check-label" for="member_{{ $member->id }}">
                                            {{ $member->first_name }} {{ $member->last_name }} ({{ $member->national_id ?? 'N/A' }})
                                        </label>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Apply Fines</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Auto Apply Modal -->
<div class="modal fade" id="autoApplyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Auto Apply Missed Saving Fines</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.fines.auto-apply') }}">
                @csrf
                <div class="modal-body">
                    <p class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This will automatically apply fines to all members who missed their savings for the selected month.
                    </p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fiscal Year *</label>
                                <select name="fiscal_year_id" class="form-select" required>
                                    <option value="">Select Fiscal Year</option>
                                    @foreach($allFiscalYears as $year)
                                        <option value="{{ $year->id }}" {{ $activeFiscalYear && $activeFiscalYear->id == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Month *</label>
                                <select name="month" class="form-select" required>
                                    <option value="">Select Month</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}">{{ \Carbon\Carbon::create()->month($i)->format('F') }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Auto Apply Fines</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function setFineId(fineId) {
    document.getElementById('payFineId').value = fineId;
    document.getElementById('waiveFineId').value = fineId;
}

function clearFilters() {
    window.location.href = '{{ route('admin.fines.index') }}';
}

// Set form actions for modals
document.getElementById('payFineForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const fineId = document.getElementById('payFineId').value;
    this.action = '{{ route('admin.fines.pay', ':id') }}'.replace(':id', fineId);
    this.submit();
});

document.getElementById('waiveFineForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const fineId = document.getElementById('waiveFineId').value;
    this.action = '{{ route('admin.fines.waive', ':id') }}'.replace(':id', fineId);
    this.submit();
});
</script>

<style>
/* Enhanced Styles */
.container-fluid {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.fiscal-year-selector .form-select {
    width: 200px;
}

/* Statistics Cards */
.stat-card {
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

.stat-card-body {
    padding: 2rem;
    position: relative;
    display: flex;
    align-items: center;
    color: white;
}

.stat-icon {
    font-size: 3rem;
    opacity: 0.3;
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
}

.stat-content {
    flex: 1;
    z-index: 1;
}

.stat-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    opacity: 0.9;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
}

.stat-subtitle {
    font-size: 0.8rem;
    opacity: 0.8;
    display: block;
    margin-top: 0.25rem;
}

.stat-trend {
    position: absolute;
    top: 1rem;
    right: 1rem;
    font-size: 1.2rem;
    opacity: 0.7;
}

.primary-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.danger-gradient { background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); }
.success-gradient { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.info-gradient { background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%); }

/* Action Cards */
.action-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.action-card-body {
    padding: 2rem;
}

.action-title {
    color: #333;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.action-btn {
    padding: 1rem 2rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    color: white;
    border: none;
    cursor: pointer;
}

.action-btn.primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.action-btn.warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.action-btn.info { background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%); }
.action-btn.secondary { background: linear-gradient(135deg, #6c757d 0%, #495057 100%); }
.action-btn.success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }

.action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    color: white;
}

/* Filter Card */
.filter-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.filter-card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: between;
    align-items: center;
}

.filter-title {
    color: #333;
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0;
}

.filter-card-body {
    padding: 1.5rem;
}

/* Data Card */
.data-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.data-card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
}

.data-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.data-subtitle {
    opacity: 0.9;
    margin: 0;
}

.data-card-body {
    padding: 2rem;
}

/* Enhanced Table */
.enhanced-table {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.enhanced-table thead th {
    border: none;
    padding: 1rem;
    font-weight: 600;
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
}

.enhanced-table tbody tr {
    transition: all 0.3s ease;
}

.table-row-hover:hover {
    background-color: rgba(102, 126, 234, 0.1);
    transform: scale(1.01);
}

.enhanced-table td {
    padding: 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #e9ecef;
}

.member-link {
    color: #667eea;
    text-decoration: none;
    font-weight: 600;
}

.member-link:hover {
    color: #764ba2;
    text-decoration: underline;
}

.amount-badge, .reason-badge, .status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    color: white;
}

.amount-badge.warning, .reason-badge.warning, .status-badge.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.reason-badge.info, .status-badge.info {
    background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
}

.reason-badge.secondary, .status-badge.secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
}

.status-badge.success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.action-buttons-inline {
    display: flex;
    gap: 0.25rem;
}

.btn-action {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 8px;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.3s ease;
    color: white;
}

.btn-action.primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.btn-action.success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.btn-action.warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.btn-action.danger { background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); }

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

/* Empty State */
.empty-state-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    text-align: center;
    padding: 4rem 2rem;
}

.member-selection {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1rem;
}

/* Responsive */
@media (max-width: 768px) {
    .container-fluid { padding: 1rem; }
    .action-buttons { flex-direction: column; }
    .action-btn { width: 100%; justify-content: center; }
    .enhanced-table { font-size: 0.8rem; }
    .action-buttons-inline { flex-direction: column; gap: 0.25rem; }
}
</style>
@endsection
