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
        Schema::create('admin_bank_details', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name');
            $table->string('account_name');
            $table->string('account_number')->nullable();
            $table->string('branch')->nullable();
            $table->string('swift_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->string('mobile_money_provider')->nullable(); // MTN, AIRTEL
            $table->string('mobile_money_number')->nullable();
            $table->text('instructions')->nullable();
            $table->timestamps();
            
            $table->index('is_active');
            $table->index('is_default');
            $table->index('mobile_money_provider');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_bank_details');
    }
};
