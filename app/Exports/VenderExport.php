<?php

namespace App\Exports;

use App\Models\Vender;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VenderExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = Vender::where('created_by', \Auth::user()->creatorId())->get();

        foreach($data as $k => $vendor)
        {
            unset($vendor->id,$vendor->password, $vendor->lang,$vendor->tax_number,$vendor->is_active, $vendor->avatar,$vendor->created_by,
                $vendor->email_verified_at, $vendor->remember_token,
                $vendor->created_at,$vendor->updated_at);
            $data[$k]["vender_id"]        = \Auth::user()->venderNumberFormat($vendor->vender_id);
            $data[$k]["balance"]          = \Auth::user()->priceFormat($vendor->balance);
//            $data[$k]["avatar"]           = !empty($vendor->avatar) ? asset(\Storage::url('uploads/avatar')) . '/' . $vendor->avatar : '-';
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            "Vendor No",
            "Name",
            "Email",
            "Contact",
            "Billing Name",
            "Billing Country",
            "Billing State",
            "Billing City",
            "Billing Phone",
            "Billing Zip",
            "Billing Address",
            "Shipping Name",
            "Shipping Country",
            "Shipping State",
            "Shipping City",
            "Shipping Phone",
            "Shipping Zip",
            "Shipping Address",
            "Balance",

        ];
    }
}
