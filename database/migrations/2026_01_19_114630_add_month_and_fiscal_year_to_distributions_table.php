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
        Schema::table('distributions', function (Blueprint $table) {
            $table->integer('month')->nullable()->after('description');
            $table->foreignId('fiscal_year_id')->nullable()->after('month')->constrained()->onDelete('cascade');
            $table->unique(
                ['deposit_id', 'type', 'amount', 'fiscal_year_id'],
                'unique_distribution_per_deposit_type_amount'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distributions', function (Blueprint $table) {
            $table->dropForeign(['fiscal_year_id']);
            $table->dropColumn(['month', 'fiscal_year_id']);
        });
    }
};
