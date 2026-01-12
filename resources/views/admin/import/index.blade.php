@extends('layouts.admin')

@section('title', 'Data Import')

@section('content')
<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:30px;">
    
    <!-- Import Instructions Card -->
    <div class="dashboard-card" style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:white; padding:25px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
        <div style="display:flex; align-items:center; margin-bottom:10px;">
            <span class="card-icon">📋</span>
            <div>
                <h4 style="margin:0; font-size:14px; opacity:0.9;">Excel Data Import</h4>
                <p style="margin:5px 0 0 0; font-size:12px; opacity:0.8;">Import SACCO data from Excel files</p>
            </div>
        </div>
    </div>

    <!-- Quick Stats Card -->
    <div class="dashboard-card" style="background:linear-gradient(135deg, #f59e0b 0%, #ef4444 100%); color:white; padding:25px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
        <div style="display:flex; align-items:center; margin-bottom:10px;">
            <span class="card-icon">📊</span>
            <div>
                <h4 style="margin:0; font-size:14px; opacity:0.9;">Import Statistics</h4>
                <p style="margin:5px 0 0 0; font-size:12px; opacity:0.8;">Track import success and errors</p>
            </div>
        </div>
    </div>
</div>

<!-- Import Options -->
<div class="dashboard-card" style="background:white; padding:25px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.1);">
    <h3 style="margin:0 0 20px 0; color:#1f2937; display:flex; align-items:center;">
        <span style="margin-right:10px;">📁</span>
        Select Import Type
    </h3>
    
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:20px; margin-bottom:30px;">
        
        <!-- Members Import -->
        <div class="import-option" onclick="showImportForm('members')">
            <div class="import-icon">👥</div>
            <h4>Import Members</h4>
            <p>Import new member registrations and contact information</p>
            <div class="import-features">
                <span>✓ First Name, Last Name</span>
                <span>✓ National ID, Email, Phone</span>
                <span>✓ Duplicate Detection</span>
                <span>✓ Email Validation</span>
            </div>
        </div>

        <!-- Financial Records Import -->
        <div class="import-option" onclick="showImportForm('financials')">
            <div class="import-icon">💰</div>
            <h4>Import Financial Records</h4>
            <p>Import member savings, welfare, and contribution data</p>
            <div class="import-features">
                <span>✓ Member ID, Date</span>
                <span>✓ Savings, Welfare, Education</span>
                <span>✓ Loan Repayments</span>
                <span>✓ Amount Validation</span>
            </div>
        </div>

        <!-- Loan Records Import -->
        <div class="import-option" onclick="showImportForm('loans')">
            <div class="import-icon">💳</div>
            <h4>Import Loan Records</h4>
            <p>Import loan summaries and repayment information</p>
            <div class="import-features">
                <span>✓ Member ID, Name</span>
                <span>✓ Loan Amounts</span>
                <span>✓ Interest Calculations</span>
                <span>✓ Balance Tracking</span>
            </div>
        </div>

        <!-- Cash Flow Import -->
        <div class="import-option" onclick="showImportForm('cashflow')">
            <div class="import-icon">💸</div>
            <h4>Import Cash Flow</h4>
            <p>Import income, expense, and transaction data</p>
            <div class="import-features">
                <span>✓ Date, Description</span>
                <span>✓ Category, Type</span>
                <span>✓ Amount, Method</span>
                <span>✓ Reference Numbers</span>
            </div>
        </div>
    </div>
</div>

<!-- Import Forms -->
<div id="import-forms" style="display:none;">
    
    <!-- Members Import Form -->
    <div id="members-form" class="import-form" style="display:none;">
        <div class="dashboard-card" style="background:white; padding:25px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.1);">
            <h3 style="margin:0 0 20px 0; color:#1f2937; display:flex; align-items:center;">
                <span style="margin-right:10px;">👥</span>
                Import Members Data
            </h3>
            
            <form id="members-upload-form" enctype="multipart/form-data" style="margin-bottom:20px;">
                @csrf
                <div class="form-group">
                    <label>Excel File (.xlsx, .xls, .csv)</label>
                    <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required class="form-input">
                </div>
                
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <button type="button" onclick="document.getElementById('members-upload-form').reset()" class="btn btn-secondary">
                        Clear
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span style="margin-right:8px;">📤</span>
                        Import Members
                    </button>
                </div>
            </form>
            
            <div class="template-section">
                <h4 style="margin:0 0 15px 0; color:#374151;">📋 Excel Template</h4>
                <p style="color:#6b7280; margin-bottom:15px;">Download the Excel template and fill in your member data:</p>
                <a href="{{ asset('templates/members_template.xlsx') }}" class="btn btn-success" download>
                    <span style="margin-right:8px;">⬇</span>
                    Download Member Template
                </a>
            </div>
            
            <div class="column-info">
                <h4 style="margin:0 0 15px 0; color:#374151;">📊 Required Columns</h4>
                <div style="background:#f8fafc; padding:15px; border-radius:8px; font-family:monospace;">
                    <strong>Column A:</strong> First Name<br>
                    <strong>Column B:</strong> Last Name<br>
                    <strong>Column C:</strong> National ID<br>
                    <strong>Column D:</strong> Email<br>
                    <strong>Column E:</strong> Phone
                </div>
            </div>
        </div>
    </div>

    <!-- Financials Import Form -->
    <div id="financials-form" class="import-form" style="display:none;">
        <div class="dashboard-card" style="background:white; padding:25px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.1);">
            <h3 style="margin:0 0 20px 0; color:#1f2937; display:flex; align-items:center;">
                <span style="margin-right:10px;">💰</span>
                Import Financial Records
            </h3>
            
            <form id="financials-upload-form" enctype="multipart/form-data" style="margin-bottom:20px;">
                @csrf
                <div class="form-group">
                    <label>Excel File (.xlsx, .xls, .csv)</label>
                    <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required class="form-input">
                </div>
                
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <button type="button" onclick="document.getElementById('financials-upload-form').reset()" class="btn btn-secondary">
                        Clear
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span style="margin-right:8px;">📤</span>
                        Import Financials
                    </button>
                </div>
            </form>
            
            <div class="template-section">
                <h4 style="margin:0 0 15px 0; color:#374151;">📋 Excel Template</h4>
                <p style="color:#6b7280; margin-bottom:15px;">Download the Excel template and fill in your financial data:</p>
                <a href="{{ asset('templates/financials_template.xlsx') }}" class="btn btn-success" download>
                    <span style="margin-right:8px;">⬇</span>
                    Download Financial Template
                </a>
            </div>
            
            <div class="column-info">
                <h4 style="margin:0 0 15px 0; color:#374151;">📊 Required Columns</h4>
                <div style="background:#f8fafc; padding:15px; border-radius:8px; font-family:monospace;">
                    <strong>Column A:</strong> Member ID<br>
                    <strong>Column B:</strong> Membership Name<br>
                    <strong>Column C:</strong> Membership Number<br>
                    <strong>Column D:</strong> Savings (UGX)<br>
                    <strong>Column E:</strong> Welfare (UGX)<br>
                    <strong>Column F:</strong> Education In (UGX)<br>
                    <strong>Column G:</strong> Fined (UGX)<br>
                    <strong>Column H:</strong> Fines Paid (UGX)<br>
                    <strong>Column I:</strong> Education Out (UGX)<br>
                    <strong>Column J:</strong> Loan Repayments (UGX)<br>
                    <strong>Column K:</strong> Loan Charges (UGX)<br>
                    <strong>Column L:</strong> Notes
                </div>
            </div>
        </div>
    </div>

    <!-- Loans Import Form -->
    <div id="loans-form" class="import-form" style="display:none;">
        <div class="dashboard-card" style="background:white; padding:25px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.1);">
            <h3 style="margin:0 0 20px 0; color:#1f2937; display:flex; align-items:center;">
                <span style="margin-right:10px;">💳</span>
                Import Loan Records
            </h3>
            
            <form id="loans-upload-form" enctype="multipart/form-data" style="margin-bottom:20px;">
                @csrf
                <div class="form-group">
                    <label>Excel File (.xlsx, .xls, .csv)</label>
                    <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required class="form-input">
                </div>
                
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <button type="button" onclick="document.getElementById('loans-upload-form').reset()" class="btn btn-secondary">
                        Clear
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span style="margin-right:8px;">📤</span>
                        Import Loans
                    </button>
                </div>
            </form>
            
            <div class="template-section">
                <h4 style="margin:0 0 15px 0; color:#374151;">📋 Excel Template</h4>
                <p style="color:#6b7280; margin-bottom:15px;">Download the Excel template and fill in your loan data:</p>
                <a href="{{ asset('templates/loans_template.xlsx') }}" class="btn btn-success" download>
                    <span style="margin-right:8px;">⬇</span>
                    Download Loan Template
                </a>
            </div>
            
            <div class="column-info">
                <h4 style="margin:0 0 15px 0; color:#374151;">📊 Required Columns</h4>
                <div style="background:#f8fafc; padding:15px; border-radius:8px; font-family:monospace;">
                    <strong>Column A:</strong> Member ID<br>
                    <strong>Column B:</strong> Member Name<br>
                    <strong>Column C:</strong> Loan Brought Forward (UGX)<br>
                    <strong>Column D:</strong> Loan Issued Current Year (UGX)<br>
                    <strong>Column E:</strong> Current Year Loan + Interest (UGX)<br>
                    <strong>Column F:</strong> Loan Balance Without Fines (UGX)<br>
                    <strong>Column G:</strong> Loan Paid/Loan Out (UGX)<br>
                    <strong>Column H:</strong> Total Loan Balance (UGX)<br>
                    <strong>Column I:</strong> Notes
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Flow Import Form -->
    <div id="cashflow-form" class="import-form" style="display:none;">
        <div class="dashboard-card" style="background:white; padding:25px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.1);">
            <h3 style="margin:0 0 20px 0; color:#1f2937; display:flex; align-items:center;">
                <span style="margin-right:10px;">💸</span>
                Import Cash Flow Data
            </h3>
            
            <form id="cashflow-upload-form" enctype="multipart/form-data" style="margin-bottom:20px;">
                @csrf
                <div class="form-group">
                    <label>Excel File (.xlsx, .xls, .csv)</label>
                    <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required class="form-input">
                </div>
                
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <button type="button" onclick="document.getElementById('cashflow-upload-form').reset()" class="btn btn-secondary">
                        Clear
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span style="margin-right:8px;">📤</span>
                        Import Cash Flow
                    </button>
                </div>
            </form>
            
            <div class="template-section">
                <h4 style="margin:0 0 15px 0; color:#374151;">📋 Excel Template</h4>
                <p style="color:#6b7280; margin-bottom:15px;">Download the Excel template and fill in your transaction data:</p>
                <a href="{{ asset('templates/cashflow_template.xlsx') }}" class="btn btn-success" download>
                    <span style="margin-right:8px;">⬇</span>
                    Download Cash Flow Template
                </a>
            </div>
            
            <div class="column-info">
                <h4 style="margin:0 0 15px 0; color:#374151;">📊 Required Columns</h4>
                <div style="background:#f8fafc; padding:15px; border-radius:8px; font-family:monospace;">
                    <strong>Column A:</strong> Transaction Date (YYYY-MM-DD)<br>
                    <strong>Column B:</strong> Description<br>
                    <strong>Column C:</strong> Category<br>
                    <strong>Column D:</strong> Type (income/expense)<br>
                    <strong>Column E:</strong> Amount (UGX)<br>
                    <strong>Column F:</strong> Payment Method<br>
                    <strong>Column G:</strong> Reference Number<br>
                    <strong>Column H:</strong> Notes
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Results -->
<div id="import-results" style="display:none;">
    <div class="dashboard-card" style="background:white; padding:25px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.1);">
        <h3 style="margin:0 0 20px 0; color:#1f2937; display:flex; align-items:center;">
            <span style="margin-right:10px;">📊</span>
            Import Results
        </h3>
        
        <div id="results-content"></div>
    </div>
</div>

<script>
function showImportForm(type) {
    // Hide all forms
    document.querySelectorAll('.import-form').forEach(form => {
        form.style.display = 'none';
    });
    
    // Show selected form
    const selectedForm = document.getElementById(type + '-form');
    if (selectedForm) {
        selectedForm.style.display = 'block';
    }
    
    // Hide results
    document.getElementById('import-results').style.display = 'none';
}

// Handle form submissions
document.addEventListener('DOMContentLoaded', function() {
    // Members form
    const membersForm = document.getElementById('members-upload-form');
    if (membersForm) {
        membersForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleImport('members', '/admin/import/members');
        });
    }
    
    // Financials form
    const financialsForm = document.getElementById('financials-upload-form');
    if (financialsForm) {
        financialsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleImport('financials', '/admin/import/financials');
        });
    }
    
    // Loans form
    const loansForm = document.getElementById('loans-upload-form');
    if (loansForm) {
        loansForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleImport('loans', '/admin/import/loans');
        });
    }
    
    // Cash Flow form
    const cashflowForm = document.getElementById('cashflow-upload-form');
    if (cashflowForm) {
        cashflowForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleImport('cashflow', '/admin/import/cashflow');
        });
    }
});

function handleImport(type, url) {
    const form = document.getElementById(type + '-upload-form');
    const formData = new FormData(form);
    const resultsDiv = document.getElementById('import-results');
    const resultsContent = document.getElementById('results-content');
    
    // Show loading
    resultsContent.innerHTML = '<div style="text-align:center; padding:40px;"><div style="font-size:24px; margin-bottom:10px;">⏳</div><p>Importing data... Please wait.</p></div>';
    resultsDiv.style.display = 'block';
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let html = '<div style="background:#d1fae5; padding:20px; border-radius:8px; margin-bottom:20px; color:#065f46;">';
            html += '<h4 style="margin:0 0 10px 0; color:#065f46;">✅ Import Successful!</h4>';
            html += '<p><strong>Records Imported:</strong> ' + data.imported + '</p>';
            
            if (data.errors && data.errors.length > 0) {
                html += '<div style="background:#fef3c7; padding:15px; border-radius:8px; margin-top:15px;">';
                html += '<h4 style="margin:0 0 10px 0; color:#991b1b;">⚠️ Warnings/Errors:</h4>';
                html += '<ul style="margin:0; padding-left:20px;">';
                data.errors.forEach(error => {
                    html += '<li style="margin-bottom:5px;">' + error + '</li>';
                });
                html += '</ul>';
                html += '</div>';
            }
            
            if (data.duplicates && data.duplicates.length > 0) {
                html += '<div style="background:#fef3c7; padding:15px; border-radius:8px; margin-top:15px;">';
                html += '<h4 style="margin:0 0 10px 0; color:#991b1b;">🔄 Duplicates Found:</h4>';
                html += '<ul style="margin:0; padding-left:20px;">';
                data.duplicates.forEach(duplicate => {
                    html += '<li style="margin-bottom:5px;">' + duplicate + '</li>';
                });
                html += '</ul>';
                html += '</div>';
            }
            
            html += '<div style="margin-top:20px;"><button onclick="location.reload()" class="btn btn-primary">Import Another File</button></div>';
            html += '</div>';
            
            resultsContent.innerHTML = html;
        } else {
            resultsContent.innerHTML = '<div style="background:#fef2f2; padding:20px; border-radius:8px; color:#dc2626;"><h4 style="margin:0 0 10px 0;">❌ Import Failed</h4><p>' + data.message + '</p><button onclick="location.reload()" class="btn btn-secondary">Try Again</button></div>';
        }
    })
    .catch(error => {
        resultsContent.innerHTML = '<div style="background:#fef2f2; padding:20px; border-radius:8px; color:#dc2626;"><h4 style="margin:0 0 10px 0;">❌ Import Failed</h4><p>Network error occurred. Please try again.</p><button onclick="location.reload()" class="btn btn-secondary">Try Again</button></div>';
    });
}
</script>

<style>
.import-option {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 25px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.import-option:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    border-color: #3b82f6;
}

.import-option::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
    transition: left 0.3s ease;
}

.import-option:hover::before {
    left: 100%;
}

.import-icon {
    font-size: 48px;
    margin-bottom: 15px;
    display: block;
}

.import-option h4 {
    margin: 0 0 10px 0;
    color: #1f2937;
    font-size: 18px;
    font-weight: 600;
}

.import-option p {
    margin: 0 0 15px 0;
    color: #6b7280;
    font-size: 14px;
    line-height: 1.4;
}

.import-features {
    text-align: left;
    margin-top: 15px;
}

.import-features span {
    display: block;
    background: #f0fdf4;
    color: #166534;
    padding: 5px 10px;
    border-radius: 4px;
    margin-bottom: 5px;
    font-size: 12px;
}

.import-form {
    margin-top: 30px;
}

.template-section {
    background: #f8fafc;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.template-section h4 {
    margin: 0 0 15px 0;
    color: #374151;
}

.template-section p {
    margin: 0 0 15px 0;
    color: #6b7280;
}

.column-info {
    background: #f3f4f6;
    padding: 15px;
    border-radius: 8px;
    font-family: monospace;
    font-size: 12px;
    line-height: 1.6;
}

.column-info h4 {
    margin: 0 0 15px 0;
    color: #374151;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
}
</style>

@endsection
