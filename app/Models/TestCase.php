<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestCase extends Model
{
    protected $fillable = ['category_id', 'test_case_id', 'description', 'steps', 'expected_result', 'step_status', 'test_status', 'comments'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
