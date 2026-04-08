<?php

namespace App\Exports;

use App\Models\Bill;
use App\Models\ProductServiceCategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BillExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = Bill::where('created_by', \Auth::user()->creatorId())->get();

        foreach($data as $k => $bill)
        {

            unset( $bill->id,$bill->vender_id,$bill->created_by, $bill->shipping_display,$bill->discount_apply,$bill->created_at,$bill->updated_at);
            $data[$k]["bill_id"] = \Auth::user()->billNumberFormat($bill->bill_id);
//            $data[$k]["vender_id"] = \Auth::user()->venderNumberFormat($bill->vender_id);
            $data[$k]['category_id'] = ProductServiceCategory::where('type', 'expense')->first()->name;
            $data[$k]["status"]       = Bill::$statues[$bill->status];

        }

        return $data;
    }

    public function headings(): array
    {
        return [
            "Bill No",
            "Bill Date",
            "Due Date",
            "Order No",
            "Status",
            "Type",
            "User Type",
            "Send Date",
            "Category"

        ];
    }
}
