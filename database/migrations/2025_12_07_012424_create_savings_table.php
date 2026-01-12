<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSavingsTable extends Migration
{
    public function up()
    {
        Schema::create('member_financials', function (Blueprint $table) {
            $table->id();

            // Member identity
            $table->unsignedBigInteger('member_id')->index();
            $table->string('name');
            $table->string('number')->index(); // membership number

            // Contributions
            $table->decimal('savings', 15, 2)->default(0);
            $table->decimal('welfare', 15, 2)->default(0);
            $table->decimal('education_in', 15, 2)->default(0);

            // Deductions / penalties
            $table->decimal('fined', 15, 2)->default(0);
            $table->decimal('fines_paid', 15, 2)->default(0);
            $table->decimal('education_out', 15, 2)->default(0);

            // Loans
            $table->decimal('loan_repayments', 15, 2)->default(0);
            $table->decimal('loan_charges', 15, 2)->default(0);

            // Optional notes
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
        Schema::dropIfExists('member_financials');
    }
}
