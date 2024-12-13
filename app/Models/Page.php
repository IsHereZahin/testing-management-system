<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'name', 'creator_id'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function testCases()
    {
        return $this->hasMany(TestCase::class);
    }
}
