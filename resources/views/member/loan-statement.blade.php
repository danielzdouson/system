<!DOCTYPE html>
<html>
<head>
    <title>Loan Statement - {{ $loan->loan_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
        }
        body { padding: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-end mb-3 no-print">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print Statement
            </button>
            <button onclick="window.close()" class="btn btn-secondary">Close</button>
        </div>

        <div class="text-center mb-4">
            <h2>SACCO Management System</h2>
            <h4>Loan Statement</h4>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <strong>Member Name:</strong> {{ $loan->member->first_name }} {{ $loan->member->last_name }}<br>
                <strong>Member Number:</strong> {{ $loan->member->membership_number ?? 'N/A' }}
            </div>
            <div class="col-6 text-end">
                <strong>Loan Number:</strong> {{ $loan->loan_number }}<br>
                <strong>Statement Date:</strong> {{ now()->format('M j, Y') }}
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Loan Summary</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Loan Purpose:</strong> {{ $loan->loan_purpose }}</p>
                        <p><strong>Original Amount:</strong> UGX {{ number_format($loan->loan_amount, 2) }}</p>
                        <p><strong>Interest Rate:</strong> {{ $loan->interest_rate }}% ({{ $loan->interest_type }})</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Total Repayable:</strong> UGX {{ number_format($loan->total_repayable, 2) }}</p>
                        <p><strong>Total Paid:</strong> UGX {{ number_format($loan->total_repayment, 2) }}</p>
                        <p><strong>Outstanding Balance:</strong> UGX {{ number_format($loan->balance, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Repayment Schedule</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Due Date</th>
                            <th>Principal</th>
                            <th>Interest</th>
                            <th>Total Due</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loan->repaymentSchedules as $schedule)
                            <tr>
                                <td>{{ $schedule->installment_number }}</td>
                                <td>{{ $schedule->due_date->format('M j, Y') }}</td>
                                <td>UGX {{ number_format($schedule->principal_due, 2) }}</td>
                                <td>UGX {{ number_format($schedule->interest_due, 2) }}</td>
                                <td>UGX {{ number_format($schedule->total_due, 2) }}</td>
                                <td>{{ ucfirst($schedule->status) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 text-center">
            <p class="text-muted">This is a computer-generated statement and does not require a signature.</p>
        </div>
    </div>
</body>
</html>
