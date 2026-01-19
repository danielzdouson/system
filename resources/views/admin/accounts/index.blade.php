@extends('layouts.admin')

@section('title', 'Member Accounts Management')

@section('content')
<div class="container-fluid" style="margin-left: 250px; padding: 20px;">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-3 text-white">
                            <i class="fas fa-users me-3"></i>
                            Member Accounts Management
                        </h1>
                        <p class="text-white fs-5 mb-0 opacity-90">
                            <i class="fas fa-user-cog me-2"></i>
                            Create and manage member user accounts
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('admin.accounts.summary') }}" class="btn btn-light btn-lg">
                            <i class="fas fa-chart-pie me-2"></i>Account Summary
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                                <i class="fas fa-users fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Members</h6>
                            <h3 class="mb-0 fw-bold">{{ $members->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                                <i class="fas fa-user-check fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">With User Accounts</h6>
                            <h3 class="mb-0 fw-bold text-success">{{ $members->whereNotNull('user')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                                <i class="fas fa-user-times fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Without User Accounts</h6>
                            <h3 class="mb-0 fw-bold text-warning">{{ $members->whereNull('user')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 text-info rounded-circle p-3">
                                <i class="fas fa-calendar fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Fiscal Year</h6>
                            <h3 class="mb-0 fw-bold">{{ $currentFiscalYear ? $currentFiscalYear->name : 'N/A' }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Members Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center p-3">
                    <h5 class="mb-0">All Members</h5>
                    <div>
                        <a href="{{ route('admin.members.index') }}" class="btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i>Add New Member
                        </a>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-bold">Member</th>
                                    <th class="fw-bold">Contact</th>
                                    <th class="fw-bold">User Account</th>
                                    <th class="fw-bold">Current Balance</th>
                                    <th class="fw-bold">Status</th>
                                    <th class="fw-bold text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($members as $member)
                                    <tr class="align-middle">
                                        <td class="py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-light rounded-circle p-3 me-3">
                                                        <i class="fas fa-user text-muted fs-5"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1 fw-bold">{{ $member->first_name }} {{ $member->last_name }}</h6>
                                                    <small class="text-muted">ID: {{ $member->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <div>
                                                @if($member->email)
                                                    <div class="mb-2"><i class="fas fa-envelope text-muted me-2"></i>{{ $member->email }}</div>
                                                @endif
                                                @if($member->phone)
                                                    <div><i class="fas fa-phone text-muted me-2"></i>{{ $member->phone }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            @if($member->user)
                                                <div>
                                                    <span class="badge bg-success mb-2 fs-6">
                                                        <i class="fas fa-check me-1"></i>Active
                                                    </span>
                                                    <br><small class="text-muted">{{ $member->user->email }}</small>
                                                    @if($member->user->deleted_at)
                                                        <br><span class="badge bg-secondary">Deactivated</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="badge bg-warning fs-6">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>No Account
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            @if($member->memberAccounts->isNotEmpty())
                                                @php
                                                    $currentAccount = null;
                                                    if($currentFiscalYear) {
                                                        $currentAccount = $member->memberAccounts->firstWhere('fiscal_year_id', $currentFiscalYear->id);
                                                    }
                                                @endphp
                                                @if($currentAccount)
                                                    <h6 class="mb-0">UGX {{ number_format($currentAccount->savings_balance ?: 0, 0) }}</h6>
                                                @else
                                                    <span class="text-muted">No account this year</span>
                                                @endif
                                            @else
                                                <span class="text-muted">No accounts</span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            @if($member->deleted_at)
                                                <span class="badge bg-secondary">Inactive</span>
                                            @else
                                                <span class="badge bg-success">Active</span>
                                            @endif
                                        </td>
                                        <td class="text-end py-3">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.accounts.show', $member->id) }}" class="btn btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </a>
                                                
                                                @if($member->user)
                                                    <!-- Password Reset Modal Trigger -->
                                                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#resetPasswordModal{{ $member->id }}">
                                                        <i class="fas fa-key me-1"></i>Reset
                                                    </button>
                                                    
                                                    <!-- Toggle Status -->
                                                    <form action="{{ route('admin.accounts.toggle-status', $member->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-{{ $member->user->deleted_at ? 'success' : 'danger' }}" 
                                                                title="{{ $member->user->deleted_at ? 'Activate Account' : 'Deactivate Account' }}"
                                                                onclick="return confirm('Are you sure you want to {{ $member->user->deleted_at ? 'activate' : 'deactivate' }} this account?')">
                                                            <i class="fas fa-{{ $member->user->deleted_at ? 'check' : 'ban' }} me-1"></i>{{ $member->user->deleted_at ? 'Activate' : 'Deactivate' }}
                                                        </button>
                                                    </form>
                                                @else
                                                    <!-- Create User Account Modal Trigger -->
                                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createUserModal{{ $member->id }}">
                                                        <i class="fas fa-user-plus me-1"></i>Create Account
                                                    </button>
                                                @endif
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

<!-- Create User Account Modals -->
@foreach($members as $member)
    @if(!$member->user)
        <div class="modal fade" id="createUserModal{{ $member->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create User Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.accounts.create-user', $member->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <strong>Member:</strong> {{ $member->first_name }} {{ $member->last_name }} (ID: {{ $member->id }})
                            </div>
                            
                            <div class="mb-3">
                                <label for="email_{{ $member->id }}" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email_{{ $member->id }}" name="email" required>
                                <div class="form-text">This will be the login email for the member.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password_{{ $member->id }}" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password_{{ $member->id }}" name="password" required minlength="8">
                            </div>
                            
                            <div class="mb-3">
                                <label for="password_confirmation_{{ $member->id }}" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation_{{ $member->id }}" name="password_confirmation" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Create Account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Password Reset Modals -->
    @if($member->user)
        <div class="modal fade" id="resetPasswordModal{{ $member->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reset Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.accounts.reset-password', $member->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <strong>Member:</strong> {{ $member->first_name }} {{ $member->last_name }}<br>
                                <strong>Email:</strong> {{ $member->user->email }}
                            </div>
                            
                            <div class="mb-3">
                                <label for="reset_password_{{ $member->id }}" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="reset_password_{{ $member->id }}" name="password" required minlength="8">
                            </div>
                            
                            <div class="mb-3">
                                <label for="reset_password_confirmation_{{ $member->id }}" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="reset_password_confirmation_{{ $member->id }}" name="password_confirmation" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning">Reset Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach
@endsection
