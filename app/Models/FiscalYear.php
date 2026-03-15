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

    public function getCarriedForwardDepositsAttribute()
    {
        // Count only carried forward deposits
        return $this->deposits()->where('is_carried_forward', true)->sum('amount');
    }

    public function getCarriedForwardSavingsAttribute()
    {
        // Calculate only carried forward savings amounts
        return Distribution::whereHas('deposit', function($query) {
            $query->where('fiscal_year_id', $this->id)
                  ->where('is_carried_forward', true);
        })->where('type', 'savings')->sum('amount');
    }

    public function getCarriedForwardWelfareAttribute()
    {
        // Calculate only carried forward welfare amounts
        return Distribution::whereHas('deposit', function($query) {
            $query->where('fiscal_year_id', $this->id)
                  ->where('is_carried_forward', true);
        })->where('type', 'welfare')->sum('amount');
    }

    public function getCarriedForwardFinesAttribute()
    {
        // Calculate only carried forward fine payments
        return Distribution::whereHas('deposit', function($query) {
            $query->where('fiscal_year_id', $this->id)
                  ->where('is_carried_forward', true);
        })->where('type', 'fines')->sum('amount');
    }

    public function getCarriedForwardOtherAttribute()
    {
        // Calculate only carried forward other amounts
        return Distribution::whereHas('deposit', function($query) {
            $query->where('fiscal_year_id', $this->id)
                  ->where('is_carried_forward', true);
        })->where('type', 'other')->sum('amount');
    }

    public function hasCarriedForwardItemsAttribute()
    {
        // Check if fiscal year has any carried forward items
        return $this->deposits()->where('is_carried_forward', true)->exists() ||
               Distribution::whereHas('deposit', function($query) {
                   $query->where('fiscal_year_id', $this->id)
                         ->where('is_carried_forward', true);
               })->exists() ||
               \App\Models\Loan::where('fiscal_year_id', $this->id)
                   ->where('is_carried_forward', true)
                   ->exists() ||
               \App\Models\Fine::where('fiscal_year_id', $this->id)
                   ->where('is_carried_forward', true)
                   ->exists();
    }

    public static function getActive()
    {
        return self::where('status', 'active')->first();
    }
}
