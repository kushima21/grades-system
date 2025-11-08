@extends('layouts.default')
@vite(['resources/css/my_class_archive.css', 'resources/js/app.js'])
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
@section('content')

    <div class="my-class-archived-main-container">
        <div class="span">
            <span>Admin</span>
            <span>></span>
            <span>Manage</span>
            <span>></span>
            <span>My Class Archived</span>
        </div>
        <h2 class="my-header">
            My Class Archived
        </h2>
        <div class="archive-folder-main-container">
            <div class="archive-folder-box">
                📁 Academic Year
            </div>
            <div class="year-box">
                📁 2025-2026
            </div>
            <div class="semester-box">
                 📁 First Semester
            </div>
        </div>
    </div>

@endsection