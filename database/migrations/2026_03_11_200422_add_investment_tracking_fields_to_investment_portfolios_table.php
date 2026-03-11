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
        Schema::table('investment_portfolios', function (Blueprint $table) {
            $table->foreignId('fiscal_year_id')->nullable()->after('id')->constrained()->onDelete('set null');
            $table->boolean('is_long_term_investment')->default(false)->after('current_value');
            $table->text('carry_forward_notes')->nullable()->after('notes');
            $table->index('fiscal_year_id');
            $table->index('is_long_term_investment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investment_portfolios', function (Blueprint $table) {
            $table->dropForeign(['fiscal_year_id']);
            $table->dropColumn(['fiscal_year_id', 'is_long_term_investment', 'carry_forward_notes']);
        });
    }
};
