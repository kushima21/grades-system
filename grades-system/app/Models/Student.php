<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    // 🔹 SET CUSTOM TABLE NAME
    protected $table = 'student_tbl';

    protected $fillable = [
        'fname',
        'mname',
        'lname',
        'studentID',
        'department',
        'email',
        'abbreviation',
        'gender',
        'year_level',
        'nationality',
        'batch_year',
    ];
}
