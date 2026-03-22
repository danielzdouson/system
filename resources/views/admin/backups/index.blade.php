@extends('layouts.admin')

@section('title', 'Backup Management')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Backup Management</h1>
        <div>
            <button type="button" class="btn btn-primary" id="createBackupBtn">
                <i class="fas fa-plus-circle me-2"></i>Create Backup Now
            </button>
            <form method="POST" action="{{ route('admin.backups.revoke-access') }}" class="d-inline-block ms-2">
                @csrf
                <button type="submit" class="btn btn-outline-danger" title="Revoke access and require password again">
                    <i class="fas fa-lock me-2"></i>Lock Backup Access
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Backups
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_backups'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-database fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Local Backups
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['local_backups'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hdd fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                S3 Cloud Backups
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['s3_backups'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cloud fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Size
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_size_formatted'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Backup Files</h6>
            <div class="text-muted small">
                <i class="fas fa-info-circle me-1"></i>
                Automated backups run every Sunday at 2:00 AM
            </div>
        </div>
        <div class="card-body">
            @if(count($backups) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="backupsTable">
                        <thead>
                            <tr>
                                <th>Filename</th>
                                <th>Date Created</th>
                                <th>Size</th>
                                <th>Storage Location</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($backups as $backup)
                                <tr>
                                    <td>
                                        <i class="fas fa-file-archive text-primary me-2"></i>
                                        {{ $backup['filename'] }}
                                    </td>
                                    <td>{{ date('M d, Y h:i A', $backup['modified']) }}</td>
                                    <td>{{ number_format($backup['size'] / 1024 / 1024, 2) }} MB</td>
                                    <td>
                                        @if($backup['location'] === 'both')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-double me-1"></i>Local + S3
                                            </span>
                                        @elseif($backup['location'] === 'local')
                                            <span class="badge bg-info">
                                                <i class="fas fa-hdd me-1"></i>Local Only
                                            </span>
                                        @else
                                            <span class="badge bg-primary">
                                                <i class="fas fa-cloud me-1"></i>S3 Only
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.backups.download', $backup['filename']) }}" 
                                               class="btn btn-sm btn-primary" 
                                               title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-warning restore-btn" 
                                                    data-filename="{{ $backup['filename'] }}"
                                                    title="Restore">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger delete-btn" 
                                                    data-filename="{{ $backup['filename'] }}"
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-database fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No backups found. Create your first backup to get started.</p>
                    <button type="button" class="btn btn-primary" id="createFirstBackupBtn">
                        <i class="fas fa-plus-circle me-2"></i>Create First Backup
                    </button>
                </div>
            @endif
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Backup Information</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="font-weight-bold">What Gets Backed Up:</h6>
                    <ul>
                        <li>Complete MySQL database (all tables and data)</li>
                        <li>All uploaded documents and files</li>
                        <li>Member uploaded forms</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="font-weight-bold">Backup Schedule:</h6>
                    <ul>
                        <li>Automated: Every Sunday at 2:00 AM</li>
                        <li>Manual: Click "Create Backup Now" anytime</li>
                        <li>Storage: Local + AWS S3 (dual redundancy)</li>
                        <li>Retention: Manual deletion only (no auto-cleanup)</li>
                    </ul>
                </div>
            </div>
            <div class="alert alert-info mt-3 mb-0">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Note:</strong> Before restoring a backup, the system automatically creates a pre-restore backup to ensure you can recover if needed.
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirm Restore
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">
                    <strong>Warning:</strong> Restoring this backup will replace all current data with the backup data.
                </p>
                <p class="mb-3">
                    The system will automatically create a backup of the current state before restoring.
                </p>
                <p class="mb-3">
                    <strong>Backup to restore:</strong> <span id="restoreFilename" class="text-primary"></span>
                </p>
                <div class="form-group">
                    <label for="confirmationInput">Type <strong>RESTORE</strong> to confirm:</label>
                    <input type="text" class="form-control" id="confirmationInput" placeholder="RESTORE">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="confirmRestoreBtn" disabled>
                    <i class="fas fa-undo me-2"></i>Restore Backup
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-trash me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this backup?</p>
                <p class="mb-0">
                    <strong>Backup:</strong> <span id="deleteFilename" class="text-danger"></span>
                </p>
                <p class="text-muted small mt-2">This will delete the backup from both local storage and AWS S3.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-2"></i>Delete Backup
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let currentFilename = '';

    $('#createBackupBtn, #createFirstBackupBtn').on('click', function() {
        const btn = $(this);
        const originalHtml = btn.html();
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Creating Backup...');

        $.ajax({
            url: '{{ route("admin.backups.create") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Backup Created!',
                        html: `
                            <p><strong>Filename:</strong> ${response.filename}</p>
                            <p><strong>Size:</strong> ${(response.size / 1024 / 1024).toFixed(2)} MB</p>
                            <p class="text-muted">Backup has been saved locally and uploaded to AWS S3.</p>
                        `,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Backup Failed',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Failed to create backup'
                });
            },
            complete: function() {
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    $('.restore-btn').on('click', function() {
        currentFilename = $(this).data('filename');
        $('#restoreFilename').text(currentFilename);
        $('#confirmationInput').val('');
        $('#confirmRestoreBtn').prop('disabled', true);
        $('#restoreModal').modal('show');
    });

    $('#confirmationInput').on('input', function() {
        const isValid = $(this).val() === 'RESTORE';
        $('#confirmRestoreBtn').prop('disabled', !isValid);
    });

    $('#confirmRestoreBtn').on('click', function() {
        const btn = $(this);
        const originalHtml = btn.html();
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Restoring...');

        $.ajax({
            url: `/admin/backups/restore/${currentFilename}`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                confirmation: 'RESTORE'
            },
            success: function(response) {
                $('#restoreModal').modal('hide');
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Restore Complete!',
                        html: `
                            <p>The backup has been restored successfully.</p>
                            <p class="text-muted">Pre-restore backup: ${response.pre_restore_backup}</p>
                        `,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Restore Failed',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                $('#restoreModal').modal('hide');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Failed to restore backup'
                });
            },
            complete: function() {
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    $('.delete-btn').on('click', function() {
        currentFilename = $(this).data('filename');
        $('#deleteFilename').text(currentFilename);
        $('#deleteModal').modal('show');
    });

    $('#confirmDeleteBtn').on('click', function() {
        const btn = $(this);
        const originalHtml = btn.html();
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Deleting...');

        $.ajax({
            url: `/admin/backups/${currentFilename}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#deleteModal').modal('hide');
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Delete Failed',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                $('#deleteModal').modal('hide');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Failed to delete backup'
                });
            },
            complete: function() {
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    @if(count($backups) > 0)
    $('#backupsTable').DataTable({
        order: [[1, 'desc']],
        pageLength: 10,
        language: {
            search: "Search backups:"
        }
    });
    @endif
});
</script>
@endsection
