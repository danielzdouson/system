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
        if (Schema::hasTable('deposits')) {
            Schema::table('deposits', function (Blueprint $table) {
                $indexes = \DB::select("SHOW INDEX FROM deposits WHERE Key_name = 'unique_deposit_per_member_date_amount'");

                if (empty($indexes)) {
                    $table->unique(
                        ['member_id', 'fiscal_year_id', 'deposit_date', 'amount'],
                        'unique_deposit_per_member_date_amount'
                    );
                }
            });
        }

        if (Schema::hasTable('distributions')) {
            Schema::table('distributions', function (Blueprint $table) {
                $indexes = \DB::select("SHOW INDEX FROM distributions WHERE Key_name = 'unique_distribution_per_deposit_type_amount'");

                if (empty($indexes)) {
                    $table->unique(
                        ['deposit_id', 'type', 'amount', 'fiscal_year_id'],
                        'unique_distribution_per_deposit_type_amount'
                    );
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('deposits')) {
            Schema::table('deposits', function (Blueprint $table) {
                $indexes = \DB::select("SHOW INDEX FROM deposits WHERE Key_name = 'unique_deposit_per_member_date_amount'");

                if (!empty($indexes)) {
                    $table->dropUnique('unique_deposit_per_member_date_amount');
                }
            });
        }

        if (Schema::hasTable('distributions')) {
            Schema::table('distributions', function (Blueprint $table) {
                $indexes = \DB::select("SHOW INDEX FROM distributions WHERE Key_name = 'unique_distribution_per_deposit_type_amount'");

                if (!empty($indexes)) {
                    $table->dropUnique('unique_distribution_per_deposit_type_amount');
                }
            });
        }
    }
};
