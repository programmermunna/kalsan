<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'ip',
        'date',
        'Details',
        'created_by'
    ];

    public function createdBy()
    {

        return $this->hasOne('App\Models\user', 'id', 'ticket_created');
    }

}
