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
        Schema::create('members', function (Blueprint $table) {
            $table->id();

            // Personal info
            $table->string('first_name');
            $table->string('last_name');
            $table->string('national_id')->nullable()->unique()->index();
            $table->string('email')->nullable()->unique()->index();
            $table->string('phone')->nullable()->index();
            $table->date('date_of_birth')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable()->default('');

            // Timestamps
            $table->timestamps();

            // Soft deletes
            $table->softDeletes(); // adds deleted_at column for soft deleting
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
