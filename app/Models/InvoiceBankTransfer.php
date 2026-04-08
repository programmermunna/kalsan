<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceBankTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'order_id',
        'amount',
        'status',
        'date',
        'receipt',
        'created_by',
    ];
}
