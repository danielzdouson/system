<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FiscalYear;
use App\Services\FiscalYearCarryForwardService;
use Illuminate\Http\Request;

class FiscalYearController extends Controller
{
    public function index()
    {
        $fiscalYears = FiscalYear::orderBy('start_date', 'desc')->get();
        $activeFiscalYear = FiscalYear::where('status', 'active')->first();
        return view('admin.fiscal-years.index', compact('fiscalYears', 'activeFiscalYear'));
    }

    public function create()
    {
        return view('admin.fiscal-years.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,inactive',
        ]);

        // If setting as active, deactivate all other fiscal years
        if ($request->status == 'active') {
            FiscalYear::where('status', 'active')->update(['status' => 'inactive']);
        }

        $fiscalYear = FiscalYear::create([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.fiscal-years.index')
            ->with('success', 'Fiscal year "' . $fiscalYear->name . '" created successfully!');
    }

    public function edit(FiscalYear $fiscalYear)
    {
        return view('admin.fiscal-years.edit', compact('fiscalYear'));
    }

    public function update(Request $request, FiscalYear $fiscalYear)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,inactive',
        ]);

        // If setting as active, deactivate all other fiscal years
        if ($request->status == 'active') {
            FiscalYear::where('id', '!=', $fiscalYear->id)
                ->where('status', 'active')
                ->update(['status' => 'inactive']);
        }

        $fiscalYear->update([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.fiscal-years.index')
            ->with('success', 'Fiscal year "' . $fiscalYear->name . '" updated successfully!');
    }

    public function destroy(FiscalYear $fiscalYear)
    {
        $fiscalYear->delete();
        return redirect()->route('admin.fiscal-years.index')
            ->with('success', 'Fiscal year deleted successfully!');
    }

    public function carryForward(FiscalYear $fromFiscalYear, FiscalYear $toFiscalYear)
    {
        $carryForwardService = new FiscalYearCarryForwardService();
        
        // Check if carry forward is needed
        if (!$carryForwardService->isCarryForwardNeeded($fromFiscalYear, $toFiscalYear)) {
            return redirect()->back()
                ->with('info', 'No items need to be carried forward from ' . $fromFiscalYear->name);
        }

        // Get carry forward summary
        $summary = $carryForwardService->getCarryForwardSummary($fromFiscalYear, $toFiscalYear);

        return view('admin.fiscal-years.carry-forward', compact('fromFiscalYear', 'toFiscalYear', 'summary'));
    }

    public function processCarryForward(Request $request, FiscalYear $fromFiscalYear, FiscalYear $toFiscalYear)
    {
        $request->validate([
            'confirm' => 'required|accepted',
        ]);

        $carryForwardService = new FiscalYearCarryForwardService();
        
        try {
            $results = $carryForwardService->carryForwardAll($fromFiscalYear, $toFiscalYear, auth()->id());
            
            $successCount = 0;
            $failureCount = 0;
            
            foreach ($results as $type => $items) {
                foreach ($items as $result) {
                    if ($result['success']) {
                        $successCount++;
                    } else {
                        $failureCount++;
                    }
                }
            }

            $message = "Carry forward completed: {$successCount} items successfully carried forward";
            if ($failureCount > 0) {
                $message .= ", {$failureCount} items failed";
            }

            return redirect()->route('admin.fiscal-years.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Carry forward failed: ' . $e->getMessage());
        }
    }

    public function carryForwardHistory(FiscalYear $fiscalYear = null)
    {
        $carryForwardService = new FiscalYearCarryForwardService();
        $history = $carryForwardService->getCarryForwardHistory($fiscalYear);

        return view('admin.fiscal-years.carry-forward-history', compact('history', 'fiscalYear'));
    }
}
