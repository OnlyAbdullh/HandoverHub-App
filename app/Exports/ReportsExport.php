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
            'Report ID',
            'Report Number',
            'Visit Date',
            'Visit Time',
            'Visit Type',
            'Visit Reason',
            'Generator ID',
            'Generator Brand',
            'Initial Meter',
            'Engine Brand',
            'Engine Capacity',
            'Site Name',
            'Site Code',
            'Oil Pressure',
            'Temperature',
            'Battery Voltage',
            'Oil Quantity',
            'Burned Oil Quantity',
            'Frequency',
            'Current Meter',
            'ATS Status',
            'Voltage L1',
            'Voltage L2',
            'Voltage L3',
            'Load L1',
            'Load L2',
            'Load L3',
            'Longitude',
            'Latitude',
            'Last Meter',
            'Last Routine Visit Date',
            'Last Routine Current Reading',
            'Technical Status',
            'Technician Notes',
            'Completed Works',
            'Replaced Parts'
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
