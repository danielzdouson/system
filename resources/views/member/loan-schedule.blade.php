@extends('layouts.member')

@section('content')
<div class="container-fluid p-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card gradient-bg text-white border-0">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-2">Full Repayment Schedule</h2>
                            <p class="mb-0 opacity-75">Loan #{{ $loan->loan_number }}</p>
                        </div>
                        <a href="{{ route('member.loans') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-2"></i>Back to Loans
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Complete Payment Schedule</h5>
                        <button onclick="window.print()" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-print me-1"></i>Print Schedule
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($loan->repaymentSchedules->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>Installment #</th>
                                        <th>Due Date</th>
                                        <th>Principal Due</th>
                                        <th>Interest Due</th>
                                        <th>Total Due</th>
                                        <th>Outstanding Balance</th>
                                        <th>Status</th>
                                        <th>Paid Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($loan->repaymentSchedules as $schedule)
                                        <tr class="{{ $schedule->status == 'paid' ? 'table-success' : ($schedule->isOverdue() ? 'table-danger' : '') }}">
                                            <td><strong>{{ $schedule->installment_number }}</strong></td>
                                            <td>{{ $schedule->due_date->format('M j, Y') }}</td>
                                            <td>UGX {{ number_format($schedule->principal_due, 0) }}</td>
                                            <td>UGX {{ number_format($schedule->interest_due, 0) }}</td>
                                            <td><strong>UGX {{ number_format($schedule->total_due, 0) }}</strong></td>
                                            <td>UGX {{ number_format($schedule->outstanding_balance, 0) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $schedule->status == 'paid' ? 'success' : ($schedule->isOverdue() ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($schedule->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $schedule->paid_date ? $schedule->paid_date->format('M j, Y') : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="2">Totals</th>
                                        <th>UGX {{ number_format($loan->repaymentSchedules->sum('principal_due'), 0) }}</th>
                                        <th>UGX {{ number_format($loan->repaymentSchedules->sum('interest_due'), 0) }}</th>
                                        <th>UGX {{ number_format($loan->repaymentSchedules->sum('total_due'), 0) }}</th>
                                        <th colspan="3"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No repayment schedule available for this loan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
