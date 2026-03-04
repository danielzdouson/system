@extends('layouts.admin')

@section('title', 'Members Management')

@push('styles')
<style>
/* Hide admin layout header and sidebar */
.header {
    visibility: hidden;
    opacity: 0;
    height: 0;
    overflow: hidden;
    margin: 0;
    padding: 0;
}

.sidebar {
    visibility: hidden;
    opacity: 0;
    width: 0;
    overflow: hidden;
    padding: 0;
    margin: 0;
}

.content {
    width: 100% !important;
    margin-left: 0 !important;
    padding: 0 !important;
}

.container {
    display: block !important;
}

/* Modern Members Management Styles */
.members-management {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 2rem 1rem;
    margin: 0;
    border-radius: 0;
}

.management-container {
    max-width: 1400px;
    margin: 0 auto;
}

/* Page Header */
.page-header {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 0;
}

.page-subtitle {
    color: #6c757d;
    font-size: 1.1rem;
    margin-top: 0.5rem;
}

/* Advanced Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--card-color) 0%, var(--card-color-light) 100%);
}

.stat-card.total {
    --card-color: #667eea;
    --card-color-light: #764ba2;
}

.stat-card.active {
    --card-color: #10b981;
    --card-color-light: #059669;
}

.stat-card.recent {
    --card-color: #f59e0b;
    --card-color-light: #ef4444;
}

.stat-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.stat-header {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
}

.stat-icon {
    width: 70px;
    height: 70px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    margin-right: 1.5rem;
    background: linear-gradient(135deg, var(--card-color) 0%, var(--card-color-light) 100%);
    color: white;
    box-shadow: 0 8px 20px rgba(var(--card-color-rgb), 0.3);
}

.stat-content h4 {
    margin: 0;
    font-size: 1rem;
    opacity: 0.8;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-content p {
    margin: 0.5rem 0 0 0;
    font-size: 2.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--card-color) 0%, var(--card-color-light) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Search and Filter Bar */
.search-filter-bar {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.search-filter-form {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr auto;
    gap: 1rem;
    align-items: end;
}

.search-input {
    position: relative;
}

.search-input input {
    width: 100%;
    padding: 1rem 1rem 1rem 3rem;
    border: 2px solid #e3e6f6;
    border-radius: 15px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: white;
}

.search-input i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
}

/* Enhanced Member Form */
.member-form-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.form-header {
    display: flex;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f8f9fa;
}

.form-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.member-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-input, .form-textarea {
    padding: 1rem;
    border: 2px solid #e3e6f6;
    border-radius: 15px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: white;
    width: 100%;
    box-sizing: border-box;
}

.form-textarea {
    resize: vertical;
    min-height: 100px;
}

.form-input:focus, .form-textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    transform: translateY(-2px);
}

/* Enhanced Members Table */
.members-table-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.table-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.table-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.table-container {
    overflow-x: auto;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
}

.members-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    min-width: 800px;
}

.members-table th {
    background: linear-gradient(135deg, #495057 0%, #343a40 100%);
    color: white;
    font-weight: 700;
    text-align: left;
    padding: 1.25rem;
    border: none;
    white-space: nowrap;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.members-table th:first-child {
    border-top-left-radius: 15px;
}

.members-table th:last-child {
    border-top-right-radius: 15px;
}

.members-table td {
    padding: 1.25rem;
    border-bottom: 1px solid #f8f9fa;
    vertical-align: middle;
    font-size: 0.9rem;
}

.members-table tbody tr {
    transition: all 0.3s ease;
}

.members-table tbody tr:hover {
    background: #f8f9fa;
    transform: scale(1.01);
}

.member-info {
    line-height: 1.6;
}

.member-name {
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.member-detail {
    font-size: 0.85rem;
    color: #6c757d;
    margin-top: 0.25rem;
    display: block;
    line-height: 1.4;
}

/* Enhanced Buttons */
.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.2);
    transition: left 0.3s ease;
}

.btn:hover::before {
    left: 100%;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

.btn-danger {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
}

.btn-danger:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(220, 38, 38, 0.4);
}

.btn-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
}

.btn-secondary:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
}

/* Enhanced Empty State */
.empty-state {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 4rem 2rem;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.empty-icon {
    font-size: 5rem;
    margin-bottom: 2rem;
    opacity: 0.3;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.empty-title {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.empty-text {
    color: #6c757d;
    margin-bottom: 2rem;
    font-size: 1.1rem;
}

/* Enhanced Messages */
.success-message {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border-radius: 15px;
    padding: 1.25rem;
    margin-bottom: 2rem;
    animation: slideIn 0.5s ease-out;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

.error-message {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    border-radius: 15px;
    padding: 1.25rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .search-filter-form {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .header-content {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .members-management {
        padding: 1.5rem;
    }
}

@media (max-width: 768px) {
    .members-management {
        padding: 1rem 0.5rem;
        min-height: 100vh;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .member-form {
        grid-template-columns: 1fr;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .members-table {
        min-width: 600px;
    }
    
    .members-table th,
    .members-table td {
        padding: 1rem;
        font-size: 0.85rem;
    }
    
    .table-header {
        flex-direction: column;
        align-items: flex-start;
    }
}

@media (max-width: 480px) {
    .members-management {
        padding: 0.5rem 0.25rem;
        min-height: 100vh;
    }
    
    .page-header,
    .member-form-card,
    .members-table-card {
        padding: 1.5rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .members-table {
        min-width: 100%;
    }
    
    .members-table th,
    .members-table td {
        padding: 0.75rem;
        font-size: 0.8rem;
    }
    
    .btn {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
    }
}

/* Animations */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.members-management > * {
    animation: fadeIn 0.6s ease-out;
}
</style>
@endpush

@section('content')
<div class="members-management">
    <div class="management-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <div>
                    <h1 class="page-title">Members Management</h1>
                    <p class="page-subtitle">Manage and monitor all SACCO members efficiently</p>
                </div>
                <div class="table-actions">
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Advanced Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h4>Total Members</h4>
                        <p>{{ $members->count() ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="stat-card active">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-content">
                        <h4>Active Members</h4>
                        <p>{{ $members->where('status', 'active')->count() ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="stat-card recent">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h4>Recent Members</h4>
                        <p>{{ $members->where('created_at', '>=', now()->subDays(30))->count() ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Bar -->
        <div class="search-filter-bar">
            <form class="search-filter-form" method="GET" action="{{ route('admin.members.index') }}">
                <div class="search-input">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search members by name, email, phone..." value="{{ request('search') }}">
                </div>
                <div class="form-group">
                    <select name="status" class="form-input">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="form-group">
                    <select name="sort" class="form-input">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="name">Name (A-Z)</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter me-2"></i>
                    Filter
                </button>
            </form>
        </div>

        <!-- Add New Member Form -->
        <div class="member-form-card">
            <div class="form-header">
                <h3 class="form-title">
                    <i class="fas fa-user-plus me-2"></i>
                    Add New Member
                </h3>
            </div>
            
            @if(session('success'))
                <div class="success-message">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif
            
            @if($errors->any())
                <div class="error-message">
                    <h6 style="margin: 0 0 0.75rem 0; font-weight: 700;">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Please fix the following errors:
                    </h6>
                    <ul style="margin: 0; padding-left: 1.5rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ route('admin.members.store') }}" class="member-form">
                @csrf
                <div class="form-group">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" required class="form-input" placeholder="Enter first name">
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" required class="form-input" placeholder="Enter last name">
                </div>
                <div class="form-group">
                    <label class="form-label">National ID</label>
                    <input type="text" name="national_id" class="form-input" placeholder="Enter national ID">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" placeholder="Enter email address">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-input" placeholder="Enter phone number">
                </div>
                <div class="form-group">
                    <label class="form-label">Physical Address</label>
                    <textarea name="physical_address" class="form-textarea" placeholder="Enter physical address" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Postal Code</label>
                    <input type="text" name="postal_code" class="form-input" placeholder="Enter postal code">
                </div>
                <div class="form-group">
                    <label class="form-label">Next of Kin Name</label>
                    <input type="text" name="next_of_kin_name" class="form-input" placeholder="Enter next of kin name">
                </div>
                <div class="form-group">
                    <label class="form-label">Next of Kin Phone</label>
                    <input type="text" name="next_of_kin_phone" class="form-input" placeholder="Enter next of kin phone number">
                </div>
                <div class="form-group">
                    <label class="form-label">Next of Kin Relationship</label>
                    <input type="text" name="next_of_kin_relationship" class="form-input" placeholder="e.g., Spouse, Parent, Sibling">
                </div>
                
                <div style="display: flex; justify-content: flex-end; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        Save Member
                    </button>
                </div>
            </form>
        </div>

        <!-- Edit Member Form -->
        @if(isset($memberToEdit))
        <div class="member-form-card">
            <div class="form-header">
                <h3 class="form-title">
                    <i class="fas fa-user-edit me-2"></i>
                    Edit Member: {{ $memberToEdit->first_name }} {{ $memberToEdit->last_name }}
                </h3>
            </div>
            
            <form method="POST" action="{{ route('admin.members.update', $memberToEdit->id) }}" class="member-form">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" required class="form-input" value="{{ $memberToEdit->first_name }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" required class="form-input" value="{{ $memberToEdit->last_name }}">
                </div>
                <div class="form-group">
                    <label class="form-label">National ID</label>
                    <input type="text" name="national_id" class="form-input" value="{{ $memberToEdit->national_id }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="{{ $memberToEdit->email }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-input" value="{{ $memberToEdit->phone }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-input" value="{{ $memberToEdit->date_of_birth ? $memberToEdit->date_of_birth->format('Y-m-d') : '' }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Physical Address</label>
                    <textarea name="physical_address" class="form-textarea" rows="3">{{ $memberToEdit->physical_address }}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Postal Code</label>
                    <input type="text" name="postal_code" class="form-input" value="{{ $memberToEdit->postal_code }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Next of Kin Name</label>
                    <input type="text" name="next_of_kin_name" class="form-input" value="{{ $memberToEdit->next_of_kin_name }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Next of Kin Phone</label>
                    <input type="text" name="next_of_kin_phone" class="form-input" value="{{ $memberToEdit->next_of_kin_phone }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Next of Kin Relationship</label>
                    <input type="text" name="next_of_kin_relationship" class="form-input" placeholder="e.g., Spouse, Parent, Sibling" value="{{ $memberToEdit->next_of_kin_relationship }}">
                </div>
                
                <div style="display: flex; justify-content: flex-end; margin-top: 2rem; gap: 1rem;">
                    <a href="{{ route('admin.members.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        Update Member
                    </button>
                </div>
            </form>
        </div>
        @endif

        <!-- Enhanced Members Table -->
        <div class="members-table-card">
            <div class="table-header">
                <div>
                    <h3 class="table-title">
                        <i class="fas fa-users me-2"></i>
                        All Members ({{ $members->count() }})
                    </h3>
                    <p style="color: #6c757d; margin: 0.5rem 0 0 0; font-size: 0.9rem;">
                        Manage and view all registered members
                    </p>
                </div>
                <div class="table-actions">
                    <button class="btn btn-primary btn-sm" onclick="window.print()">
                        <i class="fas fa-print me-1"></i>
                        Print
                    </button>
                </div>
            </div>
            
            @if(isset($members) && $members->count() > 0)
                <div class="table-container">
                    <table class="members-table">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>Member Information</th>
                                <th>National ID</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Joined Date</th>
                                <th style="width: 140px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($members->reverse() as $member)
                                <tr>
                                    <td><strong>{{ $loop->index + 1 }}</strong></td>
                                    <td>
                                        <div class="member-info">
                                            <div class="member-name">
                                                {{ $member->first_name }} {{ $member->last_name }}
                                            </div>
                                            @if($member->national_id)
                                                <div class="member-detail">
                                                    <i class="fas fa-id-card me-1"></i>
                                                    {{ $member->national_id }}
                                                </div>
                                            @endif
                                            @if($member->email)
                                                <div class="member-detail">
                                                    <i class="fas fa-envelope me-1"></i>
                                                    {{ $member->email }}
                                                </div>
                                            @endif
                                            @if($member->phone)
                                                <div class="member-detail">
                                                    <i class="fas fa-phone me-1"></i>
                                                    {{ $member->phone }}
                                                </div>
                                            @endif
                                            @if($member->physical_address)
                                                <div class="member-detail">
                                                    <i class="fas fa-home me-1"></i>
                                                    {{ Str::limit($member->physical_address, 30) }}
                                                </div>
                                            @endif
                                            @if($member->next_of_kin_name)
                                                <div class="member-detail">
                                                    <i class="fas fa-user-friends me-1"></i>
                                                    {{ $member->next_of_kin_name }} ({{ $member->next_of_kin_relationship ?? 'Kin' }})
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge" style="background: #e3e6f6; color: #495057; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem;">
                                            {{ $member->national_id ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($member->email)
                                            <a href="mailto:{{ $member->email }}" style="color: #667eea; text-decoration: none;">
                                                {{ $member->email }}
                                            </a>
                                        @else
                                            <span style="color: #6c757d;">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($member->phone)
                                            <a href="tel:{{ $member->phone }}" style="color: #667eea; text-decoration: none;">
                                                {{ $member->phone }}
                                            </a>
                                        @else
                                            <span style="color: #6c757d;">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <i class="fas fa-calendar" style="color: #6c757d; font-size: 0.8rem;"></i>
                                            <span>{{ $member->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 0.5rem;">
                                            <a href="{{ route('admin.members.edit', $member->id) }}" class="btn btn-primary btn-sm" title="Edit Member">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.members.destroy', $member->id) }}" 
                                                  onsubmit="return confirm('Are you sure you want to delete this member: {{ $member->first_name }} {{ $member->last_name }}?');" 
                                                  style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete Member">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="empty-title">No Members Found</div>
                    <div class="empty-text">
                        Start by adding your first member to get started with your SACCO management.
                    </div>
                    <a href="#add-member" class="btn btn-primary" onclick="document.querySelector('.member-form-card').scrollIntoView({behavior: 'smooth'});">
                        <i class="fas fa-user-plus me-2"></i>
                        Add First Member
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced success message handling
    const successMessage = document.querySelector('.success-message');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.opacity = '0';
            successMessage.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 500);
        }, 5000);
    }
    
    // Enhanced table row interactions
    const tableRows = document.querySelectorAll('.members-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
            this.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.1)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = 'none';
        });
    });
    
    // Search functionality
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('.members-table tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                    row.style.animation = 'fadeIn 0.3s ease-out';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Form validation enhancements
    const forms = document.querySelectorAll('.member-form');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('.form-input, .form-textarea');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.hasAttribute('required') && !this.value.trim()) {
                    this.style.borderColor = '#ef4444';
                    this.style.boxShadow = '0 0 0 4px rgba(239, 68, 68, 0.1)';
                } else if (this.value.trim()) {
                    this.style.borderColor = '#10b981';
                    this.style.boxShadow = '0 0 0 4px rgba(16, 185, 129, 0.1)';
                }
            });
            
            input.addEventListener('focus', function() {
                this.style.borderColor = '#667eea';
                this.style.boxShadow = '0 0 0 4px rgba(102, 126, 234, 0.1)';
            });
        });
    });
    
    // Smooth scroll for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
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
    
    // Print functionality
    const printButton = document.querySelector('button[onclick="window.print()"]');
    if (printButton) {
        printButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.print();
        });
    }
    
    // Add loading states for form submissions
    const forms = document.querySelectorAll('.member-form');
    forms.forEach(form => {
        const submitButton = form.querySelector('button[type="submit"]');
        
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                form.reportValidity();
                return;
            }
            
            // Show loading state
            const originalContent = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            submitButton.disabled = true;
            
            // Reset if form submission takes too long (fallback)
            setTimeout(() => {
                submitButton.innerHTML = originalContent;
                submitButton.disabled = false;
            }, 10000);
        });
        
        // Reset button state when page is navigated back
        window.addEventListener('pageshow', function() {
            submitButton.innerHTML = originalContent;
            submitButton.disabled = false;
        });
    });
    
    // Enhanced animations on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeIn 0.6s ease-out';
                entry.target.style.opacity = '1';
            }
        });
    }, observerOptions);
    
    // Observe all cards for scroll animations
    document.querySelectorAll('.stat-card, .member-form-card, .members-table-card').forEach(card => {
        card.style.opacity = '0';
        observer.observe(card);
    });
});
</script>
@endpush
