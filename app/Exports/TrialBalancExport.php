<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;

class TrialBalancExport implements FromArray , WithHeadings , WithStyles, WithCustomStartCell, WithColumnWidths, WithEvents, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public $data;         
    public $startDate;    
    public $endDate;      
    public $companyName;  

    public function __construct($data , $startDate, $endDate, $companyName)
    {
        $formattedData = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach($data as $key => $type)
        {
            $formattedData[] = [
                'Account Name' => '',
                'Account No'   => '',
                'Debit'        => '',
                'Credit'       => '',
            ];

            $formattedData[] = [
                'Account Name' => $key,
                'Account No'   => '',
                'Debit'        => '',
                'Credit'       => '',
            ];

            foreach($type as $account)
            {
                if($account['account'] == 'subAccount')
                {
                    $formattedData[] = [
                        'Account Name' => '      '.$account['account_name'],
                        'Account No'   => $account['account_code'],
                        'Debit'        => $account['totalDebit'],
                        'Credit'       => $account['totalCredit'],
                    ];
                }
                else
                {
                    $formattedData[] = [
                        'Account Name' => '   ' . $account['account_name'],
                        'Account No'   => $account['account_code'],
                        'Debit'        => $account['totalDebit'],
                        'Credit'       => $account['totalCredit'],
                    ];
                }

                if($account['account'] != 'parent' && $account['account'] != 'subAccount')
                {
                $totalDebit += $account['totalDebit'];
                $totalCredit += $account['totalCredit'];
                }
            }

        }
        if($formattedData != [])
        {
            $formattedData[] = [
                'Account Name' => 'Total',
                'Account No'   => '',
                'Debit'        => $totalDebit,
                'Credit'       => $totalCredit,
            ];
        }

        $this->data         = $formattedData;
        $this->startDate    = $startDate;
        $this->endDate      = $endDate;
        $this->companyName  = $companyName;
    }

    public function map($row): array
    {
        return [
            $row['Account Name'],
            $row['Account No'],
            ($row['Debit'] === 0 || $row['Debit'] === 0.0) ? '0' : $row['Debit'],
            ($row['Credit'] === 0 || $row['Credit'] === 0.0) ? '0' : $row['Credit'],
        ];
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
            'D' => 15,
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
            "Account Name",
            "Account No",
            "Debit",
            "Credit",
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $event->sheet->getDelegate()->mergeCells('A2:D2');
                $event->sheet->getDelegate()->mergeCells('A3:D3');
                $event->sheet->getDelegate()->mergeCells('A4:D4');

                $event->sheet->getDelegate()->setCellValue('A2', 'Trial Balance - ' . $this->companyName)->getStyle('A2')->getFont()->setBold(true);
                $event->sheet->getDelegate()->setCellValue('A3', 'Print Out Date : ' . date('Y-m-d H:i'));
                $event->sheet->getDelegate()->setCellValue('A4', 'Date : ' . $this->startDate . ' - ' . $this->endDate);

                $lastRow = $event->sheet->getHighestRow();

                $event->sheet->getStyle('A' . $lastRow . ':Z' . $lastRow)->getFont()->setBold(true);

                foreach (['A2:D2', 'A3:D3', 'A4:D4'] as $range) {
                    $event->sheet->getStyle($range)
                        ->getAlignment()
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                }

                $data = $this->data;
                foreach ($data as $index => $row) {
                    if (isset($row['Account Name']) && ($row['Account Name'] == 'Assets' || $row['Account Name'] == 'Income' || $row['Account Name'] == 'Costs of Goods Sold' || $row['Account Name'] == 'Expenses' ||
                     $row['Account Name'] ==  'Liabilities' || $row['Account Name'] ==  'Equity')) {
                        $rowIndex = $index + 7;
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
