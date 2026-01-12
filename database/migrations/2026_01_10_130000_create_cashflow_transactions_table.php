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
        Schema::create('cashflow_transactions', function (Blueprint $table) {
            $table->id();
            
            // Transaction details
            $table->date('transaction_date');
            $table->enum('transaction_type', ['INFLOW', 'OUTFLOW'])->notNull();
            $table->enum('category', ['OPERATING', 'INVESTING', 'FINANCING'])->notNull();
            $table->string('subcategory', 100);
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2)->notNull();
            
            // Reference to source transactions
            $table->enum('reference_type', [
                'DEPOSIT', 'LOAN_DISBURSEMENT', 'LOAN_REPAYMENT', 
                'WELFARE_PAYMENT', 'FINE_PAYMENT', 'EXPENSE', 'OTHER'
            ])->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_number', 100)->nullable();
            
            // Payment and status
            $table->string('payment_method', 50)->nullable();
            $table->enum('status', ['PENDING', 'CLEARED', 'RECONCILED'])->default('PENDING');
            
            // Relationships
            $table->foreignId('fiscal_year_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('member_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            // Additional fields
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('transaction_date');
            $table->index('transaction_type');
            $table->index('category');
            $table->index(['reference_type', 'reference_id']);
            $table->index('fiscal_year_id');
            $table->index('member_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashflow_transactions');
    }
};
