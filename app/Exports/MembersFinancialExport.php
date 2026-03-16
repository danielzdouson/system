<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MembersFinancialExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data)->map(function ($item) {
            return [
                $item['Member Number'],
                $item['Name'],
                $item['Total Deposits'],
                $item['Total Savings'],
                $item['Welfare'],
                $item['Outstanding Fines'],
                $item['Loan Balance'],
                $item['Available Balance'],
                $item['Distributed Funds'],
                $item['Total Shares (%)'],
                $item['Net Worth'],
                $item['Status']
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Member Number',
            'Name',
            'Total Deposits',
            'Total Savings',
            'Welfare',
            'Outstanding Fines',
            'Loan Balance',
            'Available Balance',
            'Distributed Funds',
            'Total Shares (%)',
            'Net Worth',
            'Status'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ]
            ],
            'A1:L1' => ['alignment' => ['horizontal' => 'center']],
            'C:K' => ['alignment' => ['horizontal' => 'right']],
        ];
    }

    public function title(): string
    {
        return 'Members Financial Summary - ' . now()->format('Y-m-d');
    }
}
