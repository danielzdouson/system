<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('member_loan_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('loan_brought_forward', 15, 2)->default(0);
            $table->decimal('loan_issued_current_year', 15, 2)->default(0);
            $table->decimal('current_year_loan_plus_interest', 15, 2)->default(0);
            $table->decimal('loan_balance_without_fines', 15, 2)->default(0);
            $table->decimal('loan_out', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('member_loan_summaries');
    }
};
