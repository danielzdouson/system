<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_downloads', function (Blueprint $table) {
            // Upload window management
            $table->dateTime('upload_window_expires_at')->nullable()->after('ip_address');
            $table->boolean('used_for_upload')->default(false)->after('upload_window_expires_at');
            
            // Fine tracking
            $table->decimal('fine_amount', 10, 2)->nullable()->after('used_for_upload');
            $table->string('download_purpose')->default('general')->after('fine_amount'); // 'loan_application', 'general'
            
            // Form tracking
            $table->dateTime('form_used_at')->nullable()->after('download_purpose');
            
            // Indexes for performance
            $table->index(['member_id', 'upload_window_expires_at']);
            $table->index(['member_id', 'used_for_upload']);
            $table->index(['document_id', 'download_purpose']);
        });
        
        Schema::table('uploaded_forms', function (Blueprint $table) {
            // Link to the download record that was used for this upload
            $table->foreignId('document_download_id')->nullable()->after('document_id')->constrained()->onDelete('set null');
            $table->dateTime('form_download_date')->nullable()->after('document_download_id');
            
            // Index for performance
            $table->index(['document_download_id']);
            $table->index(['member_id', 'form_download_date']);
        });
    }

    public function down(): void
    {
        Schema::table('uploaded_forms', function (Blueprint $table) {
            $table->dropForeign(['document_download_id']);
            $table->dropColumn(['document_download_id', 'form_download_date']);
        });
        
        Schema::table('document_downloads', function (Blueprint $table) {
            $table->dropColumn([
                'upload_window_expires_at',
                'used_for_upload', 
                'fine_amount',
                'download_purpose',
                'form_used_at'
            ]);
        });
    }
};
