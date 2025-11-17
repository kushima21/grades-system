<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('dashboard', function () {
    return view('admin.dashboard');
});

Route::get('/default', function () {
    return view('layouts.default');
});

Route::get('/course', function () {
    return view('admin.course');
});



// Instructor

Route::get('/my_grades', function () {
    return view('instructor.my_grades');
});


Route::get('/my_class', function () {
    return view('instructor.my_class');
});


Route::get('/grading&score', function () {
    return view('instructor.grading&score');
});

// Registrar

Route::get('/classes', function () {
    return view('registrar.classes');
});

Route::get('/my_class_archive', function () {
    return view('registrar.my_class_archive');
});


Route::get('/classes_view', function () {
    return view('registrar.classes_view');
});

//user

Route::get('/users', function () {
    return view('admin.users');
});


// Login

Route::get('/login', function () {
    return view('auth.login');
});