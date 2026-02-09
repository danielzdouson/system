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
        Schema::table('members', function (Blueprint $table) {
            // Physical address fields
            $table->text('physical_address')->nullable()->after('address');
            $table->string('postal_code')->nullable()->after('physical_address');
            
            // Next of kin fields
            $table->string('next_of_kin_name')->nullable()->after('postal_code');
            $table->string('next_of_kin_phone')->nullable()->after('next_of_kin_name');
            $table->string('next_of_kin_relationship')->nullable()->after('next_of_kin_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn([
                'physical_address',
                'postal_code',
                'next_of_kin_name',
                'next_of_kin_phone',
                'next_of_kin_relationship'
            ]);
        });
    }
};
