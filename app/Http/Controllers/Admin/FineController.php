<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fine;
use App\Models\Member;
use App\Models\FiscalYear;
use App\Models\GroupSaving;
use App\Models\Deposit;
use Carbon\Carbon;
use DB;
use PDF;

class FineController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = $request->get('fiscal_year');
        $currentDate = now();
        
        // Auto-detect current fiscal year if none selected
        if (!$selectedYear) {
            $activeFiscalYear = FiscalYear::where('start_date', '<=', $currentDate)
                ->where('end_date', '>=', $currentDate)
                ->orderBy('start_date', 'desc')
                ->first();
        } else {
            $activeFiscalYear = FiscalYear::find($selectedYear);
        }
        
        $allFiscalYears = FiscalYear::orderBy('start_date', 'desc')->get();
        
        if (!$activeFiscalYear) {
            return view('admin.fines.index', [
                'activeFiscalYear' => null,
                'allFiscalYears' => $allFiscalYears,
                'fines' => collect(),
                'statistics' => $this->getEmptyStatistics()
            ]);
        }
        
        // Build query with filters
        $query = Fine::with(['member', 'fiscalYear', 'creator'])
            ->where('fiscal_year_id', $activeFiscalYear->id);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }
        
        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }
        
        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }
        
        // Search by member name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('member', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%");
            });
        }
        
        $fines = $query->orderBy('created_at', 'desc')->paginate(50);
        $statistics = $this->calculateStatistics($activeFiscalYear->id, $request->all());
        
        return view('admin.fines.index', compact(
            'activeFiscalYear',
            'allFiscalYears', 
            'fines',
            'statistics'
        ));
    }
    
    public function create()
    {
        $members = Member::orderBy('first_name')->orderBy('last_name')->get();
        $fiscalYears = FiscalYear::orderBy('start_date', 'desc')->get();
        
        return view('admin.fines.create', compact('members', 'fiscalYears'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'fiscal_year_id' => 'required|exists:fiscal_years,id',
            'month' => 'required|integer|min:1|max:12',
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|in:missed_saving,late_payment,other',
            'description' => 'nullable|string|max:255'
        ]);
        
        // Check if fine already exists for this member, month, year, and reason
        $existingFine = Fine::where('member_id', $request->member_id)
            ->where('fiscal_year_id', $request->fiscal_year_id)
            ->where('month', $request->month)
            ->where('reason', $request->reason)
            ->first();
            
        if ($existingFine) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'A fine with these details already exists for this member.');
        }
        
        Fine::create([
            'member_id' => $request->member_id,
            'fiscal_year_id' => $request->fiscal_year_id,
            'month' => $request->month,
            'amount' => $request->amount,
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => 'pending',
            'created_by' => auth()->id()
        ]);
        
        return redirect()->route('admin.fines.index')
            ->with('success', 'Fine created successfully.');
    }
    
    public function edit(Fine $fine)
    {
        $members = Member::orderBy('first_name')->orderBy('last_name')->get();
        $fiscalYears = FiscalYear::orderBy('start_date', 'desc')->get();
        
        return view('admin.fines.edit', compact('fine', 'members', 'fiscalYears'));
    }
    
    public function update(Request $request, Fine $fine)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|in:missed_saving,late_payment,other',
            'description' => 'nullable|string|max:255'
        ]);
        
        $fine->update([
            'amount' => $request->amount,
            'reason' => $request->reason,
            'description' => $request->description
        ]);
        
        return redirect()->route('admin.fines.index')
            ->with('success', 'Fine updated successfully.');
    }
    
    public function pay(Request $request, Fine $fine)
    {
        if ($fine->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending fines can be marked as paid.');
        }
        
        $request->validate([
            'payment_method' => 'nullable|string|max:100',
            'payment_notes' => 'nullable|string|max:255'
        ]);
        
        $fine->markAsPaid();
        
        // Add payment notes to description if provided
        if ($request->payment_notes) {
            $fine->description .= "\n\nPayment Notes: " . $request->payment_notes;
            $fine->save();
        }
        
        return redirect()->back()
            ->with('success', 'Fine marked as paid successfully.');
    }
    
    public function waive(Request $request, Fine $fine)
    {
        if ($fine->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending fines can be waived.');
        }
        
        $request->validate([
            'waiver_reason' => 'required|string|max:255'
        ]);
        
        $fine->waive($request->waiver_reason);
        
        return redirect()->back()
            ->with('success', 'Fine waived successfully.');
    }
    
    public function destroy(Fine $fine)
    {
        if ($fine->status === 'paid') {
            return redirect()->back()
                ->with('error', 'Paid fines cannot be deleted.');
        }
        
        $fine->delete();
        
        return redirect()->route('admin.fines.index')
            ->with('success', 'Fine deleted successfully.');
    }
    
    public function bulkApply(Request $request)
    {
        $request->validate([
            'fiscal_year_id' => 'required|exists:fiscal_years,id',
            'month' => 'required|integer|min:1|max:12',
            'fine_type' => 'required|in:missed_saving,late_payment',
            'amount' => 'required|numeric|min:0',
            'members' => 'required|array',
            'members.*' => 'exists:members,id'
        ]);
        
        $appliedCount = 0;
        $skippedCount = 0;
        
        foreach ($request->members as $memberId) {
            // Check if fine already exists
            $existingFine = Fine::where('member_id', $memberId)
                ->where('fiscal_year_id', $request->fiscal_year_id)
                ->where('month', $request->month)
                ->where('reason', $request->fine_type)
                ->first();
                
            if (!$existingFine) {
                Fine::create([
                    'member_id' => $memberId,
                    'fiscal_year_id' => $request->fiscal_year_id,
                    'month' => $request->month,
                    'amount' => $request->amount,
                    'reason' => $request->fine_type,
                    'description' => $this->getDefaultDescription($request->fine_type, $request->month),
                    'status' => 'pending',
                    'created_by' => auth()->id()
                ]);
                $appliedCount++;
            } else {
                $skippedCount++;
            }
        }
        
        return redirect()->back()
            ->with('success', "Applied {$appliedCount} fines. Skipped {$skippedCount} existing fines.");
    }
    
    public function autoApply(Request $request)
    {
        $request->validate([
            'fiscal_year_id' => 'required|exists:fiscal_years,id',
            'month' => 'required|integer|min:1|max:12'
        ]);
        
        $fiscalYear = FiscalYear::find($request->fiscal_year_id);
        $appliedCount = 0;
        
        // Get all members who missed savings for the specified month
        $membersWithoutSavings = Member::whereDoesntHave('groupSavings', function($query) use ($request) {
            $query->where('fiscal_year_id', $request->fiscal_year_id)
                  ->where('month', $request->month)
                  ->where('status', 'paid');
        })->get();
        
        foreach ($membersWithoutSavings as $member) {
            // Check if fine already exists
            $existingFine = Fine::where('member_id', $member->id)
                ->where('fiscal_year_id', $request->fiscal_year_id)
                ->where('month', $request->month)
                ->where('reason', 'missed_saving')
                ->first();
                
            if (!$existingFine) {
                Fine::createMissedSavingFine($member->id, $request->fiscal_year_id, $request->month);
                $appliedCount++;
            }
        }
        
        return redirect()->back()
            ->with('success', "Automatically applied {$appliedCount} missed saving fines.");
    }
    
    public function reports(Request $request)
    {
        $selectedYear = $request->get('fiscal_year');
        $currentDate = now();
        
        if (!$selectedYear) {
            $activeFiscalYear = FiscalYear::where('start_date', '<=', $currentDate)
                ->where('end_date', '>=', $currentDate)
                ->orderBy('start_date', 'desc')
                ->first();
        } else {
            $activeFiscalYear = FiscalYear::find($selectedYear);
        }
        
        $allFiscalYears = FiscalYear::orderBy('start_date', 'desc')->get();
        
        if (!$activeFiscalYear) {
            return view('admin.fines.reports', [
                'activeFiscalYear' => null,
                'allFiscalYears' => $allFiscalYears,
                'reports' => []
            ]);
        }
        
        $reports = $this->generateFineReports($activeFiscalYear->id);
        
        return view('admin.fines.reports', compact(
            'activeFiscalYear',
            'allFiscalYears',
            'reports'
        ));
    }
    
    public function export(Request $request)
    {
        $selectedYear = $request->get('fiscal_year');
        $format = $request->get('format', 'csv');
        
        if (!$selectedYear) {
            return redirect()->back()
                ->with('error', 'Please select a fiscal year.');
        }
        
        $fiscalYear = FiscalYear::find($selectedYear);
        
        if (!$fiscalYear) {
            return redirect()->back()
                ->with('error', 'Invalid fiscal year.');
        }
        
        $fines = Fine::with(['member', 'creator'])
            ->where('fiscal_year_id', $selectedYear)
            ->orderBy('created_at', 'desc')
            ->get();
        
        if ($format === 'pdf') {
            return $this->exportPDF($fines, $fiscalYear);
        }
        
        return $this->exportCSV($fines, $fiscalYear);
    }
    
    private function calculateStatistics($fiscalYearId, $filters = [])
    {
        $query = Fine::where('fiscal_year_id', $fiscalYearId);
        
        // Apply same filters as main query
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (isset($filters['reason'])) {
            $query->where('reason', $filters['reason']);
        }
        
        if (isset($filters['member_id'])) {
            $query->where('member_id', $filters['member_id']);
        }
        
        if (isset($filters['month'])) {
            $query->where('month', $filters['month']);
        }
        
        $fines = $query->get();
        
        return [
            'total_fines' => $fines->count(),
            'total_amount' => $fines->sum('amount'),
            'pending_fines' => $fines->where('status', 'pending')->count(),
            'pending_amount' => $fines->where('status', 'pending')->sum('amount'),
            'paid_fines' => $fines->where('status', 'paid')->count(),
            'paid_amount' => $fines->where('status', 'paid')->sum('amount'),
            'waived_fines' => $fines->where('status', 'waived')->count(),
            'waived_amount' => $fines->where('status', 'waived')->sum('amount'),
            'missed_saving_fines' => $fines->where('reason', 'missed_saving')->count(),
            'late_payment_fines' => $fines->where('reason', 'late_payment')->count(),
            'other_fines' => $fines->where('reason', 'other')->count(),
        ];
    }
    
    private function getEmptyStatistics()
    {
        return [
            'total_fines' => 0,
            'total_amount' => 0,
            'pending_fines' => 0,
            'pending_amount' => 0,
            'paid_fines' => 0,
            'paid_amount' => 0,
            'waived_fines' => 0,
            'waived_amount' => 0,
            'missed_saving_fines' => 0,
            'late_payment_fines' => 0,
            'other_fines' => 0,
        ];
    }
    
    private function getDefaultDescription($reason, $month)
    {
        $monthName = Fine::getMonthName($month);
        
        switch ($reason) {
            case 'missed_saving':
                return "Fine for missed saving in {$monthName}";
            case 'late_payment':
                return "Fine for late payment in {$monthName}";
            default:
                return "Fine applied for {$monthName}";
        }
    }
    
    private function generateFineReports($fiscalYearId)
    {
        $reports = [];
        
        // Monthly breakdown
        $monthlyFines = Fine::where('fiscal_year_id', $fiscalYearId)
            ->selectRaw('month, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            
        $reports['monthly'] = $monthlyFines;
        
        // Reason breakdown
        $reasonFines = Fine::where('fiscal_year_id', $fiscalYearId)
            ->selectRaw('reason, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('reason')
            ->get();
            
        $reports['reasons'] = $reasonFines;
        
        // Status breakdown
        $statusFines = Fine::where('fiscal_year_id', $fiscalYearId)
            ->selectRaw('status, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('status')
            ->get();
            
        $reports['status'] = $statusFines;
        
        // Top members with most fines
        $memberFines = Fine::where('fiscal_year_id', $fiscalYearId)
            ->with('member')
            ->selectRaw('member_id, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('member_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
            
        $reports['top_members'] = $memberFines;
        
        return $reports;
    }
    
    private function exportCSV($fines, $fiscalYear)
    {
        $filename = "fines_{$fiscalYear->name}_" . date('Y-m-d') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($fines) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'ID', 'Member Name', 'National ID', 'Month', 'Amount', 
                'Reason', 'Description', 'Status', 'Created Date', 'Created By'
            ]);
            
            // Data rows
            foreach ($fines as $fine) {
                fputcsv($file, [
                    $fine->id,
                    $fine->member ? $fine->member->first_name . ' ' . $fine->member->last_name : 'Unknown',
                    $fine->member ? $fine->member->national_id : 'N/A',
                    Fine::getMonthName($fine->month),
                    $fine->amount,
                    ucfirst(str_replace('_', ' ', $fine->reason)),
                    $fine->description,
                    ucfirst($fine->status),
                    $fine->created_at->format('Y-m-d H:i:s'),
                    $fine->creator ? $fine->creator->name : 'System'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    private function exportPDF($fines, $fiscalYear)
    {
        $filename = "fines_{$fiscalYear->name}_" . date('Y-m-d') . ".pdf";
        
        $data = [
            'fines' => $fines,
            'fiscalYear' => $fiscalYear,
            'generatedDate' => now()->format('Y-m-d H:i:s')
        ];
        
        $pdf = PDF::loadView('admin.fines.pdf', $data);
        
        return $pdf->download($filename);
    }
}
