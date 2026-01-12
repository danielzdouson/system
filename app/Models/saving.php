<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Saving extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'member_id',
        'amount',
        'type',
        'status',
        'approved_by',
        'approved_at',
        'reference',
        'notes',
    ];

    protected $dates = [
        'approved_at',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
