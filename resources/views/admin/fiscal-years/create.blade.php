@extends('layouts.admin')

@section('title', 'Create Fiscal Year')

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
                                    <i class="fas fa-plus-circle me-3"></i>
                                    Create New Fiscal Year
                                </h1>
                                <p class="text-white fs-5 mb-0 opacity-90">
                                    <i class="fas fa-calendar-plus me-2"></i>
                                    Set up a new fiscal year period
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
            <!-- Create Form -->
            <div class="row">
                <div class="col-12">
                    <div class="data-card">
                        <div class="data-card-header">
                            <h5 class="data-title">
                                <i class="fas fa-edit me-2"></i>
                                Fiscal Year Information
                            </h5>
                            <div class="data-subtitle">
                                Fill in the details for the new fiscal year
                            </div>
                        </div>
                        <div class="data-card-body">
                            <form method="POST" action="{{ route('admin.fiscal-years.store') }}" id="fiscalYearForm">
                                @csrf

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
                                               value="{{ old('name') }}"
                                               placeholder="e.g., 2025/2026 or 2025-2026"
                                               required>
                                        <div class="form-text">
                                            Choose a descriptive name for this fiscal year (e.g., "2025/2026")
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
                                               value="{{ old('start_date') ?? \Carbon\Carbon::now()->startOfYear()->format('Y-m-d') }}"
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
                                               value="{{ old('end_date') ?? \Carbon\Carbon::now()->endOfYear()->format('Y-m-d') }}"
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
                                                           {{ old('status') == 'active' ? 'checked' : '' }}
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
                                                           {{ old('status') == 'inactive' ? 'checked' : 'checked' }}
                                                           required>
                                                    <label class="form-check-label" for="status_inactive">
                                                        <span class="badge bg-secondary me-2">Inactive</span>
                                                        This will be a historical/archived fiscal year
                                                    </label>
                                                </div>
                                                @if(old('status') == 'active')
                                                    <div class="alert alert-warning mt-3 mb-0">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        <strong>Warning:</strong> Setting this fiscal year as active will automatically deactivate all other fiscal years.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Quick Templates -->
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <label class="form-label">
                                            <i class="fas fa-magic text-warning me-2"></i>
                                            Quick Templates
                                        </label>
                                        <div class="d-flex gap-2 flex-wrap">
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="setTemplate('2025/2026', '2025-07-01', '2026-06-30')">
                                                July 2025 - June 2026
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="setTemplate('2026/2027', '2026-07-01', '2027-06-30')">
                                                July 2026 - June 2027
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="setTemplate('2025', '2025-01-01', '2025-12-31')">
                                                Calendar Year 2025
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="setTemplate('2026', '2026-01-01', '2026-12-31')">
                                                Calendar Year 2026
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                            <a href="{{ route('admin.fiscal-years.index') }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-times me-2"></i>
                                                Cancel
                                            </a>
                                            <div>
                                                <button type="reset" class="btn btn-outline-warning me-2">
                                                    <i class="fas fa-undo me-2"></i>
                                                    Reset Form
                                                </button>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-save me-2"></i>
                                                    Create Fiscal Year
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
.content{
    width: 900px;
}
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

.btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border: none;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
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

/* Card Styling */
.card {
    border-radius: 12px;
    border: 1px solid #e3e6f6;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.card.border-info {
    border-color: #3b82f6;
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
function setTemplate(name, startDate, endDate) {
    document.getElementById('name').value = name;
    document.getElementById('start_date').value = startDate;
    document.getElementById('end_date').value = endDate;
    
    // Show visual feedback
    const buttons = document.querySelectorAll('button[onclick^="setTemplate"]');
    buttons.forEach(btn => {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-primary');
    });
    event.target.classList.remove('btn-outline-primary');
    event.target.classList.add('btn-primary');
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
        if (this.value === 'active' && warningDiv) {
            warningDiv.style.display = 'block';
        } else if (warningDiv) {
            warningDiv.style.display = 'none';
        }
    });
});
</script>
@endsection
