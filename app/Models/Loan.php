<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'employee_id',
        'loan_option',
        'title',
        'amount',
        'start_date',
        'end_date',
        'reason',
        'created_by',
    ];

    public function employee()
    {
        return $this->hasOne('App\Models\Employee', 'id', 'employee_id');
    }

    public function loanOption()
    {
        return $this->hasOne('App\Models\LoanOption', 'id', 'loan_option');
    }
    public static $Loantypes=[
        'fixed'=>'Fixed',
        'percentage'=> 'Percentage',
    ];
}
