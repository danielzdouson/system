<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FiscalYear;
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
}
