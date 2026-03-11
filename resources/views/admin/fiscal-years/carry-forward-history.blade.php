@extends('layouts.app')

@section('title', 'Carry Forward History')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Carry Forward History</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.fiscal-years.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Fiscal Years
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @if($fiscalYear)
                    <div class="alert alert-info">
                        <h5><i class="fas fa-filter"></i> Filtered by Fiscal Year</h5>
                        <p>Showing carry forward records for <strong>{{ $fiscalYear->name }}</strong></p>
                        <a href="{{ route('admin.fiscal-years.carry-forward.history') }}" class="btn btn-sm btn-info">
                            <i class="fas fa-times"></i> Clear Filter
                        </a>
                    </div>
                    @endif

                    @if($history->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>From Fiscal Year</th>
                                    <th>To Fiscal Year</th>
                                    <th>Item Type</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Processed By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($history as $record)
                                <tr>
                                    <td>{{ $record->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $record->fromFiscalYear->name }}</td>
                                    <td>{{ $record->toFiscalYear->name }}</td>
                                    <td>{!! $record->getItemTypeBadgeAttribute() !!}</td>
                                    <td>{{ $record->description }}</td>
                                    <td>{{ number_format($record->amount, 2) }}</td>
                                    <td>{!! $record->getStatusBadgeAttribute() !!}</td>
                                    <td>{{ $record->creator?->name ?? 'System' }}</td>
                                </tr>
                                
                                @if($record->error_message)
                                <tr>
                                    <td colspan="8">
                                        <div class="alert alert-danger mb-0">
                                            <strong>Error:</strong> {{ $record->error_message }}
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> No Records Found</h5>
                        <p>No carry forward records found @if($fiscalYear) for {{ $fiscalYear->name }} @endif.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
