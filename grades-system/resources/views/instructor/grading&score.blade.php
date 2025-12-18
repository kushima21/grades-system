@extends('layouts.default')
@vite(['resources/css/grading&score.css', 'resources/js/app.js'])
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />

@section('content')
    <div class="grading-score-main-container">
        <div class="span">
            <span>Admin</span>
            <span>></span>
            <span>Manage</span>
            <span>></span>
            <span>Grading & Score</span>
        </div>
        <h2 class="my-header">
            Grading & Score
        </h2>
        <div class="search-bar">
            <form method="" action="">
                <input type="text" name="searchClass" id="searchClass" placeholder="Quick Search...">
            </form>
        </div>
        <div class="grading-wrapper">
            <table class="grading-table-container">
                <thead>
                    <tr>
                        <th>Course No</th>
                        <th>Descriptive Title</th>
                        <th>Program</th>
                        <th>Academic Year</th>
                        <th>Academic Period</th>
                        <th>Schedule</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                {{-- Loop through all available classes --}}
                @forelse ($classes as $class)
                    <tr>
                        <td>{{ $class->course_no }}</td>
                        <td>{{ $class->descriptive_title }}</td>
                        <td>{{ $class->program }}</td>
                        <td>{{ $class->academic_year }}</td>
                        <td>{{ $class->academic_period }}</td>
                        <td>{{ $class->schedule }}</td>


                        {{-- Action: View Class --}}
                        <td style="text-align:center; background-color: var(--color9b);">
                            <a href="{{ route('instructor.grading_view', ['id' => $class->id, 'academic_period' => $class->academic_period]) }}"
                               class="view-btn">
                                <i class="fa-solid fa-up-right-from-square"></i> 
                                View Class
                            </a>
                        </td>
                    </tr>
                @empty
                    {{-- No classes found --}}
                    <tr>
                        <td colspan="7" style="text-align:center;">No classes added yet.</td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>
    </div>

@endsection 