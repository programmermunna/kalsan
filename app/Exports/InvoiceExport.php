<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\ProductServiceCategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvoiceExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = Invoice::where('created_by', \Auth::user()->creatorId())->get();

        foreach($data as $k => $invoice)
        {
            unset($invoice->id, $invoice->created_by, $invoice->shipping_display,$invoice->discount_apply,$invoice->created_at,$invoice->updated_at);
            $data[$k]["invoice_id"] = \Auth::user()->invoiceNumberFormat($invoice->invoice_id);
//            $data[$k]["customer_id"] = \Auth::user()->customerNumberFormat($invoice->customer_id);
            $data[$k]["customer_id"] = (isset($invoice->customer) && isset($invoice->customer->name)) ? $invoice->customer->name : '';
            $data[$k]['category_id'] = ProductServiceCategory::where('type', 'expense')->first()->name;
            $data[$k]["status"]       = Invoice::$statues[$invoice->status];

        }

        return $data;
    }

    public function headings(): array
    {
        return [
            "Invoice No",
            "Customer",
            "Issue Date",
            "Due Date",
            "Send Date",
            "Category",
            "Ref Number",
            "Status",

        ];
    }
}
