<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('fiscal_year_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('month'); // 1-12
            $table->decimal('amount', 15, 2);
            $table->decimal('balance', 15, 2)->default(0); // Remaining balance after distribution
            $table->enum('status', ['pending', 'distributed', 'partial'])->default('pending');
            $table->date('deposit_date'); // Actual date when deposit was made
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deposit_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['savings', 'welfare', 'fines', 'other']);
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('group_savings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('fiscal_year_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('month');
            $table->decimal('amount', 15, 2)->default(0);
            $table->enum('status', ['paid', 'pending', 'exempt'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->unique(['member_id', 'fiscal_year_id', 'month']);
        });

        Schema::create('welfare_funds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiscal_year_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('month');
            $table->decimal('amount', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->unique(['fiscal_year_id', 'month']);
        });

        Schema::create('fines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('fiscal_year_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('month');
            $table->decimal('amount', 15, 2);
            $table->enum('reason', ['missed_saving', 'late_payment', 'other'])->default('missed_saving');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'paid', 'waived'])->default('pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->unique(['member_id', 'fiscal_year_id', 'month', 'reason']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('fines');
        Schema::dropIfExists('welfare_funds');
        Schema::dropIfExists('group_savings');
        Schema::dropIfExists('distributions');
        Schema::dropIfExists('deposits');
    }
};
