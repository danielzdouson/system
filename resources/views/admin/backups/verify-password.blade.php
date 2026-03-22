@extends('layouts.admin')

@section('title', 'Verify Access - Backup Management')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-warning text-white text-center py-4">
                    <i class="fas fa-shield-alt fa-3x mb-3"></i>
                    <h4 class="mb-0">Secure Area - Password Required</h4>
                </div>
                <div class="card-body p-5">
                    <div class="alert alert-warning border-left-warning" style="border-left: 4px solid #f6c23e;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                            <div>
                                <strong>Security Notice</strong>
                                <p class="mb-0 small">Backup management contains sensitive system data. Please verify your password to continue.</p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.backups.verify-password') }}" id="verifyForm">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold">
                                <i class="fas fa-lock me-2"></i>Enter Your Password
                            </label>
                            <input type="password" 
                                   class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Enter your admin password"
                                   required 
                                   autofocus>
                            @error('password')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                            <small class="form-text text-muted mt-2">
                                <i class="fas fa-info-circle me-1"></i>
                                Use the same password you use to log in to the system.
                            </small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning btn-lg" id="verifyBtn">
                                <i class="fas fa-unlock me-2"></i>Verify & Continue
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                            </a>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-database me-2"></i>What You'll Access:
                        </h6>
                        <div class="row text-center">
                            <div class="col-4">
                                <i class="fas fa-download fa-2x text-primary mb-2"></i>
                                <p class="small mb-0">Download Backups</p>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-plus-circle fa-2x text-success mb-2"></i>
                                <p class="small mb-0">Create Backups</p>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-undo fa-2x text-warning mb-2"></i>
                                <p class="small mb-0">Restore System</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center bg-light">
                    <small class="text-muted">
                        <i class="fas fa-lock me-1"></i>
                        Your session will remain authenticated for this browser session
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#verifyForm').on('submit', function() {
        const btn = $('#verifyBtn');
        btn.prop('disabled', true)
           .html('<i class="fas fa-spinner fa-spin me-2"></i>Verifying...');
    });

    // Auto-focus password field
    $('#password').focus();
});
</script>
@endsection
