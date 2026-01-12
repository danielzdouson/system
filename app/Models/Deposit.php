<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'fiscal_year_id',
        'month',
        'amount',
        'balance',
        'status',
        'deposit_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'deposit_date' => 'date',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function distributions()
    {
        return $this->hasMany(Distribution::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTotalDistributedAttribute()
    {
        return $this->distributions()->sum('amount');
    }

    public function getIsFullyDistributedAttribute()
    {
        return $this->balance == 0;
    }

    public function distribute($type, $amount, $description = null)
    {
        if ($amount > $this->balance) {
            throw new \Exception('Distribution amount exceeds available balance');
        }

        $distribution = $this->distributions()->create([
            'type' => $type,
            'amount' => $amount,
            'description' => $description,
            'created_by' => auth()->id(),
        ]);

        $this->balance = $this->balance - $amount;
        $this->status = $this->balance == 0 ? 'distributed' : 'partial';
        $this->save();

        return $distribution;
    }
}
