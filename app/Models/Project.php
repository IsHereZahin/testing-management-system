<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'description'];

    public function testers()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id');
    }

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    public function testCases()
    {
        return $this->hasManyThrough(TestCase::class, Page::class, 'project_id', 'page_id', 'id', 'id');
    }
}
