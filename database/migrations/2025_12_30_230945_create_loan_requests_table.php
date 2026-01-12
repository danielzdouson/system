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
        Schema::create('loan_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('fiscal_year_id')->constrained()->onDelete('cascade');
            $table->decimal('requested_amount', 12, 2);
            $table->string('loan_type'); // personal, emergency, business, etc.
            $table->integer('duration_months'); // loan duration in months
            $table->text('purpose')->nullable(); // loan purpose description
            $table->enum('status', ['pending', 'approved', 'rejected', 'withdrawn'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->decimal('member_savings_at_request', 12, 2); // member's total savings at time of request
            $table->decimal('existing_loan_balance', 12, 2)->default(0); // existing loan balance at time of request
            $table->timestamps();
            
            $table->index(['member_id', 'status']);
            $table->index(['fiscal_year_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_requests');
    }
};
