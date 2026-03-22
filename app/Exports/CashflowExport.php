<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CashflowExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
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
            'Description',
            'Reference Number',
            'Type',
            'Category',
            'Amount',
            'Payment Method',
            'Status',
            'Source',
            'Member ID'
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->transaction_date,
            $transaction->description,
            $transaction->reference_number,
            ucfirst($transaction->type),
            $transaction->category,
            number_format($transaction->amount, 2),
            $transaction->payment_method,
            ucfirst($transaction->status),
            $transaction->transaction_source,
            $transaction->member_id ?? 'N/A'
        ];
    }

    public function styles($sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => 'E2E8F0']
                ]
            ],
            'A1:J1' => ['alignment' => ['horizontal' => 'center']],
            'E:E' => ['alignment' => ['horizontal' => 'right']], // Amount column right-aligned
        ];
    }

    public function title(): string
    {
        return 'Cashflow Report - ' . now()->format('Y-m-d');
    }
}
