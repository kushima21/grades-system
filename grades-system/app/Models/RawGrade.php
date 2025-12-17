<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawGrade extends Model
{
    use HasFactory;

    protected $table = 'raw_grades';
    protected $primaryKey = 'rawID';

    protected $fillable = [
        'classID', 'course_no', 'descriptive_title', 'instructor', 'academic_period', 'schedule',
        'studentID', 'name', 'gender', 'email','program','abbreviation', 'department',
        'prelim', 'midterm_raw', 'midterm', 'semi_finals_raw', 'semi_finals', 'final_raw', 'final', 'remarks',
    ];
}
