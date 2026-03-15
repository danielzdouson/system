@extends('layouts.member')

@section('content')
<div class="container-fluid p-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card gradient-bg text-white border-0">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-2">Apply for a Loan</h2>
                            <p class="mb-0 opacity-75">Start your loan application process</p>
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
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Eligibility Status</h5>
                </div>
                <div class="card-body">
                    @if($hasOverduePayments)
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Not Eligible:</strong> You have overdue loan payments. Please clear all overdue payments before applying for a new loan.
                        </div>
                    @elseif($activeLoansCount >= 3)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Maximum Loans Reached:</strong> You currently have {{ $activeLoansCount }} active loans. Please complete some loans before applying for new ones.
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Eligible:</strong> You are eligible to apply for a loan.
                        </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded">
                                <strong>Active Loans:</strong>
                                <p class="mb-0 h4 text-primary">{{ $activeLoansCount }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded">
                                <strong>Overdue Payments:</strong>
                                <p class="mb-0 h4 {{ $hasOverduePayments ? 'text-danger' : 'text-success' }}">
                                    {{ $hasOverduePayments ? 'Yes' : 'No' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Application Process</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> Loan applications are processed offline. Please visit the SACCO office or contact an administrator to submit your loan application.
                    </div>

                    <h6 class="mt-4">Required Documents:</h6>
                    <ul>
                        <li>Completed loan application form</li>
                        <li>Valid identification (ID or Passport)</li>
                        <li>Proof of income or business registration</li>
                        <li>Guarantor information (if required)</li>
                    </ul>

                    <h6 class="mt-4">Application Steps:</h6>
                    <ol>
                        <li>Visit the SACCO office to collect the application form</li>
                        <li>Fill out the form completely and accurately</li>
                        <li>Attach all required documents</li>
                        <li>Submit the application to the loan officer</li>
                        <li>Wait for approval notification (typically 5-7 business days)</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Loan Calculator</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Use this calculator to estimate your monthly payments</p>
                    <div class="mb-3">
                        <label class="form-label">Loan Amount (UGX)</label>
                        <input type="number" class="form-control" id="loanAmount" value="1000000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Interest Rate (%)</label>
                        <input type="number" class="form-control" id="interestRate" value="10">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration (months)</label>
                        <input type="number" class="form-control" id="duration" value="12">
                    </div>
                    <button onclick="calculateLoan()" class="btn btn-primary w-100">Calculate</button>
                    <div id="result" class="mt-3"></div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-phone me-2"></i>Need Help?</h5>
                </div>
                <div class="card-body">
                    <p>Contact us for assistance:</p>
                    <p class="mb-2"><i class="fas fa-envelope me-2"></i>info@sacco.com</p>
                    <p class="mb-2"><i class="fas fa-phone me-2"></i>+256 XXX XXXXXX</p>
                    <a href="{{ route('member.loans.info') }}" class="btn btn-outline-info w-100 mt-3">
                        <i class="fas fa-info-circle me-2"></i>Loan Information
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calculateLoan() {
    const amount = parseFloat(document.getElementById('loanAmount').value);
    const rate = parseFloat(document.getElementById('interestRate').value) / 100 / 12;
    const months = parseInt(document.getElementById('duration').value);
    
    const monthlyPayment = (amount * rate * Math.pow(1 + rate, months)) / (Math.pow(1 + rate, months) - 1);
    const totalPayment = monthlyPayment * months;
    const totalInterest = totalPayment - amount;
    
    document.getElementById('result').innerHTML = `
        <div class="bg-light p-3 rounded">
            <strong>Monthly Payment:</strong>
            <p class="h4 text-primary mb-2">UGX ${monthlyPayment.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</p>
            <strong>Total Interest:</strong>
            <p class="mb-2">UGX ${totalInterest.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</p>
            <strong>Total Payment:</strong>
            <p class="mb-0">UGX ${totalPayment.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</p>
        </div>
    `;
}
</script>
@endsection
