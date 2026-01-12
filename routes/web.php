<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Home Route
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard route
Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::resource('members', App\Http\Controllers\Admin\MemberController::class)->except(['show']);
    Route::get('members/{member}', [App\Http\Controllers\Admin\MemberController::class, 'show'])->name('members.show');
    Route::post('members/{member}/update-shares', [App\Http\Controllers\Admin\MemberController::class, 'updateShares'])
        ->name('members.update-shares');

    // Fiscal Year Routes
    Route::get('fiscal-years', [App\Http\Controllers\Admin\FiscalYearController::class, 'index'])->name('fiscal-years.index');
    Route::get('fiscal-years/create', [App\Http\Controllers\Admin\FiscalYearController::class, 'create'])->name('fiscal-years.create');
    Route::post('fiscal-years', [App\Http\Controllers\Admin\FiscalYearController::class, 'store'])->name('fiscal-years.store');
    Route::get('fiscal-years/{fiscalYear}/edit', [App\Http\Controllers\Admin\FiscalYearController::class, 'edit'])->name('fiscal-years.edit');
    Route::put('fiscal-years/{fiscalYear}', [App\Http\Controllers\Admin\FiscalYearController::class, 'update'])->name('fiscal-years.update');
    Route::delete('fiscal-years/{fiscalYear}', [App\Http\Controllers\Admin\FiscalYearController::class, 'destroy'])->name('fiscal-years.destroy');

    // Group Savings Routes
    Route::get('group-savings/dashboard', [App\Http\Controllers\Admin\GroupSavingsController::class, 'dashboard'])->name('group-savings.dashboard');
    Route::get('group-savings/monthly/{month}', [App\Http\Controllers\Admin\GroupSavingsController::class, 'monthlyView'])->name('group-savings.monthly');
    Route::get('group-savings/monthly/{month}/{fiscal_year}', [App\Http\Controllers\Admin\GroupSavingsController::class, 'monthlyViewWithFiscalYear'])->name('group-savings.monthly.fiscal');
    Route::get('group-savings/create-deposit', [App\Http\Controllers\Admin\GroupSavingsController::class, 'createDeposit'])->name('group-savings.create-deposit');
    Route::post('group-savings/deposit', [App\Http\Controllers\Admin\GroupSavingsController::class, 'storeDeposit'])->name('group-savings.store-deposit');
    Route::get('group-savings/distribute/{depositId}', [App\Http\Controllers\Admin\GroupSavingsController::class, 'distributeDeposit'])->name('group-savings.distribute');
    Route::post('group-savings/distribute/{depositId}', [App\Http\Controllers\Admin\GroupSavingsController::class, 'storeDistribution'])->name('group-savings.store-distribution');
    Route::get('group-savings/fines', [App\Http\Controllers\Admin\GroupSavingsController::class, 'finesIndex'])->name('group-savings.fines');
    Route::post('group-savings/fines/{fineId}/pay', [App\Http\Controllers\Admin\GroupSavingsController::class, 'payFine'])->name('group-savings.fines.pay');
    Route::post('group-savings/fines/{fineId}/waive', [App\Http\Controllers\Admin\GroupSavingsController::class, 'waiveFine'])->name('group-savings.fines.waive');
    Route::get('group-savings/pending', [App\Http\Controllers\Admin\GroupSavingsController::class, 'pendingMonths'])->name('group-savings.pending');
    Route::post('group-savings/apply-fines', [App\Http\Controllers\Admin\GroupSavingsController::class, 'applyFines'])->name('group-savings.apply-fines');
    Route::get('group-savings/export/{fiscalYearId}/{month}', [App\Http\Controllers\Admin\GroupSavingsController::class, 'exportMonthCSV'])->name('group-savings.export.csv');

    // Group Loans Routes (Main System)
    Route::get('group-loans', [App\Http\Controllers\Admin\GroupLoanController::class, 'index'])->name('group-loans.index');
    Route::get('group-loans/requests', [App\Http\Controllers\Admin\GroupLoanController::class, 'requests'])->name('group-loans.requests');
    Route::post('group-loans/requests', [App\Http\Controllers\Admin\GroupLoanController::class, 'storeRequest'])->name('group-loans.requests.store');
    Route::post('group-loans/direct-loan', [App\Http\Controllers\Admin\GroupLoanController::class, 'createDirectLoan'])->name('group-loans.direct-loan.store');
    Route::post('group-loans/requests/{loanRequest}/approve', [App\Http\Controllers\Admin\GroupLoanController::class, 'approveRequest'])->name('group-loans.requests.approve');
    Route::post('group-loans/requests/{loanRequest}/reject', [App\Http\Controllers\Admin\GroupLoanController::class, 'rejectRequest'])->name('group-loans.requests.reject');

    // Accounts Routes
    Route::get('accounts', [App\Http\Controllers\Admin\AccountController::class, 'index'])->name('accounts.index');
    Route::get('accounts/summary', [App\Http\Controllers\Admin\AccountController::class, 'summary'])->name('accounts.summary');
    Route::get('accounts/{memberId}', [App\Http\Controllers\Admin\AccountController::class, 'show'])->name('accounts.show');
    Route::get('group-loans/all', [App\Http\Controllers\Admin\GroupLoanController::class, 'loans'])->name('group-loans.all');
    Route::get('group-loans/{loan}', [App\Http\Controllers\Admin\GroupLoanController::class, 'show'])->name('group-loans.show');
    Route::get('group-loans/{loan}/payment/{schedule}', [App\Http\Controllers\Admin\GroupLoanController::class, 'showPaymentForm'])->name('group-loans.payment.form');
    Route::post('group-loans/{loan}/payment/{schedule}', [App\Http\Controllers\Admin\GroupLoanController::class, 'recordPayment'])->name('group-loans.payment.record');
    Route::post('group-loans/{loan}/repayment', [App\Http\Controllers\Admin\GroupLoanController::class, 'recordRepayment'])->name('group-loans.repayment');
    Route::post('group-loans/schedules/{schedule}/penalty', [App\Http\Controllers\Admin\GroupLoanController::class, 'applyPenalty'])->name('group-loans.penalty');
    Route::get('group-loans/reports', [App\Http\Controllers\Admin\GroupLoanController::class, 'reports'])->name('group-loans.reports');

    // Financials Routes
    Route::get('financials', function() {
        return view('admin.financials.index');
    })->name('financials.index');
    
    Route::get('financials/create', function() {
        $members = \App\Models\Member::latest()->get();
        return view('admin.financials.create', compact('members'));
    })->name('financials.create');
    
    Route::post('financials', function() {
        return redirect()->route('admin.financials.index')->with('success', 'Financial saved successfully.');
    })->name('financials.store');
    
    // Cashflow Routes
    Route::get('cashflow', [App\Http\Controllers\Admin\CashflowController::class, 'index'])->name('cashflow.index');
    Route::get('cashflow/create', [App\Http\Controllers\Admin\CashflowController::class, 'create'])->name('cashflow.create');
    Route::post('cashflow', [App\Http\Controllers\Admin\CashflowController::class, 'store'])->name('cashflow.store');
    Route::get('cashflow/dashboard', [App\Http\Controllers\Admin\CashflowController::class, 'dashboard'])->name('cashflow.dashboard');
    Route::get('cashflow/monthly-statement', [App\Http\Controllers\Admin\CashflowController::class, 'monthlyStatement'])->name('cashflow.monthly-statement');
    Route::get('cashflow/fiscal-year-statement', [App\Http\Controllers\Admin\CashflowController::class, 'fiscalYearStatement'])->name('cashflow.fiscal-year-statement');
    Route::get('cashflow/{transaction}', [App\Http\Controllers\Admin\CashflowController::class, 'show'])->name('cashflow.show');
    Route::get('cashflow/{transaction}/edit', [App\Http\Controllers\Admin\CashflowController::class, 'edit'])->name('cashflow.edit');
    Route::put('cashflow/{transaction}', [App\Http\Controllers\Admin\CashflowController::class, 'update'])->name('cashflow.update');
    Route::delete('cashflow/{transaction}', [App\Http\Controllers\Admin\CashflowController::class, 'destroy'])->name('cashflow.destroy');
    Route::post('cashflow/{transaction}/approve', [App\Http\Controllers\Admin\CashflowController::class, 'approve'])->name('cashflow.approve');
    Route::post('cashflow/reconcile', [App\Http\Controllers\Admin\CashflowController::class, 'reconcile'])->name('cashflow.reconcile');
    Route::get('cashflow/export-monthly', [App\Http\Controllers\Admin\CashflowController::class, 'exportMonthlyStatement'])->name('cashflow.export.monthly');
    Route::get('cashflow/export-fiscal-year', [App\Http\Controllers\Admin\CashflowController::class, 'exportFiscalYearStatement'])->name('cashflow.export.fiscal-year');
    Route::get('cashflow/position', [App\Http\Controllers\Admin\CashflowController::class, 'getCashPosition'])->name('cashflow.position');
    
    // Import Routes
    Route::get('import', function() {
        return view('admin.import.index');
    })->name('import.index');
    
    Route::post('import/loans', function(\Illuminate\Http\Request $request) {
        return app(\App\Http\Controllers\ImportController::class)->importLoans($request);
    })->name('import.loans');
    
    Route::post('import/cashflow', function(\Illuminate\Http\Request $request) {
        return app(\App\Http\Controllers\ImportController::class)->importCashflow($request);
    })->name('import.cashflow');
    
    // Reports Routes
    Route::get('reports/index', [App\Http\Controllers\ReportsController::class, 'index'])->name('reports.index');
    Route::get('reports/members', [App\Http\Controllers\ReportsController::class, 'memberReports'])->name('reports.members');
    Route::get('reports/savings', [App\Http\Controllers\ReportsController::class, 'savingsReports'])->name('reports.savings');
    Route::get('reports/loans', [App\Http\Controllers\ReportsController::class, 'loanReports'])->name('reports.loans');
    Route::get('reports/cashflow', [App\Http\Controllers\ReportsController::class, 'cashflowReports'])->name('reports.cashflow');
});
