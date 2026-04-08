<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCreditNotes extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice',
        'customer',
        'amount',
        'date',
    ];
    
    public function custom_customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer');
    }

    public function usedCreditNote()
    {
        return $this->hasMany(CreditNote::class, 'credit_note', 'id')->sum('amount');
    }

    public function invoices()
    {
        return $this->hasOne(Invoice::class, 'id', 'invoice');
    }

    public static function creditNumberFormat($number)
    {
        return '#CN' . sprintf("%05d", $number);
    }
    
    public static $statues = [
        'Pending',
        'Partially Used',
        'Fully Used',
    ];
}
