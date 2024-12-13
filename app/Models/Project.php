<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'description'];

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function testCases()
    {
        return $this->hasManyThrough(TestCase::class, Section::class);
    }

    public function testers()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id');
    }
}
