<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Distribute Deposit - {{ config('app.name', 'Laravel') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* Modern Distribute Page Styles */
        body {
            font-family: 'Figtree', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        
        .distribute-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .header-subtitle {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }
        
        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            color: white;
        }
        
        .content-wrapper {
            padding: 2.5rem;
        }
        
        .deposit-info-card {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        
        .info-item {
            background: white;
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .info-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        
        .info-label {
            font-size: 0.8rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .info-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }
        
        .member-link {
            color: #3b82f6;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        
        .member-link:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }
        
        .distribution-form {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .form-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .category-item {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 1rem;
            transition: all 0.3s ease;
        }
        
        .category-item:hover {
            border-color: #667eea;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }
        
        .category-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .category-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }
        
        .icon-savings { background: #dcfce7; color: #166534; }
        .icon-welfare { background: #fee2e2; color: #991b1b; }
        .icon-fines { background: #fef3c7; color: #92400e; }
        .icon-other { background: #f3e8ff; color: #6b21a8; }
        
        .amount-input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }
        
        .amount-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .input-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .input-group-text {
            background: #f1f5f9;
            border: 2px solid #e2e8f0;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            color: #64748b;
            font-weight: 500;
        }
        
        .description-input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }
        
        .description-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .summary-section {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-label {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .summary-value {
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .status-badge {
            padding: 0.75rem 1.5rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        
        .submit-section {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .btn-cancel {
            background: #f1f5f9;
            color: #475569;
            border: 2px solid #e2e8f0;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-cancel:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
        }
        
        .month-selection {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid rgba(59, 130, 246, 0.1);
        }
        
        .month-selection .form-title {
            font-size: 1.1rem;
            color: #1e40af;
            margin-bottom: 1rem;
        }
        
        .form-select, .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .form-select:focus, .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .tips-section {
            background: linear-gradient(135deg, #fef3c7 0%, #fbbf24 100%);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .tips-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #92400e;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .tips-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .tip-item {
            background: rgba(255, 255, 255, 0.8);
            padding: 1.5rem;
            border-radius: 12px;
            border: 1px solid rgba(146, 64, 14, 0.1);
        }
        
        .tip-item h6 {
            color: #92400e;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .tip-item p {
            color: #78350f;
            font-size: 0.9rem;
            margin: 0;
        }
        
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .page-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .content-wrapper {
                padding: 1.5rem;
            }
            
            .category-grid {
                grid-template-columns: 1fr;
            }
            
            .summary-grid {
                grid-template-columns: 1fr;
            }
            
            .tips-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="distribute-container">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-hand-holding-usd"></i>
                    Distribute Deposit Funds
                </h1>
                <p class="header-subtitle">Allocate deposit amount to different categories</p>
            </div>
            <a href="{{ route('admin.group-savings.dashboard') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
        </div>
        
        <div class="content-wrapper">
            <!-- Month Selection -->
            <div class="month-selection">
                <h4 class="form-title">
                    <i class="fas fa-calendar-alt"></i>
                    Select Distribution Month
                </h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="distribution_month" class="form-label fw-semibold">
                            <i class="fas fa-calendar text-primary me-1"></i>
                            Target Month
                        </label>
                        <select name="distribution_month" id="distribution_month" class="form-select">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == $deposit->month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }} {{ $m == $deposit->month ? '(Current Month)' : '' }}
                                </option>
                            @endfor
                        </select>
                        <small class="text-muted">Select which month to apply this distribution to</small>
                    </div>
                    <div class="col-md-6">
                        <label for="distribution_note" class="form-label fw-semibold">
                            <i class="fas fa-sticky-note text-primary me-1"></i>
                            Distribution Note
                        </label>
                        <input type="text" 
                               name="distribution_note" 
                               id="distribution_note" 
                               class="form-control" 
                               placeholder="Optional note for this distribution">
                        <small class="text-muted">Add a note explaining this distribution</small>
                    </div>
                </div>
            </div>
            
            <!-- Deposit Information -->
            <div class="deposit-info-card">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-user"></i>
                            Member
                        </div>
                        <p class="info-value">
                            <a href="{{ route('admin.members.show', $deposit->member->id) }}" class="member-link">
                                {{ $deposit->member->first_name }} {{ $deposit->member->last_name }}
                            </a>
                        </p>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-calendar"></i>
                            Original Deposit Month
                        </div>
                        <p class="info-value">
                            {{ \Carbon\Carbon::create()->month($deposit->month)->format('F') }} {{ $deposit->fiscal_year ? $deposit->fiscal_year->year : 'N/A' }}
                        </p>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-clock"></i>
                            Deposit Date
                        </div>
                        <p class="info-value">
                            {{ $deposit->deposit_date->format('M d, Y') }}
                        </p>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-money-bill-wave"></i>
                            Available Balance
                        </div>
                        <p class="info-value" id="available_balance">
                            {{ number_format($availableBalance, 2) }}
                        </p>
                    </div>
                </div>
                
                <!-- Month Selection for Distribution -->
                <div class="month-selection mt-4">
                    <h4 class="form-title">
                        <i class="fas fa-calendar-alt"></i>
                        Select Distribution Month
                    </h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="distribution_month" class="form-label fw-semibold">
                                <i class="fas fa-calendar text-primary me-1"></i>
                                Target Month
                            </label>
                            <select name="distribution_month" id="distribution_month" class="form-select">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == $deposit->month ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->format('F') }} {{ $m == $deposit->month ? '(Current Month)' : '' }}
                                    </option>
                                @endfor
                            </select>
                            <small class="text-muted">Select which month to apply this distribution to</small>
                        </div>
                        <div class="col-md-6">
                            <label for="distribution_note" class="form-label fw-semibold">
                                <i class="fas fa-sticky-note text-primary me-1"></i>
                                Distribution Note
                            </label>
                            <input type="text" 
                                   name="distribution_note" 
                                   id="distribution_note" 
                                   class="form-control" 
                                   placeholder="Optional note for this distribution">
                            <small class="text-muted">Add a note explaining this distribution</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tips Section -->
            <div class="tips-section">
                <h3 class="tips-title">
                    <i class="fas fa-lightbulb"></i>
                    Distribution Tips
                </h3>
                <div class="tips-grid">
                    <div class="tip-item">
                        <h6>
                            <i class="fas fa-piggy-bank me-1"></i>
                            Member Savings
                        </h6>
                        <p class="small">Member savings are tracked per month and contribute to their total savings.</p>
                    </div>
                    <div class="tip-item">
                        <h6>
                            <i class="fas fa-users me-1"></i>
                            Welfare Fund
                        </h6>
                        <p class="small">Welfare funds are group-based and can be used for member assistance programs.</p>
                    </div>
                    <div class="tip-item">
                        <h6>
                            <i class="fas fa-calendar-alt me-1"></i>
                            Month Flexibility
                        </h6>
                        <p class="small">You can distribute funds to any month within the fiscal year, allowing for flexible savings management.</p>
                    </div>
                    <div class="tip-item">
                        <h6>
                            <i class="fas fa-sticky-note me-1"></i>
                            Track Distributions
                        </h6>
                        <p class="small">Add notes to track why distributions were made to specific months for better record keeping.</p>
                    </div>
                </div>
            </div>
            
            <!-- Distribution Form -->
            <form method="POST" action="{{ route('admin.group-savings.store-distribution', $deposit->id) }}" class="distribution-form">
                @csrf
                <h2 class="form-title">
                    <i class="fas fa-chart-pie"></i>
                    Distribution Categories
                </h2>
                
                <div class="category-grid">
                    <div class="category-item">
                        <div class="category-label">
                            <div class="category-icon icon-savings">
                                <i class="fas fa-piggy-bank"></i>
                            </div>
                            Member Savings
                        </div>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-piggy-bank"></i>
                            </span>
                            <input type="number" 
                                   name="savings_amount" 
                                   id="savings_amount" 
                                   class="amount-input" 
                                   placeholder="0.00" 
                                   step="0.01" 
                                   min="0" 
                                   max="{{ $deposit->balance }}">
                            <span class="input-group-text">UGX</span>
                        </div>
                        <small class="text-muted">Amount allocated to member's personal savings</small>
                    </div>
                    
                    <div class="category-item">
                        <div class="category-label">
                            <div class="category-icon icon-welfare">
                                <i class="fas fa-users"></i>
                            </div>
                            Welfare Fund
                        </div>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-users"></i>
                            </span>
                            <input type="number" 
                                   name="welfare_amount" 
                                   id="welfare_amount" 
                                   class="amount-input" 
                                   placeholder="0.00" 
                                   step="0.01" 
                                   min="0" 
                                   max="{{ $deposit->balance }}">
                            <span class="input-group-text">UGX</span>
                        </div>
                        <small class="text-muted">Amount allocated to group welfare fund</small>
                    </div>
                    
                    <div class="category-item">
                        <div class="category-label">
                            <div class="category-icon icon-fines">
                                <i class="fas fa-gavel"></i>
                            </div>
                            Fines & Penalties
                        </div>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-gavel"></i>
                            </span>
                            <input type="number" 
                                   name="fines_amount" 
                                   id="fines_amount" 
                                   class="amount-input" 
                                   placeholder="0.00" 
                                   step="0.01" 
                                   min="0" 
                                   max="{{ $deposit->balance }}">
                            <span class="input-group-text">UGX</span>
                        </div>
                        <small class="text-muted">Amount allocated to fines and penalties</small>
                    </div>
                    
                    <div class="category-item">
                        <div class="category-label">
                            <div class="category-icon icon-other">
                                <i class="fas fa-ellipsis-h"></i>
                            </div>
                            Other Amount
                        </div>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-ellipsis-h"></i>
                            </span>
                            <input type="number" 
                                   name="other_amount" 
                                   id="other_amount" 
                                   class="amount-input" 
                                   placeholder="0.00" 
                                   step="0.01" 
                                   min="0" 
                                   max="{{ $deposit->balance }}">
                            <span class="input-group-text">UGX</span>
                        </div>
                        <small class="text-muted">Amount allocated to other funds</small>
                    </div>
                </div>
                
                <!-- Other Description -->
                <div class="row g-4 mt-2">
                    <div class="col-12">
                        <label for="other_description" class="form-label fw-semibold">
                            <i class="fas fa-comment text-primary me-1"></i>
                            Other Description (if applicable)
                        </label>
                        <input type="text" 
                               name="other_description" 
                               id="other_description" 
                               class="description-input" 
                               placeholder="Describe what the 'other' amount is for">
                    </div>
                </div>
                
                <!-- Summary Section -->
                <div class="summary-section">
                    <div class="summary-grid">
                        <div class="summary-item">
                            <div class="summary-label">Total Available</div>
                            <div class="summary-value" id="total_deposit">{{ number_format($availableBalance, 2) }}</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Total Distributed</div>
                            <div class="summary-value" id="total_distributed">0.00</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Remaining Balance</div>
                            <div class="summary-value" id="remaining_balance">{{ number_format($availableBalance, 2) }}</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Status</div>
                            <div class="status-badge badge-warning" id="distribution_status">
                                <i class="fas fa-exclamation-triangle"></i>
                                Remaining
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Section -->
                <div class="submit-section">
                    <a href="{{ route('admin.group-savings.dashboard') }}" class="btn-cancel">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                    <button type="submit" class="btn-submit" id="submit_btn" disabled>
                        <i class="fas fa-check"></i>
                        Complete Distribution
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const totalDeposit = {{ $availableBalance }};
        const inputs = ['savings_amount', 'welfare_amount', 'fines_amount', 'other_amount'];
        
        function updateSummary() {
            let totalDistributed = 0;
            
            inputs.forEach(id => {
                const value = parseFloat(document.getElementById(id).value) || 0;
                totalDistributed += value;
            });
            
            const remaining = totalDeposit - totalDistributed;
            
            // Update all balance displays
            document.getElementById('total_distributed').textContent = totalDistributed.toLocaleString();
            document.getElementById('remaining_balance').textContent = remaining.toLocaleString();
            document.getElementById('available_balance').textContent = remaining.toLocaleString();
            
            const statusEl = document.getElementById('distribution_status');
            const submitBtn = document.getElementById('submit_btn');
            
            // Change available balance color based on remaining amount
            const availableBalanceEl = document.getElementById('available_balance');
            if (remaining < 0) {
                availableBalanceEl.style.color = '#dc2626'; // Red for over
            } else if (remaining === 0) {
                availableBalanceEl.style.color = '#059669'; // Green for perfect
            } else {
                availableBalanceEl.style.color = '#1e293b'; // Default for remaining
            }
            
            if (Math.abs(remaining) < 0.01) {
                statusEl.innerHTML = '<i class="fas fa-check-circle"></i> Perfect';
                statusEl.className = 'status-badge badge-success';
                submitBtn.disabled = false;
            } else if (remaining < 0) {
                statusEl.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Over';
                statusEl.className = 'status-badge badge-danger';
                submitBtn.disabled = true;
            } else {
                statusEl.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Remaining';
                statusEl.className = 'status-badge badge-warning';
                submitBtn.disabled = false;
            }
        }
        
        // Auto-format inputs
        inputs.forEach(id => {
            const input = document.getElementById(id);
            input.addEventListener('input', function() {
                updateSummary();
            });
            
            input.addEventListener('blur', function() {
                if (this.value && !isNaN(this.value)) {
                    this.value = parseFloat(this.value).toFixed(2);
                }
            });
        });
        
        updateSummary();
    });
    </script>
</body>
</html>
