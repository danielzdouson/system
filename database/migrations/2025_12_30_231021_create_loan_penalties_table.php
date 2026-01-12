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
        Schema::create('loan_penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->foreignId('repayment_schedule_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('fiscal_year_id')->constrained()->onDelete('cascade');
            $table->decimal('penalty_amount', 12, 2);
            $table->enum('penalty_type', ['late_payment', 'missed_payment', 'default_interest'])->default('late_payment');
            $table->date('penalty_date');
            $table->date('due_date_of_missed_payment'); // the original due date that was missed
            $table->integer('days_overdue');
            $table->decimal('penalty_rate', 5, 2); // penalty rate as percentage
            $table->enum('status', ['pending', 'paid', 'waived'])->default('pending');
            $table->date('paid_date')->nullable();
            $table->foreignId('waived_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('waiver_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['loan_id', 'status']);
            $table->index(['member_id', 'status']);
            $table->index(['fiscal_year_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_penalties');
    }
};
