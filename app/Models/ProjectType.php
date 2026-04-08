<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectType extends Model
{
    protected $primaryKey = 'project_type_id';
    
    protected $fillable = [
        'project_type_name',
        'created_by'
    ];
    
    public function projects()
    {
        return $this->hasMany(Project::class, 'project_type_id', 'project_type_id');
    }
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
