<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UploadedForm extends Model
{
    protected $fillable = [
        'member_id',
        'document_id',
        'filename',
        'file_path',
        'loan_amount',
        'guarantors_required',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'loan_amount' => 'decimal:2',
        'guarantors_required' => 'integer',
        'reviewed_at' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function guarantors(): HasMany
    {
        return $this->hasMany(LoanGuarantor::class);
    }

    public function confirmedGuarantors(): HasMany
    {
        return $this->hasMany(LoanGuarantor::class)->where('guarantee_status', 'confirmed');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(GuarantorNotification::class);
    }

    // Scopes
    public function scopePendingGuarantors($query)
    {
        return $query->where('status', 'pending_guarantors');
    }

    public function scopeReadyForReview($query)
    {
        return $query->where('status', 'ready_for_review');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Helper methods
    public function hasRequiredGuarantors(): bool
    {
        if ($this->guarantors_required === 0) {
            return true; // No guarantors required
        }

        $confirmedPercentage = $this->confirmedGuarantors()->sum('guarantee_percentage');
        return $confirmedPercentage >= 100;
    }

    public function getGuarantorProgress(): array
    {
        $confirmedPercentage = $this->confirmedGuarantors()->sum('guarantee_percentage');
        $pendingCount = $this->guarantors()->where('guarantee_status', 'pending')->count();
        
        return [
            'confirmed_percentage' => $confirmedPercentage,
            'required_percentage' => 100,
            'is_complete' => $confirmedPercentage >= 100,
            'pending_count' => $pendingCount,
            'confirmed_count' => $this->confirmedGuarantors()->count(),
        ];
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'ready_for_review' || $this->hasRequiredGuarantors();
    }

    public function approve(User $reviewer, string $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'admin_notes' => $notes,
        ]);
    }

    public function reject(User $reviewer, string $notes): void
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'admin_notes' => $notes,
        ]);
    }

    public function getDownloadUrl(): string
    {
        return route('member.documents.download-uploaded', $this->id);
    }

    public function getStatusBadge(): string
    {
        return match($this->status) {
            'pending_guarantors' => '<span class="badge bg-warning">Pending Guarantors</span>',
            'ready_for_review' => '<span class="badge bg-info">Ready for Review</span>',
            'approved' => '<span class="badge bg-success">Approved</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    public function calculateGuarantorsRequired(): int
    {
        if (!$this->loan_amount) {
            return 0;
        }

        // Calculate based on loan amount tiers
        if ($this->loan_amount <= 500000) {
            return 1;
        } elseif ($this->loan_amount <= 1000000) {
            return 2;
        } else {
            return 3;
        }
    }
}
