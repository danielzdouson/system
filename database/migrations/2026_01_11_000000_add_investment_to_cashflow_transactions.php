<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cashflow_transactions', function (Blueprint $table) {
            // Add INVESTMENT to the reference_type enum
            $table->enum('reference_type', [
                'DEPOSIT', 
                'LOAN_DISBURSEMENT', 
                'LOAN_REPAYMENT', 
                'WELFARE_PAYMENT', 
                'FINE_PAYMENT', 
                'EXPENSE', 
                'INVESTMENT',
                'OTHER'
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cashflow_transactions', function (Blueprint $table) {
            // Remove INVESTMENT from reference_type enum
            $table->enum('reference_type', [
                'DEPOSIT', 
                'LOAN_DISBURSEMENT', 
                'LOAN_REPAYMENT', 
                'WELFARE_PAYMENT', 
                'FINE_PAYMENT', 
                'EXPENSE', 
                'OTHER'
            ])->change();
        });
    }
};
