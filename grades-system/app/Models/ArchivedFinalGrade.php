<?php

namespace App\Models;
use App\Models\Student;
use Illuminate\Database\Eloquent\Model;

class ArchivedFinalGrade extends Model
{
    protected $table = 'archived_final_grades';

    protected $fillable = [
        'classID', 'subject_code', 'descriptive_title', 'units', 'instructor',
        'academic_period', 'academic_year', 'schedule', 'studentID', 'name',
        'gender', 'email','program','abbreviation', 'department', 'prelim', 'midterm', 'semi_finals',
        'final', 'remarks','final_remark', 'status', 'added_by'
    ];

    public $timestamps = true;

       // Relationship to student_tbl
    public function student()
    {
        return $this->belongsTo(Student::class, 'studentID', 'studentID');
    }
    // Optional relationship
    public function class()
    {
        return $this->belongsTo(ClassArchive::class, 'classID', 'id');
    }




    
}


