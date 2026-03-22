<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FiscalYearContext;
use App\Models\CashFlow;
use Illuminate\View\View;

class FinancialController extends Controller
{
    /**
     * Display the financial dashboard
     */
    public function index(): View
    {
        $currentFiscalYear = FiscalYearContext::getCurrent();
        $allFiscalYears = FiscalYearContext::getAllForSelector();
        
        // Get financial statistics filtered by current fiscal year
        $memberFinancialService = new \App\Services\MemberFinancialSummaryService();
        $stats = $memberFinancialService->getMembersSummaryStats($currentFiscalYear);
        
        return view('admin.financials.index', compact('currentFiscalYear', 'allFiscalYears', 'stats'));
    }
}
