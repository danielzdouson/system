<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupSaving extends Model
{
    use HasFactory;

    protected $table = 'group_savings';

    protected $fillable = [
        'member_id',
        'fiscal_year_id',
        'month',
        'amount',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function markAsPaid($amount, $notes = null)
    {
        $this->update([
            'amount' => $amount,
            'status' => 'paid',
            'notes' => $notes,
            'created_by' => auth()->id(),
        ]);
    }

    public function markAsPending()
    {
        $this->update([
            'status' => 'pending',
            'amount' => 0,
        ]);
    }

    public function markAsExempt($reason = null)
    {
        $this->update([
            'status' => 'exempt',
            'notes' => $reason,
            'created_by' => auth()->id(),
        ]);
    }
}
