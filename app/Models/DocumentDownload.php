<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentDownload extends Model
{
    protected $fillable = [
        'document_id',
        'member_id',
        'fine_applied',
        'fine_id',
        'downloaded_at',
        'ip_address',
        'upload_window_expires_at',
        'used_for_upload',
        'fine_amount',
        'download_purpose',
        'form_used_at',
    ];

    protected $casts = [
        'fine_applied' => 'boolean',
        'downloaded_at' => 'datetime',
        'upload_window_expires_at' => 'datetime',
        'form_used_at' => 'datetime',
        'used_for_upload' => 'boolean',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function fine(): BelongsTo
    {
        return $this->belongsTo(Fine::class);
    }

    // Helper methods
    public function applyFine(): ?Fine
    {
        if ($this->fine_applied || !$this->document->requires_fine) {
            return $this->fine;
        }

        // Get current fiscal year
        $fiscalYear = \App\Models\FiscalYear::where('status', 'active')
            ->orWhere(function($query) {
                $query->where('start_date', '<=', now())
                      ->where('end_date', '>=', now());
            })
            ->first();

        if (!$fiscalYear) {
            // If no active fiscal year, create one or get the latest
            $fiscalYear = \App\Models\FiscalYear::latest()->first();
        }

        // Get a valid system user
        $systemUser = \App\Models\User::first();
        $createdById = $systemUser ? $systemUser->id : null;

        $fine = Fine::create([
            'member_id' => $this->member_id,
            'fiscal_year_id' => $fiscalYear->id ?? null,
            'month' => now()->month,
            'amount' => $this->document->fine_amount,
            'reason' => 'other', // Document download fines don't fit standard categories
            'description' => 'Document download fine: ' . $this->document->title,
            'status' => 'pending',
            'created_by' => $createdById,
        ]);

        $this->update([
            'fine_applied' => true,
            'fine_id' => $fine->id,
        ]);

        return $fine;
    }

    public function getDownloadUrl(): string
    {
        return route('member.documents.download', $this->document_id);
    }

    // New helper methods for upload window management
    public function isUploadWindowValid(): bool
    {
        return !$this->used_for_upload && 
               $this->upload_window_expires_at && 
               $this->upload_window_expires_at > now();
    }

    public function getUploadWindowStatus(): string
    {
        if ($this->used_for_upload) {
            return 'used';
        }

        if (!$this->upload_window_expires_at) {
            return 'no_window';
        }

        if ($this->upload_window_expires_at <= now()) {
            return 'expired';
        }

        return 'valid';
    }

    public function getRemainingUploadDays(): int
    {
        if (!$this->upload_window_expires_at || $this->upload_window_expires_at <= now()) {
            return 0;
        }

        return max(0, now()->diffInDays($this->upload_window_expires_at));
    }

    public function getUploadWindowStatusBadge(): string
    {
        return match($this->getUploadWindowStatus()) {
            'valid' => '<span class="badge bg-success">Valid (' . $this->getRemainingUploadDays() . ' days left)</span>',
            'expired' => '<span class="badge bg-danger">Expired</span>',
            'used' => '<span class="badge bg-secondary">Used</span>',
            'no_window' => '<span class="badge bg-warning">No Window</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    public function markAsUsedForUpload(): void
    {
        $this->update([
            'used_for_upload' => true,
            'form_used_at' => now(),
        ]);
    }

    public static function getValidDownloadForUpload(int $memberId, int $documentId): ?self
    {
        return self::where('member_id', $memberId)
            ->where('document_id', $documentId)
            ->where('used_for_upload', false)
            ->where('upload_window_expires_at', '>', now())
            ->orderBy('upload_window_expires_at', 'desc')
            ->first();
    }
}
