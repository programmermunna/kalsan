<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'name',
        'purchase_date',
        'supported_date',
        'amount',
        'description',
        'created_by',
    ];

    public function employees()
    {
        return $this->belongsToMany('App\Models\Employee', 'employees', '', 'user_id');
    }

    public function users($users)
    {
        $userIds = explode(',', $users);
    
        // Use eager loading to fetch users for the given employee IDs
        $users = User::whereIn('id', $userIds)
            ->with('employee')
            ->get();
    
        return $users;
    }


}
