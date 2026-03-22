<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'member_id',
        'role', // admin, loans_officer, treasurer, member, super_admin
    ];

    /**
     * Attributes that should be hidden (not returned in JSON).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts (auto convert column types).
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /* ===========================
     |  USER RELATIONSHIPS
     ============================*/

    // If a User is linked to a Member account
    public function member()
    {
        return $this->hasOne(Member::class, 'user_id');
    }

    // User verifying a member's KYC
    public function membersVerified()
    {
        return $this->hasMany(Member::class, 'kyc_verified_by');
    }

    // User approving savings
    public function savingsApproved()
    {
        return $this->hasMany(Saving::class, 'approved_by');
    }

    // User approving loans
    public function loansApproved()
    {
        return $this->hasMany(Loan::class, 'approved_by');
    }

    // User receiving repayments
    public function repaymentsReceived()
    {
        return $this->hasMany(Repayment::class, 'received_by');
    }

    // User handling general transactions
    public function transactionsHandled()
    {
        return $this->hasMany(Transaction::class, 'handled_by');
    }

    /* ===========================
     |  ROLE HELPERS
     ============================*/

    public function isAdmin()
    {
        return $this->role === 'admin' || $this->role === 'super_admin';
    }

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isLoansOfficer()
    {
        return $this->role === 'loans_officer';
    }

    public function isTreasurer()
    {
        return $this->role === 'treasurer';
    }

    public function isMember()
    {
        return $this->role === 'member';
    }
}
