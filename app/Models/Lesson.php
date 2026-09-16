<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Lesson extends Model
{
    protected $collection = 'lessons';

    protected $fillable = [
        'course_id',
        'title',
        'content',
        'position',
    ];
}
