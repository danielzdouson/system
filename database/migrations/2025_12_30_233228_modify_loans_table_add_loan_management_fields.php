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
        Schema::table('loans', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('loans', 'loan_request_id')) {
                $table->unsignedBigInteger('loan_request_id')->nullable();
            }
            if (!Schema::hasColumn('loans', 'fiscal_year_id')) {
                $table->foreignId('fiscal_year_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('loans', 'loan_number')) {
                $table->string('loan_number')->unique();
            }
            if (!Schema::hasColumn('loans', 'principal_amount')) {
                $table->decimal('principal_amount', 12, 2);
            }
            if (!Schema::hasColumn('loans', 'interest_rate')) {
                $table->decimal('interest_rate', 5, 2);
            }
            if (!Schema::hasColumn('loans', 'interest_type')) {
                $table->enum('interest_type', ['flat', 'reducing_balance'])->default('reducing_balance');
            }
            if (!Schema::hasColumn('loans', 'duration_months')) {
                $table->integer('duration_months');
            }
            if (!Schema::hasColumn('loans', 'monthly_installment')) {
                $table->decimal('monthly_installment', 12, 2);
            }
            if (!Schema::hasColumn('loans', 'total_interest')) {
                $table->decimal('total_interest', 12, 2);
            }
            if (!Schema::hasColumn('loans', 'total_repayable')) {
                $table->decimal('total_repayable', 12, 2);
            }
            if (!Schema::hasColumn('loans', 'balance')) {
                $table->decimal('balance', 12, 2);
            }
            if (!Schema::hasColumn('loans', 'paid_amount')) {
                $table->decimal('paid_amount', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('loans', 'disbursement_date')) {
                $table->date('disbursement_date');
            }
            if (!Schema::hasColumn('loans', 'first_payment_date')) {
                $table->date('first_payment_date');
            }
            if (!Schema::hasColumn('loans', 'maturity_date')) {
                $table->date('maturity_date');
            }
            if (!Schema::hasColumn('loans', 'completed_at')) {
                $table->timestamp('completed_at')->nullable();
            }
            if (!Schema::hasColumn('loans', 'disbursed_by')) {
                $table->unsignedBigInteger('disbursed_by');
            }
            if (!Schema::hasColumn('loans', 'status')) {
                $table->enum('status', ['active', 'completed', 'defaulted', 'suspended'])->default('active');
            }
            
            // Add indexes only for columns that exist
            $table->index('loan_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
