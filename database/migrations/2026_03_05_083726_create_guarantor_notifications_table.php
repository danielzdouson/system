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
        Schema::create('guarantor_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uploaded_form_id')->constrained()->onDelete('cascade');
            $table->text('notification_message');
            $table->timestamp('sent_at');
            $table->boolean('sent_to_all_members')->default(true);
            $table->timestamps();
            
            $table->index('uploaded_form_id');
            $table->index('sent_to_all_members');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guarantor_notifications');
    }
};
