@extends('layouts.member')

@section('title', 'Documents')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Available Documents</h2>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                @foreach ($documents as $document)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">{{ $document->title }}</h5>
                                <p class="card-text">{{ $document->description }}</p>
                                
                                <div class="mb-3">
                                    <span class="badge bg-{{ $document->getTypeColor() }} fs-6">
                                        {{ $document->document_type }}
                                    </span>
                                </div>

                                @if($document->requires_fine)
                                    <div class="alert alert-warning py-2 mb-3">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Fine Required:</strong> {{ number_format($document->fine_amount, 2) }} UGX
                                    </div>
                                @endif

                                <div class="d-grid gap-2">
                                    <a href="{{ route('member.documents.show', $document) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                    
                                    @if($document->document_type === 'loan_form')
                                        <a href="{{ route('member.documents.upload-form', $document) }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-upload"></i> Upload Form
                                        </a>
                                    @endif

                                    <form action="{{ route('member.documents.download', $document) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-download"></i> Download
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                @endforeach
            </div>

            @if($documents->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No documents available</h4>
                    <p class="text-muted">Check back later for new documents.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
