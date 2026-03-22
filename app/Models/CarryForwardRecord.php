<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarryForwardRecord extends Model
{
    protected $fillable = [
        'from_fiscal_year_id',
        'to_fiscal_year_id',
        'item_type',
        'item_id',
        'description',
        'amount',
        'status',
        'error_message',
        'created_by',
        'completed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function fromFiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class, 'from_fiscal_year_id');
    }

    public function toFiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class, 'to_fiscal_year_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Polymorphic relationship to get the actual item
    public function item()
    {
        return $this->morphTo('item', 'item_type', 'item_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('item_type', $type);
    }

    public function scopeBetweenFiscalYears($query, $fromId, $toId)
    {
        return $query->where('from_fiscal_year_id', $fromId)
                    ->where('to_fiscal_year_id', $toId);
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'error_message' => null,
        ]);
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }

    public function getStatusBadgeAttribute(): string
    {
        $colors = [
            'pending' => 'warning',
            'completed' => 'success',
            'failed' => 'danger',
        ];

        $color = $colors[$this->status] ?? 'secondary';
        return '<span class="badge bg-' . $color . '">' . ucfirst($this->status) . '</span>';
    }

    public function getItemTypeBadgeAttribute(): string
    {
        $colors = [
            'loan' => 'primary',
            'fine' => 'info',
            'investment' => 'success',
        ];

        $color = $colors[$this->item_type] ?? 'secondary';
        return '<span class="badge bg-' . $color . '">' . ucfirst($this->item_type) . '</span>';
    }

    // Constants
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    const TYPE_LOAN = 'loan';
    const TYPE_FINE = 'fine';
    const TYPE_INVESTMENT = 'investment';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_FAILED => 'Failed',
        ];
    }

    public static function getItemTypes(): array
    {
        return [
            self::TYPE_LOAN => 'Loan',
            self::TYPE_FINE => 'Fine',
            self::TYPE_INVESTMENT => 'Investment',
        ];
    }
}
