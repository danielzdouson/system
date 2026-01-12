@extends('layouts.admin')

@section('title', 'Pending Months')

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
                                    <i class="fas fa-clock me-3"></i>
                                    Pending Months - {{ $activeFiscalYear->name }}
                                </h1>
                                <p class="text-white fs-5 mb-0 opacity-90">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Members with pending savings for {{ $activeFiscalYear->name }}
                                </p>
                            </div>
                            <div class="text-end">
                                <div class="month-badge">
                                    <span class="badge bg-white text-warning fs-6 px-3 py-2">
                                        <i class="fas fa-hourglass-half me-2"></i>
                                        {{ $pendingSavings->count() }} Members
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
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
                                <a href="{{ route('admin.group-savings.dashboard') }}" class="action-btn primary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Back to Dashboard
                                </a>
                                <a href="{{ route('admin.group-savings.fines') }}" class="action-btn warning">
                                    <i class="fas fa-gavel me-2"></i>
                                    Fines Management
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Pending Members Table -->
            <div class="row">
                <div class="col-12">
                    <div class="data-card">
                        <div class="data-card-header">
                            <h5 class="data-title">
                                <i class="fas fa-users me-2"></i>
                                Members with Pending Savings
                            </h5>
                            <div class="data-subtitle">
                                Complete list of members who have pending savings to be processed
                            </div>
                        </div>
                        <div class="data-card-body">
                            @if($pendingSavings->count() > 0)
                                <div class="table-responsive">
                                    <table class="enhanced-table">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>
                                                    <i class="fas fa-user me-2"></i>
                                                    Member Name
                                                </th>
                                                <th>
                                                    <i class="fas fa-id-card me-2"></i>
                                                    National ID
                                                </th>
                                                <th>
                                                    <i class="fas fa-calendar me-2"></i>
                                                    Pending Months
                                                </th>
                                                <th>
                                                    <i class="fas fa-money-bill-wave me-2"></i>
                                                    Total Amount
                                                </th>
                                                <th>
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    Status
                                                </th>
                                                <th>
                                                    <i class="fas fa-cogs me-2"></i>
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pendingSavings as $memberId => $savings)
                                                <tr class="table-row-hover">
                                                    <td>
                                                        <div class="member-info">
                                                            <a href="{{ route('admin.members.show', $savings->first()->member->id) }}" class="member-link">
                                                                <strong>{{ $savings->first()->member->first_name }} {{ $savings->first()->member->last_name }}</strong>
                                                            </a>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="id-badge">{{ $savings->first()->member->national_id }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="pending-months">
                                                            @foreach($savings as $saving)
                                                                <span class="month-badge warning">
                                                                    {{ $saving->month }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="amount-badge warning">
                                                            UGX {{ number_format($savings->sum('amount'), 0) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="status-badge warning">
                                                            <i class="fas fa-clock me-1"></i>
                                                            Pending
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="action-buttons-inline">
                                                            <a href="{{ route('admin.group-savings.monthly', $savings->first()->month) }}" 
                                                               class="btn-action primary" title="View Monthly Details">
                                                                <i class="fas fa-eye"></i>
                                                                <span>View</span>
                                                            </a>
                                                            <button class="btn-action warning" title="Apply Fine" 
                                                                    onclick="applyFine({{ $savings->first()->member->id }}, {{ $savings->first()->month }})">
                                                                <i class="fas fa-gavel"></i>
                                                                <span>Fine</span>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="empty-state text-center py-5">
                                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                                    <h4 class="text-success">No Pending Savings</h4>
                                    <p class="text-muted">All members have completed their savings for this fiscal year.</p>
                                    <a href="{{ route('admin.group-savings.dashboard') }}" class="btn btn-primary">
                                        <i class="fas fa-arrow-left me-2"></i>
                                        Back to Dashboard
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Apply Fine Modal -->
<div class="modal fade" id="applyFineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-gavel me-2"></i>
                    Apply Fine
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.group-savings.apply-fines') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="member_id" id="fine_member_id">
                    <input type="hidden" name="month" id="fine_month">
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label fw-semibold">
                            <i class="fas fa-money-bill-wave text-primary me-1"></i>
                            Fine Amount (UGX)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white">
                                <i class="fas fa-currency-sign"></i>
                            </span>
                            <input type="number" name="amount" id="fine_amount" class="form-control" 
                                   step="0.01" min="0" value="10000" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reason" class="form-label fw-semibold">
                            <i class="fas fa-tag text-info me-1"></i>
                            Reason
                        </label>
                        <select name="reason" id="fine_reason" class="form-select" required>
                            <option value="missed_saving">Missed Saving</option>
                            <option value="late_payment">Late Payment</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">
                            <i class="fas fa-comment text-warning me-1"></i>
                            Description
                        </label>
                        <textarea name="description" id="fine_description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary-gradient">
                        <i class="fas fa-check me-2"></i> Apply Fine
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Enhanced Page Header */
.page-header {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    box-shadow: 0 10px 30px rgba(240, 147, 251, 0.3);
}

.month-badge .badge {
    border-radius: 50px;
    font-weight: 600;
}

/* Enhanced Action Cards */
.action-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 2rem;
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
}

.action-btn.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.action-btn.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    color: white;
}

/* Enhanced Data Card */
.data-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 2rem;
}

.data-card-header {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
}

.enhanced-table tbody tr {
    transition: all 0.3s ease;
}

.table-row-hover:hover {
    background-color: rgba(240, 147, 251, 0.1);
    transform: scale(1.01);
}

.enhanced-table td {
    padding: 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #e9ecef;
}

.member-info strong {
    color: #333;
    font-weight: 600;
}

.id-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.pending-months {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.month-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 15px;
    font-size: 0.7rem;
    font-weight: 600;
    color: white;
}

.month-badge.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.amount-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    color: white;
}

.amount-badge.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    color: white;
}

.status-badge.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.action-buttons-inline {
    display: flex;
    gap: 0.5rem;
}

.btn-action {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    color: white;
}

.btn-action.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.btn-action.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    color: white;
}

/* Empty State */
.empty-state {
    padding: 3rem;
}

/* Enhanced Modal */
.modal-content {
    border-radius: 20px;
    border: none;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
}

.modal-header {
    border-radius: 20px 20px 0 0;
    border: none;
}

.btn-primary-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 0.75rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary-gradient:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header {
        padding: 1.5rem;
    }
    
    .page-header h1 {
        font-size: 1.5rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .action-btn {
        width: 100%;
        justify-content: center;
    }
    
    .data-card-header {
        padding: 1.5rem;
    }
    
    .data-card-body {
        padding: 1rem;
    }
    
    .enhanced-table {
        font-size: 0.8rem;
    }
    
    .enhanced-table th,
    .enhanced-table td {
        padding: 0.5rem;
    }
    
    .action-buttons-inline {
        flex-direction: column;
        gap: 0.25rem;
    }
}
</style>

@section('scripts')
<script>
function applyFine(memberId, month) {
    document.getElementById('fine_member_id').value = memberId;
    document.getElementById('fine_month').value = month;
    new bootstrap.Modal(document.getElementById('applyFineModal')).show();
}

// Auto-fill member info when modal opens
document.getElementById('applyFineModal').addEventListener('show.bs.modal', function() {
    const memberId = document.getElementById('fine_member_id').value;
    const month = document.getElementById('fine_month').value;
    const memberName = document.querySelector(`tr:has([onclick*="${memberId}"]) strong`).textContent;
    
    const description = document.getElementById('fine_description');
    description.value = `Fine for missed saving in ${getMonthName(month)} - ${memberName}`;
});

function getMonthName(month) {
    const months = ['January', 'February', 'March', 'April', 'May', 'June', 
                   'July', 'August', 'September', 'October', 'November', 'December'];
    return months[month - 1] || 'Unknown';
}
</script>
@endsection
