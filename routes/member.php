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
    
    // Loans
    Route::get('/loans', [MemberDashboardController::class, 'loans'])->name('loans');
    
    // Profile route
    Route::get('/profile', function() {
        return redirect()->route('member.dashboard');
    })->name('profile.edit');
});
