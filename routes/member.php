<?php

use App\Http\Controllers\Member\MemberDashboardController;
use Illuminate\Support\Facades\Route;

// Member routes - require authentication and member role
Route::middleware(['auth', 'member'])->prefix('member')->name('member.')->group(function () {
    
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
    });
});
