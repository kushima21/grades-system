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
                <div class="my-box-c">
                    <img class="logo-image" src="{{ asset('system_images/icon.png') }}" alt="LOGO">
                    <div class="m-subcontainer">
                        <h3 class="my-box-head">
                            CS-10
                        </h3>
                        <h2 class="my-descriptive">
                            Programming 1
                        </h2>
                    </div>
                </div>
                <div class="my-class-details-container">
                    <ul>
                        <li>
                            School Year:
                            <span>2025-2026</span>
                        </li>
                        <li>
                            Semester:
                            <span>First Semester</span>
                        </li>
                        <li>
                            Schedule:
                            <span>10:00am - 12:00pm M</span>
                        </li>
                        <li>
                            Student:
                            <span>35</span>
                        </li>
                    </ul>
                </div>
                <div class="my-class-bottom">
                    <p class="email">Instructor: johnhondrada@ckcm.edu.ph</p>
                    <button type="button" class="viewBtn">View Class</button>
                </div>
            </div>
        </div>
    </div>
@endsection