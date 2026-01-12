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
        Schema::create('cash_flows', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->string('description');
            $table->string('reference_number')->nullable();
            $table->enum('type', ['income', 'expense']);
            $table->string('category');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method');
            $table->enum('status', ['cleared', 'pending', 'reconciled'])->default('pending');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            
            // Add indexes for better performance on frequently queried columns
            $table->index('transaction_date');
            $table->index('type');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_flows');
    }
};
