<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
class CourseController extends Controller
{
public function store(Request $request)
{
    $request->validate([
        'course_no' => 'required|string|max:255',
        'descriptive_title' => 'required|string|max:255',
        'course_components' => 'required|string|max:255',
        'units' => 'required|integer|min:1',
    ]);

    Course::create([
        'course_no' => $request->course_no,
        'descriptive_title' => $request->descriptive_title,
        'course_components' => $request->course_components,
        'units' => $request->units,
    ]);

    return redirect()->route('course.index')->with('success', 'Course created successfully!');
}

public function index()
{
    $courses = Course::orderBy('created_at', 'desc')->get();
    return view('admin.course', compact('courses'));
}

public function destroy($id)
{
    $course = Course::findOrFail($id);
    $course->delete();

    return redirect()->route('course.index')->with('success', 'Course deleted successfully!');
}

public function update(Request $request, $id)
{
    $request->validate([
        'course_no' => 'required|string|max:255',
        'descriptive_title' => 'required|string|max:255',
        'course_components' => 'required|string|max:255',
        'units' => 'required|integer|min:1',
    ]);

    $course = Course::findOrFail($id);

    $course->update([
        'course_no' => $request->course_no,
        'descriptive_title' => $request->descriptive_title,
        'course_components' => $request->course_components,
        'units' => $request->units,
    ]);

    return redirect()->route('course.index')->with('success', 'Course updated successfully!');
}

public function search(Request $request)
{
    $query = $request->get('query', '');

    $courses = Course::where('course_no', 'LIKE', "%{$query}%")
                ->orWhere('descriptive_title', 'LIKE', "%{$query}%")
                ->limit(5)
                ->get();

    return response()->json($courses);
}

}
