<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ReportsExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected array $reports;

    public function __construct(array $reports)
    {
        $this->reports = $reports;
    }

    public function array(): array
    {
        return $this->reports;
    }

    public function headings(): array
    {
        return [
            'User Name',
            'Report ID',
            'Report Number',
            'Site Name',
            'Site Code',
            'Visit Date',
            'Engine Brand',
            'Engine Capacity',
            'Visit Type',
            'Visit Reason',
            'Last Meter',
            'Last Routine Visit Date',
            'Last Routine Current Reading',
            'Technical Status',
            'Technician Notes',
            'Replaced Part',
            'Qty',
            'Faulty_Qty',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as header
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FFE0E0E0',
                    ],
                ],
            ],
        ];
    }
}
