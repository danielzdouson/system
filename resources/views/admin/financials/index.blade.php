<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Financial Dashboard - SACCO System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f4f6f8;
            overflow-x: hidden;
        }
        
        .minimal-container {
            padding: 20px;
            max-width: 100%;
            margin: 0 auto;
        }
        
        .back-button {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            color: white;
        }
        
        .page-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .page-header h2 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        
        .page-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
    </style>
</head>
<body>

<div class="minimal-container">
    <!-- Back to Dashboard Button -->
    <a href="{{ route('dashboard') }}" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Back to Dashboard
    </a>
    
    <!-- Page Header -->
    <div class="page-header">
        <h2><i class="fas fa-chart-pie me-2"></i>Financial Dashboard</h2>
        <p>Comprehensive financial overview and member standings</p>
    </div>
@php
    // Get real financial data from service
    $memberFinancialService = new \App\Services\MemberFinancialSummaryService();
    $stats = $memberFinancialService->getMembersSummaryStats();
    
    // Calculate cash flow data
    $cashFlowIncome = \App\Models\CashFlow::where('type', 'income')->sum('amount');
    $cashFlowExpenses = \App\Models\CashFlow::where('type', 'expense')->sum('amount');
    $netCashFlow = $cashFlowIncome - $cashFlowExpenses;
    
    @endphp




<!-- Detailed Financial Summary -->
<div style="background:white; padding:25px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.1);">
    <h3 style="margin:0 0 20px 0; color:#1f2937;">📊 Financial Summary</h3>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:30px;">
        <!-- Income Section -->
        <div>
            <h4 style="margin:0 0 15px 0; color:#059669;">Income Sources</h4>
            <div style="background:#f0fdf4; padding:15px; border-radius:8px; margin-bottom:10px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="color:#6b7280;">Member Savings</span>
                    <span style="color:#059669; font-weight:600;">UGX {{ number_format($stats['total_savings'], 2) }}</span>
                </div>
            </div>
            <div style="background:#f0fdf4; padding:15px; border-radius:8px; margin-bottom:10px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="color:#6b7280;">Cash Flow Income</span>
                    <span style="color:#059669; font-weight:600;">UGX {{ number_format($cashFlowIncome, 2) }}</span>
                </div>
            </div>
            <div style="background:#f0fdf4; padding:15px; border-radius:8px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="color:#6b7280;">Total Income</span>
                    <span style="color:#059669; font-weight:600; font-size:18px;">UGX {{ number_format($stats['total_savings'] + $cashFlowIncome, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Expenses Section -->
        <div>
            <h4 style="margin:0 0 15px 0; color:#dc2626;">Expenses</h4>
            <div style="background:#fef2f2; padding:15px; border-radius:8px; margin-bottom:10px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="color:#6b7280;">Loan Disbursements</span>
                    <span style="color:#dc2626; font-weight:600;">UGX {{ number_format($stats['total_loan_balance'], 2) }}</span>
                </div>
            </div>
            <div style="background:#fef2f2; padding:15px; border-radius:8px; margin-bottom:10px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="color:#6b7280;">Cash Flow Expenses</span>
                    <span style="color:#dc2626; font-weight:600;">UGX {{ number_format($cashFlowExpenses, 2) }}</span>
                </div>
            </div>
            <div style="background:#fef2f2; padding:15px; border-radius:8px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="color:#6b7280;">Total Expenses</span>
                    <span style="color:#dc2626; font-weight:600; font-size:18px;">UGX {{ number_format($stats['total_loan_balance'] + $cashFlowExpenses, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Members Financial Standing Section -->
<div style="background:white; padding:25px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.1); margin-top:30px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h3 style="margin:0; color:#1f2937;">
            <i class="fas fa-users me-2"></i>
            Members Financial Standing
        </h3>
        <div style="display:flex; gap:10px; align-items:center;">
            <input type="text" id="memberSearch" placeholder="Search members..." 
                   style="padding:8px 12px; border:1px solid #e5e7eb; border-radius:6px; min-width:200px;">
            <button onclick="exportMembersData()" class="btn btn-sm btn-success">
                <i class="fas fa-download me-1"></i> Export
            </button>
        </div>
    </div>

    <!-- Enhanced Summary Cards -->
    <div id="membersSummaryCards" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:15px; margin-bottom:25px;">
        <!-- Will be populated by JavaScript with comprehensive financial data -->
    </div>

    <!-- Members Table -->
    <div class="table-responsive">
        <table class="table table-hover table-striped" id="membersTable" style="font-size: 0.875rem; white-space: nowrap;">
            <thead class="table-dark">
                <tr>
                    <th style="min-width: 140px;">Member Name</th>
                    <th style="min-width: 120px;">Member Number</th>
                    <th style="min-width: 120px;">Total Deposits</th>
                    <th style="min-width: 110px;">Total Savings</th>
                    <th style="min-width: 80px;">Welfare</th>
                    <th style="min-width: 130px;">Outstanding Fines</th>
                    <th style="min-width: 110px;">Loan Balance</th>
                    <th style="min-width: 130px;">Available Balance</th>
                    <th style="min-width: 130px;">Distributed Funds</th>
                    <th style="min-width: 100px;">Shares on Hold</th>
                    <th style="min-width: 100px;">Total Shares</th>
                    <th style="min-width: 100px;">Net Worth</th>
                    <th style="min-width: 80px;">Status</th>
                </tr>
            </thead>
            <tbody id="membersTableBody">
                <tr>
                    <td colspan="13" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="mt-2">Loading members data...</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div id="membersPagination" class="d-flex justify-content-center mt-3">
        <!-- Will be populated by JavaScript -->
    </div>
</div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<script>
let currentPage = 1;
let searchTimer;

document.addEventListener('DOMContentLoaded', function() {
    loadMembersData();
    
    // Setup search
    document.getElementById('memberSearch').addEventListener('input', function(e) {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            currentPage = 1;
            loadMembersData();
        }, 500);
    });
});

function loadMembersData() {
    const search = document.getElementById('memberSearch').value;
    
    fetch(`{{ route('admin.financials.members-sector') }}?page=${currentPage}&per_page=20&search=${search}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateSummaryCards(data.data.stats);
                updateMembersTable(data.data.members);
                updatePagination(data.data.members);
            }
        })
        .catch(error => {
            console.error('Error loading members data:', error);
            document.getElementById('membersTableBody').innerHTML = 
                '<tr><td colspan="12" class="text-center text-danger py-4">Error loading data. Please try again.</td></tr>';
        });
}

function updateSummaryCards(stats) {
    const cardsHtml = `
        <div class="card text-center" style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:white; border:none;">
            <div class="card-body">
                <h6 class="card-title mb-2">👥 Total Members</h6>
                <h4 class="mb-1">${stats.total_members}</h4>
                <small>Active Accounts</small>
            </div>
        </div>
        <div class="card text-center" style="background:linear-gradient(135deg, #10b981 0%, #059669 100%); color:white; border:none;">
            <div class="card-body">
                <h6 class="card-title mb-2">💰 Total Savings</h6>
                <h4 class="mb-1">UGX ${number_format(stats.total_savings, 0)}</h4>
                <small>Member Contributions</small>
            </div>
        </div>
        <div class="card text-center" style="background:linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color:white; border:none;">
            <div class="card-body">
                <h6 class="card-title mb-2">💵 Available Balance</h6>
                <h4 class="mb-1">UGX ${number_format(stats.total_available_balance, 0)}</h4>
                <small>Deposits - Distributed</small>
            </div>
        </div>
        <div class="card text-center" style="background:linear-gradient(135deg, #ec4899 0%, #be185d 100%); color:white; border:none;">
            <div class="card-body">
                <h6 class="card-title mb-2">🤝 Total Welfare</h6>
                <h4 class="mb-1">UGX ${number_format(stats.total_welfare, 0)}</h4>
                <small>Welfare Fund Balance</small>
            </div>
        </div>
        <div class="card text-center" style="background:linear-gradient(135deg, #f59e0b 0%, #ef4444 100%); color:white; border:none;">
            <div class="card-body">
                <h6 class="card-title mb-2">💳 Total Loans</h6>
                <h4 class="mb-1">UGX ${number_format(stats.total_loan_balance, 0)}</h4>
                <small>Outstanding Loans</small>
            </div>
        </div>
        <div class="card text-center" style="background:linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color:white; border:none;">
            <div class="card-body">
                <h6 class="card-title mb-2">🔒 Shares on Hold</h6>
                <h4 class="mb-1">${number_format(stats.total_shares_on_hold, 2)}%</h4>
                <small>Restricted Share Percentage</small>
            </div>
        </div>
        <div class="card text-center" style="background:linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); color:white; border:none;">
            <div class="card-body">
                <h6 class="card-title mb-2">📊 Total Shares</h6>
                <h4 class="mb-1">${number_format(stats.total_shares, 2)}%</h4>
                <small>Total Share Distribution</small>
            </div>
        </div>
        <div class="card text-center" style="background:linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); color:white; border:none;">
            <div class="card-body">
                <h6 class="card-title mb-2">� Total Net Worth</h6>
                <h4 class="mb-1">UGX ${number_format(stats.total_net_worth, 0)}</h4>
                <small>Assets - Liabilities</small>
            </div>
        </div>
        <div class="card text-center" style="background:linear-gradient(135deg, #84cc16 0%, #65a30d 100%); color:white; border:none;">
            <div class="card-body">
                <h6 class="card-title mb-2">📈 Total Deposits</h6>
                <h4 class="mb-1">UGX ${number_format(stats.total_deposits, 0)}</h4>
                <small>All Time Deposits</small>
            </div>
        </div>
    `;
    document.getElementById('membersSummaryCards').innerHTML = cardsHtml;
}

function updateMembersTable(members) {
    let tbodyHtml = '';
    
    members.data.forEach(member => {
        const netWorthClass = member.net_worth >= 0 ? 'text-success' : 'text-danger';
        const statusBadge = getStatusBadge(member.status);
        
        tbodyHtml += `
            <tr>
                <td><strong>${member.name}</strong></td>
                <td>${member.member_number}</td>
                <td class="text-success">UGX ${number_format(member.total_deposits, 0)}</td>
                <td class="text-success">UGX ${number_format(member.total_savings, 0)}</td>
                <td class="text-info">UGX ${number_format(member.welfare, 0)}</td>
                <td class="text-danger">UGX ${number_format(member.outstanding_fines, 0)}</td>
                <td class="text-danger">UGX ${number_format(member.loan_balance, 0)}</td>
                <td class="text-primary fw-bold">UGX ${number_format(member.available_balance, 0)}</td>
                <td class="text-warning">UGX ${number_format(member.distributed_funds, 0)}</td>
                <td class="text-warning">${number_format(member.shares_on_hold, 2)}%</td>
                <td class="text-info">${number_format(member.total_shares, 2)}%</td>
                <td class="${netWorthClass} fw-bold">UGX ${number_format(member.net_worth, 0)}</td>
                <td>${statusBadge}</td>
            </tr>
        `;
    });
    
    if (members.data.length === 0) {
        tbodyHtml = '<tr><td colspan="13" class="text-center py-4">No members found</td></tr>';
    }
    
    document.getElementById('membersTableBody').innerHTML = tbodyHtml;
}

function getStatusBadge(status) {
    const badges = {
        'active': '<span class="badge bg-success">Active</span>',
        'inactive': '<span class="badge bg-secondary">Inactive</span>',
        'delinquent': '<span class="badge bg-danger">Delinquent</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
}

function updatePagination(members) {
    const pagination = document.getElementById('membersPagination');
    
    if (members.last_page <= 1) {
        pagination.innerHTML = '';
        return;
    }
    
    let paginationHtml = '<nav><ul class="pagination">';
    
    // Previous button
    if (members.prev_page_url) {
        paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${members.current_page - 1})">Previous</a></li>`;
    }
    
    // Page numbers
    for (let i = 1; i <= members.last_page; i++) {
        const activeClass = i === members.current_page ? 'active' : '';
        paginationHtml += `<li class="page-item ${activeClass}"><a class="page-link" href="#" onclick="changePage(${i})">${i}</a></li>`;
    }
    
    // Next button
    if (members.next_page_url) {
        paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${members.current_page + 1})">Next</a></li>`;
    }
    
    paginationHtml += '</ul></nav>';
    pagination.innerHTML = paginationHtml;
}

function changePage(page) {
    currentPage = page;
    loadMembersData();
}

function exportMembersData() {
    const search = document.getElementById('memberSearch').value;
    window.open(`{{ route('admin.financials.members-sector') }}?export=1&search=${search}`, '_blank');
}

// Helper function for number formatting
function number_format(number, decimals) {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(number);
}
</script>
