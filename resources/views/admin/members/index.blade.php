@extends('layouts.admin')

@section('title', 'Members Management')

@section('content')
<div style="display:grid; grid-template-columns:1fr 2fr; gap:20px; margin-bottom:30px;">
    
    <!-- Member Stats Cards -->
    <div class="dashboard-card" style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:white; padding:25px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
        <div style="display:flex; align-items:center; margin-bottom:10px;">
            <span class="card-icon">👥</span>
            <div>
                <h4 style="margin:0; font-size:14px; opacity:0.9;">Total Members</h4>
                <p style="margin:5px 0 0 0; font-size:32px; font-weight:bold;">{{ $members->count() ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Active vs Inactive Members -->
    <div class="dashboard-card" style="background:linear-gradient(135deg, #f59e0b 0%, #ef4444 100%); color:white; padding:25px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
        <div style="display:flex; align-items:center; margin-bottom:10px;">
            <span class="card-icon">👥</span>
            <div>
                <h4 style="margin:0; font-size:14px; opacity:0.9;">Member Status</h4>
                <p style="margin:5px 0 0 0; font-size:12px; opacity:0.8;">Active vs Inactive breakdown</p>
            </div>
        </div>
    </div>

    <!-- Recent Members -->
    <div class="dashboard-card" style="background:linear-gradient(135deg, #10b981 0%, #059669 100%); color:white; padding:25px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
        <div style="display:flex; align-items:center; margin-bottom:10px;">
            <span class="card-icon">🕐</span>
            <div>
                <h4 style="margin:0; font-size:14px; opacity:0.9;">Recent Members</h4>
                <p style="margin:5px 0 0 0; font-size:12px; opacity:0.8;">Latest member registrations</p>
            </div>
        </div>
    </div>
</div>

<!-- Add New Member -->
<div class="dashboard-card" style="background:white; padding:25px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.1);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h3 style="margin:0; color:#1f2937; display:flex; align-items:center;">
            <span style="margin-right:10px;">➕</span>
            Add New Member
        </h3>
        
        @if(session('success'))
            <div class="success-message" style="background:#d1fae5; padding:15px; border-radius:8px; margin-bottom:20px; color:#065f46;">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.members.store') }}" class="member-form">
            @csrf
            <div class="form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" required class="form-input">
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" required class="form-input">
                </div>
            </div>
            
            <div class="form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="form-group">
                    <label>National ID</label>
                    <input type="text" name="national_id" class="form-input">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-input">
                </div>
            </div>
            
            <div class="form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-input">
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; margin-top:20px;">
                <button type="submit" class="btn btn-primary">
                    <span style="margin-right:8px;">💾</span>
                    Save Member
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Members Table -->
<div class="dashboard-card" style="background:white; padding:25px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.1);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h3 style="margin:0; color:#1f2937; display:flex; align-items:center;">
            <span style="margin-right:10px;">👥</span>
            All Members ({{ $members->count() }})
        </h3>
        
        @if(isset($members) && $members->count() > 0)
            <div class="table-container">
                <table class="members-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>National ID</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $member)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="member-info">
                                        <strong>{{ $member->first_name }} {{ $member->last_name }}</strong>
                                        @if($member->national_id)
                                            <div class="member-detail">ID: {{ $member->national_id }}</div>
                                        @endif
                                        @if($member->email)
                                            <div class="member-detail">📧 {{ $member->email }}</div>
                                        @endif
                                        @if($member->phone)
                                            <div class="member-detail">📱 {{ $member->phone }}</div>
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
                                            <span style="margin-right:5px;">🗑️</span>
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
            <div class="empty-state" style="text-align:center; padding:40px; color:#6b7280;">
                <div style="font-size:48px; margin-bottom:15px;">👥</div>
                <h4 style="margin:0; color:#374151;">No Members Found</h4>
                <p style="margin:10px 0 0 0; color:#6b7280;">Start by adding your first member to get started.</p>
                <a href="{{ route('admin.members.index') }}" class="btn btn-primary">
                    <span style="margin-right:8px;">➕</span>
                    Add First Member
                </a>
            </div>
        @endif
    </div>
</div>

<style>
.member-form {
    display: grid;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-input {
    width: 100%;
    padding: 12px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.form-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.btn {
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
}

.btn-danger {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: white;
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.success-message {
    animation: slideIn 0.5s ease-out;
}

.table-container {
    overflow-x: auto;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.members-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

.members-table th {
    background: #f8fafc;
    color: #374151;
    font-weight: 600;
    text-align: left;
    padding: 15px;
    border-bottom: 2px solid #e5e7eb;
    white-space: nowrap;
}

.members-table td {
    padding: 15px;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: top;
}

.members-table tr:hover {
    background: #f8fafc;
}

.member-info {
    line-height: 1.4;
}

.member-detail {
    font-size: 12px;
    color: #6b7280;
    margin-top: 4px;
}

.action-form {
    display: inline;
}

.empty-state {
    background: #f8fafc;
    border: 2px dashed #e5e7eb;
    border-radius: 8px;
    padding: 20px;
}

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

@endsection
