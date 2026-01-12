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
        Schema::create('member_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('fiscal_year_id')->constrained()->onDelete('cascade');
            $table->decimal('total_deposited', 15, 2)->default(0);
            $table->decimal('total_distributed', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->decimal('savings_balance', 15, 2)->default(0);
            $table->decimal('welfare_balance', 15, 2)->default(0);
            $table->decimal('fines_balance', 15, 2)->default(0);
            $table->decimal('other_balance', 15, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['member_id', 'fiscal_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_accounts');
    }
};
