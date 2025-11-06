@extends('layouts.default')
@vite(['resources/css/my_grades.css', 'resources/js/app.js'])
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
<link rel="icon" href="cmd-icon.png" type="image/png">
@section('content')
    <div class="my_grades-main-container">
        <div class="span">
            <span>Admin</span>
            <span>></span>
            <span>Manage</span>
            <span>></span>
            <span>My Grades</span>
        </div>
        <h2 class="my-header">
            My Grades
        </h2>
        <div class="search-bar">
            <form method="" action="">
                <input type="text" name="searchClass" id="searchClass" placeholder="Quick Search...">
            </form>
        </div>

        <div class="my_grades-container-box">
            <div class="my-grades-box">
                <div class="my-class-header">
                    <h3 class="my-c">CS-10</h3>
                    <h3 class="subheader">Programming 1</h3>
                </div>
                <div class="middle-c">
                    <span>School Year: 2025-2026</span>
                    <br>
                    <span>School Period: 1st Semester</span>
                    <br>
                    <span>Schedule: 8:00am-10:00am MTH</span>
                    <br>
                    <span>Status: Active</span>
                </div>
                <div class="bottom-c">
                    <span>Instructor: John Mark Hondrada</span>
                    <a href="#">
                        <i class="fa-solid fa-right-from-bracket"></i> 
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection