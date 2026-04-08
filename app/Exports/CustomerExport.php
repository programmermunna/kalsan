<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = Customer::where('created_by', \Auth::user()->creatorId())->get();
        foreach($data as $k => $customer)
        {
            unset($customer->id,$customer->password,$customer->avatar,
                $customer->tax_number,$customer->is_active, $customer->lang,
                $customer->created_by, $customer->email_verified_at, $customer->remember_token,
                $customer->created_at,$customer->updated_at);
            $data[$k]["customer_id"] = \Auth::user()->customerNumberFormat($customer->customer_id);
            $data[$k]["balance"]     = \Auth::user()->priceFormat($customer->balance);
//            $data[$k]["avatar"]      = !empty($customer->avatar) ? asset(\Storage::url('uploads/avatar')) . '/' . $customer->avatar : '-';
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            "Customer No",
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
