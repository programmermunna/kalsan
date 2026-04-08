<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $fillable = [
        'branch_id','department_id','name','created_by'
    ];
    public function branch(){
        return $this->hasOne('App\Models\Branch','id','branch_id');
    }
}
