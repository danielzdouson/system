<!DOCTYPE html>
<html>
<head>
    <title>Loan Clearance Certificate - {{ $loan->loan_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
        }
        body { padding: 40px; }
        .certificate {
            border: 10px solid #1e40af;
            padding: 40px;
            margin: 20px auto;
            max-width: 800px;
        }
        .certificate-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .certificate-body {
            text-align: center;
            line-height: 2;
        }
    </style>
</head>
<body>
    <div class="text-end mb-3 no-print">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Print Certificate
        </button>
        <button onclick="window.close()" class="btn btn-secondary">Close</button>
    </div>

    <div class="certificate">
        <div class="certificate-header">
            <h1 style="color: #1e40af;">SACCO Management System</h1>
            <h3>LOAN CLEARANCE CERTIFICATE</h3>
        </div>

        <div class="certificate-body">
            <p style="font-size: 18px;">This is to certify that</p>
            <h2 style="color: #1e40af;">{{ $loan->member->first_name }} {{ $loan->member->last_name }}</h2>
            <p style="font-size: 18px;">Member Number: {{ $loan->member->membership_number ?? 'N/A' }}</p>
            
            <p style="font-size: 16px; margin-top: 30px;">
                Has successfully completed repayment of Loan #{{ $loan->loan_number }}<br>
                Original Amount: UGX {{ number_format($loan->loan_amount, 2) }}<br>
                Total Repaid: UGX {{ number_format($loan->total_repayment, 2) }}<br>
                Completion Date: {{ $loan->completed_at ? $loan->completed_at->format('F j, Y') : 'N/A' }}
            </p>

            <p style="font-size: 16px; margin-top: 30px;">
                This member has fulfilled all loan obligations and is in good standing with the SACCO.
            </p>

            <div style="margin-top: 60px;">
                <div class="row">
                    <div class="col-6">
                        <p>_______________________<br>Authorized Signature</p>
                    </div>
                    <div class="col-6">
                        <p>_______________________<br>Date</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <p class="text-muted">Certificate Number: CERT-{{ $loan->id }}-{{ now()->format('Ymd') }}</p>
        </div>
    </div>
</body>
</html>
