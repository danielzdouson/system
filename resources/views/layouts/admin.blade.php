<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'SACCO System')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f4f6f8;
        }

        .header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: #ffffff;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-bottom: 3px solid #3b82f6;
        }

        .header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #1e293b 0%, #111827 100%);
            color: #ffffff;
            padding: 25px 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar h3 {
            margin-top: 0;
            font-size: 14px;
            text-transform: uppercase;
            opacity: 0.9;
            font-weight: 600;
            margin-bottom: 25px;
            letter-spacing: 1px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            color: #e5e7eb;
            text-decoration: none;
            padding: 12px 16px;
            font-size: 14px;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            font-weight: 500;
        }

        .sidebar a:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: #3b82f6;
            transform: translateX(5px);
        }

        .sidebar a.active {
            background-color: rgba(59, 130, 246, 0.2);
            border-left-color: #3b82f6;
            color: #ffffff;
        }

        .nav-link {
            display: flex;
            align-items: center;
            color: #e5e7eb;
            text-decoration: none;
            padding: 12px 16px;
            font-size: 14px;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.3s ease;
        }

        .nav-link:hover {
            color: #ffffff;
            background-color: rgba(59, 130, 246, 0.3);
            border-left-color: #3b82f6;
            transform: translateX(5px);
        }

        .nav-link:hover::before {
            left: 100%;
        }

        .nav-icon {
            margin-right: 12px;
            font-size: 18px;
            z-index: 1;
            position: relative;
        }

        .dashboard-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .card-icon {
            font-size: 24px;
            margin-bottom: 10px;
            display: block;
        }

        .action-card {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            text-decoration: none;
            color: #374151;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
            transition: left 0.3s ease;
        }

        .action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            border-color: #3b82f6;
        }

        .action-card:hover::before {
            left: 100%;
        }

        .action-icon {
            font-size: 32px;
            margin-bottom: 10px;
            display: block;
        }

        .action-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .action-desc {
            font-size: 12px;
            color: #6b7280;
            line-height: 1.4;
        }

        .content {
            flex: 1;
            padding: 20px;
        }

        .card {
            background: #ffffff;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .nav-submenu {
            margin-left: 30px;
            margin-top: 5px;
            margin-bottom: 10px;
            display: none;
        }

        .nav-submenu.show {
            display: block;
        }

        .nav-dropdown {
            position: relative;
        }

        .nav-dropdown-toggle {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-dropdown-icon {
            margin-left: auto;
            transition: transform 0.3s ease;
            font-size: 12px;
        }

        .nav-dropdown.open .nav-dropdown-icon {
            transform: rotate(180deg);
        }

        .nav-sublink {
            display: block;
            padding: 8px 15px;
            color: #6b7280;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 3px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .nav-sublink:hover {
            background: #f3f4f6;
            color: #374151;
        }

        .nav-sublink.active {
            background: #e5e7eb;
            color: #1f2937;
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>SACCO Management System</h2>
</div>

<div class="container">

    <div class="sidebar">
        <h3>📊 Navigation</h3>

        <a href="{{ route('dashboard') }}" class="nav-link">
            <span class="nav-icon">🏠</span> Dashboard
        </a>
        <a href="{{ route('admin.members.index') }}" class="nav-link">
            <span class="nav-icon">👥</span> Members
        </a>
        <a href="{{ route('admin.group-savings.dashboard') }}" class="nav-link">
            <span class="nav-icon">💰</span> Group Savings
        </a>
        <div class="nav-dropdown" id="groupLoansDropdown">
            <div class="nav-link nav-dropdown-toggle" onclick="toggleDropdown('groupLoansDropdown')">
                <div>
                    <span class="nav-icon">💳</span> Group Loans
                </div>
                <span class="nav-dropdown-icon">▼</span>
            </div>
            <div class="nav-submenu" id="groupLoansSubmenu">
                <a href="{{ route('admin.group-loans.index') }}" class="nav-sublink">
                    <span class="nav-icon">📋</span> Loan Overview
                </a>
                <a href="{{ route('admin.group-loans.requests') }}" class="nav-sublink">
                    <span class="nav-icon">📝</span> Loan Requests
                </a>
                <a href="{{ route('admin.group-loans.all') }}" class="nav-sublink">
                    <span class="nav-icon">📊</span> All Loans
                </a>
                <a href="{{ route('admin.group-loans.reports') }}" class="nav-sublink">
                    <span class="nav-icon">📈</span> Loan Reports
                </a>
            </div>
        </div>
        <a href="{{ route('admin.cashflow.index') }}" class="nav-link">
            <span class="nav-icon">💸</span> Cash Flow
        </a>
        <a href="{{ route('admin.accounts.index') }}" class="nav-link">
            <span class="nav-icon">🏦</span> Accounts
        </a>
        <a href="{{ route('admin.financials.index') }}" class="nav-link">
            <span class="nav-icon">📈</span> Financials
        </a>
        <a href="{{ route('admin.reports.index') }}" class="nav-link">
            <span class="nav-icon">📊</span> Reports
        </a>
        <a href="{{ route('admin.import.index') }}" class="nav-link">
            <span class="nav-icon">📁</span> Data Import
        </a>
    </div>

    <div class="content">
        @yield('content')
    </div>

</div>

<script>
function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const submenu = dropdown.querySelector('.nav-submenu');
    
    // Toggle the dropdown
    dropdown.classList.toggle('open');
    submenu.classList.toggle('show');
    
    // Close other dropdowns
    document.querySelectorAll('.nav-dropdown').forEach(otherDropdown => {
        if (otherDropdown.id !== dropdownId) {
            otherDropdown.classList.remove('open');
            otherDropdown.querySelector('.nav-submenu').classList.remove('show');
        }
    });
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.nav-dropdown')) {
        document.querySelectorAll('.nav-dropdown').forEach(dropdown => {
            dropdown.classList.remove('open');
            dropdown.querySelector('.nav-submenu').classList.remove('show');
        });
    }
});

// Set active state for current page
document.addEventListener('DOMContentLoaded', function() {
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-link, .nav-sublink');
    
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
        }
    });
});
</script>

</body>
</html>
