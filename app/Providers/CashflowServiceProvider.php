<?php

namespace App\Providers;

use App\Models\Deposit;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Observers\DepositObserver;
use App\Observers\LoanObserver;
use App\Observers\LoanRepaymentObserver;
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
    }
}
