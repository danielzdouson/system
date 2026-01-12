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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_request_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('fiscal_year_id')->constrained()->onDelete('cascade');
            $table->string('loan_number')->unique(); // auto-generated loan number
            $table->decimal('principal_amount', 12, 2);
            $table->decimal('interest_rate', 5, 2); // interest rate as percentage
            $table->enum('interest_type', ['flat', 'reducing_balance'])->default('reducing_balance');
            $table->integer('duration_months');
            $table->decimal('monthly_installment', 12, 2); // calculated monthly payment
            $table->decimal('total_interest', 12, 2); // total interest over loan period
            $table->decimal('total_repayable', 12, 2); // principal + interest
            $table->decimal('balance', 12, 2); // current outstanding balance
            $table->decimal('paid_amount', 12, 2)->default(0); // total amount paid so far
            $table->enum('status', ['active', 'completed', 'defaulted', 'suspended'])->default('active');
            $table->date('disbursement_date');
            $table->date('first_payment_date');
            $table->date('maturity_date'); // final payment due date
            $table->date('completed_at')->nullable();
            $table->foreignId('disbursed_by')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['member_id', 'status']);
            $table->index(['fiscal_year_id', 'status']);
            $table->index('loan_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
