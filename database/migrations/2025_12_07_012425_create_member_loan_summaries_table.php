<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberLoanSummariesTable extends Migration
{
    public function up()
    {
        Schema::create('member_loan_summaries', function (Blueprint $table) {
            $table->id();

            // Member identity
            $table->unsignedBigInteger('member_id')->index();
            $table->string('name');

            // Loan figures
            $table->decimal('loan_brought_forward', 15, 2)->default(0);
            $table->decimal('loan_issued_current_year', 15, 2)->default(0);
            $table->decimal('current_year_loan_plus_interest', 15, 2)->default(0);

            $table->decimal('loan_balance_without_fines', 15, 2)->default(0);
            $table->decimal('loan_out', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);

            // Optional bookkeeping
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign key
            $table->foreign('member_id')
                  ->references('id')
                  ->on('members')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('member_loan_summaries');
    }
}
