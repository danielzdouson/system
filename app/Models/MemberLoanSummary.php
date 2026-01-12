<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberLoanSummary extends Model
{
    use SoftDeletes;

    protected $table = 'member_loan_summaries';

    protected $fillable = [
        'member_id',
        'name',
        'loan_brought_forward',
        'loan_issued_current_year',
        'current_year_loan_plus_interest',
        'loan_balance_without_fines',
        'loan_out',
        'total',
        'notes',
    ];

    protected $casts = [
        'loan_brought_forward' => 'decimal:2',
        'loan_issued_current_year' => 'decimal:2',
        'current_year_loan_plus_interest' => 'decimal:2',
        'loan_balance_without_fines' => 'decimal:2',
        'loan_out' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /** Relationships */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
