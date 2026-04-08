<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Revenue;
use App\Models\BillProduct;
use App\Models\Customer;
use App\Models\BillAccount;
use App\Models\InvoiceProduct;
use App\Models\JournalItem;
use App\Models\Payment;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ReceivableExport implements  FromArray , WithHeadings , WithStyles, WithCustomStartCell, WithColumnWidths, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($data , $startDate, $endDate, $companyName)
    {
        $formattedData = [];
        $total = 0;
        
        foreach($data as $invoice)
        {
            $invoiceBalance = $invoice['total_price'] - $invoice['pay_price'];
            $balance = $invoiceBalance - $invoice['credit_price'];
            $total += $balance;
            
            $formattedData[] = [
                'Customer Name'         => $invoice['name'],
                'Invoice Balance'       => $invoiceBalance,
                'Available Credits'     => !empty($invoice['credit_price']) ? $invoice['credit_price'] : 0,
                'Balance'               => $balance,
            ];

        }
        if($formattedData != [])
        {
            $formattedData[] = [
                'Customer Name'         => 'Total',
                'Invoice Balance'       => '',
                'Available Credits'     => '',
                'Balance'               => $total,
            ];
        }
        
        $this->data         = $formattedData;
        $this->startDate    = $startDate;
        $this->endDate      = $endDate;
        $this->companyName  = $companyName;
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 20,
            'C' => 20,
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

            return [
                "Customer Name",
                "Invoice Balance",
                "Available Credits",
                "Balance",
            ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $event->sheet->getDelegate()->mergeCells('A2:D2');
                $event->sheet->getDelegate()->mergeCells('A3:D3');
                $event->sheet->getDelegate()->mergeCells('A4:D4');

                $event->sheet->getDelegate()->setCellValue('A2', 'Receivable Report ' .' - ' . $this->companyName)->getStyle('A2')->getFont()->setBold(true);
                $event->sheet->getDelegate()->setCellValue('A3', 'Print Out Date : ' . date('Y-m-d H:i'));
                $event->sheet->getDelegate()->setCellValue('A4', 'Date : ' . $this->startDate . ' - ' . $this->endDate);

                $startRow = 2;
                $lastRow = $event->sheet->getHighestRow();

                $event->sheet->getStyle('A' . $startRow . ':Z' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


                $data = $this->data;
                foreach ($data as $index => $row) {
                    if (isset($row['Customer Name']) && ($row['Customer Name'] == 'Total')) {
                        $rowIndex = $index + 7; // Adjust for 1-based indexing and header row
                        $event->sheet->getStyle('A' . $rowIndex . ':D' . $rowIndex)
                            ->applyFromArray([
                                'font' => [
                                    'bold' => true,
                                ],
                            ]);
                    }
                }
            },
        ];
    }

}
