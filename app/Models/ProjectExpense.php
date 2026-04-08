<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectExpense extends Model
{
    protected $fillable = [
        'name',
        'date',
        'description',
        'amount',
        'attachment',
        'project_id',
        'task_id',
        'created_by',
    ];

    public function task()
    {
        return $this->hasOne('App\Models\ProjectTask', 'id', 'task_id');
    }

    public function project()
    {
        return $this->hasOne('App\Models\Project', 'id', 'project_id');
    }
}
