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
        Schema::create('loan_guarantors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uploaded_form_id')->constrained()->onDelete('cascade');
            $table->foreignId('guarantor_member_id')->constrained('members')->onDelete('cascade');
            $table->decimal('guarantee_percentage', 5, 2); // 0.00 to 100.00
            $table->decimal('guaranteed_amount', 12, 2);
            $table->enum('guarantee_status', ['pending', 'confirmed', 'withdrawn', 'called_upon'])->default('pending');
            $table->text('guarantee_confirmation')->nullable();
            $table->timestamp('guaranteed_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            
            $table->index(['uploaded_form_id', 'guarantee_status']);
            $table->index('guarantor_member_id');
            $table->unique(['uploaded_form_id', 'guarantor_member_id'], 'unique_form_guarantor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_guarantors');
    }
};
