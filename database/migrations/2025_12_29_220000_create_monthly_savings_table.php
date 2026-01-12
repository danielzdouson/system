<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_savings', function (Blueprint $table) {
            $table->id();
            
            // Member reference
            $table->unsignedBigInteger('member_id')->index();
            $table->string('member_name');
            $table->string('membership_number')->nullable();
            
            // Monthly savings data
            $table->decimal('start_balance', 15, 2)->default(0);
            $table->decimal('jul_25', 15, 2)->default(0);
            $table->decimal('aug_25', 15, 2)->default(0);
            $table->decimal('sep_25', 15, 2)->default(0);
            $table->decimal('oct_25', 15, 2)->default(0);
            $table->decimal('nov_25', 15, 2)->default(0);
            $table->decimal('dec_25', 15, 2)->default(0);
            $table->decimal('jan_26', 15, 2)->default(0);
            $table->decimal('feb_26', 15, 2)->default(0);
            $table->decimal('mar_26', 15, 2)->default(0);
            $table->decimal('apr_26', 15, 2)->default(0);
            $table->decimal('may_26', 15, 2)->default(0);
            $table->decimal('jun_26', 15, 2)->default(0);
            
            // Totals
            $table->decimal('year_2024_2025_totals', 15, 2)->default(0);
            $table->decimal('current_year_savings', 15, 2)->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign key
            $table->foreign('member_id')
                  ->references('id')
                  ->on('members')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_savings');
    }
};
