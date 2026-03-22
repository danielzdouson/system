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
        Schema::create('document_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->boolean('fine_applied')->default(false);
            $table->foreignId('fine_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('downloaded_at');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            
            $table->index(['document_id', 'member_id']);
            $table->index('fine_applied');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_downloads');
    }
};
