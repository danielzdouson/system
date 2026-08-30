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
        if (!Schema::hasTable('member_accounts')) {
            return;
        }

        Schema::table('member_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('member_accounts', 'shares_on_hold')) {
                $table->decimal('shares_on_hold', 15, 2)->default(0)->after('other_balance');
            }

            if (!Schema::hasColumn('member_accounts', 'total_shares')) {
                $table->decimal('total_shares', 15, 2)->default(0)->after('shares_on_hold');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('member_accounts')) {
            return;
        }

        Schema::table('member_accounts', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('member_accounts', 'shares_on_hold')) {
                $columns[] = 'shares_on_hold';
            }

            if (Schema::hasColumn('member_accounts', 'total_shares')) {
                $columns[] = 'total_shares';
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
