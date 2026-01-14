<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;

class CashflowStatementExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $statement;

    public function __construct($statement)
    {
        $this->statement = $statement;
    }

    public function collection()
    {
        $rows = [];

        // Operating Activities
        if (!empty($this->statement['operating_activities']['details'])) {
            foreach ($this->statement['operating_activities']['details'] as $detail) {
                $rows[] = [
                    'Category' => 'Operating Activities',
                    'Subcategory' => $detail['subcategory'],
                    'Description' => $detail['subcategory'],
                    'Inflows' => $detail['inflows'],
                    'Outflows' => $detail['outflows'],
                    'Net' => $detail['net'],
                    'Type' => 'Operating'
                ];
            }
        }

        // Investing Activities
        if (!empty($this->statement['investing_activities']['details'])) {
            foreach ($this->statement['investing_activities']['details'] as $detail) {
                $rows[] = [
                    'Category' => 'Investing Activities',
                    'Subcategory' => $detail['subcategory'],
                    'Description' => $detail['subcategory'],
                    'Inflows' => $detail['inflows'],
                    'Outflows' => $detail['outflows'],
                    'Net' => $detail['net'],
                    'Type' => 'Investing'
                ];
            }
        }

        // Financing Activities
        if (!empty($this->statement['financing_activities']['details'])) {
            foreach ($this->statement['financing_activities']['details'] as $detail) {
                $rows[] = [
                    'Category' => 'Financing Activities',
                    'Subcategory' => $detail['subcategory'],
                    'Description' => $detail['subcategory'],
                    'Inflows' => $detail['inflows'],
                    'Outflows' => $detail['outflows'],
                    'Net' => $detail['net'],
                    'Type' => 'Financing'
                ];
            }
        }

        // Summary rows
        $rows[] = [
            'Category' => 'Summary',
            'Subcategory' => 'Opening Balance',
            'Description' => 'Balance brought forward',
            'Inflows' => 0,
            'Outflows' => 0,
            'Net' => $this->statement['summary']['opening_balance'],
            'Type' => ''
        ];

        $rows[] = [
            'Category' => 'Summary',
            'Subcategory' => 'Net Cashflow',
            'Description' => 'Total cash movement for period',
            'Inflows' => 0,
            'Outflows' => 0,
            'Net' => $this->statement['summary']['net_cashflow'],
            'Type' => ''
        ];

        $rows[] = [
            'Category' => 'Summary',
            'Subcategory' => 'Closing Balance',
            'Description' => 'Balance carried forward',
            'Inflows' => 0,
            'Outflows' => 0,
            'Net' => $this->statement['summary']['closing_balance'],
            'Type' => ''
        ];

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'Category',
            'Subcategory',
            'Description',
            'Inflows',
            'Outflows',
            'Net',
            'Type'
        ];
    }

    public function map($row): array
    {
        return [
            $row['Category'],
            $row['Subcategory'],
            $row['Description'],
            number_format($row['Inflows'], 2),
            number_format($row['Outflows'], 2),
            number_format($row['Net'], 2),
            $row['Type']
        ];
    }

    public function styles($sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A2:G1000' => ['font' => ['bold' => false]],
        ];
    }

    public function title(): string
    {
        return 'Cashflow Statement - ' . $this->statement['period'];
    }
}
