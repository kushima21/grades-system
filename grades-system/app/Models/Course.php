<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
protected $fillable = [
    'course_no',
    'descriptive_title',
    'course_components',
    'units',
    'created_at',
    'updated_at'
];

}
