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
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->decimal('loan_amount', 15, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->integer('loan_term'); // in months
            $table->string('loan_purpose');
            $table->enum('loan_status', [
                'pending', 'approved', 'disbursed', 'active', 
                'arrears', 'completed', 'defaulted', 'written_off'
            ])->default('pending');
            $table->date('disbursement_date')->nullable();
            $table->date('first_payment_date')->nullable();
            $table->date('maturity_date')->nullable();
            $table->decimal('monthly_payment', 15, 2);
            $table->decimal('total_interest', 15, 2);
            $table->decimal('total_repayment', 15, 2);
            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('arrears', 15, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->string('guarantor_name')->nullable();
            $table->string('guarantor_phone', 20)->nullable();
            $table->text('guarantor_address')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['member_id', 'loan_status']);
            $table->index('loan_status');
            $table->index('disbursement_date');
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
