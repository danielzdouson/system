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
        Schema::table('loans', function (Blueprint $table) {
            $table->foreignId('carried_forward_from_fiscal_year_id')->nullable()->after('fiscal_year_id')->constrained('fiscal_years')->onDelete('set null');
            $table->foreignId('original_fiscal_year_id')->nullable()->after('carried_forward_from_fiscal_year_id')->constrained('fiscal_years')->onDelete('set null');
            $table->boolean('is_carried_forward')->default(false)->after('original_fiscal_year_id');
            $table->timestamp('carried_forward_at')->nullable()->after('is_carried_forward');
            $table->index('carried_forward_from_fiscal_year_id');
            $table->index('is_carried_forward');
        });

        Schema::table('fines', function (Blueprint $table) {
            $table->foreignId('carried_forward_from_fiscal_year_id')->nullable()->after('fiscal_year_id')->constrained('fiscal_years')->onDelete('set null');
            $table->foreignId('original_fiscal_year_id')->nullable()->after('carried_forward_from_fiscal_year_id')->constrained('fiscal_years')->onDelete('set null');
            $table->boolean('is_carried_forward')->default(false)->after('original_fiscal_year_id');
            $table->timestamp('carried_forward_at')->nullable()->after('is_carried_forward');
            $table->index('carried_forward_from_fiscal_year_id');
            $table->index('is_carried_forward');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['carried_forward_from_fiscal_year_id']);
            $table->dropForeign(['original_fiscal_year_id']);
            $table->dropColumn(['carried_forward_from_fiscal_year_id', 'original_fiscal_year_id', 'is_carried_forward', 'carried_forward_at']);
        });

        Schema::table('fines', function (Blueprint $table) {
            $table->dropForeign(['carried_forward_from_fiscal_year_id']);
            $table->dropForeign(['original_fiscal_year_id']);
            $table->dropColumn(['carried_forward_from_fiscal_year_id', 'original_fiscal_year_id', 'is_carried_forward', 'carried_forward_at']);
        });
    }
};
