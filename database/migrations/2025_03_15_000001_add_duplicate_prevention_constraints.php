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
        // Add unique constraint to prevent duplicate deposits
        // Same member cannot have multiple deposits with same amount on same date in same fiscal year
        try {
            Schema::table('deposits', function (Blueprint $table) {
                $table->unique(
                    ['member_id', 'fiscal_year_id', 'deposit_date', 'amount'],
                    'unique_deposit_per_member_date_amount'
                );
            });
        } catch (\Exception $e) {
            // Constraint may already exist or duplicates exist - skip
        }

        // Add unique constraint to prevent duplicate distributions
        // Same deposit cannot have multiple distributions of same type and amount
        try {
            Schema::table('distributions', function (Blueprint $table) {
                $table->unique(
                    ['deposit_id', 'type', 'amount', 'fiscal_year_id'],
                    'unique_distribution_per_deposit_type_amount'
                );
            });
        } catch (\Exception $e) {
            // Constraint may already exist or duplicates exist - skip
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropUnique('unique_deposit_per_member_date_amount');
        });

        Schema::table('distributions', function (Blueprint $table) {
            $table->dropUnique('unique_distribution_per_deposit_type_amount');
        });
    }
};
