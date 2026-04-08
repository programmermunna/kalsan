<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceProduct extends Model
{
    protected $fillable = [
        'invoice_id',
        'type',
        'ticket_number',
        'passanger_name',
        'destination',
        'fare',
        'commission',
        'cust_commission',
        'description',
        'discount',
        'tax',
        'refund',
        'price',
    ];

    public function product(){
        return $this->hasOne('App\Models\ProductService', 'id', 'product_id');
    }

}
