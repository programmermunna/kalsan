<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_warehouse',
        'to_warehouse',
        'product_id',
        'quantity',
        'date',
        'created_by',
    ];

    public function product()
    {
        return $this->hasOne('App\Models\ProductService', 'id', 'product_id');
    }

    public function fromWarehouse()
    {
        return $this->hasOne('App\Models\warehouse', 'id', 'from_warehouse');
    }
    public function toWarehouse()
    {
        return $this->hasOne('App\Models\warehouse', 'id', 'to_warehouse');
    }

}
