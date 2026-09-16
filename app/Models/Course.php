<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Course extends Model
{
    protected $collection = 'courses';

    protected $fillable = [
        'title',
        'description',
        'category_id',
        'teacher_id',
    ];
}
