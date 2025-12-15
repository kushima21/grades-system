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
            @foreach ($classes as $class)
                <div class="my-box">
                    <div class="my-class-header">
                        <h3 class="my-c">{{ $class->course_no }}</h3>
                        <h3 class="subheader">{{ $class->descriptive_title }}</h3>
                    </div>
                    <div class="middle-c">
                        <span>{{ $class->academic_year }}</span>
                        <br>
                        <span>School Period: {{ $class->academic_period }}</span>
                        <br>
                        <span>Schedule: {{ $class->schedule }}</span>
                        <br>
                        <span>Status: {{ $class->status }}</span>
                        <br>
                        <span>{{ $class->program }}</span>
                    </div>
                    <div class="bottom-c">
                        <span>Instructor: {{ $class->instructor }}</span>
                        <a href="{{ route('class.show', $class->id) }}" class="icon view-icon" data-tooltip="View">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>


    </div>
@endsection