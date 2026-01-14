<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalGrade extends Model
{
    protected $table = 'final_grade'; // Table name

    // ✅ Add only the columns you want mass assignable
    protected $fillable = [
        'student_id',
        'course_id',
        'grade',
        'remarks',
        'final_remark',
    ];
}
