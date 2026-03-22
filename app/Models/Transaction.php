<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'amount',
        'type',
        'related_id',
        'related_type',
        'status',
        'reference',
        'notes',
        'handled_by',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    // Polymorphic relation to Loan / Saving / Repayment etc.
    public function related()
    {
        return $this->morphTo();
    }

    // Accessor for description to maintain compatibility
    public function getDescriptionAttribute()
    {
        return $this->notes;
    }
}
