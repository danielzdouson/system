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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('UGX');
            $table->enum('payment_type', ['loan_payment', 'fine_payment', 'savings_deposit', 'education', 'other']);
            $table->enum('payment_method', ['mobile_money', 'card', 'bank_transfer']);
            $table->enum('mobile_network', ['MTN', 'AIRTEL'])->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('transaction_reference')->unique(); // Pesapal merchant reference
            $table->string('pesapal_tracking_id')->nullable(); // Pesapal transaction tracking ID
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->unsignedBigInteger('related_id')->nullable(); // loan_id, fine_id, etc.
            $table->string('related_type')->nullable();
            $table->json('admin_bank_details')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['member_id', 'status']);
            $table->index(['payment_type', 'status']);
            $table->index('transaction_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
