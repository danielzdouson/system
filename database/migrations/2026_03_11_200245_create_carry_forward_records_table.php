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
        Schema::create('carry_forward_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_fiscal_year_id')->constrained('fiscal_years')->onDelete('cascade');
            $table->foreignId('to_fiscal_year_id')->constrained('fiscal_years')->onDelete('cascade');
            $table->enum('item_type', ['loan', 'fine', 'investment']);
            $table->unsignedBigInteger('item_id');
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['from_fiscal_year_id', 'to_fiscal_year_id'], 'cf_fiscal_years_index');
            $table->index(['item_type', 'item_id'], 'cf_item_index');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carry_forward_records');
    }
};
