<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'physical_address',
        'postal_code',
        'next_of_kin_name',
        'next_of_kin_phone',
        'next_of_kin_relationship',
        'user_id',
    ];

    // Cast date_of_birth to a date
    protected $casts = [
        'date_of_birth' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function monthlySaving()
    {
        return $this->hasOne(MonthlySaving::class);
    }

    public function memberFinancial()
    {
        return $this->hasOne(MemberFinancial::class);
    }

    public function memberLoanSummary()
    {
        return $this->hasOne(MemberLoanSummary::class);
    }

    public function memberAccounts()
    {
        return $this->hasMany(MemberAccount::class);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function fines()
    {
        return $this->hasMany(Fine::class);
    }

    public function loanGuaranteesGiven(): HasMany
    {
        return $this->hasMany(LoanGuarantor::class, 'guarantor_member_id');
    }

    public function uploadedForms(): HasMany
    {
        return $this->hasMany(UploadedForm::class);
    }

    // Helper methods
    public function isEligibleToGuarantee(): bool
    {
        // Check if member has overdue loans
        $hasOverdueLoans = $this->loans()
            ->where('status', 'active')
            ->where('balance', '>', 0)
            ->exists();

        if ($hasOverdueLoans) {
            return false;
        }

        // Check if member has too many active guarantees
        $activeGuarantees = $this->loanGuaranteesGiven()
            ->whereIn('guarantee_status', ['confirmed', 'called_upon'])
            ->count();

        if ($activeGuarantees >= 3) {
            return false;
        }

        // Check if member has sufficient savings
        $totalSavings = $this->memberAccounts()->sum('balance');
        if ($totalSavings <= 0) {
            return false;
        }

        return true;
    }

    public function getMaxGuaranteeAmount(): float
    {
        $totalSavings = $this->memberAccounts()->sum('balance');
        return $totalSavings * 0.5; // Can guarantee up to 50% of savings
    }

    public function getActiveGuaranteeCount(): int
    {
        return $this->loanGuaranteesGiven()
            ->whereIn('guarantee_status', ['confirmed', 'called_upon'])
            ->count();
    }

    public function getTotalGuaranteedAmount(): float
    {
        return $this->loanGuaranteesGiven()
            ->whereIn('guarantee_status', ['confirmed', 'called_upon'])
            ->sum('guaranteed_amount');
    }
}
