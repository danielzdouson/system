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

// Dashboard route - redirect based on user role
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user && $user->isMember()) {
        return redirect()->route('member.dashboard');
    }
    return app(App\Http\Controllers\Admin\DashboardController::class)->index();
})->middleware('auth')->name('dashboard')->middleware('auth');

// Debug route - check member layout
Route::get('/debug-member', function () {
    $user = auth()->user();
    return 'User: ' . ($user ? $user->name : 'Not logged in') . 
           ', Role: ' . ($user ? $user->role : 'No role') . 
           ', Is Member: ' . ($user ? ($user->isMember() ? 'YES' : 'NO') : 'No user');
})->middleware('auth');

// Temporarily change user to member for testing
Route::get('/make-member', function () {
    $user = auth()->user();
    if ($user) {
        $user->role = 'member';
        $user->save();
        return 'Changed role to member. Please refresh the page.';
    }
    return 'No user logged in.';
})->middleware('auth');

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
    Route::post('fiscal-years/{fiscalYear}/activate', [App\Http\Controllers\Admin\FiscalYearController::class, 'activate'])->name('fiscal-years.activate');
    Route::post('fiscal-years/clear-session', [App\Http\Controllers\Admin\FiscalYearController::class, 'clearSession'])->name('fiscal-years.clear-session');
    
    // Carry Forward Routes
    Route::get('fiscal-years/{fromFiscalYear}/carry-forward/{toFiscalYear}', [App\Http\Controllers\Admin\FiscalYearController::class, 'carryForward'])->name('fiscal-years.carry-forward');
    Route::post('fiscal-years/{fromFiscalYear}/carry-forward/{toFiscalYear}/process', [App\Http\Controllers\Admin\FiscalYearController::class, 'processCarryForward'])->name('fiscal-years.carry-forward.process');
    Route::get('fiscal-years/carry-forward-history/{fiscalYear?}', [App\Http\Controllers\Admin\FiscalYearController::class, 'carryForwardHistory'])->name('fiscal-years.carry-forward.history');

    // Group Savings Routes
    Route::get('group-savings/dashboard', [App\Http\Controllers\Admin\GroupSavingsController::class, 'dashboard'])->name('group-savings.dashboard');
    Route::get('group-savings/monthly/{month}', [App\Http\Controllers\Admin\GroupSavingsController::class, 'monthlyView'])->name('group-savings.monthly');
    Route::get('group-savings/monthly/{month}/{fiscal_year}', [App\Http\Controllers\Admin\GroupSavingsController::class, 'monthlyViewWithFiscalYear'])->name('group-savings.monthly.fiscal');
    Route::get('group-savings/create-deposit', [App\Http\Controllers\Admin\GroupSavingsController::class, 'createDeposit'])->name('group-savings.create-deposit');
    Route::post('group-savings/deposit', [App\Http\Controllers\Admin\GroupSavingsController::class, 'storeDeposit'])->name('group-savings.store-deposit');
    Route::get('group-savings/pending', [App\Http\Controllers\Admin\GroupSavingsController::class, 'pendingMonths'])->name('group-savings.pending');
    Route::get('group-savings/distribute/{depositId}', [App\Http\Controllers\Admin\GroupSavingsController::class, 'distributeDeposit'])->name('group-savings.distribute');
    Route::post('group-savings/distribute/{depositId}', [App\Http\Controllers\Admin\GroupSavingsController::class, 'storeDistribution'])->name('group-savings.store-distribution');
    Route::get('group-savings/distribute-balance/{memberId}/{month}', [App\Http\Controllers\Admin\GroupSavingsController::class, 'distributeBalance'])->name('group-savings.distribute-balance');
    Route::get('group-savings/distribute-balance/{memberId}/{month}/{fiscalYearId}', [App\Http\Controllers\Admin\GroupSavingsController::class, 'distributeBalance'])->name('group-savings.distribute-balance.fiscal');
    Route::post('group-savings/distribute-balance/{memberId}/{month}', [App\Http\Controllers\Admin\GroupSavingsController::class, 'storeBalanceDistribution'])->name('group-savings.store-balance-distribution');
    Route::post('group-savings/distribute-balance/{memberId}/{month}/{fiscalYearId}', [App\Http\Controllers\Admin\GroupSavingsController::class, 'storeBalanceDistribution'])->name('group-savings.store-balance-distribution.fiscal');
    // Enhanced Fines Management Routes (Dedicated System)
    Route::prefix('fines')->name('fines.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\FineController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\FineController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\FineController::class, 'store'])->name('store');
        Route::get('/reports', [App\Http\Controllers\Admin\FineController::class, 'reports'])->name('reports');
        Route::get('/export', [App\Http\Controllers\Admin\FineController::class, 'export'])->name('export');
        Route::post('/bulk-apply', [App\Http\Controllers\Admin\FineController::class, 'bulkApply'])->name('bulk-apply');
        Route::post('/auto-apply', [App\Http\Controllers\Admin\FineController::class, 'autoApply'])->name('auto-apply');
        Route::get('/{fine}', [App\Http\Controllers\Admin\FineController::class, 'show'])->name('show');
        Route::get('/{fine}/edit', [App\Http\Controllers\Admin\FineController::class, 'edit'])->name('edit');
        Route::put('/{fine}', [App\Http\Controllers\Admin\FineController::class, 'update'])->name('update');
        Route::delete('/{fine}', [App\Http\Controllers\Admin\FineController::class, 'destroy'])->name('destroy');
        Route::post('/{fine}/pay', [App\Http\Controllers\Admin\FineController::class, 'pay'])->name('pay');
        Route::post('/{fine}/waive', [App\Http\Controllers\Admin\FineController::class, 'waive'])->name('waive');
    });

    // Legacy Group Savings Fines Routes (Keep for backward compatibility)
    Route::get('group-savings/fines', [App\Http\Controllers\Admin\GroupSavingsController::class, 'finesIndex'])->name('group-savings.fines');
    Route::post('group-savings/fines/{fineId}/pay', [App\Http\Controllers\Admin\GroupSavingsController::class, 'payFine'])->name('group-savings.fines.pay');
    Route::post('group-savings/fines/{fineId}/waive', [App\Http\Controllers\Admin\GroupSavingsController::class, 'waiveFine'])->name('group-savings.fines.waive');
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
    Route::post('accounts/{memberId}/create-user', [App\Http\Controllers\Admin\AccountController::class, 'createUserAccount'])->name('accounts.create-user');
    Route::post('accounts/{memberId}/reset-password', [App\Http\Controllers\Admin\AccountController::class, 'resetPassword'])->name('accounts.reset-password');
    Route::post('accounts/{memberId}/toggle-status', [App\Http\Controllers\Admin\AccountController::class, 'toggleUserStatus'])->name('accounts.toggle-status');
    Route::get('group-loans/all', [App\Http\Controllers\Admin\GroupLoanController::class, 'loans'])->name('group-loans.all');
    Route::get('group-loans/{loan}', [App\Http\Controllers\Admin\GroupLoanController::class, 'show'])->name('group-loans.show');
    Route::get('group-loans/{loan}/payment/{schedule}', [App\Http\Controllers\Admin\GroupLoanController::class, 'showPaymentForm'])->name('group-loans.payment.form');
    Route::post('group-loans/{loan}/payment/{schedule}', [App\Http\Controllers\Admin\GroupLoanController::class, 'recordPayment'])->name('group-loans.payment.record');
    Route::post('group-loans/{loan}/repayment', [App\Http\Controllers\Admin\GroupLoanController::class, 'recordRepayment'])->name('group-loans.repayment');
    Route::post('group-loans/schedules/{schedule}/penalty', [App\Http\Controllers\Admin\GroupLoanController::class, 'applyPenalty'])->name('group-loans.penalty');
    Route::get('group-loans/reports', [App\Http\Controllers\Admin\GroupLoanController::class, 'reports'])->name('group-loans.reports');

    // Financials Routes
    Route::get('financials', [App\Http\Controllers\Admin\FinancialController::class, 'index'])->name('financials.index');
    
    Route::get('financials/create', function() {
        $members = \App\Models\Member::latest()->get();
        return view('admin.financials.create', compact('members'));
    })->name('financials.create');
    
    Route::post('financials', function() {
        return redirect()->route('admin.financials.index')->with('success', 'Financial saved successfully.');
    })->name('financials.store');
    
    Route::get('financials/members-sector', [App\Http\Controllers\Admin\MemberFinancialController::class, 'membersSector'])->name('financials.members-sector');
    
    // Cashflow Routes
    Route::get('cashflow', [App\Http\Controllers\Admin\CashflowController::class, 'index'])->name('cashflow.index');
    Route::get('cashflow/load', [App\Http\Controllers\Admin\CashflowController::class, 'loadTransactionsAjax'])->name('cashflow.load');
    Route::get('cashflow/create', [App\Http\Controllers\Admin\CashflowController::class, 'create'])->name('cashflow.create');
    Route::post('cashflow', [App\Http\Controllers\Admin\CashflowController::class, 'store'])->name('cashflow.store');
    Route::get('cashflow/dashboard', [App\Http\Controllers\Admin\CashflowController::class, 'dashboard'])->name('cashflow.dashboard');
    Route::get('cashflow/monthly-statement', [App\Http\Controllers\Admin\CashflowController::class, 'monthlyStatement'])->name('cashflow.monthly-statement');
    Route::get('cashflow/fiscal-year-statement', [App\Http\Controllers\Admin\CashflowController::class, 'fiscalYearStatement'])->name('cashflow.fiscal-year-statement');
    // Export routes must come BEFORE parameterized routes
    Route::get('cashflow/export', [App\Http\Controllers\Admin\CashflowController::class, 'export'])->name('cashflow.export');
    Route::get('cashflow/export-monthly', [App\Http\Controllers\Admin\CashflowController::class, 'exportMonthlyStatement'])->name('cashflow.export.monthly');
    Route::get('cashflow/comprehensive-monthly', [App\Http\Controllers\Admin\CashflowController::class, 'comprehensiveMonthlyReport'])->name('cashflow.comprehensive-monthly');
    // Parameterized routes must come AFTER specific routes
    Route::get('cashflow/{transaction}', [App\Http\Controllers\Admin\CashflowController::class, 'show'])->name('cashflow.show');
    Route::get('cashflow/{transaction}/edit', [App\Http\Controllers\Admin\CashflowController::class, 'edit'])->name('cashflow.edit');
    Route::put('cashflow/{transaction}', [App\Http\Controllers\Admin\CashflowController::class, 'update'])->name('cashflow.update');
    Route::delete('cashflow/{transaction}', [App\Http\Controllers\Admin\CashflowController::class, 'destroy'])->name('cashflow.destroy');
    Route::post('cashflow/{transaction}/approve', [App\Http\Controllers\Admin\CashflowController::class, 'approve'])->name('cashflow.approve');
    Route::post('cashflow/reconcile', [App\Http\Controllers\Admin\CashflowController::class, 'reconcile'])->name('cashflow.reconcile');
    Route::post('cashflow/bulk-approve', [App\Http\Controllers\Admin\CashflowController::class, 'bulkApprove'])->name('cashflow.bulk-approve');

    // Cash Flow Dashboard Routes
    Route::get('cashflow-dashboard', [App\Http\Controllers\Admin\CashFlowDashboardController::class, 'index'])->name('admin.cashflow.dashboard');
    Route::get('cashflow-dashboard/data', [App\Http\Controllers\Admin\CashFlowDashboardController::class, 'getDashboardData'])->name('admin.cashflow.dashboard.data');
    Route::get('cashflow-dashboard/months', [App\Http\Controllers\Admin\CashFlowDashboardController::class, 'getMonths'])->name('admin.cashflow.dashboard.months');
    Route::get('cashflow/export-monthly-pdf', [App\Http\Controllers\Admin\CashflowController::class, 'exportMonthlyStatementPDF'])->name('cashflow.export.monthly.pdf');
    Route::get('cashflow/export-fiscal-year', [App\Http\Controllers\Admin\CashflowController::class, 'exportFiscalYearStatement'])->name('cashflow.export.fiscal-year');
    Route::get('cashflow/position', [App\Http\Controllers\Admin\CashflowController::class, 'getCashPosition'])->name('cashflow.position');
    
    // Investment Management Routes
    Route::get('/investments', [App\Http\Controllers\Admin\InvestmentController::class, 'index'])->name('investments.index');
    Route::get('/investments/create', [App\Http\Controllers\Admin\InvestmentController::class, 'create'])->name('investments.create');
    Route::post('/investments', [App\Http\Controllers\Admin\InvestmentController::class, 'store'])->name('investments.store');
    Route::get('/investments/{investment}', [App\Http\Controllers\Admin\InvestmentController::class, 'show'])->name('investments.show');
    Route::get('/investments/{investment}/edit', [App\Http\Controllers\Admin\InvestmentController::class, 'edit'])->name('investments.edit');
    Route::put('/investments/{investment}', [App\Http\Controllers\Admin\InvestmentController::class, 'update'])->name('investments.update');
    Route::delete('/investments/{investment}', [App\Http\Controllers\Admin\InvestmentController::class, 'destroy'])->name('investments.destroy');
    Route::post('/investments/{investment}/add-transaction', [App\Http\Controllers\Admin\InvestmentController::class, 'addTransaction'])->name('investments.add-transaction');
    Route::post('/investments/{investment}/mark-matured', [App\Http\Controllers\Admin\InvestmentController::class, 'markAsMatured'])->name('investments.mark-matured');
    Route::post('/investments/{investment}/close', [App\Http\Controllers\Admin\InvestmentController::class, 'close'])->name('investments.close');
    
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

    // Document Management Routes
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\DocumentController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\DocumentController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\DocumentController::class, 'store'])->name('store');
        Route::get('/{document}/edit', [App\Http\Controllers\Admin\DocumentController::class, 'edit'])->name('edit');
        Route::put('/{document}', [App\Http\Controllers\Admin\DocumentController::class, 'update'])->name('update');
        Route::delete('/{document}', [App\Http\Controllers\Admin\DocumentController::class, 'destroy'])->name('destroy');
        
        // Uploaded Forms Management
        Route::get('/uploaded-forms', [App\Http\Controllers\Admin\DocumentController::class, 'downloadForms'])->name('uploaded-forms');
        Route::get('/uploaded-forms/{uploadedForm}/review', [App\Http\Controllers\Admin\DocumentController::class, 'reviewForm'])->name('review-form');
        Route::post('/uploaded-forms/{uploadedForm}/approve', [App\Http\Controllers\Admin\DocumentController::class, 'approveForm'])->name('approve-form');
        Route::post('/uploaded-forms/{uploadedForm}/reject', [App\Http\Controllers\Admin\DocumentController::class, 'rejectForm'])->name('reject-form');
        Route::get('/uploaded-forms/{uploadedForm}/guarantors', [App\Http\Controllers\Admin\DocumentController::class, 'viewGuarantors'])->name('view-guarantors');
        Route::get('/uploaded-forms/{uploadedForm}/download', [App\Http\Controllers\Admin\DocumentController::class, 'downloadUploadedForm'])->name('download-uploaded-form');
    });

    // Backup Management Routes
    Route::prefix('backups')->name('backups.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\BackupController::class, 'index'])->name('index');
        Route::post('/verify-password', [App\Http\Controllers\Admin\BackupController::class, 'verifyPassword'])->name('verify-password');
        Route::post('/revoke-access', [App\Http\Controllers\Admin\BackupController::class, 'revokeAccess'])->name('revoke-access');
        Route::post('/create', [App\Http\Controllers\Admin\BackupController::class, 'create'])->name('create');
        Route::get('/download/{filename}', [App\Http\Controllers\Admin\BackupController::class, 'download'])->name('download');
        Route::delete('/{filename}', [App\Http\Controllers\Admin\BackupController::class, 'delete'])->name('delete');
        Route::post('/restore/{filename}', [App\Http\Controllers\Admin\BackupController::class, 'restore'])->name('restore');
    });
});

// Test route directly in web.php
Route::get('/member/documents/pending-guarantees', [App\Http\Controllers\Member\DocumentController::class, 'pendingGuarantees'])->name('test-pending-guarantees');
Route::get('/member/documents/guarantor-history', [App\Http\Controllers\Member\DocumentController::class, 'guarantorHistory'])->name('test-guarantor-history');
