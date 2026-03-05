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
    ];

    protected $casts = [
        'fine_applied' => 'boolean',
        'downloaded_at' => 'datetime',
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

        $fine = Fine::create([
            'member_id' => $this->member_id,
            'amount' => $this->document->fine_amount,
            'reason' => 'Document download: ' . $this->document->title,
            'status' => 'pending',
            'created_by' => 1, // System user
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
}
