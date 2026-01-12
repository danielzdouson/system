<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WelfareFund extends Model
{
    use HasFactory;

    protected $table = 'welfare_funds';

    protected $fillable = [
        'fiscal_year_id',
        'month',
        'amount',
        'description',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
