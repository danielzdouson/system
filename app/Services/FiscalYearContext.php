<?php

namespace App\Services;

use App\Models\FiscalYear;

class FiscalYearContext
{
    /**
     * Get the currently selected fiscal year from session
     * NO FALLBACK - returns null if no session set (strict mode)
     * 
     * @return FiscalYear|null
     */
    public static function getCurrent(): ?FiscalYear
    {
        // Only check session - no fallback to active fiscal year
        $sessionFiscalYearId = session('current_fiscal_year_id');
        
        if ($sessionFiscalYearId) {
            $fiscalYear = FiscalYear::find($sessionFiscalYearId);
            if ($fiscalYear) {
                return $fiscalYear;
            }
        }
        
        // Return null if no fiscal year explicitly selected
        return null;
    }
    
    /**
     * Get the currently selected fiscal year from session
     * WITH FALLBACK to 'active' fiscal year (legacy mode)
     * 
     * @return FiscalYear|null
     */
    public static function getCurrentWithFallback(): ?FiscalYear
    {
        // First check session for explicit selection
        $sessionFiscalYearId = session('current_fiscal_year_id');
        
        if ($sessionFiscalYearId) {
            $fiscalYear = FiscalYear::find($sessionFiscalYearId);
            if ($fiscalYear) {
                return $fiscalYear;
            }
        }
        
        // Fall back to the active fiscal year
        return FiscalYear::where('status', 'active')->first();
    }
    
    /**
     * Get the ID of the current fiscal year
     * 
     * @return int|null
     */
    public static function getCurrentId(): ?int
    {
        $fiscalYear = self::getCurrent();
        return $fiscalYear ? $fiscalYear->id : null;
    }
    
    /**
     * Set the current fiscal year in session
     * 
     * @param int $fiscalYearId
     * @return void
     */
    public static function setCurrent(int $fiscalYearId): void
    {
        session(['current_fiscal_year_id' => $fiscalYearId]);
    }
    
    /**
     * Clear the fiscal year session (view all years)
     * 
     * @return void
     */
    public static function clear(): void
    {
        session()->forget('current_fiscal_year_id');
    }
    
    /**
     * Check if a specific fiscal year is currently selected
     * 
     * @param int $fiscalYearId
     * @return bool
     */
    public static function isCurrent(int $fiscalYearId): bool
    {
        return self::getCurrentId() === $fiscalYearId;
    }
    
    /**
     * Get all fiscal years for selector dropdown
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAllForSelector()
    {
        return FiscalYear::orderBy('start_date', 'desc')->get();
    }
    
    /**
     * Apply fiscal year filter to a query - STRICT MODE
     * Returns query that matches NO records if no fiscal year selected
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $dateField - The date field to filter on
     * @param string|null $fiscalYearIdField - Optional fiscal_year_id field name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function applyFilter($query, string $dateField = 'created_at', ?string $fiscalYearIdField = null)
    {
        $currentFiscalYear = self::getCurrent();
        
        // STRICT: If no fiscal year selected, return query that matches nothing
        if (!$currentFiscalYear) {
            return $query->whereRaw('1 = 0'); // This ensures no records are returned
        }
        
        // If fiscal_year_id field exists on the table, use that
        if ($fiscalYearIdField) {
            return $query->where($fiscalYearIdField, $currentFiscalYear->id);
        }
        
        // Otherwise filter by date range
        return $query->whereBetween($dateField, [
            $currentFiscalYear->start_date,
            $currentFiscalYear->end_date
        ]);
    }
}
