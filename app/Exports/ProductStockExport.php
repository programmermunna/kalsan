<?php

namespace App\Exports;

use App\Models\StockReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductStockExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = [];
        $data = StockReport::where('created_by' , \Auth::user()->id)->get();
        if (!empty($data)) {
            foreach ($data as $k => $Stock) {
                // $product  = $Stock->product_id;
                $product = StockReport::products($Stock->product_id);
                // unset($Stock->id,$Stock->created_by,$Stock->updated_at,$Stock->type_id);
                // $data[$k]["product_id"]        = $product;

                $formattedDate = $Stock->created_at->format('d M Y');
                $data[$k] = [
                    'product_name' => $product,
                    'quantity' => $Stock->quantity,
                    'type' => ucfirst($Stock->type),
                    'description' => $Stock->description,
                    'date' => $formattedDate,
                ];
            }
        }
        // return $data;
        return collect($data);
    }

    public function headings(): array
    {
        return [
            "Product Name",
            "Quantity",
            "Type",
            "Description",
            "Date",
        ];
    }
}
