<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['project_id', 'name'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}
