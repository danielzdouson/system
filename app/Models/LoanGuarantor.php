<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanGuarantor extends Model
{
    protected $fillable = [
        'uploaded_form_id',
        'guarantor_member_id',
        'guarantee_percentage',
        'guaranteed_amount',
        'guarantee_status',
        'guarantee_confirmation',
        'guaranteed_at',
        'withdrawn_at',
        'notes',
        'ip_address',
    ];

    protected $casts = [
        'guarantee_percentage' => 'decimal:2',
        'guaranteed_amount' => 'decimal:2',
        'guaranteed_at' => 'datetime',
        'withdrawn_at' => 'datetime',
    ];

    public function uploadedForm(): BelongsTo
    {
        return $this->belongsTo(UploadedForm::class);
    }

    public function guarantor(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'guarantor_member_id');
    }

    // Scopes
    public function scopeConfirmed($query)
    {
        return $query->where('guarantee_status', 'confirmed');
    }

    public function scopePending($query)
    {
        return $query->where('guarantee_status', 'pending');
    }

    public function scopeWithdrawn($query)
    {
        return $query->where('guarantee_status', 'withdrawn');
    }

    public function scopeCalledUpon($query)
    {
        return $query->where('guarantee_status', 'called_upon');
    }

    public function scopeForUploadedForm($query, $uploadedFormId)
    {
        return $query->where('uploaded_form_id', $uploadedFormId);
    }

    // Helper methods
    public function confirmGuarantee(string $confirmation = null, string $ipAddress = null): void
    {
        if ($this->guarantee_status !== 'pending') {
            throw new \Exception('Only pending guarantees can be confirmed');
        }

        $this->update([
            'guarantee_status' => 'confirmed',
            'guarantee_confirmation' => $confirmation,
            'guaranteed_at' => now(),
            'ip_address' => $ipAddress,
        ]);

        // Check if form is ready for review
        $this->uploadedForm->refresh();
        if ($this->uploadedForm->hasRequiredGuarantors()) {
            $this->uploadedForm->update(['status' => 'ready_for_review']);
        }
    }

    public function withdrawGuarantee(string $reason = null): void
    {
        if ($this->guarantee_status === 'withdrawn') {
            throw new \Exception('Guarantee already withdrawn');
        }

        if ($this->guarantee_status === 'called_upon') {
            throw new \Exception('Cannot withdraw called-upon guarantee');
        }

        $this->update([
            'guarantee_status' => 'withdrawn',
            'withdrawn_at' => now(),
            'notes' => $reason,
        ]);

        // Update form status if no longer has required guarantors
        $this->uploadedForm->refresh();
        if (!$this->uploadedForm->hasRequiredGuarantors() && $this->uploadedForm->status === 'ready_for_review') {
            $this->uploadedForm->update(['status' => 'pending_guarantors']);
        }
    }

    public function isCalledUpon(): bool
    {
        return $this->guarantee_status === 'called_upon';
    }

    public function calculateGuaranteedAmount(): float
    {
        if ($this->guaranteed_amount) {
            return $this->guaranteed_amount;
        }

        $loanAmount = $this->uploadedForm->loan_amount;
        return ($loanAmount * $this->guarantee_percentage) / 100;
    }

    public function canBeConfirmed(): bool
    {
        return $this->guarantee_status === 'pending' && $this->guarantor->isEligibleToGuarantee();
    }

    public function getStatusBadge(): string
    {
        return match($this->guarantee_status) {
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'confirmed' => '<span class="badge bg-success">Confirmed</span>',
            'withdrawn' => '<span class="badge bg-secondary">Withdrawn</span>',
            'called_upon' => '<span class="badge bg-danger">Called Upon</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    public function getFormattedGuaranteePercentage(): string
    {
        return number_format($this->guarantee_percentage, 2) . '%';
    }

    public function getFormattedGuaranteedAmount(): string
    {
        return 'UGX ' . number_format($this->calculateGuaranteedAmount(), 2);
    }
}
