@extends('layouts.member')

@section('content')
<div class="container-fluid p-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card gradient-bg text-white border-0">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-2">Loan Information</h2>
                            <p class="mb-0 opacity-75">Everything you need to know about SACCO loans</p>
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
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>About SACCO Loans</h5>
                </div>
                <div class="card-body">
                    <p>Our SACCO provides loans to members to help them achieve their financial goals. Loans are available for various purposes including business, education, and personal needs.</p>
                    
                    <h6 class="mt-4">Loan Features:</h6>
                    <ul>
                        <li>Competitive interest rates</li>
                        <li>Flexible repayment terms</li>
                        <li>Quick approval process</li>
                        <li>No hidden charges</li>
                    </ul>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Frequently Asked Questions</h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    How do I apply for a loan?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Contact the SACCO office or visit in person to submit your loan application. You'll need to provide necessary documentation and meet eligibility requirements.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    What are the eligibility requirements?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    You must be an active member with regular savings contributions. Specific requirements may vary based on loan type and amount.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    How long does approval take?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Loan applications are typically reviewed within 5-7 business days. You'll be notified of the decision via phone or email.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-phone me-2"></i>Contact Us</h5>
                </div>
                <div class="card-body">
                    <p>For more information about loans, please contact:</p>
                    <p class="mb-2"><i class="fas fa-envelope me-2"></i>info@sacco.com</p>
                    <p class="mb-2"><i class="fas fa-phone me-2"></i>+256 XXX XXXXXX</p>
                    <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>SACCO Office</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <a href="{{ route('member.loans.apply') }}" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-plus-circle me-2"></i>Apply for Loan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
