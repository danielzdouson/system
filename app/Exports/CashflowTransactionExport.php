<?php

namespace App\Exports;

use App\Models\CashflowTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;

class CashflowTransactionExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $transactions;

    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    public function collection()
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        return [
            'Transaction Date',
            'Type',
            'Category',
            'Subcategory',
            'Description',
            'Reference Number',
            'Payment Method',
            'Amount',
            'Status',
            'Member',
            'Created By',
            'Approved By',
            'Approved At'
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->transaction_date->format('Y-m-d'),
            $transaction->transaction_type,
            $transaction->category,
            $transaction->subcategory,
            $transaction->description,
            $transaction->reference_number,
            $transaction->payment_method,
            number_format($transaction->amount, 2),
            $transaction->status,
            $transaction->member ? $transaction->member->full_name : 'N/A',
            $transaction->creator ? $transaction->creator->name : 'N/A',
            $transaction->approver ? $transaction->approver->name : 'N/A',
            $transaction->approved_at ? $transaction->approved_at->format('Y-m-d H:i:s') : 'N/A'
        ];
    }

    public function styles($sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A1:G1000' => ['font' => ['bold' => false]],
        ];
    }

    public function title(): string
    {
        return 'Cashflow Transactions - ' . now()->format('Y-m-d');
    }
}
