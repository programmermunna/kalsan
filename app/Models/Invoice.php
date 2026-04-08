<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Utility;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_id',
        'customer_id',
        'issue_date',
        'due_date',
        'arrival_date',
        'pnr',
        'p_mobile',
        'p_email',
        'origin',
        'destination',
        'status',
        'category_id',
        'vender_id',
        'created_by',
        'branch_id',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'arrival_date' => 'date',
    ];

    public static $statues = [
        'Draft',
        'Sent',
        'Unpaid',
        'Partialy Paid',
        'Paid',
        'Refunded',
    ];


    public function tax()
    {
        return $this->hasOne('App\Models\Tax', 'id', 'tax_id');
    }

    public function items()
    {
        return $this->hasMany('App\Models\InvoiceProduct', 'invoice_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\InvoicePayment', 'invoice_id', 'id');
    }
    public function bankPayments()
    {
        return $this->hasMany('App\Models\InvoiceBankTransfer', 'invoice_id', 'id')->where('status','!=','Approved');
    }
    public function customer()
    {
        return $this->hasOne('App\Models\Customer', 'id', 'customer_id');
    }

    public function getSubTotal()
    {
        $subTotal = 0;
        foreach ($this->items as $product) {
            $linePrice = (float) ($product->price ?? 0);
            $lineDiscount = (float) ($product->discount ?? 0);
            $subTotal += ($linePrice + $lineDiscount);
        }

        return $subTotal;
    }

    public function getTotalTax()
    {
        $taxData = Utility::getTaxData();
        $totalTax = 0;

        foreach ($this->items as $product) {
            if (empty($product->tax)) {
                continue;
            }

            $taxArr = array_filter(explode(',', $product->tax));
            $lineAmount = (float) ($product->price ?? 0);

            foreach ($taxArr as $tax) {
                $rate = !empty($taxData[$tax]['rate']) ? $taxData[$tax]['rate'] : 0;
                $totalTax += Utility::taxRate($rate, $lineAmount, 1, 0);
            }
        }

        return $totalTax;
    }

    public function getTotalDiscount()
    {
        $totalDiscount = 0;
        foreach($this->items as $product)
        {
            $totalDiscount += $product->discount;
        }

        return $totalDiscount;
    }

    public function getTotal()
    {
        return ($this->getSubTotal() -$this->getTotalDiscount()) + $this->getTotalTax();
    }

    public function getDue()
    {
        $paid = $this->payments->sum('amount');
        $due = ($this->getTotal() - $paid) - $this->invoiceTotalCreditNote();

        return max($due, 0);
    }

    public static function change_status($invoice_id, $status)
    {

        $invoice         = Invoice::find($invoice_id);
        $invoice->status = $status;
        $invoice->update();
    }

    public function category()
    {
        return $this->hasOne('App\Models\ProductServiceCategory', 'id', 'category_id');
    }

    public function creditNote()
    {

        return $this->hasMany('App\Models\CreditNote', 'invoice', 'id');
    }

    public function invoiceTotalCreditNote()
    {
        return $this->hasMany(CreditNote::class, 'invoice', 'id')->sum('amount');
    }

    public function invoiceTotalCustomerCreditNote()
    {
        return $this->hasMany(CustomerCreditNotes::class, 'invoice', 'id')->sum('amount');
    }

    public function lastPayments()
    {
        return $this->hasOne('App\Models\InvoicePayment', 'invoice_id', 'id')->latest('created_at');
    }

    public function taxes()
    {
        return $this->hasOne('App\Models\Tax', 'id', 'tax');
    }

    public function user()
    {
    return $this->belongsTo(User::class, 'created_by');
    }



}
