<?php

namespace App\Exports;

use App\Models\Revenue;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AccountStatementExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = [];
        $data = Revenue::where('created_by' , \Auth::user()->id)->get();
        if (!empty($data)) {
            foreach ($data as $k => $Statement) {
                unset($Statement->id,$Statement->created_by, $Statement->updated_at, $Statement->created_at,$Statement->account_id, $Statement->customer_id,$Statement->category_id,
                $Statement->payment_method, $Statement->reference, $Statement->add_receipt);
            }
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            "Date",
            "Amount",
            "Description",
        ];
    }
}
