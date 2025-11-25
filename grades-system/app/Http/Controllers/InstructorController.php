<?php

namespace App\Http\Controllers;
use App\Models\Classes;
use App\Models\User;

use Illuminate\Http\Request;

class InstructorController extends Controller
{
    public function index()
    {
        $user = auth()->user(); // Get the logged-in instructor
        $classes = Classes::where('instructor', $user->name)->get(); // Fetch only their classes

        return view('instructor.my_class', compact('classes'));
    }

      public function grading()
    {
        $user = auth()->user(); // Get the logged-in instructor
        $classes = Classes::where('instructor', $user->name)->get(); // Fetch only their classes

        return view('instructor.grading&score', compact('classes'));
    }
    
       public function studentGrades()
    {
        $user = auth()->user(); // Get the logged-in instructor
        $classes = Classes::where('instructor', $user->name)->get(); // Fetch their classes

        // You can adjust the logic here depending on what you want to display
        return view('instructor.student_grades', compact('classes'));
    }
}