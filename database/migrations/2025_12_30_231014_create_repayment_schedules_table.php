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
        Schema::create('repayment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->integer('installment_number');
            $table->date('due_date');
            $table->decimal('principal_due', 12, 2);
            $table->decimal('interest_due', 12, 2);
            $table->decimal('total_due', 12, 2);
            $table->decimal('principal_paid', 12, 2)->default(0);
            $table->decimal('interest_paid', 12, 2)->default(0);
            $table->decimal('penalty_charged', 12, 2)->default(0);
            $table->decimal('penalty_paid', 12, 2)->default(0);
            $table->enum('status', ['pending', 'paid', 'overdue', 'partially_paid'])->default('pending');
            $table->date('paid_date')->nullable();
            $table->decimal('outstanding_balance', 12, 2); // balance after this installment
            $table->timestamps();
            
            $table->index(['loan_id', 'installment_number']);
            $table->index(['loan_id', 'due_date']);
            $table->index(['loan_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repayment_schedules');
    }
};
