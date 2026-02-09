@extends('layouts.admin')

@section('title', 'Members Management')

@push('styles')
<style>
/* Members Management Styles */
.members-dashboard {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    padding: 1rem;
    width: 100%;
    box-sizing: border-box;
}

/* Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.stat-header {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-right: 1rem;
}

.stat-icon.total {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stat-icon.active {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.stat-icon.recent {
    background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);
    color: white;
}

.stat-content h4 {
    margin: 0;
    font-size: 0.9rem;
    opacity: 0.9;
    font-weight: 600;
}

.stat-content p {
    margin: 0.5rem 0 0 0;
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
}

/* Member Form */
.member-form-card {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.form-header {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
}

.form-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.member-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-weight: 500;
    color: #6c757d;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.form-input {
    padding: 0.75rem 1rem;
    border: 2px solid #e3e6f6;
    border-radius: 12px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    background: white;
    width: 100%;
    box-sizing: border-box;
}

.form-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* Members Table */
.members-table-card {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.table-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.table-container {
    overflow-x: auto;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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
    font-weight: 600;
    text-align: left;
    padding: 1rem;
    border: none;
    white-space: nowrap;
    font-size: 0.9rem;
}

.members-table th:first-child {
    border-top-left-radius: 12px;
}

.members-table th:last-child {
    border-top-right-radius: 12px;
}

.members-table td {
    padding: 1rem;
    border-bottom: 1px solid #f8f9fa;
    vertical-align: middle;
    font-size: 0.9rem;
}

.members-table tr:hover {
    background: #f8f9fa;
}

.member-info {
    line-height: 1.4;
}

.member-name {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.member-detail {
    font-size: 0.8rem;
    color: #6c757d;
    margin-top: 0.25rem;
    display: block;
}

.action-form {
    display: inline;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
}

.btn-danger {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: white;
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(220, 38, 38, 0.2);
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
}

/* Empty State */
.empty-state {
    background: white;
    border-radius: 20px;
    padding: 3rem;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.empty-text {
    color: #6c757d;
    margin-bottom: 2rem;
}

/* Success Message */
.success-message {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 2rem;
    animation: slideIn 0.5s ease-out;
}

/* Responsive Design */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .member-form {
        grid-template-columns: 1fr;
    }
    
    .members-table {
        min-width: 600px;
    }
    
    .members-table th,
    .members-table td {
        padding: 0.75rem;
        font-size: 0.8rem;
    }
}

@media (max-width: 480px) {
    .members-dashboard {
        padding: 0.5rem;
    }
    
    .stat-card {
        padding: 1rem;
    }
    
    .members-table {
        min-width: 100%;
    }
    
    .members-table th,
    .members-table td {
        padding: 0.5rem;
        font-size: 0.75rem;
    }
}

/* Animations */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endpush

@section('content')
<div class="members-dashboard">
    <div class="container-fluid">
        <!-- Member Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon total">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h4>Total Members</h4>
                        <p>{{ $members->count() ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon active">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-content">
                        <h4>Active Members</h4>
                        <p>{{ $members->where('status', 'active')->count() ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon recent">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h4>Recent Members</h4>
                        <p>{{ $members->where('created_at', '>=', now()->subDays(30))->count() ?? 0 }}</p>
                    </div>
                </div>
            </div>
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
                    {{ session('success') }}
                </div>
            @endif
            
            @if($errors->any())
                <div class="error-message" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border-radius: 12px; padding: 1rem; margin-bottom: 2rem; border: 1px solid rgba(239, 68, 68, 0.2);">
                    <h6 style="margin: 0 0 0.5rem 0; font-weight: 600;">
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
                    <textarea name="physical_address" class="form-input" placeholder="Enter physical address" rows="3"></textarea>
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
                
                <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        Save Member
                    </button>
                </div>
            </form>
        </div>

        <!-- Members Table -->
        <div class="members-table-card">
            <div class="table-header">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="table-title">
                        <i class="fas fa-users me-2"></i>
                        All Members ({{ $members->count() }})
                    </h3>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
            
            @if(isset($members) && $members->count() > 0)
                <div class="table-container">
                    <table class="members-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Name</th>
                                <th>National ID</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Joined</th>
                                <th style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($members->reverse() as $member)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>
                                        <div class="member-info">
                                            <div class="member-name">{{ $member->first_name }} {{ $member->last_name }}</div>
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
                                    <td>{{ $member->national_id ?? 'N/A' }}</td>
                                    <td>{{ $member->email ?? 'N/A' }}</td>
                                    <td>{{ $member->phone ?? 'N/A' }}</td>
                                    <td>{{ $member->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.members.destroy', $member->id) }}" 
                                              onsubmit="return confirm('Delete this member: {{ $member->first_name }} {{ $member->last_name }}?');" 
                                              class="action-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash me-1"></i>
                                                Delete
                                            </button>
                                        </form>
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
                    <div class="empty-text">Start by adding your first member to get started.</div>
                    <a href="{{ route('admin.members.create') }}" class="btn btn-primary">
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
    // Auto-hide success message after 5 seconds
    const successMessage = document.querySelector('.success-message');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.opacity = '0';
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 500);
        }, 5000);
    }
    
    // Add hover effects to table rows
    const tableRows = document.querySelectorAll('.members-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script>
@endpush
