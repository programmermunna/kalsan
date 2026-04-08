<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesReportExport implements FromArray, WithHeadings, WithStyles, WithCustomStartCell, WithColumnWidths, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function __construct($data, $startDate, $endDate, $companyName, $reportName)
    {

        $formattedData = [];

        if ($reportName == 'Item') {
            foreach ($data as $value) {
                $formattedData[] = [
                    'Item Name' => $value['name'],
                    'Quantity Sold' => $value['quantity'],
                    'Amount' => $value['price'],
                    'Average Amount' => $value['avg_price'],
                ];
            }
        } else {
            foreach ($data as $value) {
                $formattedData[] = [
                    'Customer Name' => $value['name'],
                    'Invoice Count' => $value['invoice_count'],
                    'Sales' => $value['price'],
                    'Sales With Tax' => $value['price'] + $value['total_tax'],
                ];
            }
        }

        $this->data = $formattedData;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->companyName = $companyName;
        $this->reportName = $reportName;
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 15,
            'C' => 15,
            'D' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A6')->getFont()->setBold(true);
        $sheet->getStyle('B6')->getFont()->setBold(true);
        $sheet->getStyle('C6')->getFont()->setBold(true);
        $sheet->getStyle('D6')->getFont()->setBold(true);

    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {

        if ($this->reportName == 'Item') {
            return [
                "Item Name",
                "Quantity Sold",
                "Amount",
                "Average Amount",
            ];
        } else {
            return [
                "Customer Name",
                "Invoice Count",
                "Sales",
                "Sales With Tax",
            ];
        }

    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $event->sheet->getDelegate()->mergeCells('A2:D2');
                $event->sheet->getDelegate()->mergeCells('A3:D3');
                $event->sheet->getDelegate()->mergeCells('A4:D4');

                $event->sheet->getDelegate()->setCellValue('A2', 'Sales By ' . $this->reportName . ' - ' . $this->companyName)->getStyle('A2')->getFont()->setBold(true);
                $event->sheet->getDelegate()->setCellValue('A3', 'Print Out Date : ' . date('Y-m-d H:i'));
                $event->sheet->getDelegate()->setCellValue('A4', 'Date : ' . $this->startDate . ' - ' . $this->endDate);

                $startRow = 2;
                $lastRow = $event->sheet->getHighestRow();

                $event->sheet->getStyle('A' . $startRow . ':Z' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            },
        ];
    }

}
