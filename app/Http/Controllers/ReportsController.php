<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Loan;
use App\Models\GroupSaving;
use App\Models\Deposit;
use App\Models\Fine;
use App\Models\FiscalYear;
use App\Models\CashFlow;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function memberReports()
    {
        $activeFiscalYear = FiscalYear::getActive();
        $members = Member::with(['accounts', 'loans', 'deposits'])
            ->when($activeFiscalYear, function($query) use ($activeFiscalYear) {
                $query->with(['accounts' => function($q) use ($activeFiscalYear) {
                    $q->where('fiscal_year_id', $activeFiscalYear->id);
                }]);
            })
            ->get();

        return view('admin.reports.members', compact('members', 'activeFiscalYear'));
    }

    public function savingsReports()
    {
        $activeFiscalYear = FiscalYear::getActive();
        
        $savings = GroupSaving::when($activeFiscalYear, function($query) use ($activeFiscalYear) {
            $query->where('fiscal_year_id', $activeFiscalYear->id);
        })->with(['member'])->get();

        $deposits = Deposit::when($activeFiscalYear, function($query) use ($activeFiscalYear) {
            $query->where('fiscal_year_id', $activeFiscalYear->id);
        })->with(['member', 'distributions'])->get();

        return view('admin.reports.savings', compact('savings', 'deposits', 'activeFiscalYear'));
    }

    public function loanReports()
    {
        $activeFiscalYear = FiscalYear::getActive();
        
        $loans = Loan::when($activeFiscalYear, function($query) use ($activeFiscalYear) {
            $query->where('fiscal_year_id', $activeFiscalYear->id);
        })->with(['member', 'payments'])->get();

        return view('admin.reports.loans', compact('loans', 'activeFiscalYear'));
    }

    public function cashflowReports()
    {
        $activeFiscalYear = FiscalYear::getActive();
        
        $cashflows = CashFlow::when($activeFiscalYear, function($query) use ($activeFiscalYear) {
            $query->where('fiscal_year_id', $activeFiscalYear->id);
        })->with(['member'])->get();

        return view('admin.reports.cashflow', compact('cashflows', 'activeFiscalYear'));
    }
}
