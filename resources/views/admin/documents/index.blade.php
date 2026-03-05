@extends('layouts.admin')

@section('title', 'Document Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Document Management</h2>
                <a href="{{ route('admin.documents.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Upload Document
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Fine Required</th>
                                    <th>Fine Amount</th>
                                    <th>Uploaded By</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($documents as $document)
                                    <tr>
                                        <td>{{ $document->title }}</td>
                                        <td>
                                            <span class="badge bg-{{ $document->getTypeColor() }}">
                                                {{ $document->document_type }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($document->requires_fine)
                                                <span class="badge bg-danger">YES</span>
                                            @else
                                                <span class="badge bg-success">NO</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($document->fine_amount, 2) }} UGX</td>
                                        <td>{{ $document->uploadedBy->name ?? 'System' }}</td>
                                        <td>
                                            @if($document->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.documents.edit', $document) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.documents.destroy', $document) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this document?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No documents found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
