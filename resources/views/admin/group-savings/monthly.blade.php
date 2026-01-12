@extends('layouts.admin')

@section('title', 'Monthly View - ' . $monthName)

@section('content')
    <div class="container-fluid">
        <!-- Enhanced Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h2 mb-2 text-white">
                                <i class="fas fa-calendar-alt me-3"></i>
                                Monthly View - {{ $monthName }} {{ $activeFiscalYear->name }}
                            </h1>
                            <p class="text-white fs-5 mb-0 opacity-90">
                                <i class="fas fa-chart-line me-2"></i>
                                Detailed breakdown of deposits and distributions for {{ $monthName }}
                            </p>
                        </div>
                        <div class="text-end">
                            <div class="month-badge">
                                <span class="badge bg-white text-info fs-6 px-3 py-2">
                                    <i class="fas fa-calendar me-2"></i>
                                    {{ $monthName }} {{ $month <= 6 ? '2025' : '2024' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Monthly Summary Cards -->
        <div class="stats-grid">
            <div class="stat-card primary-gradient">
                <div class="stat-card-body">
                    <div class="stat-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="stat-content">
                        <h4 class="stat-title">Total Deposits</h4>
                        <h2 class="stat-number">UGX {{ number_format(collect($monthlyData)->sum('deposit.amount'), 0) }}</h2>
                    </div>
                    <div class="stat-trend">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card success-gradient">
                <div class="stat-card-body">
                    <div class="stat-icon">
                        <i class="fas fa-piggy-bank"></i>
                    </div>
                    <div class="stat-content">
                        <h4 class="stat-title">Total Savings</h4>
                        <h2 class="stat-number">UGX {{ number_format(collect($monthlyData)->sum('savings_amount'), 0) }}</h2>
                    </div>
                    <div class="stat-trend">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card info-gradient">
                <div class="stat-card-body">
                    <div class="stat-icon">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <div class="stat-content">
                        <h4 class="stat-title">Total Welfare</h4>
                        <h2 class="stat-number">UGX {{ number_format(collect($monthlyData)->sum('welfare_amount'), 0) }}</h2>
                    </div>
                    <div class="stat-trend">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card warning-gradient">
                <div class="stat-card-body">
                    <div class="stat-icon">
                        <i class="fas fa-gavel"></i>
                    </div>
                    <div class="stat-content">
                        <h4 class="stat-title">Total Fines</h4>
                        <h2 class="stat-number">UGX {{ number_format(collect($monthlyData)->sum('fines_amount'), 0) }}</h2>
                    </div>
                    <div class="stat-trend">
                        <i class="fas fa-exclamation-triangle"></i>
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
                            <a href="{{ route('admin.group-savings.dashboard') }}" class="action-btn primary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Back to Dashboard
                            </a>
                            <a href="{{ route('admin.group-savings.create-deposit') }}" class="action-btn success">
                                <i class="fas fa-plus-circle me-2"></i>
                                New Deposit
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

        <!-- Enhanced Member Details Table -->
        <div class="row">
            <div class="col-12">
                <div class="data-card">
                    <div class="data-card-header">
                        <h5 class="data-title">
                            <i class="fas fa-users me-2"></i>
                            Member Details - {{ $monthName }}
                        </h5>
                        <div class="data-subtitle">
                            Complete breakdown of all member activities for {{ $monthName }}
                        </div>
                    </div>
                    <div class="data-card-body">
                        <div class="table-responsive">
                            <table class="enhanced-table">
                                <thead class="table-dark">
                                    <tr>
                                        <th><i class="fas fa-user me-2"></i> Member Name</th>
                                        <th><i class="fas fa-wallet me-2"></i> Deposit</th>
                                        <th><i class="fas fa-piggy-bank me-2"></i> Savings</th>
                                        <th><i class="fas fa-hands-helping me-2"></i> Welfare</th>
                                        <th><i class="fas fa-gavel me-2"></i> Fines</th>
                                        <th><i class="fas fa-coins me-2"></i> Other</th>
                                        <th><i class="fas fa-balance-scale me-2"></i> Balance</th>
                                        <th><i class="fas fa-cog me-2"></i> Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($monthlyData as $data)
                                        <tr class="table-row-hover">
                                            <td>
                                                <div class="member-info">
                                                    <strong>{{ $data['member']->first_name }} {{ $data['member']->last_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">ID: {{ $data['member']->membership_number ?? 'N/A' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($data['deposit'])
                                                    <span class="amount-badge success">
                                                        UGX {{ number_format($data['deposit']->amount, 0) }}
                                                    </span>
                                                @else
                                                    <span class="no-data-badge">No Deposit</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($data['savings_amount'] > 0)
                                                    <span class="amount-badge info">
                                                        UGX {{ number_format($data['savings_amount'], 0) }}
                                                    </span>
                                                @else
                                                    <span class="no-data-badge">UGX 0</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($data['welfare_amount'] > 0)
                                                    <span class="amount-badge warning">
                                                        UGX {{ number_format($data['welfare_amount'], 0) }}
                                                    </span>
                                                @else
                                                    <span class="no-data-badge">UGX 0</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($data['fines_amount'] > 0)
                                                    <span class="amount-badge warning">
                                                        UGX {{ number_format($data['fines_amount'], 0) }}
                                                    </span>
                                                @else
                                                    <span class="no-data-badge">UGX 0</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($data['other_amount'] > 0)
                                                    <span class="amount-badge secondary">
                                                        UGX {{ number_format($data['other_amount'], 0) }}
                                                    </span>
                                                @else
                                                    <span class="no-data-badge">UGX 0</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="amount-badge success">
                                                    UGX {{ number_format($data['current_balance'], 0) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-buttons-inline">
                                                    @if($data['deposit'])
                                                        <a href="{{ route('admin.group-savings.distribute', $data['deposit']->id) }}" class="btn-action primary">
                                                            <i class="fas fa-coins"></i>
                                                            Distribute
                                                        </a>
                                                    @endif
                                                    <button class="btn-action warning" onclick="applyFine({{ $data['member']->id }}, {{ request('month') }})">
                                                        <i class="fas fa-gavel"></i>
                                                        Fine
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
        background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        box-shadow: 0 10px 30px rgba(54, 209, 220, 0.3);
    }

    .month-badge .badge {
        border-radius: 50px;
        font-weight: 600;
    }

    /* Enhanced Statistics Cards */
    .stat-card {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
    }

    .stat-card-body {
        padding: 1.5rem;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
        flex: 1;
        text-align: center;
    }

    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.3;
        position: absolute;
        top: 1rem;
        right: 1rem;
    }

    .stat-content {
        z-index: 1;
        width: 100%;
    }

    .stat-title {
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .stat-number {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0;
        line-height: 1.2;
    }

    .stat-trend {
        position: absolute;
        top: 1rem;
        right: 1rem;
        font-size: 1rem;
        opacity: 0.7;
    }

    /* Grid Layout for Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stats-grid .stat-card {
        min-height: 180px;
    }

    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        
        .stat-card-body {
            padding: 1rem;
        }
        
        .stat-icon {
            font-size: 2rem;
        }
        
        .stat-title {
            font-size: 0.8rem;
        }
        
        .stat-number {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .stat-card-body {
            padding: 1.2rem;
        }
        
        .stat-icon {
            font-size: 2.2rem;
        }
        
        .stat-title {
            font-size: 0.85rem;
        }
        
        .stat-number {
            font-size: 1.6rem;
        }
    }

    .primary-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .success-gradient {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }

    .info-gradient {
        background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
    }

    .warning-gradient {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    /* Enhanced Action Cards */
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
    }

    .action-btn.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .action-btn.success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
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

    .amount-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        color: white;
    }

    .amount-badge.success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }

    .amount-badge.info {
        background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
    }

    .amount-badge.warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .amount-badge.secondary {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    }

    .no-data-badge {
        background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
        color: #6c757d;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        color: white;
    }

    .status-badge.success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }

    .status-badge.warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .status-badge.info {
        background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
    }

    .status-badge.secondary {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
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

    .btn-action.success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }

    .btn-action.warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        color: white;
    }

    /* Enhanced Modal */
    .modal-content {
        border-radius: 20px;
        }

        .action-btn.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .action-btn.success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
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
        }

        .data-card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
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
@endsection

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
