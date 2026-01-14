<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;

class StudentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'fname'        => 'required|string|max:255',
            'mname'        => 'nullable|string|max:255',
            'lname'        => 'required|string|max:255',
            'studentID'    => 'required|string|max:50|unique:student_tbl,studentID',
            'department'   => 'required|string',
            'email'   => 'required|email',
            'abbreviation' => 'required|string',
            'gender'       => 'required|string',
            'year_level'   => 'required|integer',
            'nationality'  => 'required|string|max:100',
            'batch_year'   => 'required|string|max:50',
            'major'   => 'nullable|string|max:100',
        ]);

        Student::create([
            'fname'        => $request->fname,
            'mname'        => $request->mname,
            'lname'        => $request->lname,
            'studentID'    => $request->studentID,
            'department'   => $request->department,
            'email'   => $request->email,
            'abbreviation' => $request->abbreviation,
            'gender'       => $request->gender,
            'year_level'   => $request->year_level,
            'nationality'  => $request->nationality,
            'batch_year'   => $request->batch_year,
            'major'   => $request->major,
        ]);

        return redirect()->back()->with('success', 'Student successfully added!');
    }
}
