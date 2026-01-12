<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id')->nullable()->index();
            
            // transaction amount & type
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['deposit', 'withdrawal', 'loan_disbursement', 'repayment', 'fee', 'adjustment'])->index();
            
            // relation to domain entities (nullable)
            $table->unsignedBigInteger('related_id')->nullable()->index();
            $table->string('related_type')->nullable()->index(); // e.g. "App\\Models\\Loan" or "App\\Models\\Saving"
            
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('reference')->nullable()->index();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('handled_by')->nullable(); // user who processed it

            $table->timestamps();

            // foreign keys
            // $table->foreign('member_id')->references('id')->on('members')->onDelete('set null');
            // $table->foreign('handled_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
