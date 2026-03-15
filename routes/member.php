<?php

use App\Http\Controllers\Member\MemberDashboardController;
use Illuminate\Support\Facades\Route;

// Simple test route without member middleware
Route::get('/simple-test', function () {
    return [
        'app_env' => config('app.env'),
        'auth_check' => auth()->check(),
        'user_id' => auth()->id(),
        'user_name' => auth()->user() ? auth()->user()->name : 'No user',
        'user_role' => auth()->user() ? auth()->user()->role : 'No role'
    ];
});

// Root level test route
Route::get('/root-test', function () {
    return 'Member root route is working!';
});

// Member routes - require authentication only
Route::middleware(['auth'])->name('member.')->group(function () {
    
    // Test route for debugging
    Route::get('/test', function () {
        $user = auth()->user();
        $member = $user ? $user->member : null;
        return [
            'user_logged_in' => $user ? true : false,
            'user_name' => $user ? $user->name : 'No user',
            'member_exists' => $member ? true : false,
            'member_id' => $member ? $member->id : 'No member',
            'user_role' => $user ? $user->role : 'No role'
        ];
    })->name('test');

    // Simple test route
    Route::get('/simple', function () {
        return 'Member routes are working!';
    })->name('simple');
    
    // Dashboard
    Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
    
    // Default redirect to dashboard
    Route::get('/', function () {
        return redirect()->route('member.dashboard');
    });
    
    // Transaction History
    Route::get('/transactions', [MemberDashboardController::class, 'transactions'])->name('transactions');
    
    // Statement Download
    Route::post('/statement/download', [MemberDashboardController::class, 'downloadStatement'])->name('statement.download');
    
    // Loans
    Route::get('/loans', [MemberDashboardController::class, 'loans'])->name('loans');
    Route::get('/loans/{id}/payment', [App\Http\Controllers\Member\MemberLoanController::class, 'payment'])->name('loans.payment');
    Route::get('/loans/{id}/details', [App\Http\Controllers\Member\MemberLoanController::class, 'details'])->name('loans.details');
    Route::get('/loans/{id}/statement', [App\Http\Controllers\Member\MemberLoanController::class, 'statement'])->name('loans.statement');
    Route::get('/loans/{id}/certificate', [App\Http\Controllers\Member\MemberLoanController::class, 'certificate'])->name('loans.certificate');
    Route::get('/loans/{id}/schedule', [App\Http\Controllers\Member\MemberLoanController::class, 'schedule'])->name('loans.schedule');
    Route::get('/loans/export', [App\Http\Controllers\Member\MemberLoanController::class, 'export'])->name('loans.export');
    Route::get('/loans/apply', [App\Http\Controllers\Member\MemberLoanController::class, 'apply'])->name('loans.apply');
    Route::get('/loans/info', [App\Http\Controllers\Member\MemberLoanController::class, 'info'])->name('loans.info');
    
    // Profile route
    Route::get('/profile', function() {
        return redirect()->route('member.dashboard');
    })->name('profile.edit');

    // Document Management Routes
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [App\Http\Controllers\Member\DocumentController::class, 'index'])->name('index');
        Route::get('/{document}', [App\Http\Controllers\Member\DocumentController::class, 'show'])->name('show');
        Route::post('/{document}/download', [App\Http\Controllers\Member\DocumentController::class, 'download'])->name('download');
        Route::get('/{document}/upload-form', [App\Http\Controllers\Member\DocumentController::class, 'uploadForm'])->name('upload-form');
        Route::post('/{document}/upload-form', [App\Http\Controllers\Member\DocumentController::class, 'storeUpload'])->name('store-upload');
        Route::get('/my-uploads', [App\Http\Controllers\Member\DocumentController::class, 'myUploads'])->name('my-uploads');
        
        // Guarantor Routes
        Route::get('/pending-guarantees', [App\Http\Controllers\Member\DocumentController::class, 'pendingGuarantees'])->name('pending-guarantees');
        Route::get('/guarantee-details/{uploadedForm}', [App\Http\Controllers\Member\DocumentController::class, 'guaranteeDetails'])->name('guarantee-details');
        Route::post('/guarantee-details/{uploadedForm}/confirm', [App\Http\Controllers\Member\DocumentController::class, 'confirmGuarantee'])->name('confirm-guarantee');
        Route::post('/withdraw-guarantee/{loanGuarantor}', [App\Http\Controllers\Member\DocumentController::class, 'withdrawGuarantee'])->name('withdraw-guarantee');
        Route::get('/guarantor-history', [App\Http\Controllers\Member\DocumentController::class, 'guarantorHistory'])->name('guarantor-history');
        
        // Test route for guarantor history
        Route::get('/guarantor-history-test', function () {
            return 'Guarantor history route is working!';
        })->name('guarantor-history-test');
    });
});
