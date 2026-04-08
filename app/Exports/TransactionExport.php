<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransactionExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = [];
        $data = Transaction::where('created_by' , \Auth::user()->id)->get();
        if (!empty($data)) {
            foreach ($data as $k => $Transaction) {
                $account  = Transaction::accounts($Transaction->account);
                unset($Transaction->id,$Transaction->created_by, $Transaction->updated_at, $Transaction->created_at,$Transaction->user_type, $Transaction->user_id,$Transaction->payment_id);
                $data[$k]["account"]        = $account;
            }
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            "Account",
            "Type",
            "Amount",
            "Description",
            "Date",
            "Category",
        ];
    }
}
