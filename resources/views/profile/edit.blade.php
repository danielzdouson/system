@extends('layouts.member')

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card gradient-bg text-white border-0 animate__animated animate__fadeInDown">
                <div class="card-body py-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2 animate__animated animate__fadeInLeft">
                                <i class="fas fa-user-cog me-3"></i>Profile Settings
                            </h2>
                            <p class="mb-0 opacity-75 animate__animated animate__fadeInLeft animate__delay-1s">
                                Manage your personal information and security settings
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="animate__animated animate__fadeInRight">
                                <a href="{{ route('member.dashboard') }}" class="btn btn-light">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Profile Information -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm animate__animated animate__fadeInUp">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-user text-primary me-2"></i>
                        Profile Information
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="#" id="profileForm">
                        @csrf
                        <div class="text-center mb-4">
                            <div class="user-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="changeAvatar()">
                                <i class="fas fa-camera me-2"></i>Change Avatar
                            </button>
                        </div>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ Auth::user()->name }}" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ Auth::user()->email }}" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       placeholder="Enter your phone number">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="role" class="form-label">Account Role</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-shield-alt"></i></span>
                                <input type="text" class="form-control" id="role" name="role" 
                                       value="{{ ucfirst(Auth::user()->role) }}" readonly>
                            </div>
                            <small class="text-muted">Contact administrator to change your role</small>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Security Settings -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm animate__animated animate__fadeInUp animate__delay-1s">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-lock text-warning me-2"></i>
                        Security Settings
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="#" id="passwordForm">
                        @csrf
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input type="password" class="form-control" id="current_password" 
                                       name="current_password" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="new_password" 
                                       name="new_password" required minlength="8">
                            </div>
                            <small class="text-muted">Password must be at least 8 characters</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password_confirmation" 
                                       name="password_confirmation" required>
                            </div>
                        </div>
                        
                        <!-- Password Strength Indicator -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">Password Strength</small>
                                <small id="strengthText" class="text-muted">Enter a password</small>
                            </div>
                            <div class="progress" style="height: 4px;">
                                <div id="strengthBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-2"></i>Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Additional Settings -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm animate__animated animate__fadeInUp animate__delay-2s">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-bell text-info me-2"></i>
                        Notification Preferences
                    </h5>
                </div>
                <div class="card-body">
                    <form id="notificationForm">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="email_notifications" checked>
                                <label class="form-check-label" for="email_notifications">
                                    Email Notifications
                                </label>
                            </div>
                            <small class="text-muted">Receive email updates about your account</small>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="sms_notifications">
                                <label class="form-check-label" for="sms_notifications">
                                    SMS Notifications
                                </label>
                            </div>
                            <small class="text-muted">Receive SMS alerts for important transactions</small>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="payment_reminders" checked>
                                <label class="form-check-label" for="payment_reminders">
                                    Payment Reminders
                                </label>
                            </div>
                            <small class="text-muted">Get reminded about upcoming loan payments</small>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-save me-2"></i>Save Preferences
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm animate__animated animate__fadeInUp animate__delay-3s">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-cog text-secondary me-2"></i>
                        Account Settings
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Two-Factor Authentication</h6>
                                <small class="text-muted">Add an extra layer of security</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="two_factor">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Account Status</h6>
                                <small class="text-muted">Current account status</small>
                            </div>
                            <span class="badge bg-success">Active</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Member Since</h6>
                                <small class="text-muted">Account creation date</small>
                            </div>
                            <span class="text-muted">{{ Auth::user()->created_at->format('M j, Y') }}</span>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid">
                        <button type="button" class="btn btn-outline-danger" onclick="confirmDeactivate()">
                            <i class="fas fa-user-times me-2"></i>Deactivate Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
// Password strength checker
function checkPasswordStrength(password) {
    let strength = 0;
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]+/)) strength++;
    if (password.match(/[A-Z]+/)) strength++;
    if (password.match(/[0-9]+/)) strength++;
    if (password.match(/[$@#&!]+/)) strength++;
    
    const strengthPercentage = (strength / 5) * 100;
    strengthBar.style.width = strengthPercentage + '%';
    
    // Update bar color and text
    strengthBar.className = 'progress-bar';
    if (strength <= 2) {
        strengthBar.classList.add('bg-danger');
        strengthText.textContent = 'Weak';
        strengthText.className = 'text-danger';
    } else if (strength <= 3) {
        strengthBar.classList.add('bg-warning');
        strengthText.textContent = 'Medium';
        strengthText.className = 'text-warning';
    } else {
        strengthBar.classList.add('bg-success');
        strengthText.textContent = 'Strong';
        strengthText.className = 'text-success';
    }
}

// Form handlers
document.getElementById('new_password')?.addEventListener('input', function() {
    checkPasswordStrength(this.value);
});

document.getElementById('profileForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    // Add loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
    
    // Simulate API call
    setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Updated!';
        setTimeout(() => {
            submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Update Profile';
        }, 2000);
    }, 1500);
});

document.getElementById('passwordForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;
    
    if (newPassword !== confirmPassword) {
        alert('Passwords do not match!');
        return;
    }
    
    // Add loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Changing...';
    
    // Simulate API call
    setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Changed!';
        this.reset();
        document.getElementById('strengthBar').style.width = '0%';
        document.getElementById('strengthText').textContent = 'Enter a password';
        document.getElementById('strengthText').className = 'text-muted';
        setTimeout(() => {
            submitBtn.innerHTML = '<i class="fas fa-key me-2"></i>Change Password';
        }, 2000);
    }, 1500);
});

document.getElementById('notificationForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
    
    setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Saved!';
        setTimeout(() => {
            submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Preferences';
        }, 2000);
    }, 1000);
});

function changeAvatar() {
    alert('Avatar upload functionality would be implemented here');
}

function confirmDeactivate() {
    if (confirm('Are you sure you want to deactivate your account? This action cannot be undone.')) {
        alert('Account deactivation would be processed here');
    }
}
</script>
@endsection
