<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestCase extends Model
{
    protected $fillable = [
        'page_id',
        'section',
        'test_case_id',
        'test_title',
        'description',
        'test_status',
        'comments',
        'tested_by',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function tested_by()
    {
        return $this->belongsTo(User::class);
    }
}
