@extends('layouts.default')
@vite(['resources/css/my_class.css', 'resources/js/app.js'])
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

@section('content')
    <div class="my-class-main-container">
        <div class="span">
            <span>Admin</span>
            <span>></span>
            <span>Manage</span>
            <span>></span>
            <span>My Classes</span>
        </div>
        <h2 class="my-header">
            My Classes
        </h2>

        <div class="class-search">
            <form method="" action="">
                <input type="" name="searchBtn" id="searchClass" placeholder="Quick Search...">
            </form>
        </div>

        <div class="my-class-main-box-container">
            <div class="my-box">
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
                    <span>Total Student: 30</span>
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