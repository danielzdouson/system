@extends('layouts.admin')

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card gradient-bg text-white border-0">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-2">Bank Details Management</h2>
                            <p class="mb-0 opacity-75">Manage bank and mobile money details for payments</p>
                        </div>
                        <button onclick="showAddModal()" class="btn btn-light">
                            <i class="fas fa-plus me-2"></i>Add Bank Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bank Details List -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    @if($bankDetails->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Bank/Provider</th>
                                        <th>Account Name</th>
                                        <th>Account Number/Phone</th>
                                        <th>Status</th>
                                        <th>Default</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bankDetails as $bank)
                                        <tr>
                                            <td>
                                                @if($bank->mobile_money_provider)
                                                    <span class="badge bg-primary">Mobile Money</span>
                                                @else
                                                    <span class="badge bg-info">Bank Transfer</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $bank->bank_name }}</strong>
                                                @if($bank->mobile_money_provider)
                                                    <br><small class="text-muted">{{ $bank->provider_label }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $bank->account_name }}</td>
                                            <td>
                                                @if($bank->mobile_money_provider)
                                                    {{ $bank->mobile_money_number }}
                                                @else
                                                    {{ $bank->account_number }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($bank->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($bank->is_default)
                                                    <span class="badge bg-primary">Default</span>
                                                @else
                                                    <span class="text-muted">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button onclick="editBankDetail({{ $bank->id }})" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button onclick="deleteBankDetail({{ $bank->id }})" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="bg-light rounded-circle p-4 d-inline-block mb-3">
                                <i class="fas fa-university fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">No bank details configured</h5>
                            <p class="text-muted">Add bank details to enable bank transfer payments</p>
                            <button onclick="showAddModal()" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add Bank Details
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="bankDetailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Bank Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="bankDetailForm">
                    @csrf
                    <input type="hidden" id="bankDetailId" name="id" value="">

                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select id="detailType" name="detail_type" class="form-select" onchange="toggleTypeFields()">
                            <option value="bank">Bank Transfer</option>
                            <option value="mobile_money">Mobile Money</option>
                        </select>
                    </div>

                    <!-- Bank Fields -->
                    <div id="bankFields">
                        <div class="mb-3">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Account Name</label>
                            <input type="text" name="account_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Account Number</label>
                            <input type="text" name="account_number" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Branch (Optional)</label>
                            <input type="text" name="branch" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SWIFT Code (Optional)</label>
                            <input type="text" name="swift_code" class="form-control">
                        </div>
                    </div>

                    <!-- Mobile Money Fields -->
                    <div id="mobileMoneyFields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Bank Name (Provider)</label>
                            <input type="text" name="bank_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Account Name</label>
                            <input type="text" name="account_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mobile Money Provider</label>
                            <select name="mobile_money_provider" class="form-select" required>
                                <option value="">Select Provider</option>
                                <option value="MTN">MTN Mobile Money</option>
                                <option value="AIRTEL">Airtel Money</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mobile Money Number</label>
                            <input type="text" name="mobile_money_number" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Instructions (Optional)</label>
                        <textarea name="instructions" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_default" id="isDefault">
                            <label class="form-check-label" for="isDefault">Set as Default</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" onclick="saveBankDetail()" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<script>
let modal;

function showAddModal() {
    document.getElementById('bankDetailForm').reset();
    document.getElementById('bankDetailId').value = '';
    document.getElementById('modalTitle').textContent = 'Add Bank Details';
    document.getElementById('detailType').value = 'bank';
    toggleTypeFields();
    modal = new bootstrap.Modal(document.getElementById('bankDetailModal'));
    modal.show();
}

function editBankDetail(id) {
    // Fetch bank detail data and populate form
    fetch(`/admin/payments/bank-details/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('bankDetailId').value = data.id;
            document.getElementById('modalTitle').textContent = 'Edit Bank Details';
            
            // Populate form fields
            const form = document.getElementById('bankDetailForm');
            form.bank_name.value = data.bank_name;
            form.account_name.value = data.account_name;
            
            if (data.mobile_money_provider) {
                document.getElementById('detailType').value = 'mobile_money';
                form.mobile_money_provider.value = data.mobile_money_provider;
                form.mobile_money_number.value = data.mobile_money_number;
            } else {
                document.getElementById('detailType').value = 'bank';
                form.account_number.value = data.account_number;
                form.branch.value = data.branch || '';
                form.swift_code.value = data.swift_code || '';
            }
            
            form.instructions.value = data.instructions || '';
            form.is_default.checked = data.is_default;
            
            toggleTypeFields();
            modal = new bootstrap.Modal(document.getElementById('bankDetailModal'));
            modal.show();
        });
}

function toggleTypeFields() {
    const type = document.getElementById('detailType').value;
    const bankFields = document.getElementById('bankFields');
    const mobileFields = document.getElementById('mobileMoneyFields');
    
    bankFields.style.display = type === 'bank' ? 'block' : 'none';
    mobileFields.style.display = type === 'mobile_money' ? 'block' : 'none';
}

async function saveBankDetail() {
    const form = document.getElementById('bankDetailForm');
    const formData = new FormData(form);
    const id = document.getElementById('bankDetailId').value;
    
    const url = id ? `{{ route('admin.payments.update-bank-details', ':id') }}`.replace(':id', id) : '{{ route('admin.payments.store-bank-details') }}';
    const method = id ? 'PUT' : 'POST';
    
    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        });
        
        if (response.ok) {
            modal.hide();
            location.reload();
        } else {
            alert('Failed to save bank details');
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
    }
}

function deleteBankDetail(id) {
    if (!confirm('Are you sure you want to delete this bank detail?')) return;
    
    fetch(`{{ route('admin.payments.delete-bank-details', ':id') }}`.replace(':id', id), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => {
        if (response.ok) {
            location.reload();
        } else {
            alert('Failed to delete bank detail');
        }
    });
}
</script>
@endsection
