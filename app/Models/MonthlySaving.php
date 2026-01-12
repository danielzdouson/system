<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonthlySaving extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'member_id',
        'member_name',
        'membership_number',
        'start_balance',
        'jul_25',
        'aug_25',
        'sep_25',
        'oct_25',
        'nov_25',
        'dec_25',
        'jan_26',
        'feb_26',
        'mar_26',
        'apr_26',
        'may_26',
        'jun_26',
        'year_2024_2025_totals',
        'current_year_savings',
    ];

    protected $casts = [
        'start_balance' => 'decimal:2',
        'jul_25' => 'decimal:2',
        'aug_25' => 'decimal:2',
        'sep_25' => 'decimal:2',
        'oct_25' => 'decimal:2',
        'nov_25' => 'decimal:2',
        'dec_25' => 'decimal:2',
        'jan_26' => 'decimal:2',
        'feb_26' => 'decimal:2',
        'mar_26' => 'decimal:2',
        'apr_26' => 'decimal:2',
        'may_26' => 'decimal:2',
        'jun_26' => 'decimal:2',
        'year_2024_2025_totals' => 'decimal:2',
        'current_year_savings' => 'decimal:2',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    // Get monthly savings as array
    public function getMonthlySavings()
    {
        return [
            'Jul-25' => $this->jul_25,
            'Aug-25' => $this->aug_25,
            'Sep-25' => $this->sep_25,
            'Oct-25' => $this->oct_25,
            'Nov-25' => $this->nov_25,
            'Dec-25' => $this->dec_25,
            'Jan-26' => $this->jan_26,
            'Feb-26' => $this->feb_26,
            'Mar-26' => $this->mar_26,
            'Apr-26' => $this->apr_26,
            'May-26' => $this->may_26,
            'Jun-26' => $this->jun_26,
        ];
    }

    // Calculate total monthly contributions
    public function getTotalMonthlyContributions()
    {
        return $this->jul_25 + $this->aug_25 + $this->sep_25 + $this->oct_25 + 
               $this->nov_25 + $this->dec_25 + $this->jan_26 + $this->feb_26 + 
               $this->mar_26 + $this->apr_26 + $this->may_26 + $this->jun_26;
    }

    // Get months with contributions
    public function getActiveMonths()
    {
        $monthly = $this->getMonthlySavings();
        return array_filter($monthly, function($amount) {
            return $amount > 0;
        });
    }
}
