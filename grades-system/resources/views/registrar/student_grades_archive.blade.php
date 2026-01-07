@extends('layouts.default')
@vite(['resources/css/student_grades.css', 'resources/js/app.js'])
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">




@section('content')
    <div class="student-grades-main-container">
         <div class="breadcrumb">
            <span>Admin</span>
            <span>></span>
            <span>Manage</span>
            <span>></span>
            <span>Student Grades Archived</span>
        </div>
        <h2 class="my-header">Student Grades Archived</h2>
    </div>
@endsection