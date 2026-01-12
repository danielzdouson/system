@extends('layouts.admin')

@section('title', 'Reports Dashboard')

@section('content')
<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:30px;">
    <!-- Summary Cards -->
    <div style="background:linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color:white; padding:25px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
        <div style="display:flex; align-items:center; margin-bottom:10px;">
            <span style="font-size:28px; margin-right:15px;">📊</span>
            <div>
                <h4 style="margin:0; font-size:14px; opacity:0.9;">System Overview</h4>
                <p style="margin:5px 0 0 0; font-size:12px; opacity:0.8;">Complete SACCO performance metrics</p>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div style="background:linear-gradient(135deg, #ec4899 0%, #0ea5e9 100%); color:white; padding:25px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
        <div style="display:flex; align-items:center; margin-bottom:10px;">
            <span style="font-size:28px; margin-right:15px;">📈</span>
            <div>
                <h4 style="margin:0; font-size:14px; opacity:0.9;">Quick Stats</h4>
                <p style="margin:5px 0 0 0; font-size:12px; opacity:0.8;">Key performance indicators</p>
            </div>
        </div>
    </div>
</div>

<!-- Report Categories -->
<div style="background:white; padding:25px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.1); margin-bottom:30px;">
    <h3 style="margin:0 0 20px 0; color:#1f2937; display:flex; align-items:center;">
        <span style="margin-right:10px;">📋</span>
        Available Reports
    </h3>
    
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); gap:20px;">
        
        <!-- Financial Reports -->
        <div style="background:#f8fafc; border:2px solid #e5e7eb; border-radius:8px; padding:20px; transition:all 0.3s ease;">
            <div style="display:flex; align-items:center; margin-bottom:15px;">
                <span style="font-size:24px; margin-right:10px;">💰</span>
                <h4 style="margin:0; color:#1f2937;">Financial Reports</h4>
            </div>
            <ul style="margin:0; padding-left:20px; color:#374151;">
                <li style="margin-bottom:8px;">Income Statement</li>
                <li style="margin-bottom:8px;">Expense Report</li>
                <li style="margin-bottom:8px;">Balance Sheet</li>
                <li style="margin-bottom:8px;">Cash Flow Statement</li>
            </ul>
        </div>

        <!-- Member Reports -->
        <div style="background:#f0fdf4; border:2px solid #bbf7d0; border-radius:8px; padding:20px; transition:all 0.3s ease;">
            <div style="display:flex; align-items:center; margin-bottom:15px;">
                <span style="font-size:24px; margin-right:10px;">👥</span>
                <h4 style="margin:0; color:#166534;">Member Reports</h4>
            </div>
            <ul style="margin:0; padding-left:20px; color:#166534;">
                <li style="margin-bottom:8px;">Member Register</li>
                <li style="margin-bottom:8px;">Active Members</li>
                <li style="margin-bottom:8px;">Member Contributions</li>
                <li style="margin-bottom:8px;">Membership Status</li>
            </ul>
        </div>

        <!-- Loan Reports -->
        <div style="background:#fef3c7; border:2px solid #fbbf24; border-radius:8px; padding:20px; transition:all 0.3s ease;">
            <div style="display:flex; align-items:center; margin-bottom:15px;">
                <span style="font-size:24px; margin-right:10px;">💳</span>
                <h4 style="margin:0; color:#92400e;">Loan Reports</h4>
            </div>
            <ul style="margin:0; padding-left:20px; color:#92400e;">
                <li style="margin-bottom:8px;">Loan Portfolio</li>
                <li style="margin-bottom:8px;">Loan Performance</li>
                <li style="margin-bottom:8px;">Arrears Report</li>
                <li style="margin-bottom:8px;">Repayment Schedule</li>
            </ul>
        </div>

        <!-- Transaction Reports -->
        <div style="background:#eff6ff; border:2px solid #bfdbfe; border-radius:8px; padding:20px; transition:all 0.3s ease;">
            <div style="display:flex; align-items:center; margin-bottom:15px;">
                <span style="font-size:24px; margin-right:10px;">💸</span>
                <h4 style="margin:0; color:#1e40af;">Transaction Reports</h4>
            </div>
            <ul style="margin:0; padding-left:20px; color:#1e40af;">
                <li style="margin-bottom:8px;">Daily Transactions</li>
                <li style="margin-bottom:8px;">Monthly Summary</li>
                <li style="margin-bottom:8px;">Audit Trail</li>
                <li style="margin-bottom:8px;">Reconciliation Report</li>
            </ul>
        </div>

        <!-- Compliance Reports -->
        <div style="background:#f3e8ff; border:2px solid #c7d2fa; border-radius:8px; padding:20px; transition:all 0.3s ease;">
            <div style="display:flex; align-items:center; margin-bottom:15px;">
                <span style="font-size:24px; margin-right:10px;">🔐</span>
                <h4 style="margin:0; color:#6366f1;">Compliance Reports</h4>
            </div>
            <ul style="margin:0; padding-left:20px; color:#6366f1;">
                <li style="margin-bottom:8px;">Regulatory Compliance</li>
                <li style="margin-bottom:8px;">Audit Compliance</li>
                <li style="margin-bottom:8px;">Risk Assessment</li>
                <li style="margin-bottom:8px;">Annual Returns</li>
            </ul>
        </div>
    </div>
</div>

<!-- Report Generation Tools -->
<div style="background:white; padding:25px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.1);">
    <h3 style="margin:0 0 20px 0; color:#1f2937; display:flex; align-items:center;">
        <span style="margin-right:10px;">⚙️</span>
        Report Generation Tools
    </h3>
    
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:15px;">
        <div style="background:#f8fafc; border:1px solid #e5e7eb; border-radius:8px; padding:20px; text-align:center;">
            <span style="font-size:32px; margin-bottom:10px; display:block;">📅</span>
            <h4 style="margin:0 0 10px 0; color:#374151;">Monthly Reports</h4>
            <p style="color:#6b7280; margin-bottom:15px;">Generate comprehensive monthly SACCO reports</p>
            <button style="background:#3b82f6; color:white; padding:12px 20px; border:none; border-radius:6px; cursor:pointer; font-weight:600;">Generate Monthly Report</button>
        </div>
        
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:20px; text-align:center;">
            <span style="font-size:32px; margin-bottom:10px; display:block;">📊</span>
            <h4 style="margin:0 0 10px 0; color:#166534;">Annual Reports</h4>
            <p style="color:#6b7280; margin-bottom:15px;">Generate annual financial statements and summaries</p>
            <button style="background:#059669; color:white; padding:12px 20px; border:none; border-radius:6px; cursor:pointer; font-weight:600;">Generate Annual Report</button>
        </div>
        
        <div style="background:#fef3c7; border:1px solid #fbbf24; border-radius:8px; padding:20px; text-align:center;">
            <span style="font-size:32px; margin-bottom:10px; display:block;">🎯</span>
            <h4 style="margin:0 0 10px 0; color:#92400e;">Custom Reports</h4>
            <p style="color:#6b7280; margin-bottom:15px;">Create custom reports with specific parameters</p>
            <button style="background:#dc2626; color:white; padding:12px 20px; border:none; border-radius:6px; cursor:pointer; font-weight:600;">Create Custom Report</button>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div style="background:white; padding:25px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.1);">
    <h3 style="margin:0 0 20px 0; color:#1f2937; display:flex; align-items:center;">
        <span style="margin-right:10px;">🕐</span>
        Recent Report Activity
    </h3>
    
    <div style="text-align:center; padding:40px; color:#6b7280;">
        <div style="font-size:48px; margin-bottom:15px;">📄</div>
        <h4 style="margin:0; color:#374151;">No Recent Reports Generated</h4>
        <p style="margin:10px 0 0 0; color:#6b7280;">Start generating reports to see your activity history here.</p>
        <div style="margin-top:20px;">
            <a href="{{ route('admin.financials.index') }}" style="background:#3b82f6; color:white; padding:12px 24px; border-radius:8px; text-decoration:none; display:inline-block;">Go to Financial Data</a>
        </div>
    </div>
</div>

@endsection
