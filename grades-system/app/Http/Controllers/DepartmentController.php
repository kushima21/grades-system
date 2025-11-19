<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;

class DepartmentController extends Controller
{
    // Show the department page (blade)
    public function index()
    {
        $departments = Department::all();
        return view('admin.departments', compact('departments'));
    }

    // Store new department
    public function store(Request $request)
    {
        $request->validate([
            'department_code' => 'required|unique:departments,department_code|max:10',
            'department_name' => 'required|max:255',
        ]);

        Department::create([
            'department_code' => $request->department_code,
            'department_name' => $request->department_name,
        ]);

        return redirect()->back()->with('success', 'Department created successfully!');
    }
}
