<?php

namespace App\Providers;

use App\Models\Deposit;
use App\Models\Distribution;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\LoanPenalty;
use App\Models\LoanRepayment;
use App\Models\WelfareFund;
use App\Observers\DepositObserver;
use App\Observers\DistributionObserver;
use App\Observers\FineObserver;
use App\Observers\LoanObserver;
use App\Observers\LoanPenaltyObserver;
use App\Observers\LoanRepaymentObserver;
use App\Observers\WelfareFundObserver;
use Illuminate\Support\ServiceProvider;

class CashflowServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register observers for auto-posting cashflow transactions
        Deposit::observe(DepositObserver::class);
        Loan::observe(LoanObserver::class);
        LoanRepayment::observe(LoanRepaymentObserver::class);
        
        // New observers for complete cashflow tracking
        Distribution::observe(DistributionObserver::class);
        WelfareFund::observe(WelfareFundObserver::class);
        Fine::observe(FineObserver::class);
        LoanPenalty::observe(LoanPenaltyObserver::class);
    }
}
