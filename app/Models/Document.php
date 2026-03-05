<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'title',
        'description',
        'filename',
        'original_filename',
        'file_path',
        'file_size',
        'document_type',
        'requires_fine',
        'fine_amount',
        'is_active',
        'uploaded_by',
    ];

    protected $casts = [
        'requires_fine' => 'boolean',
        'is_active' => 'boolean',
        'fine_amount' => 'decimal:2',
        'file_size' => 'integer',
    ];

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(DocumentDownload::class);
    }

    public function uploadedForms(): HasMany
    {
        return $this->hasMany(UploadedForm::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLoanForms($query)
    {
        return $query->where('document_type', 'loan_form');
    }

    public function scopeLegalDocuments($query)
    {
        return $query->whereIn('document_type', ['constitution', 'legal']);
    }

    // Helper methods
    public function isDownloadableBy(Member $member): bool
    {
        return $this->is_active;
    }

    public function getFineAmount(): float
    {
        return $this->requires_fine ? $this->fine_amount : 0;
    }

    public function getFormattedFileSize(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getDocumentTypeBadge(): string
    {
        return match($this->document_type) {
            'constitution' => '<span class="badge bg-primary">Constitution</span>',
            'legal' => '<span class="badge bg-info">Legal Document</span>',
            'loan_form' => '<span class="badge bg-warning">Loan Form</span>',
            'other' => '<span class="badge bg-secondary">Other</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    public function getTypeColor(): string
    {
        return match($this->document_type) {
            'constitution' => 'primary',
            'legal' => 'info',
            'loan_form' => 'warning',
            'other' => 'secondary',
            default => 'secondary',
        };
    }

    public function getFormattedDocumentType(): string
    {
        return match($this->document_type) {
            'constitution' => 'Constitution',
            'legal' => 'Legal Document',
            'loan_form' => 'Loan Form',
            'other' => 'Other',
            default => 'Unknown',
        };
    }

    public function getFormattedFineAmount(): string
    {
        return number_format($this->fine_amount, 2) . ' UGX';
    }

    public function getStatusBadge(): string
    {
        return $this->is_active 
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-secondary">Inactive</span>';
    }

    public function getFineBadge(): string
    {
        return $this->requires_fine 
            ? '<span class="badge bg-danger">YES</span>'
            : '<span class="badge bg-success">NO</span>';
    }
}
