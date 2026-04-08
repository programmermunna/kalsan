<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'complaint_from',
        'complaint_against',
        'title',
        'complaint_date',
        'description',
        'created_by',
    ];


    public function employee()
    {
        return $this->hasOne('App\Models\Employee', 'id', 'employee_id');
    }

    public function complaintFrom()
    {
        return $this->hasOne('App\Models\Employee', 'id', 'complaint_from');
    }
    public function complaintAgainst()
    {
        return $this->hasOne('App\Models\Employee', 'id', 'complaint_against');
    }
}
