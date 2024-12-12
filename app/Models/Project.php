<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'description'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function testCases()
    {
        return $this->hasManyThrough(TestCase::class, Section::class);
    }
}
