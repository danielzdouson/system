@extends('layouts.admin')

@section('title', 'Edit Fiscal Year')

@section('content')
<div class="content-wrapper">
    <!-- Enhanced Page Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="page-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h2 mb-2 text-white">
                                    <i class="fas fa-edit me-3"></i>
                                    Edit Fiscal Year
                                </h1>
                                <p class="text-white fs-5 mb-0 opacity-90">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    Modify fiscal year: <strong>{{ $fiscalYear->name }}</strong>
                                </p>
                            </div>
                            <div class="text-end">
                                <a href="{{ route('admin.fiscal-years.index') }}" class="btn btn-outline-light">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Back to Fiscal Years
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid px-4" style="max-width: 800px; margin: 0 auto;">
            <!-- Edit Form -->
            <div class="row">
                <div class="col-12">
                    <div class="data-card">
                        <div class="data-card-header">
                            <h5 class="data-title">
                                <i class="fas fa-edit me-2"></i>
                                Fiscal Year Information
                            </h5>
                            <div class="data-subtitle">
                                Update the details for this fiscal year
                            </div>
                        </div>
                        <div class="data-card-body">
                            <form method="POST" action="{{ route('admin.fiscal-years.update', $fiscalYear) }}" id="fiscalYearForm">
                                @csrf
                                @method('PUT')

                                <!-- Error Messages -->
                                @if ($errors->any())
                                    <div class="alert alert-danger mb-4">
                                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <!-- Fiscal Year Name -->
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <label for="name" class="form-label">
                                            <i class="fas fa-tag text-primary me-2"></i>
                                            Fiscal Year Name
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name', $fiscalYear->name) }}"
                                               placeholder="e.g., 2025/2026 or 2025-2026"
                                               required>
                                        <div class="form-text">
                                            Choose a descriptive name for this fiscal year
                                        </div>
                                        @error('name')
                                            <div class="invalid-feedback d-block">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Date Range -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="start_date" class="form-label">
                                            <i class="fas fa-calendar-day text-success me-2"></i>
                                            Start Date
                                        </label>
                                        <input type="date" 
                                               class="form-control @error('start_date') is-invalid @enderror" 
                                               id="start_date" 
                                               name="start_date" 
                                               value="{{ old('start_date', $fiscalYear->start_date->format('Y-m-d')) }}"
                                               required>
                                        <div class="form-text">
                                            First day of the fiscal year
                                        </div>
                                        @error('start_date')
                                            <div class="invalid-feedback d-block">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="end_date" class="form-label">
                                            <i class="fas fa-calendar-check text-danger me-2"></i>
                                            End Date
                                        </label>
                                        <input type="date" 
                                               class="form-control @error('end_date') is-invalid @enderror" 
                                               id="end_date" 
                                               name="end_date" 
                                               value="{{ old('end_date', $fiscalYear->end_date->format('Y-m-d')) }}"
                                               required>
                                        <div class="form-text">
                                            Last day of the fiscal year
                                        </div>
                                        @error('end_date')
                                            <div class="invalid-feedback d-block">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status Selection -->
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <label class="form-label">
                                            <i class="fas fa-toggle-on text-info me-2"></i>
                                            Fiscal Year Status
                                        </label>
                                        <div class="card border-info">
                                            <div class="card-body">
                                                <div class="form-check form-check-inline mb-2">
                                                    <input class="form-check-input" 
                                                           type="radio" 
                                                           name="status" 
                                                           id="status_active" 
                                                           value="active" 
                                                           {{ old('status', $fiscalYear->status) == 'active' ? 'checked' : '' }}
                                                           required>
                                                    <label class="form-check-label" for="status_active">
                                                        <span class="badge bg-success me-2">Active</span>
                                                        This will be the current fiscal year
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" 
                                                           type="radio" 
                                                           name="status" 
                                                           id="status_inactive" 
                                                           value="inactive" 
                                                           {{ old('status', $fiscalYear->status) == 'inactive' ? 'checked' : '' }}
                                                           required>
                                                    <label class="form-check-label" for="status_inactive">
                                                        <span class="badge bg-secondary me-2">Inactive</span>
                                                        This will be a historical/archived fiscal year
                                                    </label>
                                                </div>
                                                @if(old('status', $fiscalYear->status) == 'active' && $fiscalYear->status != 'active')
                                                    <div class="alert alert-warning mt-3 mb-0">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        <strong>Warning:</strong> Setting this fiscal year as active will automatically deactivate all other fiscal years.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fiscal Year Information -->
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <div class="card bg-light">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">
                                                    <i class="fas fa-info-circle text-primary me-2"></i>
                                                    Fiscal Year Summary
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p class="mb-2">
                                                            <strong>Duration:</strong> 
                                                            {{ \Carbon\Carbon::parse($fiscalYear->start_date)->diffInDays(\Carbon\Carbon::parse($fiscalYear->end_date)) }} days
                                                        </p>
                                                        <p class="mb-2">
                                                            <strong>Created:</strong> 
                                                            {{ $fiscalYear->created_at->format('M d, Y H:i') }}
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="mb-2">
                                                            <strong>Last Updated:</strong> 
                                                            {{ $fiscalYear->updated_at->format('M d, Y H:i') }}
                                                        </p>
                                                        <p class="mb-2">
                                                            <strong>CSV Generated:</strong> 
                                                            <span class="badge {{ $fiscalYear->csv_generated ? 'bg-success' : 'bg-secondary' }}">
                                                                {{ $fiscalYear->csv_generated ? 'Yes' : 'No' }}
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Warning for Active Fiscal Year -->
                                @if($fiscalYear->status == 'active')
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <div class="alert alert-info">
                                                <h6><i class="fas fa-info-circle me-2"></i>Active Fiscal Year Notice</h6>
                                                <p class="mb-0">
                                                    This fiscal year is currently <strong>active</strong>. Changing it to inactive will make it unavailable for new transactions. 
                                                    Make sure another fiscal year is set as active if needed.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Form Actions -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                            <a href="{{ route('admin.fiscal-years.index') }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-times me-2"></i>
                                                Cancel
                                            </a>
                                            <div>
                                                <button type="reset" class="btn btn-outline-warning me-2" onclick="resetForm()">
                                                    <i class="fas fa-undo me-2"></i>
                                                    Reset Changes
                                                </button>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save me-2"></i>
                                                    Update Fiscal Year
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Styles -->
<style>
/* Data Cards */
.data-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    padding: 0;
    margin-bottom: 2rem;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.data-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.data-card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    text-align: center;
}

.data-title {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.data-subtitle {
    opacity: 0.9;
    font-size: 0.9rem;
}

.data-card-body {
    padding: 2rem;
}

/* Form Controls */
.form-control {
    border: 2px solid #e3e6f6;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-label {
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 0.5rem;
}

.form-text {
    color: #6c757d;
    font-size: 0.85rem;
    margin-top: 0.25rem;
}

/* Buttons */
.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.btn-outline-primary:hover {
    transform: translateY(-1px);
}

/* Alert Styling */
.alert {
    border-radius: 12px;
    border: none;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
}

.alert-danger {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    color: #dc2626;
}

.alert-warning {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    color: #d97706;
}

.alert-info {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    color: #2563eb;
}

/* Card Styling */
.card {
    border-radius: 12px;
    border: 1px solid #e3e6f6;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.card.border-info {
    border-color: #3b82f6;
}

.card.bg-light {
    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%) !important;
}

/* Form Check Styling */
.form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}

.form-check-label {
    cursor: pointer;
    user-select: none;
}

/* Responsive Design */
@media (max-width: 768px) {
    .data-card-body {
        padding: 1rem;
    }
    
    .d-flex {
        flex-direction: column;
    }
    
    .btn-group {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>

<!-- JavaScript -->
<script>
// Store original values for reset functionality
const originalValues = {
    name: document.getElementById('name').value,
    start_date: document.getElementById('start_date').value,
    end_date: document.getElementById('end_date').value,
    status: document.querySelector('input[name="status"]:checked').value
};

function resetForm() {
    document.getElementById('name').value = originalValues.name;
    document.getElementById('start_date').value = originalValues.start_date;
    document.getElementById('end_date').value = originalValues.end_date;
    document.querySelector(`input[name="status"][value="${originalValues.status}"]`).checked = true;
    
    // Hide/show warning based on original status
    const warningDiv = document.querySelector('.alert-warning');
    if (warningDiv) {
        warningDiv.style.display = originalValues.status === 'active' ? 'block' : 'none';
    }
}

// Date validation
document.getElementById('start_date').addEventListener('change', function() {
    const startDate = new Date(this.value);
    const endDateInput = document.getElementById('end_date');
    const endDate = new Date(endDateInput.value);
    
    if (endDate && startDate >= endDate) {
        endDateInput.min = this.value;
        // Set end date to one year after start date if invalid
        const newEndDate = new Date(startDate);
        newEndDate.setFullYear(newEndDate.getFullYear() + 1);
        newEndDate.setDate(newEndDate.getDate() - 1);
        endDateInput.value = newEndDate.toISOString().split('T')[0];
    }
});

document.getElementById('end_date').addEventListener('change', function() {
    const startDate = new Date(document.getElementById('start_date').value);
    const endDate = new Date(this.value);
    
    if (startDate && endDate <= startDate) {
        this.setCustomValidity('End date must be after start date');
    } else {
        this.setCustomValidity('');
    }
});

// Status change warning
document.querySelectorAll('input[name="status"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const warningDiv = document.querySelector('.alert-warning');
        const originalStatus = originalValues.status;
        
        if (this.value === 'active' && originalStatus !== 'active' && warningDiv) {
            warningDiv.style.display = 'block';
        } else if (warningDiv) {
            warningDiv.style.display = 'none';
        }
    });
});

// Initialize warning display
document.addEventListener('DOMContentLoaded', function() {
    const currentStatus = document.querySelector('input[name="status"]:checked').value;
    const warningDiv = document.querySelector('.alert-warning');
    if (warningDiv) {
        warningDiv.style.display = (currentStatus === 'active' && originalValues.status !== 'active') ? 'block' : 'none';
    }
});
</script>
@endsection
