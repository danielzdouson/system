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
        Schema::table('cashflow_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('cashflow_transactions', 'is_reallocation')) {
                $table->boolean('is_reallocation')->default(false)->after('status');
                $table->index('is_reallocation');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cashflow_transactions', function (Blueprint $table) {
            $table->dropIndex(['is_reallocation']);
            $table->dropColumn('is_reallocation');
        });
    }
};
