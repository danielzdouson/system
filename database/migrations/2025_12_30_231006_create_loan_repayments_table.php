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
        Schema::create('loan_repayments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->foreignId('repayment_schedule_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('installment_number'); // 1, 2, 3, etc.
            $table->decimal('amount_paid', 12, 2);
            $table->decimal('principal_component', 12, 2);
            $table->decimal('interest_component', 12, 2);
            $table->decimal('penalty_component', 12, 2)->default(0);
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'mobile_money', 'savings_deduction'])->default('cash');
            $table->string('transaction_reference')->nullable();
            $table->foreignId('received_by')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['loan_id', 'installment_number']);
            $table->index(['loan_id', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_repayments');
    }
};
