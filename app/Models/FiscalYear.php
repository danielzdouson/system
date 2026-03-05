<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiscalYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'status',
        'csv_generated',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'csv_generated' => 'boolean',
    ];

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function savings()
    {
        return $this->hasMany(GroupSaving::class);
    }

    public function welfareFunds()
    {
        return $this->hasMany(WelfareFund::class);
    }

    public function fines()
    {
        return $this->hasMany(Fine::class);
    }

    public function getTotalDepositsAttribute()
    {
        // Count all deposits including virtual deposits to get complete picture
        return $this->deposits()->sum('amount');
    }

    public function getTotalSavingsAttribute()
    {
        // Calculate from distributions to get actual savings amounts
        return Distribution::whereHas('deposit', function($query) {
            $query->where('fiscal_year_id', $this->id);
        })->where('type', 'savings')->sum('amount');
    }

    public function getTotalWelfareAttribute()
    {
        // Calculate from distributions to get actual welfare amounts
        return Distribution::whereHas('deposit', function($query) {
            $query->where('fiscal_year_id', $this->id);
        })->where('type', 'welfare')->sum('amount');
    }

    public function getTotalFinesAttribute()
    {
        // Calculate from distributions to get actual fine payments
        return Distribution::whereHas('deposit', function($query) {
            $query->where('fiscal_year_id', $this->id);
        })->where('type', 'fines')->sum('amount');
    }

    public function getTotalOtherAttribute()
    {
        // Calculate from distributions to get actual other amounts
        return Distribution::whereHas('deposit', function($query) {
            $query->where('fiscal_year_id', $this->id);
        })->where('type', 'other')->sum('amount');
    }

    public function getTotalDistributedAttribute()
    {
        // Calculate total distributed funds
        return Distribution::whereHas('deposit', function($query) {
            $query->where('fiscal_year_id', $this->id);
        })->sum('amount');
    }

    public function getPendingMonthsCountAttribute()
    {
        return $this->savings()->where('status', 'pending')->count();
    }

    public static function getActive()
    {
        return self::where('status', 'active')->first();
    }
}
