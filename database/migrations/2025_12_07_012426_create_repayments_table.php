<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('repayments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_id')->index();
            $table->unsignedBigInteger('member_id')->index();
            
            $table->decimal('amount', 15, 2);
            $table->decimal('principal_component', 15, 2)->nullable();
            $table->decimal('interest_component', 15, 2)->nullable();

            $table->enum('method', ['cash', 'bank_transfer', 'mobile_money', 'cheque', 'other'])->default('cash');
            $table->string('reference')->nullable()->index();
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('received_by')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            // foreign keys
            // $table->foreign('loan_id')->references('id')->on('loans')->onDelete('cascade');
            // $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            // $table->foreign('received_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('repayments');
    }
}
