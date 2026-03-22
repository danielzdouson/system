<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\FiscalYear;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cash_flows', function (Blueprint $table) {
            $table->foreignId('fiscal_year_id')->nullable()->after('user_id')->constrained('fiscal_years')->onDelete('set null');
            $table->index(['fiscal_year_id', 'transaction_date'], 'cash_flows_fiscal_date_idx');
        });

        // Backfill existing records based on transaction_date
        $this->backfillFiscalYears();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_flows', function (Blueprint $table) {
            $table->dropIndex('cash_flows_fiscal_date_idx');
            $table->dropForeign(['fiscal_year_id']);
            $table->dropColumn('fiscal_year_id');
        });
    }

    /**
     * Backfill fiscal_year_id for existing records based on transaction_date
     */
    private function backfillFiscalYears(): void
    {
        $fiscalYears = FiscalYear::all();
        
        foreach ($fiscalYears as $fiscalYear) {
            DB::table('cash_flows')
                ->whereNull('fiscal_year_id')
                ->whereBetween('transaction_date', [$fiscalYear->start_date, $fiscalYear->end_date])
                ->update(['fiscal_year_id' => $fiscalYear->id]);
        }

        // For any remaining records that don't match any fiscal year, 
        // assign to the most recent fiscal year
        $mostRecentFiscalYear = FiscalYear::orderBy('start_date', 'desc')->first();
        if ($mostRecentFiscalYear) {
            DB::table('cash_flows')
                ->whereNull('fiscal_year_id')
                ->update(['fiscal_year_id' => $mostRecentFiscalYear->id]);
        }
    }
};
