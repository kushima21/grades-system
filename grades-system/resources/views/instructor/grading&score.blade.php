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
                        <th>Academic Year</th>
                        <th>Academic Period</th>
                        <th>Schedule</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>CS 10</td>
                        <td>Programming 1</td>
                        <td>2025-2026</td>
                        <td>1st Semester</td>
                        <td>10:00am-12:00pm Mon/Tue/Wed</td>
                        <td>
                            <div class="action-container">
                                <button class="view-score-btn">View Class</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection