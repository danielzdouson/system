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
        Schema::create('investment_portfolios', function (Blueprint $table) {
            $table->id();
            
            // Investment details
            $table->string('name', 200);
            $table->enum('investment_type', [
                'FIXED_DEPOSIT', 
                'TREASURY_BILL', 
                'CORPORATE_BOND', 
                'EQUITY', 
                'MUTUAL_FUND',
                'REAL_ESTATE',
                'OTHER'
            ])->notNull();
            
            $table->string('institution', 200); // Bank/Financial institution
            $table->decimal('principal_amount', 15, 2)->notNull();
            $table->decimal('interest_rate', 8, 4)->nullable(); // Annual interest rate
            $table->date('investment_date')->notNull();
            $table->date('maturity_date')->nullable();
            
            // Status and tracking
            $table->enum('status', ['ACTIVE', 'MATURED', 'CLOSED', 'DEFAULTED'])->default('ACTIVE');
            $table->decimal('current_value', 15, 2)->nullable(); // Current market value
            $table->decimal('total_returns', 15, 2)->default(0); // Total returns received
            
            // Reference and documentation
            $table->string('reference_number', 100)->unique();
            $table->string('account_number', 100)->nullable();
            $table->text('notes')->nullable();
            
            // Approvals and audit
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('investment_date');
            $table->index('maturity_date');
            $table->index('status');
            $table->index('investment_type');
            $table->index('reference_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_portfolios');
    }
};
