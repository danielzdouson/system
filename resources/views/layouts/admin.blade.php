<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'SACCO System')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- PWA Meta Tags -->
    <meta name="application-name" content="SACCO Management System">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SACCO">
    <meta name="description" content="Complete SACCO management system for members, loans, savings, and financial operations">
    <meta name="theme-color" content="#3b82f6">
    
    <!-- Cache Control - Prevent caching issues -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    
    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" sizes="72x72" href="/images/icons/icon-72x72.png">
    <link rel="apple-touch-icon" sizes="96x96" href="/images/icons/icon-96x96.png">
    <link rel="apple-touch-icon" sizes="128x128" href="/images/icons/icon-128x128.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/images/icons/icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/images/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/images/icons/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="384x384" href="/images/icons/icon-384x384.png">
    <link rel="apple-touch-icon" sizes="512x512" href="/images/icons/icon-512x512.png">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="/images/icons/icon-72x72.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/icons/icon-72x72.png">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    @stack('styles')

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f4f6f8;
            overflow-x: hidden;
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
            position: relative;
            flex-direction: row;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #1e293b 0%, #111827 100%);
            color: #ffffff;
            padding: 25px 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            flex-shrink: 0;
            position: relative;
            z-index: 10;
            order: 1;
            margin: 0;
            left: 0;
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
            overflow-x: visible;
            position: relative;
            order: 2;
            width: calc(100% - 250px);
            margin-left: 0;
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

        /* Gradient utilities */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%) !important;
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%) !important;
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%) !important;
        }

        /* Icon box enhancements */
        .icon-box {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .icon-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        /* Card hover effects */
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        /* Table enhancements */
        .table-hover tbody tr:hover {
            background-color: rgba(59, 130, 246, 0.05);
        }

        /* Badge improvements */
        .badge {
            font-weight: 500;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="d-flex justify-content-between align-items-center">
        <h2><i class="fas fa-university me-2"></i>SACCO Management System</h2>
    </div>
</div>

<div class="container">

    <div class="sidebar">
        <h3><i class="fas fa-compass me-2"></i>Navigation</h3>

        <a href="{{ route('dashboard') }}" class="nav-link">
            <span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span> Dashboard
        </a>
        <a href="{{ route('admin.members.index') }}" class="nav-link">
            <span class="nav-icon"><i class="fas fa-users"></i></span> Members
        </a>
        <a href="{{ route('admin.group-savings.dashboard') }}" class="nav-link">
            <span class="nav-icon"><i class="fas fa-piggy-bank"></i></span> Group Savings
        </a>
        <a href="{{ route('admin.fines.index') }}" class="nav-link">
            <span class="nav-icon"><i class="fas fa-gavel"></i></span> Fines Management
        </a>
        <a href="{{ route('admin.fiscal-years.index') }}" class="nav-link">
            <span class="nav-icon"><i class="fas fa-calendar-alt"></i></span> Fiscal Years
        </a>
        <div class="nav-dropdown" id="groupLoansDropdown">
            <div class="nav-link nav-dropdown-toggle" onclick="toggleDropdown('groupLoansDropdown')">
                <div>
                    <span class="nav-icon"><i class="fas fa-hand-holding-usd"></i></span> Group Loans
                </div>
                <span class="nav-dropdown-icon">▼</span>
            </div>
            <div class="nav-submenu" id="groupLoansSubmenu">
                <a href="{{ route('admin.group-loans.index') }}" class="nav-sublink">
                    <span class="nav-icon"><i class="fas fa-list"></i></span> Loan Overview
                </a>
                <a href="{{ route('admin.group-loans.requests') }}" class="nav-sublink">
                    <span class="nav-icon"><i class="fas fa-file-invoice"></i></span> Loan Requests
                </a>
                <a href="{{ route('admin.group-loans.all') }}" class="nav-sublink">
                    <span class="nav-icon"><i class="fas fa-chart-bar"></i></span> All Loans
                </a>
                <a href="{{ route('admin.group-loans.reports') }}" class="nav-sublink">
                    <span class="nav-icon"><i class="fas fa-chart-line"></i></span> Loan Reports
                </a>
            </div>
        </div>
        <a href="{{ route('admin.cashflow.index') }}" class="nav-link">
            <span class="nav-icon"><i class="fas fa-chart-line"></i></span> Cash Flow
        </a>
        <a href="{{ route('admin.investments.index') }}" class="nav-link">
            <span class="nav-icon"><i class="fas fa-chart-pie"></i></span> Investments
        </a>
        <a href="{{ route('admin.accounts.index') }}" class="nav-link">
            <span class="nav-icon"><i class="fas fa-wallet"></i></span> Accounts
        </a>
        <div class="nav-dropdown" id="documentsDropdown">
            <div class="nav-link nav-dropdown-toggle" onclick="toggleDropdown('documentsDropdown')">
                <div>
                    <span class="nav-icon"><i class="fas fa-file-alt"></i></span> Documents
                </div>
                <span class="nav-dropdown-icon">▼</span>
            </div>
            <div class="nav-submenu" id="documentsSubmenu">
                <a href="{{ route('admin.documents.index') }}" class="nav-sublink">
                    <span class="nav-icon"><i class="fas fa-upload"></i></span> Upload Documents
                </a>
                <a href="{{ route('admin.documents.uploaded-forms') }}" class="nav-sublink">
                    <span class="nav-icon"><i class="fas fa-file-contract"></i></span> Uploaded Forms
                </a>
            </div>
        </div>
        <a href="{{ route('admin.financials.index') }}" class="nav-link">
            <span class="nav-icon"><i class="fas fa-chart-pie"></i></span> Financials
        </a>
        <a href="{{ route('admin.backups.index') }}" class="nav-link">
            <span class="nav-icon"><i class="fas fa-database"></i></span> Backup Management
        </a>
        <a href="{{ route('admin.reports.index') }}" class="nav-link" style="display: none;">
            <span class="nav-icon"><i class="fas fa-file-alt"></i></span> Reports
        </a>
        <a href="{{ route('admin.import.index') }}" class="nav-link" style="display: none;">
            <span class="nav-icon"><i class="fas fa-file-import"></i></span> Data Import
        </a>
    </div>

    <div class="content">
        {{-- Warning Banner - No Fiscal Year Selected --}}
        @php
            $currentFiscalYear = \App\Services\FiscalYearContext::getCurrent();
        @endphp
        @if(!$currentFiscalYear)
            <div class="alert alert-warning alert-dismissible fade show m-3" role="alert" style="border-left: 5px solid #ffc107;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x me-3 text-warning"></i>
                    <div>
                        <h5 class="alert-heading mb-1">No Fiscal Year Selected</h5>
                        <p class="mb-0">
                            You must select a fiscal year to view data. 
                            <a href="{{ route('admin.fiscal-years.index') }}" class="alert-link fw-bold">
                                Click here to select a fiscal year <i class="fas fa-arrow-right"></i>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        @endif
        
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- PWA Service Worker Registration -->
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('ServiceWorker registered:', registration);
                
                // Check for updates periodically
                setInterval(() => {
                    registration.update();
                }, 60000); // Check every minute
            })
            .catch(err => {
                console.log('ServiceWorker registration failed:', err);
            });
    });
}

// PWA Install Prompt
let deferredPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    
    // Show install button/banner
    showInstallPromotion();
});

function showInstallPromotion() {
    // Create install banner if it doesn't exist
    if (!document.getElementById('pwa-install-banner')) {
        const banner = document.createElement('div');
        banner.id = 'pwa-install-banner';
        banner.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 15px;
            max-width: 350px;
            animation: slideIn 0.3s ease-out;
        `;
        
        banner.innerHTML = `
            <i class="fas fa-download fa-2x"></i>
            <div style="flex: 1;">
                <strong style="display: block; margin-bottom: 5px;">Install SACCO App</strong>
                <small>Get quick access from your desktop or home screen</small>
            </div>
            <button id="pwa-install-btn" style="
                background: white;
                color: #1e40af;
                border: none;
                padding: 8px 15px;
                border-radius: 5px;
                cursor: pointer;
                font-weight: bold;
            ">Install</button>
            <button id="pwa-dismiss-btn" style="
                background: transparent;
                color: white;
                border: none;
                cursor: pointer;
                font-size: 20px;
                padding: 0 5px;
            ">&times;</button>
        `;
        
        document.body.appendChild(banner);
        
        // Install button click
        document.getElementById('pwa-install-btn').addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                console.log(`User response to install prompt: ${outcome}`);
                deferredPrompt = null;
                banner.remove();
            }
        });
        
        // Dismiss button click
        document.getElementById('pwa-dismiss-btn').addEventListener('click', () => {
            banner.remove();
            localStorage.setItem('pwa-install-dismissed', Date.now());
        });
        
        // Auto-dismiss after 30 seconds
        setTimeout(() => {
            if (banner.parentElement) {
                banner.remove();
            }
        }, 30000);
    }
}

// Check if already dismissed recently (within 7 days)
const dismissed = localStorage.getItem('pwa-install-dismissed');
if (dismissed && (Date.now() - parseInt(dismissed)) < 7 * 24 * 60 * 60 * 1000) {
    // Don't show install prompt if dismissed within last 7 days
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
    });
}

// Handle successful installation
window.addEventListener('appinstalled', () => {
    console.log('PWA installed successfully');
    deferredPrompt = null;
    const banner = document.getElementById('pwa-install-banner');
    if (banner) banner.remove();
});

// Add slide-in animation
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);
</script>

@stack('scripts')

</body>
</html>
