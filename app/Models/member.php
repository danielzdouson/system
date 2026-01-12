<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    // Fillable fields for mass assignment
    protected $fillable = [
        'first_name',
        'last_name',
        'national_id',
        'email',
        'phone',
        'date_of_birth',
        'address',
        'city',
        'country',
    ];

    // Cast date_of_birth to a date
    protected $casts = [
        'date_of_birth' => 'date',
    ];
}
