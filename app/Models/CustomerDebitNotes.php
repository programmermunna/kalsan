<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerDebitNotes extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill',
        'amount',
        'date',
    ];

    public function usedDebitNote()
    {
        return $this->hasMany(DebitNote::class, 'debit_note', 'id')->sum('amount');
    }

    public function bills()
    {
        return $this->hasOne(Bill::class, 'id', 'bill')->where('type' , 'Bill');
    }

    public static function debitNumberFormat($number)
    {
        return '#DN' . sprintf("%05d", $number);
    }
    
    public static $statues = [
        'Pending',
        'Partially Used',
        'Fully Used',
    ];
}
