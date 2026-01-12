<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'member_id',
        'amount',
        'principal_component',
        'interest_component',
        'method',
        'reference',
        'paid_at',
        'received_by',
        'notes',
    ];

    protected $dates = [
        'paid_at',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
