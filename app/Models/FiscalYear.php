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
        return $this->deposits()->sum('amount');
    }

    public function getTotalSavingsAttribute()
    {
        return $this->savings()->sum('amount');
    }

    public function getTotalWelfareAttribute()
    {
        return $this->welfareFunds()->sum('amount');
    }

    public function getTotalFinesAttribute()
    {
        return $this->fines()->sum('amount');
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
