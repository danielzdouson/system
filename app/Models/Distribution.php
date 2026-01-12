<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'deposit_id',
        'type',
        'amount',
        'description',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function deposit()
    {
        return $this->belongsTo(Deposit::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
