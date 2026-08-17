<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('member_financials')) {
            Schema::create('member_financials', function (Blueprint $table) {
                $table->id();
                $table->foreignId('member_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->string('number')->nullable();
                $table->decimal('savings', 15, 2)->default(0);
                $table->decimal('welfare', 15, 2)->default(0);
                $table->decimal('education_in', 15, 2)->default(0);
                $table->decimal('fined', 15, 2)->default(0);
                $table->decimal('fines_paid', 15, 2)->default(0);
                $table->decimal('education_out', 15, 2)->default(0);
                $table->decimal('loan_repayments', 15, 2)->default(0);
                $table->decimal('loan_charges', 15, 2)->default(0);
                $table->text('notes')->nullable();
                $table->timestamp('deleted_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('member_financials');
    }
};
