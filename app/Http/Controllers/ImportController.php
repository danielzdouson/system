<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\Member;
use App\Models\MemberFinancial;
use App\Models\MemberLoanSummary;
use App\Models\CashFlow;

class ImportController extends Controller
{
    public function importMembers(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $file = $request->file('excel_file');
            $import = new MemberImport();
            Excel::import($import, $file);
            
            return response()->json([
                'success' => true,
                'message' => 'Members imported successfully!',
                'imported' => $import->getImportedCount(),
                'errors' => $import->getErrors(),
                'duplicates' => $import->getDuplicates()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error importing members: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importFinancials(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $file = $request->file('excel_file');
            $import = new FinancialImport();
            Excel::import($import, $file);
            
            return response()->json([
                'success' => true,
                'message' => 'Financial records imported successfully!',
                'imported' => $import->getImportedCount(),
                'errors' => $import->getErrors(),
                'duplicates' => $import->getDuplicates()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error importing financial records: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importLoans(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $file = $request->file('excel_file');
            $import = new LoanImport();
            Excel::import($import, $file);
            
            return response()->json([
                'success' => true,
                'message' => 'Loan records imported successfully!',
                'imported' => $import->getImportedCount(),
                'errors' => $import->getErrors(),
                'duplicates' => $import->getDuplicates()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error importing loan records: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importCashFlow(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $file = $request->file('excel_file');
            $import = new CashFlowImport();
            Excel::import($import, $file);
            
            return response()->json([
                'success' => true,
                'message' => 'Cash flow records imported successfully!',
                'imported' => $import->getImportedCount(),
                'errors' => $import->getErrors(),
                'duplicates' => $import->getDuplicates()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error importing cash flow records: ' . $e->getMessage()
            ], 500);
        }
    }
}

class MemberImport implements ToModel, WithHeadingRow, WithValidation
{
    private $importedCount = 0;
    private $errors = [];
    private $duplicates = [];

    public function model(array $row)
    {
        try {
            // Validate required fields
            if (empty($row[0]) || empty($row[1])) {
                $this->errors[] = "Row " . ($this->importedCount + 1) . ": Missing required fields (first name, last name)";
                return null;
            }

            // Check for duplicate national ID
            $nationalId = $row[2] ?? null;
            if ($nationalId && Member::where('national_id', $nationalId)->exists()) {
                $this->duplicates[] = "Row " . ($this->importedCount + 1) . ": Duplicate National ID: $nationalId";
                return null;
            }

            // Check for duplicate email
            $email = $row[3] ?? null;
            if ($email && Member::where('email', $email)->exists()) {
                $this->duplicates[] = "Row " . ($this->importedCount + 1) . ": Duplicate Email: $email";
                return null;
            }

            Member::create([
                'first_name' => $row[0],
                'last_name' => $row[1],
                'national_id' => $nationalId,
                'email' => $email,
                'phone' => $row[4] ?? null,
            ]);

            $this->importedCount++;
            return null;
        } catch (\Exception $e) {
            $this->errors[] = "Row " . ($this->importedCount + 1) . ": " . $e->getMessage();
            return null;
        }
    }

    public function rules(): array
    {
        return [
            '0' => 'required|string|max:255',
            '1' => 'required|string|max:255',
            '2' => 'nullable|string|max:50',
            '3' => 'nullable|email|max:255',
            '4' => 'nullable|string|max:20',
        ];
    }

    public function getImportedCount() { return $this->importedCount; }
    public function getErrors() { return $this->errors; }
    public function getDuplicates() { return $this->duplicates; }
}

class FinancialImport implements ToModel, WithHeadingRow, WithValidation
{
    private $importedCount = 0;
    private $errors = [];
    private $duplicates = [];

    public function model(array $row)
    {
        try {
            $memberId = $row[0] ?? null;
            if (!$memberId) {
                $this->errors[] = "Row " . ($this->importedCount + 1) . ": Member ID is required";
                return null;
            }

            MemberFinancial::create([
                'member_id' => $memberId,
                'name' => $row[1] ?? 'Unknown',
                'number' => $row[2] ?? null,
                'savings' => $row[3] ?? 0,
                'welfare' => $row[4] ?? 0,
                'education_in' => $row[5] ?? 0,
                'fined' => $row[6] ?? 0,
                'fines_paid' => $row[7] ?? 0,
                'education_out' => $row[8] ?? 0,
                'loan_repayments' => $row[9] ?? 0,
                'loan_charges' => $row[10] ?? 0,
                'notes' => $row[11] ?? null,
            ]);

            $this->importedCount++;
            return null;
        } catch (\Exception $e) {
            $this->errors[] = "Row " . ($this->importedCount + 1) . ": " . $e->getMessage();
            return null;
        }
    }

    public function rules(): array
    {
        return [
            '0' => 'required|exists:members,id',
            '1' => 'required|string|max:255',
            '2' => 'nullable|string|max:255',
            '3' => 'nullable|numeric|min:0',
            '4' => 'nullable|numeric|min:0',
            '5' => 'nullable|numeric|min:0',
            '6' => 'nullable|numeric|min:0',
            '7' => 'nullable|numeric|min:0',
            '8' => 'nullable|numeric|min:0',
            '9' => 'nullable|numeric|min:0',
            '10' => 'nullable|numeric|min:0',
            '11' => 'nullable|string|max:1000',
        ];
    }

    public function getImportedCount() { return $this->importedCount; }
    public function getErrors() { return $this->errors; }
    public function getDuplicates() { return $this->duplicates; }
}

class LoanImport implements ToModel, WithHeadingRow, WithValidation
{
    private $importedCount = 0;
    private $errors = [];
    private $duplicates = [];

    public function model(array $row)
    {
        try {
            $memberId = $row[0] ?? null;
            if (!$memberId) {
                $this->errors[] = "Row " . ($this->importedCount + 1) . ": Member ID is required";
                return null;
            }

            MemberLoanSummary::create([
                'member_id' => $memberId,
                'name' => $row[1] ?? 'Unknown',
                'loan_brought_forward' => $row[2] ?? 0,
                'loan_issued_current_year' => $row[3] ?? 0,
                'current_year_loan_plus_interest' => $row[4] ?? 0,
                'loan_balance_without_fines' => $row[5] ?? 0,
                'loan_out' => $row[6] ?? 0,
                'total' => $row[7] ?? 0,
                'notes' => $row[8] ?? null,
            ]);

            $this->importedCount++;
            return null;
        } catch (\Exception $e) {
            $this->errors[] = "Row " . ($this->importedCount + 1) . ": " . $e->getMessage();
            return null;
        }
    }

    public function rules(): array
    {
        return [
            '0' => 'required|exists:members,id',
            '1' => 'required|string|max:255',
            '2' => 'nullable|numeric|min:0',
            '3' => 'nullable|numeric|min:0',
            '4' => 'nullable|numeric|min:0',
            '5' => 'nullable|numeric|min:0',
            '6' => 'nullable|numeric|min:0',
            '7' => 'nullable|numeric|min:0',
            '8' => 'nullable|string|max:1000',
        ];
    }

    public function getImportedCount() { return $this->importedCount; }
    public function getErrors() { return $this->errors; }
    public function getDuplicates() { return $this->duplicates; }
}

class CashFlowImport implements ToModel, WithHeadingRow, WithValidation
{
    private $importedCount = 0;
    private $errors = [];
    private $duplicates = [];

    public function model(array $row)
    {
        try {
            // Validate required fields
            if (empty($row[0]) || empty($row[2]) || empty($row[3])) {
                $this->errors[] = "Row " . ($this->importedCount + 1) . ": Missing required fields (date, description, amount)";
                return null;
            }

            CashFlow::create([
                'user_id' => auth()->id(),
                'transaction_date' => $row[0],
                'description' => $row[1],
                'category' => $row[2],
                'type' => $row[3],
                'amount' => $row[4],
                'payment_method' => $row[5] ?? 'cash',
                'reference_number' => $row[6] ?? null,
                'notes' => $row[7] ?? null,
                'status' => 'cleared',
            ]);

            $this->importedCount++;
            return null;
        } catch (\Exception $e) {
            $this->errors[] = "Row " . ($this->importedCount + 1) . ": " . $e->getMessage();
            return null;
        }
    }

    public function rules(): array
    {
        return [
            '0' => 'required|date',
            '1' => 'required|string|max:1000',
            '2' => 'required|string|max:255',
            '3' => 'required|in:income,expense',
            '4' => 'required|numeric|min:0',
            '5' => 'nullable|string|max:255',
            '6' => 'nullable|string|max:255',
            '7' => 'nullable|string|max:1000',
        ];
    }

    public function getImportedCount() { return $this->importedCount; }
    public function getErrors() { return $this->errors; }
    public function getDuplicates() { return $this->duplicates; }
}
