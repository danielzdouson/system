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
        Schema::table('member_accounts', function (Blueprint $table) {
            $table->decimal('shares_on_hold', 15, 2)->default(0)->after('other_balance');
            $table->decimal('total_shares', 15, 2)->default(0)->after('shares_on_hold');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_accounts', function (Blueprint $table) {
            $table->dropColumn(['shares_on_hold', 'total_shares']);
        });
    }
};
