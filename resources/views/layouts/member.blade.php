<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Member Portal</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome 6 -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <!-- Animate.css -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

        <style>
            :root {
                --primary-color: #4f46e5;
                --primary-dark: #4338ca;
                --secondary-color: #06b6d4;
                --success-color: #10b981;
                --danger-color: #ef4444;
                --warning-color: #f59e0b;
                --info-color: #3b82f6;
                --light-bg: #f8fafc;
                --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
                --card-hover-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            }

            body {
                font-family: 'Figtree', sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
            }

            .navbar {
                background: rgba(255, 255, 255, 0.95) !important;
                backdrop-filter: blur(10px);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                transition: all 0.3s ease;
            }

            .navbar-brand {
                font-weight: 700;
                font-size: 1.5rem;
                background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                transition: transform 0.3s ease;
            }

            .navbar-brand:hover {
                transform: translateY(-2px);
            }

            .nav-link {
                font-weight: 500;
                border-radius: 8px !important;
                transition: all 0.3s ease;
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
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                transition: left 0.5s;
            }

            .nav-link:hover::before {
                left: 100%;
            }

            .nav-link.active {
                background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)) !important;
                box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3);
            }

            .main-content {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                margin: 20px;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                min-height: calc(100vh - 200px);
            }

            .btn-primary {
                background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
                border: none;
                border-radius: 10px;
                font-weight: 600;
                transition: all 0.3s ease;
                box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3);
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
            }

            .btn-danger {
                background: linear-gradient(135deg, var(--danger-color), #dc2626);
                border: none;
                border-radius: 10px;
                font-weight: 600;
                transition: all 0.3s ease;
            }

            .btn-danger:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3);
            }

            .card {
                border: none;
                border-radius: 15px;
                box-shadow: var(--card-shadow);
                transition: all 0.3s ease;
                overflow: hidden;
            }

            .card:hover {
                box-shadow: var(--card-hover-shadow);
                transform: translateY(-2px);
            }

            .card-header {
                background: linear-gradient(135deg, #f8fafc, #e2e8f0);
                border-bottom: 2px solid #e2e8f0;
                font-weight: 600;
            }

            .footer {
                background: rgba(255, 255, 255, 0.95) !important;
                backdrop-filter: blur(10px);
                border-top: 1px solid rgba(226, 232, 240, 0.8);
            }

            .user-avatar {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 600;
                font-size: 1.1rem;
            }

            .mobile-menu-btn {
                display: none;
            }

            @media (max-width: 768px) {
                .desktop-nav {
                    display: none !important;
                }
                
                .mobile-menu-btn {
                    display: block !important;
                }
                
                .main-content {
                    margin: 10px;
                    border-radius: 15px;
                }
                
                .container-fluid {
                    padding-left: 10px;
                    padding-right: 10px;
                }
                
                .card-body {
                    padding: 1rem !important;
                }
                
                .table-responsive {
                    font-size: 0.875rem;
                }
                
                .btn {
                    font-size: 0.875rem;
                    padding: 0.5rem 1rem;
                }
                
                .badge {
                    font-size: 0.75rem;
                }
                
                h1, h2 {
                    font-size: 1.5rem;
                }
                
                h3, h4 {
                    font-size: 1.25rem;
                }
                
                h5 {
                    font-size: 1.1rem;
                }
                
                .user-avatar {
                    width: 35px;
                    height: 35px;
                    font-size: 0.9rem;
                }
                
                .progress {
                    height: 6px !important;
                }
                
                .mobile-hidden {
                    display: none !important;
                }
                
                .mobile-full {
                    width: 100% !important;
                }
                
                .mobile-center {
                    text-align: center !important;
                }
                
                .mobile-stack {
                    flex-direction: column !important;
                }
                
                .mobile-gap-2 {
                    gap: 0.5rem !important;
                }
            }
            
            @media (max-width: 576px) {
                .container-fluid {
                    padding-left: 5px;
                    padding-right: 5px;
                }
                
                .card-body {
                    padding: 0.75rem !important;
                }
                
                .btn-sm {
                    font-size: 0.75rem;
                    padding: 0.375rem 0.75rem;
                }
                
                .table-responsive {
                    font-size: 0.8rem;
                }
                
                .badge {
                    font-size: 0.7rem;
                    padding: 0.25rem 0.5rem;
                }
                
                h1, h2 {
                    font-size: 1.25rem;
                }
                
                h3, h4 {
                    font-size: 1.1rem;
                }
                
                h5 {
                    font-size: 1rem;
                }
                
                .mobile-xs-hidden {
                    display: none !important;
                }
            }

            .gradient-bg {
                background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            }

            .text-gradient {
                background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
        </style>

        <!-- PWA Meta Tags -->
        <meta name="application-name" content="SACCO Management System">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="SACCO">
        <meta name="theme-color" content="#4f46e5">
        
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
    </head>
        <div class="min-h-screen">
            <!-- Member Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light sticky-top">
                <div class="container-fluid">
                    <!-- Logo -->
                    <a class="navbar-brand" href="{{ route('member.dashboard') }}">
                        <i class="fas fa-piggy-bank me-2"></i>
                        SACCO Portal
                    </a>

                    <!-- Mobile menu button -->
                    <button class="navbar-toggler mobile-menu-btn" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarNav">
                        <!-- Navigation Links -->
                        <ul class="navbar-nav mx-auto desktop-nav">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('member.dashboard') ? 'active' : '' }}" 
                                   href="{{ route('member.dashboard') }}">
                                    <i class="fas fa-home me-2"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('member.transactions') ? 'active' : '' }}" 
                                   href="{{ route('member.transactions') }}">
                                    <i class="fas fa-exchange-alt me-2"></i>Transactions
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('member.loans') ? 'active' : '' }}" 
                                   href="{{ route('member.loans') }}">
                                    <i class="fas fa-hand-holding-usd me-2"></i>My Loans
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('member.documents.*') ? 'active' : '' }}" 
                                   href="{{ route('member.documents.index') }}">
                                    <i class="fas fa-file-alt me-2"></i>Documents
                                </a>
                            </li>
                            <li class="nav-item" style="display: none;">
                                <a class="nav-link {{ request()->routeIs('member.documents.pending-guarantees') ? 'active' : '' }}" 
                                   href="{{ route('member.documents.pending-guarantees') }}">
                                    <i class="fas fa-handshake me-2"></i>Pending Guarantees
                                </a>
                            </li>
                            <li class="nav-item" style="display: none;">
                                <a class="nav-link {{ request()->routeIs('member.documents.guarantor-history') ? 'active' : '' }}" 
                                   href="{{ route('member.documents.guarantor-history') }}">
                                    <i class="fas fa-history me-2"></i>Guarantor History
                                </a>
                            </li>
                        </ul>

                        <!-- Right side: Profile & Logout -->
                        <div class="d-flex align-items-center">
                            <!-- Profile Dropdown -->
                            <div class="dropdown me-3">
                                <button class="btn btn-link text-decoration-none dropdown-toggle d-flex align-items-center"
                                        type="button" id="profileDropdown" data-bs-toggle="dropdown">
                                    @if(Auth::user()->profile_photo)
                                        <img src="/storage/{{ Auth::user()->profile_photo }}"
                                             alt="Profile"
                                             class="rounded-circle me-2"
                                             style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #4f46e5;"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="user-avatar me-2" style="display: none;">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        </div>
                                    @else
                                        <div class="user-avatar me-2">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="d-none d-md-inline text-muted">{{ Auth::user()->name ?? 'Profile' }}</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="fas fa-user me-2"></i>Edit Profile
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="main-content py-4">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="footer py-3 mt-4">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="text-sm text-muted">
                                © {{ date('Y') }} {{ config('app.name', 'SACCO System') }}. All rights reserved.
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                Secure Banking Portal
                            </small>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        <!-- Bootstrap JS Bundle -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            // Add smooth scrolling and animations
            document.addEventListener('DOMContentLoaded', function() {
                // Animate cards on scroll
                const observerOptions = {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                };

                const observer = new IntersectionObserver(function(entries) {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                        }
                    });
                }, observerOptions);

                document.querySelectorAll('.card').forEach(card => {
                    observer.observe(card);
                });
                
                // Add ripple effect to buttons
                document.querySelectorAll('.btn').forEach(button => {
                    button.addEventListener('click', function(e) {
                        const ripple = document.createElement('span');
                        const rect = this.getBoundingClientRect();
                        const size = Math.max(rect.width, rect.height);
                        const x = e.clientX - rect.left - size / 2;
                        const y = e.clientY - rect.top - size / 2;
                        
                        ripple.style.width = ripple.style.height = size + 'px';
                        ripple.style.left = x + 'px';
                        ripple.style.top = y + 'px';
                        ripple.classList.add('ripple');
                        
                        this.appendChild(ripple);
                        
                        setTimeout(() => {
                            ripple.remove();
                        }, 600);
                    });
                });
                
                // Add hover effects to cards
                document.querySelectorAll('.card').forEach(card => {
                    card.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-5px)';
                        this.style.boxShadow = '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)';
                    });
                    
                    card.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateY(0)';
                        this.style.boxShadow = 'var(--card-shadow)';
                    });
                });
                
                // Add smooth scroll behavior
                document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                    anchor.addEventListener('click', function (e) {
                        e.preventDefault();
                        const target = document.querySelector(this.getAttribute('href'));
                        if (target) {
                            target.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    });
                });
                
                // Add loading states to forms
                document.querySelectorAll('form').forEach(form => {
                    form.addEventListener('submit', function() {
                        const submitBtn = this.querySelector('button[type="submit"]');
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
                        }
                    });
                });
                
                // Add tooltips
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
        </script>
        
        <style>
            /* Ripple effect */
            .btn {
                position: relative;
                overflow: hidden;
            }
            
            .ripple {
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.5);
                transform: scale(0);
                animation: ripple-animation 0.6s ease-out;
                pointer-events: none;
            }
            
            @keyframes ripple-animation {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
            
            /* Smooth transitions */
            * {
                transition: all 0.3s ease;
            }
            
            /* Custom scrollbar */
            ::-webkit-scrollbar {
                width: 8px;
            }
            
            ::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 10px;
            }
            
            ::-webkit-scrollbar-thumb {
                background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
                border-radius: 10px;
            }
            
            ::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
            }
            
            /* Loading animation */
            .loading {
                pointer-events: none;
                opacity: 0.6;
            }
            
            /* Focus states */
            .form-control:focus {
                border-color: var(--primary-color);
                box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
            }
            
            .form-select:focus {
                border-color: var(--primary-color);
                box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
            }
            
            /* Enhanced table hover */
            .table-hover tbody tr:hover {
                background-color: rgba(79, 70, 229, 0.05);
                transform: scale(1.01);
            }
            
            /* Mobile optimizations */
            @media (max-width: 768px) {
                .table-responsive {
                    border-radius: 10px;
                }
                
                .btn-group {
                    flex-direction: column;
                    width: 100%;
                }
                
                .btn-group .btn {
                    border-radius: 8px !important;
                    margin-bottom: 5px;
                }
            }
        </style>

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
            background: linear-gradient(135deg, #4f46e5 0%, #764ba2 100%);
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
                color: #4f46e5;
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
    </body>
</html>
