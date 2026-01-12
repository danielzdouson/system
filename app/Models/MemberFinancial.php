<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberFinancial extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'member_id', 'name', 'number', 
        'savings', 'welfare', 'education_in', 
        'fined', 'fines_paid', 'education_out', 
        'loan_repayments', 'loan_charges', 'notes'
    ];

    protected $casts = [
        'savings' => 'decimal:2',
        'welfare' => 'decimal:2',
        'education_in' => 'decimal:2',
        'fined' => 'decimal:2',
        'fines_paid' => 'decimal:2',
        'education_out' => 'decimal:2',
        'loan_repayments' => 'decimal:2',
        'loan_charges' => 'decimal:2',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
