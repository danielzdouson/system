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
        Schema::create('investment_transactions', function (Blueprint $table) {
            $table->id();
            
            // Link to investment portfolio
            $table->foreignId('investment_portfolio_id')->constrained()->onDelete('cascade');
            
            // Transaction details
            $table->enum('transaction_type', [
                'INITIAL_INVESTMENT',    // Money invested
                'ADDITIONAL_CONTRIBUTION', // Top-up investment
                'INTEREST_INCOME',       // Interest received
                'DIVIDEND_INCOME',       // Dividend received
                'CAPITAL_GAIN',          // Profit from sale
                'PRINCIPAL_RETURN',      // Maturity proceeds
                'INVESTMENT_EXPENSE',    // Fees, taxes, etc.
                'WITHDRAWAL',            // Money withdrawn
                'REINVESTMENT'           // Returns reinvested
            ])->notNull();
            
            $table->decimal('amount', 15, 2)->notNull();
            $table->date('transaction_date')->notNull();
            $table->text('description')->nullable();
            
            // Reference information
            $table->string('reference_number', 100)->nullable();
            $table->string('receipt_number', 100)->nullable();
            $table->string('payment_method', 50)->nullable();
            
            // Financial tracking
            $table->decimal('running_balance', 15, 2)->nullable(); // Cumulative investment value
            $table->decimal('accumulated_returns', 15, 2)->default(0); // Total returns to date
            
            // Audit and approval
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('investment_portfolio_id');
            $table->index('transaction_type');
            $table->index('transaction_date');
            $table->index('reference_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_transactions');
    }
};
